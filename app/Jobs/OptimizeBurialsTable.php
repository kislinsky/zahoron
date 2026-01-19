<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizeBurialsTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;

    public function handle(): void
    {
        Log::info('Запуск оптимизации таблицы burials');
        $startTime = microtime(true);

        DB::statement("OPTIMIZE TABLE burials");

        $duration = round(microtime(true) - $startTime, 2);
        Log::info("Таблица burials успешно оптимизирована за {$duration} сек.");
    }
}
