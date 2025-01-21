<?php

namespace App\Filament\Resources\SeoObjectResource\Pages;

use App\Filament\Resources\SeoObjectResource;
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
}
