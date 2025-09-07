<?php

namespace App\Filament\Resources\CallStatResource\Widgets;

use App\Models\CallStat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CallsPerDayChart extends ChartWidget
{
    protected static ?string $heading = 'Звонки по дням (последние 30 дней)';

    protected function getData(): array
    {
        $data = CallStat::select(
                DB::raw('DATE(date_start) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN call_status LIKE "11%" THEN 1 ELSE 0 END) as accepted')
            )
            ->where('date_start', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Все звонки',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Принятые',
                    'data' => $data->pluck('accepted')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
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