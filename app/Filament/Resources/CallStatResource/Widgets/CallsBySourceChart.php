<?php

namespace App\Filament\Resources\CallStatResource\Widgets;

use App\Models\CallStat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CallsBySourceChart extends ChartWidget
{
    protected static ?string $heading = 'Звонки по источникам (UTM)';

    protected function getData(): array
    {
        $sources = CallStat::select('utm_source', DB::raw('COUNT(*) as count'))
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $sources->pluck('count')->toArray(),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'],
                ],
            ],
            'labels' => $sources->pluck('utm_source')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}