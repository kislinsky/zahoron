<?php

namespace App\Filament\Resources\UsefulMortuaryResource\Pages;

use App\Filament\Resources\UsefulMortuaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsefulMortuaries extends ListRecords
{
    protected static string $resource = UsefulMortuaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
