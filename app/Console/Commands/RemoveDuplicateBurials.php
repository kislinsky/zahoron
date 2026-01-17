<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Burial;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateBurials extends Command
{
    protected $signature = 'burials:remove-duplicates';
    protected $description = 'Удаляет дубликаты захоронений по ФИО, датам и кладбищу';

    public function handle()
    {
        $this->info('Поиск дубликатов...');

        // Настройки для оптимизации при работе с большими объемами данных
        DB::statement('SET SESSION sql_mode = ""'); // Отключаем строгий режим MySQL
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $totalDeleted = 0;
        $batchSize = 1000; // Удаляем пачками по 1000 записей
        
        // Сначала находим ID дубликатов одним запросом
        $duplicateGroups = DB::table('burials')
            ->select([
                'name',
                'surname',
                'patronymic',
                'date_birth',
                'date_death',
                'cemetery_id',
                DB::raw('GROUP_CONCAT(id ORDER BY id) as ids')
            ])
            ->whereNotNull('name') // Исключаем пустые имена
            ->groupBy([
                'name',
                'surname',
                'patronymic',
                'date_birth',
                'date_death',
                'cemetery_id'
            ])
            ->havingRaw('COUNT(*) > 1')
            ->cursor();

        $idsToDelete = [];
        
        // Создаем прогресс-бар для отображения прогресса обработки групп дубликатов
        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat(' %current% групп обработано [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
        $this->info("Обработка групп дубликатов...");
        
        foreach ($duplicateGroups as $group) {
            $ids = explode(',', (string)$group->ids);
            
            // Фильтруем пустые значения
            $ids = array_filter($ids, function($id) {
                return !empty($id) && is_numeric($id);
            });
            
            if (count($ids) > 1) {
                // Оставляем первый ID, остальные добавляем в массив для удаления
                $idsToDelete = array_merge($idsToDelete, array_slice($ids, 1));
                
                // Удаляем пачками для оптимизации памяти
                if (count($idsToDelete) >= $batchSize) {
                    $this->deleteBatch($idsToDelete);
                    $totalDeleted += count($idsToDelete);
                    $idsToDelete = [];
                }
            }
            
            // Обновляем прогресс-бар после обработки каждой группы
            $progressBar->advance();
        }

        // Удаляем оставшиеся записи
        if (!empty($idsToDelete)) {
            $this->deleteBatch($idsToDelete);
            $totalDeleted += count($idsToDelete);
        }
        
        // Завершаем прогресс-бар
        $progressBar->finish();
        $this->newLine(); // Добавляем пустую строку после прогресс-бара

        $this->info("Готово! Всего удалено {$totalDeleted} дубликатов.");
    }

    /**
     * Удаление пачки записей
     */
    private function deleteBatch(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        // Преобразуем ID в целые числа для безопасности
        $ids = array_map('intval', $ids);
        
        // Удаление с использованием chunk для больших пачек
        $chunkSize = 500;
        $chunks = array_chunk($ids, $chunkSize);
        
        foreach ($chunks as $chunk) {
            Burial::whereIn('id', $chunk)->delete();
        }
        
        $this->info("Удалено " . count($ids) . " записей");
    }
}