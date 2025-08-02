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
        // Отключаем логирование запросов для ускорения
        DB::disableQueryLog();
        
        $this->info('Поиск дубликатов...');

        // Используем chunkById для обработки больших объемов данных
        $totalDeleted = 0;
        
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

        foreach ($duplicateGroups as $group) {
            $ids = explode(',', $group->ids);
            // Оставляем первый ID, остальные удаляем
            $idsToDelete = array_slice($ids, 1);
            
            $count = count($idsToDelete);
            if ($count > 0) {
                // Массовое удаление
                Burial::whereIn('id', $idsToDelete)->delete();
                $totalDeleted += $count;
            }
        }

        $this->info("Готово! Всего удалено {$totalDeleted} дубликатов.");
        
        // Включаем логирование обратно
        DB::enableQueryLog();
    }
}