<?php

namespace App\Filament\Resources\ViewResource\Widgets;

use App\Models\View;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ViewsByDeviceChart extends ChartWidget
{
    protected static ?string $heading = 'Просмотры по устройствам';

    protected function getData(): array
    {
        $viewsByDevice = View::select('device', DB::raw('COUNT(*) as count'))
            ->groupBy('device')
            ->orderBy('count', 'desc')
            ->get();

        $labels = $viewsByDevice->pluck('device')->map(function ($device) {
            return match ($device) {
                'desktop' => 'Компьютеры',
                'tablet' => 'Планшеты',
                'mobile' => 'Мобильные',
                default => $device,
            };
        })->toArray();

        return [
            'datasets' => [
                [
                    'data' => $viewsByDevice->pluck('count')->toArray(),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}