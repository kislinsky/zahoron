<?php

namespace App\Filament\Resources\ViewResource\Pages;

use App\Filament\Resources\ViewResource;
use App\Filament\Resources\ViewResource\Widgets\ViewStatsOverview;
use App\Filament\Resources\ViewResource\Widgets\ViewsPerDayChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsByEntityTypeChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsBySourceChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsByDeviceChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsByLocationChart;
use App\Filament\Resources\ViewResource\Widgets\TopViewedEntities;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListViewStats extends ListRecords
{
    protected static string $resource = ViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ViewStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ViewsPerDayChart::class,
            ViewsByEntityTypeChart::class,
            ViewsByDeviceChart::class,
            TopViewedEntities::class,
        ];
    }
}