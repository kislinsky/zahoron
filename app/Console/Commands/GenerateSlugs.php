<?php

namespace App\Console\Commands;

use App\Models\Cemetery;
use App\Models\Church;
use App\Models\Crematorium;
use App\Models\Mortuary;
use App\Models\Mosque;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateSlugs extends Command
{
    protected $signature = 'slugs:generate 
                            {model? : Specific model to update (e.g., Cemetery, Church, Crematorium, Mortuary, Mosque)}
                            {--F|force : Force regenerate all slugs, not just empty ones}
                            {--D|duplicates : Only fix duplicate slugs}';
    
    protected $description = 'Generate slugs for models where slug is empty or null or fix duplicates';

    public function handle()
    {
        $models = [
            'Cemetery' => [Cemetery::class => 'title'],
            'Church' => [Church::class => 'title'],
            'Crematorium' => [Crematorium::class => 'title'],
            'Mortuary' => [Mortuary::class => 'title'],
            'Mosque' => [Mosque::class => 'title']
        ];

        $selectedModel = $this->argument('model');
        
        // Если указана конкретная модель
        if ($selectedModel) {
            if (!isset($models[$selectedModel])) {
                $this->error("Model '{$selectedModel}' not found. Available models: " . implode(', ', array_keys($models)));
                return 1;
            }
            
            $models = [$selectedModel => $models[$selectedModel]];
        } else {
            // Выбор модели через интерактивный список
            if (!$this->confirm('Do you want to process all models?', false)) {
                $selected = $this->choice(
                    'Select model to process:',
                    array_keys($models),
                    0
                );
                $models = [$selected => $models[$selected]];
            }
        }

        // Выбор режима работы
        $mode = $this->choice(
            'Select mode:',
            [
                'Fix empty/null slugs (default)',
                'Fix duplicate slugs only',
                'Regenerate all slugs (force)'
            ],
            0
        );

        $totalUpdated = 0;
        $results = [];

        foreach ($models as $modelName => $modelConfig) {
            $modelClass = key($modelConfig);
            $titleField = current($modelConfig);
            $table = (new $modelClass())->getTable();
            
            $this->info("Processing {$modelName} (table: {$table})...");

            if (strpos($mode, 'duplicate') !== false) {
                $count = $this->fixDuplicateSlugs($modelClass, $titleField, $table);
            } elseif (strpos($mode, 'force') !== false || $this->option('force')) {
                $count = $this->regenerateAllSlugs($modelClass, $titleField, $table);
            } else {
                $count = $this->fixEmptySlugs($modelClass, $titleField, $table);
            }

            $results[$modelName] = $count;
            $totalUpdated += $count;
        }

        $this->table(['Model', 'Updated'], array_map(
            fn ($model, $count) => [$model, $count],
            array_keys($results),
            $results
        ));

        $this->info("Total updated: {$totalUpdated}");

        return 0;
    }

    /**
     * Fix empty or null slugs
     */
    protected function fixEmptySlugs($modelClass, $titleField, $table): int
    {
        $count = 0;
        
        DB::transaction(function () use ($modelClass, $titleField, $table, &$count) {
            $modelClass::query()
                ->whereNotNull($titleField)
                ->where(function($query) {
                    $query->whereNull('slug')
                          ->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(500, function ($records) use ($modelClass, $titleField, &$count) {
                    $this->processRecords($records, $modelClass, $titleField, $count);
                });
        });

        return $count;
    }

    /**
     * Fix duplicate slugs
     */
    protected function fixDuplicateSlugs($modelClass, $titleField, $table): int
    {
        $count = 0;
        
        DB::transaction(function () use ($modelClass, $titleField, $table, &$count) {
            // Находим дубликаты slug
            $duplicates = DB::table($table)
                ->select('slug', DB::raw('COUNT(*) as count'))
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->groupBy('slug')
                ->having('count', '>', 1)
                ->get();

            $this->info("Found {$duplicates->count()} duplicate slug groups");

            foreach ($duplicates as $duplicate) {
                $records = $modelClass::where('slug', $duplicate->slug)
                    ->orderBy('id')
                    ->get();

                // Первую запись оставляем с оригинальным slug
                $first = true;
                foreach ($records as $record) {
                    if ($first) {
                        $first = false;
                        continue;
                    }

                    // Генерируем уникальный slug для дубликатов
                    $newSlug = $this->generateUniqueSlug($record->{$titleField}, $modelClass, $record->id);
                    
                    if ($newSlug !== $record->slug) {
                        $record->slug = $newSlug;
                        $record->save();
                        $count++;
                        
                        $this->info("Updated ID {$record->id}: {$duplicate->slug} -> {$newSlug}", 'v');
                    }
                }
            }
        });

        return $count;
    }

    /**
     * Regenerate all slugs
     */
    protected function regenerateAllSlugs($modelClass, $titleField, $table): int
    {
        $count = 0;
        
        DB::transaction(function () use ($modelClass, $titleField, $table, &$count) {
            $modelClass::query()
                ->whereNotNull($titleField)
                ->orderBy('id')
                ->chunkById(500, function ($records) use ($modelClass, $titleField, &$count) {
                    $this->processRecords($records, $modelClass, $titleField, $count, true);
                });
        });

        return $count;
    }

    /**
     * Process batch of records
     */
    protected function processRecords($records, $modelClass, $titleField, &$count, $force = false): void
    {
        $recordsToUpdate = [];
        
        foreach ($records as $record) {
            $newSlug = generateUniqueSlug($record->{$titleField}, $modelClass);
            
            if ($force || $newSlug !== $record->slug) {
                $recordsToUpdate[] = [
                    'id' => $record->id,
                    'slug' => $newSlug
                ];
            }
        }

        if (!empty($recordsToUpdate)) {
            $this->batchUpdate($modelClass, $recordsToUpdate);
            $count += count($recordsToUpdate);
            
            if ($this->getOutput()->isVerbose()) {
                foreach ($recordsToUpdate as $update) {
                    $this->line("Updated ID {$update['id']}: {$update['slug']}", 'v');
                }
            }
        }
    }

    /**
     * Batch update slugs
     */
    protected function batchUpdate($modelClass, $recordsToUpdate): void
    {
        $case = "CASE id ";
        $ids = [];
        $params = [];
        $table = (new $modelClass())->getTable();
        
        foreach ($recordsToUpdate as $record) {
            $case .= "WHEN ? THEN ? ";
            $ids[] = $record['id'];
            $params[] = $record['id'];
            $params[] = $record['slug'];
        }
        $case .= "END";
        
        $ids = implode(',', $ids);
        
        DB::update(
            "UPDATE {$table} 
            SET slug = {$case} 
            WHERE id IN ({$ids})",
            $params
        );
    }

    /**
     * Generate unique slug with optional exclusion
     */
    protected function generateUniqueSlug($title, $modelClass, $excludeId = null): string
    {
        $slug = generateUniqueSlug($title, $modelClass);
        
        // Проверяем уникальность с учетом исключения
        if ($excludeId) {
            $counter = 1;
            $originalSlug = $slug;
            
            while ($modelClass::where('slug', $slug)
                ->where('id', '!=', $excludeId)
                ->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
        }
        
        return $slug;
    }
}