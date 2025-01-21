<?php

namespace App\Filament\Resources\CemeteryResource\Pages;

use App\Filament\Resources\CemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCemeteries extends ListRecords
{
    protected static string $resource = CemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
