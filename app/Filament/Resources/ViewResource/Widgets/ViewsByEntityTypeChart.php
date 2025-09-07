<?php

namespace App\Filament\Resources\ViewResource\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ViewsByEntityTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Распределение по типам объектов';

    protected function getData(): array
    {
        $viewsByType = View::select('entity_type', DB::raw('COUNT(*) as count'))
            ->groupBy('entity_type')
            ->orderBy('count', 'desc')
            ->get();

        $labels = $viewsByType->pluck('entity_type')->map(function ($type) {
            return match ($type) {
                'cemetery' => 'Кладбища',
                'mortuary' => 'Морги',
                'organization' => 'Организации',
                'page' => 'Страницы',
                default => $type,
            };
        })->toArray();

        return [
            'datasets' => [
                [
                    'data' => $viewsByType->pluck('count')->toArray(),
                    'backgroundColor' => ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}