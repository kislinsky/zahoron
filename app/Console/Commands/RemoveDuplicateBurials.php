<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Burial; // Предполагается, что у вас есть модель Burial
use Illuminate\Support\Facades\DB;

class RemoveDuplicateBurials extends Command
{
    protected $signature = 'burials:remove-duplicates';
    protected $description = 'Удаляет дубликаты захоронений по ФИО, датам и кладбищу';

    public function handle()
    {
        // Находим дубликаты
        $duplicates = Burial::query()
            ->select([
                'name',
                'surname',
                'patronymic',
                'date_birth',
                'date_death',
                'cemetery_id',
                DB::raw('COUNT(*) as count')
            ])
            ->groupBy([
                'name',
                'surname',
                'patronymic',
                'date_birth',
                'date_death',
                'cemetery_id'
            ])
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('Дубликаты не найдены.');
            return;
        }

        $this->info('Найдено ' . $duplicates->count() . ' групп дубликатов.');

        $totalDeleted = 0;

        foreach ($duplicates as $duplicate) {
            $this->line("Обработка: {$duplicate->surname} {$duplicate->name} {$duplicate->patronymic}");

            // Находим все записи с этими данными
            $records = Burial::where([
                'name' => $duplicate->name,
                'surname' => $duplicate->surname,
                'patronymic' => $duplicate->patronymic,
                'date_birth' => $duplicate->date_birth,
                'date_death' => $duplicate->date_death,
                'cemetery_id' => $duplicate->cemetery_id,
            ])->orderBy('id')->get();

            // Оставляем первую запись, остальные удаляем
            $recordsToDelete = $records->slice(1);
            
            $deletedCount = count($recordsToDelete);
            $totalDeleted += $deletedCount;

            $this->info("Удалено {$deletedCount} дубликатов.");

            foreach ($recordsToDelete as $record) {
                $record->delete();
            }
        }

        $this->info("Готово! Всего удалено {$totalDeleted} дубликатов.");
    }
}