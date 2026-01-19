<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeBurialsTable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateBurials extends Command
{
    protected $signature = 'burials:remove-duplicates';
    protected $description = 'Удаляет дубликаты захоронений по ФИО, датам и кладбищу';

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->info('Поиск дубликатов...');
        $startTime = microtime(true);

        $deleted = DB::delete("
            DELETE b1 FROM burials b1
            INNER JOIN burials b2 ON
                b1.surname = b2.surname AND
                b1.name = b2.name AND
                b1.patronymic = b2.patronymic AND
                b1.date_birth = b2.date_birth AND
                b1.date_death = b2.date_death AND
                b1.cemetery_id = b2.cemetery_id
            WHERE b1.id > b2.id
        ");

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Готово! Удалено дубликатов: {$deleted}. За {$duration} секунд.");

        // Оптимизация таблицы захоронений
        OptimizeBurialsTable::dispatch();
    }
}
