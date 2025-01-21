<?php

namespace App\Filament\Resources\MortuaryResource\Pages;

use App\Filament\Resources\MortuaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMortuaries extends ListRecords
{
    protected static string $resource = MortuaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
