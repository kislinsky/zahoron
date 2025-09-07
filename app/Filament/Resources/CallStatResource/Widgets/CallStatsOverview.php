<?php

namespace App\Filament\Resources\CallStatResource\Widgets;

use App\Models\CallStat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CallStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->format('Y-m-d');
        
        $totalToday = CallStat::whereDate('date_start', $today)->count();
        $acceptedToday = CallStat::whereDate('date_start', $today)
            ->where('call_status', 'like', '11%')
            ->count();
        $rejectedToday = CallStat::whereDate('date_start', $today)
            ->whereNotNull('call_status')
            ->whereNot('call_status', 'like', '11%')
            ->count();

        $totalCalls = CallStat::count();
        $acceptedCalls = CallStat::where('call_status', 'like', '11%')->count();
        $qualityCalls = CallStat::where('is_quality', true)->count();
        $avgDuration = CallStat::where('duration', '>', 0)->avg('duration') ?? 0;

        return [
            Stat::make('Звонков сегодня', $totalToday)
                ->description($acceptedToday . ' принятых, ' . $rejectedToday . ' отклоненных')
                ->descriptionIcon('heroicon-m-phone-arrow-up-right')
                ->color('success'),

            Stat::make('Всего звонков', $totalCalls)
                ->description($totalCalls > 0 ? $acceptedCalls . ' принятых (' . round(($acceptedCalls/$totalCalls)*100, 1) . '%)' : 'нет данных')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Качественных звонков', $qualityCalls)
                ->description($totalCalls > 0 ? round(($qualityCalls/$totalCalls)*100, 1) . '% от общего числа' : 'нет данных')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),

            Stat::make('Средняя длительность', gmdate('H:i:s', (int)$avgDuration))
                ->description('среднее время разговора')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}