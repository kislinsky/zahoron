<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = User::whereDate('created_at', today())->count();
        $yesterday = User::whereDate('created_at', today()->subDay())->count();
        $thisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $lastWeek = User::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $thisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $total = User::count();

        $changeToday = $yesterday > 0 ? round((($today - $yesterday) / $yesterday) * 100, 1) : ($today > 0 ? 100 : 0);
        $changeWeek = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1) : ($thisWeek > 0 ? 100 : 0);

        return [
            Stat::make('Новых сегодня', $today)
                ->description($changeToday >= 0 ? "↑ {$changeToday}%" : "↓ {$changeToday}%")
                ->descriptionIcon($changeToday >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($changeToday >= 0 ? 'success' : 'danger')
                ->chart($this->getLast7DaysData()),

            Stat::make('За эту неделю', $thisWeek)
                ->description($changeWeek >= 0 ? "↑ {$changeWeek}%" : "↓ {$changeWeek}%")
                ->descriptionIcon($changeWeek >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($changeWeek >= 0 ? 'success' : 'danger')
                ->chart($this->getWeeklyComparisonData()),

            Stat::make('За этот месяц', $thisMonth)
                ->description('Всего пользователей: ' . $total)
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($this->getMonthlyData()),
        ];
    }

    private function getLast7DaysData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getWeeklyComparisonData(): array
    {
        $data = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();
            $count = User::whereBetween('created_at', [$start, $end])->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getMonthlyData(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}