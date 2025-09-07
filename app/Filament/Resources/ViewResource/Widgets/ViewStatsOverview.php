<?php

namespace App\Filament\Resources\ViewResource\Widgets;

use App\Models\View;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ViewStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->format('Y-m-d');
        
        // Статистика за сегодня
        $totalToday = View::whereDate('created_at', $today)->count();
        $uniqueVisitorsToday = View::whereDate('created_at', $today)
            ->distinct('session_id')
            ->count();
        $registeredUsersToday = View::whereDate('created_at', $today)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();

        // Общая статистика
        $totalViews = View::count();
        $uniqueVisitors = View::distinct('session_id')->count();
        $registeredUsers = View::whereNotNull('user_id')->distinct('user_id')->count();
        $avgViewsPerSession = $totalViews > 0 ? round($totalViews / $uniqueVisitors, 1) : 0;

        return [
            Stat::make('Просмотров сегодня', $totalToday)
                ->description($uniqueVisitorsToday . ' уникальных посетителей')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Всего просмотров', $totalViews)
                ->description($uniqueVisitors . ' уникальных посетителей')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Зарегистрированных', $registeredUsers)
                ->description($registeredUsersToday . ' сегодня')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Среднее на сессию', $avgViewsPerSession)
                ->description('просмотров на посетителя')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }
}