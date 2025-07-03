<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use App\Models\Cemetery;
use Illuminate\Support\Facades\DB;

class CheckCityDuplicates extends Command
{
    protected $signature = 'cities:check-duplicates';
    protected $description = 'Check for city duplicates and clean up, prioritizing cities with most organizations';

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

        $this->info('Found ' . $duplicates->count() . ' duplicate city names.');

        $deletedCount = 0;
        $mergedCount = 0;

        foreach ($duplicates as $cityName) {
            $this->line("\nProcessing duplicates for: {$cityName}");

            // Получаем города, отсортированные по количеству организаций (по убыванию)
            $cities = City::withCount('organizations')
                ->where('title', $cityName)
                ->orderByDesc('organizations_count')
                ->get();

            $mainCity = $cities->first();
            $otherCities = $cities->slice(1);

            $this->info("Main city ID: {$mainCity->id} (Organizations: {$mainCity->organizations_count})");

            foreach ($otherCities as $cityToProcess) {
                try {
                    DB::transaction(function () use ($mainCity, $cityToProcess, &$deletedCount, &$mergedCount) {
                        $hasOrganizations = $cityToProcess->organizations_count > 0;
                        $hasCemeteries = $cityToProcess->cemeteries()->exists();

                        // Переносим организации (если есть)
                        if ($hasOrganizations) {
                            $orgCount = $cityToProcess->organizations()->count();
                            $cityToProcess->organizations()->update(['city_id' => $mainCity->id]);
                            $this->line("Moved {$orgCount} organizations to main city ID: {$mainCity->id}");
                        }

                        // Переносим кладбища (если есть)
                        if ($hasCemeteries) {
                            $cemeteryCount = $cityToProcess->cemeteries()->count();
                            $cityToProcess->cemeteries()->update(['city_id' => $mainCity->id]);
                            $this->line("Moved {$cemeteryCount} cemeteries to main city ID: {$mainCity->id}");
                        }

                        // Удаляем город-дубликат
                        $cityToProcess->delete();

                        if ($hasOrganizations || $hasCemeteries) {
                            $mergedCount++;
                            $this->info("Merged and deleted city ID: {$cityToProcess->id}");
                        } else {
                            $deletedCount++;
                            $this->line("Deleted empty city ID: {$cityToProcess->id}");
                        }
                    });
                } catch (\Exception $e) {
                    $this->error("Error processing city ID: {$cityToProcess->id} - " . $e->getMessage());
                }
            }
        }

        $this->info("\nOperation completed!");
        $this->info("Total merged cities: {$mergedCount}");
        $this->info("Total deleted empty cities: {$deletedCount}");
        
        return 0;
    }
}