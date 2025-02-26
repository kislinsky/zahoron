<?php

namespace App\Filament\Resources\UsefulCemeteryResource\Pages;

use App\Filament\Resources\UsefulCemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsefulCemeteries extends ListRecords
{
    protected static string $resource = UsefulCemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
