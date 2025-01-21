<?php

namespace App\Filament\Resources\CrematoriumResource\Pages;

use App\Filament\Resources\CrematoriumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCrematoria extends ListRecords
{
    protected static string $resource = CrematoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
