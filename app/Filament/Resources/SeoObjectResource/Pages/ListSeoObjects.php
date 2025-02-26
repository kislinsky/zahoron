<?php

namespace App\Filament\Resources\SeoObjectResource\Pages;

use App\Filament\Resources\SeoObjectResource;
use App\Filament\Resources\SeoObjectResource\Widgets\SeoInfoWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeoObjects extends ListRecords
{
    protected static string $resource = SeoObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SeoInfoWidget::class,
        ];
    }
}
