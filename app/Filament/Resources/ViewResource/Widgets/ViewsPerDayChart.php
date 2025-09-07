<?php

namespace App\Filament\Resources\ViewResource\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ViewsPerDayChart extends ChartWidget
{
    protected static ?string $heading = 'Просмотры по дням (последние 30 дней)';

    protected function getData(): array
    {
        $data = View::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_views'),
                DB::raw('COUNT(DISTINCT session_id) as unique_visitors')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Все просмотры',
                    'data' => $data->pluck('total_views')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Уникальные посетители',
                    'data' => $data->pluck('unique_visitors')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}