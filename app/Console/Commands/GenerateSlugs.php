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
    protected $signature = 'slugs:generate';
    protected $description = 'Generate slugs for models where slug is empty or null';

    public function handle()
    {
        $models = [
            Cemetery::class => 'title',
            Crematorium::class => 'title', 
            Mortuary::class => 'title',
            Mosque::class => 'title',
            Church::class => 'title'
        ];

        $totalUpdated = 0;
        $results = [];

        foreach ($models as $model => $titleField) {
            $count = 0;
            
            DB::transaction(function () use ($model, $titleField, &$count) {
                $model::query()
                    ->whereNotNull($titleField)
                    ->where(function($query) {
                        $query->whereNull('slug')
                              ->orWhere('slug', '');
                    })
                    ->orderBy('id')
                    ->chunkById(500, function ($records) use ($model, $titleField, &$count) {
                        $recordsToUpdate = [];
                        
                        foreach ($records as $record) {
                            $newSlug = generateUniqueSlug($record->{$titleField}, $model);
                            
                            if ($newSlug !== $record->slug) {
                                $recordsToUpdate[] = [
                                    'id' => $record->id,
                                    'slug' => $newSlug
                                ];
                            }
                        }

                        if (!empty($recordsToUpdate)) {
                            $case = "CASE id ";
                            $ids = [];
                            $params = [];
                            
                            foreach ($recordsToUpdate as $record) {
                                $case .= "WHEN ? THEN ? ";
                                $ids[] = $record['id'];
                                $params[] = $record['id'];
                                $params[] = $record['slug'];
                            }
                            $case .= "END";
                            
                            $ids = implode(',', $ids);
                            
                            DB::update(
                                "UPDATE {$model::query()->getModel()->getTable()} 
                                SET slug = {$case} 
                                WHERE id IN ({$ids})",
                                $params
                            );
                            
                            $count += count($recordsToUpdate);
                        }
                    });
            });

            $results[$model] = $count;
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
}