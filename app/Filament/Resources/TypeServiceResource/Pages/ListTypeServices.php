<?php

namespace App\Filament\Resources\TypeServiceResource\Pages;

use App\Filament\Resources\TypeServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeServices extends ListRecords
{
    protected static string $resource = TypeServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
