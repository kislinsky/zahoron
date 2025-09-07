<?php

namespace App\Filament\Resources\CallStatResource\Widgets;

use App\Models\CallStat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CallStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Распределение по статусам';

    protected function getData(): array
    {
        $statuses = CallStat::select('call_status', DB::raw('COUNT(*) as count'))
            ->whereNotNull('call_status')
            ->groupBy('call_status')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $statuses->pluck('count')->toArray(),
                    'backgroundColor' => ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#3b82f6', '#ec4899'],
                ],
            ],
            'labels' => $statuses->pluck('call_status')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}