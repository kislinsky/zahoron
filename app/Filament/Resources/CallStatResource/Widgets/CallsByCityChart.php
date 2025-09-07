<?php

namespace App\Filament\Resources\CallStatResource\Widgets;

use App\Models\CallStat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CallsByCityChart extends ChartWidget
{
    protected static ?string $heading = 'Топ городов по звонкам';

    protected function getData(): array
    {
        $cities = CallStat::select('city', DB::raw('COUNT(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Количество звонков',
                    'data' => $cities->pluck('count')->toArray(),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $cities->pluck('city')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}