<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class CheckCityDuplicates extends Command
{
    protected $signature = 'cities:check-duplicates';
    protected $description = 'Check for city duplicates and clean up cities without organizations';

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
        $mergedCount = 0;

        foreach ($duplicates as $cityName) {
            $this->line("Processing duplicates for city: {$cityName}");

            // Получаем все города с этим именем с подсчетом организаций
            $cities = City::withCount('organizations')
                ->where('title', $cityName)
                ->orderByDesc('organizations_count')
                ->get();

            // Если только один город - пропускаем
            if ($cities->count() < 2) {
                continue;
            }

            // Разделяем города с организациями и без
            $withOrgs = $cities->filter(fn($city) => $city->organizations_count > 0);
            $withoutOrgs = $cities->filter(fn($city) => $city->organizations_count === 0);

            // Случай 1: есть города без организаций
            if ($withoutOrgs->isNotEmpty()) {
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
            } 
            // Случай 2: все города имеют организации
            else {
                $this->info("All duplicates for '{$cityName}' have organizations");

                // Сортируем по количеству организаций (убывание)
                $sortedCities = $cities->sortByDesc('organizations_count');
                $mainCity = $sortedCities->first();
                $otherCities = $sortedCities->slice(1);

                foreach ($otherCities as $cityToMerge) {
                    try {
                        DB::transaction(function () use ($mainCity, $cityToMerge) {
                            // Переносим организации
                            $cityToMerge->organizations()->update(['city_id' => $mainCity->id]);
                            
                            // Удаляем город
                            $cityToMerge->delete();
                        });

                        $mergedCount++;
                        $this->line("Merged organizations from city ID: {$cityToMerge->id} ({$cityToMerge->organizations_count} orgs) to city ID: {$mainCity->id}");
                    } catch (\Exception $e) {
                        $this->error("Failed to merge city ID: {$cityToMerge->id} - " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Completed!");
        $this->info("Deleted {$deletedCount} duplicate cities without organizations.");
        $this->info("Merged {$mergedCount} duplicate cities with organizations.");
        
        return 0;
    }
}