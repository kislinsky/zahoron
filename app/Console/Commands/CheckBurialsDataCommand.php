<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Burial; // Предполагаемая модель
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckBurialsDataCommand extends Command
{
    /**
     * Название и сигнатура команды
     */
    protected $signature = 'burials:check 
                            {--chunk-size=1000 : Размер чанка для обработки}
                            {--limit=0 : Ограничение количества записей для проверки (0 - все)}
                            {--skip-fixed : Пропускать уже исправленные записи (status != 1)}
                            {--log : Включить подробное логирование}';

    /**
     * Описание команды
     */
    protected $description = 'Проверка данных захоронений на корректность и обновление статуса';

    /**
     * Счетчики для статистики
     */
    private $totalProcessed = 0;
    private $totalFailed = 0;
    private $totalSkipped = 0;
    private $batchSize;

    /**
     * Регулярное выражение для проверки формата даты
     */
    private const DATE_REGEX = '/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.(19|20)\d{2}$/';

    /**
     * Выполнение команды
     */
    public function handle()
    {
        $this->batchSize = (int)$this->option('chunk-size');
        $limit = (int)$this->option('limit');
        $skipFixed = $this->option('skip-fixed');
        $logEnabled = $this->option('log');

        $this->info('Начало проверки данных захоронений...');
        $this->info("Размер чанка: {$this->batchSize}");
        $this->info("Ограничение: " . ($limit > 0 ? $limit : 'нет'));
        
        if ($skipFixed) {
            $this->info('Будут пропущены записи со status != 1');
        }

        // Настройка запроса
        $query = Burial::query();
        
        if ($skipFixed) {
            $query->where('status', 1); // Проверяем только корректные записи
        }
        
        if ($limit > 0) {
            $query->limit($limit);
        }

        $totalToCheck = $query->count();
        $this->info("Всего записей для проверки: {$totalToCheck}");

        $bar = $this->output->createProgressBar($totalToCheck);
        $bar->start();

        // Обработка чанками для экономии памяти
        $query->chunkById($this->batchSize, function ($burials) use ($bar, $logEnabled) {
            $updates = [];
            $now = now();

            foreach ($burials as $burial) {
                $this->totalProcessed++;
                
                $newStatus = $this->checkBurialData($burial);
                
                // Если статус изменился
                if ($burial->status != $newStatus) {
                    $updates[] = [
                        'id' => $burial->id,
                        'status' => $newStatus,
                        'updated_at' => $now,
                    ];
                    
                    if ($newStatus == 0) {
                        $this->totalFailed++;
                        
                        if ($logEnabled) {
                            Log::info('Запись с ошибкой', [
                                'id' => $burial->id,
                                'old_status' => $burial->status,
                                'new_status' => $newStatus,
                                'data' => $burial->only(['name', 'surname', 'patronymic', 'date_birth', 'date_death'])
                            ]);
                        }
                    }
                } else {
                    $this->totalSkipped++;
                }
                
                $bar->advance();
            }

            // Массовое обновление
            if (!empty($updates)) {
                $this->batchUpdate($updates);
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Вывод статистики
        $this->info('Проверка завершена!');
        $this->info("Обработано записей: {$this->totalProcessed}");
        $this->info("Исправлено записей: {$this->totalFailed}");
        $this->info("Пропущено записей: {$this->totalSkipped}");
        
        if ($logEnabled) {
            $this->info('Подробное логирование включено. Проверьте storage/logs/laravel.log');
        }

        return Command::SUCCESS;
    }

    /**
     * Проверка данных одного захоронения
     */
    private function checkBurialData(Burial $burial): int
    {
        // Проверка обязательных полей
        if (empty($burial->name) || 
            empty($burial->surname) || 
            empty($burial->patronymic) || 
            empty($burial->date_birth) || 
            empty($burial->date_death)) {
            return 0;
        }

        // Проверка формата дат
        if (!$this->isValidDateFormat($burial->date_birth) || 
            !$this->isValidDateFormat($burial->date_death)) {
            return 0;
        }

        // Проверка, что дата смерти позже даты рождения
        if (!$this->isDeathAfterBirth($burial->date_birth, $burial->date_death)) {
            return 0;
        }

        // Все проверки пройдены
        return 1;
    }

    /**
     * Проверка формата даты (дд.мм.гггг)
     */
    private function isValidDateFormat(string $date): bool
    {
        // Проверка по регулярному выражению
        if (!preg_match(self::DATE_REGEX, $date)) {
            return false;
        }

        // Дополнительная проверка на корректность даты (например, 31.02.2023)
        try {
            list($day, $month, $year) = explode('.', $date);
            return checkdate((int)$month, (int)$day, (int)$year);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Проверка, что дата смерти позже даты рождения
     */
    private function isDeathAfterBirth(string $birthDate, string $deathDate): bool
    {
        try {
            $birth = Carbon::createFromFormat('d.m.Y', $birthDate);
            $death = Carbon::createFromFormat('d.m.Y', $deathDate);
            
            return $death->greaterThan($birth);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Массовое обновление записей
     */
    private function batchUpdate(array $updates): void
    {
        $table = (new Burial())->getTable();
        
        $caseIds = implode(',', array_column($updates, 'id'));
        $caseStatus = 'CASE id ';
        $caseUpdated = 'CASE id ';
        
        foreach ($updates as $update) {
            $caseStatus .= "WHEN {$update['id']} THEN {$update['status']} ";
            $caseUpdated .= "WHEN {$update['id']} THEN '{$update['updated_at']}' ";
        }
        
        $caseStatus .= 'END';
        $caseUpdated .= 'END';
        
        DB::statement("
            UPDATE {$table} 
            SET 
                status = {$caseStatus},
                updated_at = {$caseUpdated}
            WHERE id IN ({$caseIds})
        ");
    }
}