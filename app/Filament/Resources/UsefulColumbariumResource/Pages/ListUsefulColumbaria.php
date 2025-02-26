<?php

namespace App\Filament\Resources\UsefulColumbariumResource\Pages;

use App\Filament\Resources\UsefulColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsefulColumbaria extends ListRecords
{
    protected static string $resource = UsefulColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
