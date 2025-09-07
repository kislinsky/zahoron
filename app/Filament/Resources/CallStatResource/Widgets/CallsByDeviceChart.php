<?php

namespace App\Filament\Resources\CallStatResource\Widgets;

use App\Models\CallStat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CallsByDeviceChart extends ChartWidget
{
    protected static ?string $heading = 'Звонки по устройствам';

    protected function getData(): array
    {
        $devices = CallStat::select('device', DB::raw('COUNT(*) as count'))
            ->whereNotNull('device')
            ->groupBy('device')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $devices->pluck('count')->toArray(),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b'],
                ],
            ],
            'labels' => $devices->pluck('device')->map(fn($device) => ucfirst($device))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}