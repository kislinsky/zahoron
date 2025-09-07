<?php

namespace App\Filament\Resources\CallStatResource\Pages;

use App\Filament\Resources\CallStatResource;
use App\Filament\Resources\CallStatResource\Widgets\CallStatsOverview;
use App\Filament\Resources\CallStatResource\Widgets\CallsPerDayChart;
use App\Filament\Resources\CallStatResource\Widgets\CallStatusChart;
use App\Filament\Resources\CallStatResource\Widgets\CallsBySourceChart;
use App\Filament\Resources\CallStatResource\Widgets\CallsByDeviceChart;
use App\Filament\Resources\CallStatResource\Widgets\CallsByCityChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCallStats extends ListRecords
{
    protected static string $resource = CallStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CallStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CallsPerDayChart::class,
            CallStatusChart::class,
            CallsBySourceChart::class,
            CallsByDeviceChart::class,
            CallsByCityChart::class,
        ];
    }
}