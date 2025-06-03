<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class CheckCityDuplicates extends Command
{
    /**
     * The title and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:check-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for city duplicates and clean up cities without organizations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting duplicate cities check...');

        // Находим дубликаты городов (с одинаковыми названиями)
        $duplicates = City::select('title')
            ->groupBy('title')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('title');

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate cities found.');
            return 0;
        }

        $this->info('Found ' . $duplicates->count() . ' city names with duplicates.');

        $deletedCount = 0;

        foreach ($duplicates as $cityName) {
            $this->line("Processing duplicates for city: {$cityName}");

            // Получаем все города с этим именем
            $cities = City::withCount('organizations')
                ->where('title', $cityName)
                ->get();

            // Группируем по наличию организаций
            $withOrgs = $cities->filter(fn($city) => $city->organizations_count > 0);
            $withoutOrgs = $cities->filter(fn($city) => $city->organizations_count === 0);

            // Если есть города с организациями и без - удаляем те, где организаций нет
            if ($withOrgs->isNotEmpty() && $withoutOrgs->isNotEmpty()) {
                $this->info("Found {$withoutOrgs->count()} duplicates without organizations for '{$cityName}'");

                // Удаляем города без организаций
                foreach ($withoutOrgs as $city) {
                    try {
                        $city->delete();
                        $deletedCount++;
                        $this->line("Deleted city ID: {$city->id} (no organizations)");
                    } catch (\Exception $e) {
                        $this->error("Failed to delete city ID: {$city->id} - " . $e->getMessage());
                    }
                }
            } else {
                $this->line("No action needed for '{$cityName}' - all duplicates have the same organization status");
            }
        }

        $this->info("Completed! Deleted {$deletedCount} duplicate cities without organizations.");
        return 0;
    }
}