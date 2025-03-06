<?php

namespace App\Filament\Resources\FuneralServiceResource\Pages;

use App\Filament\Resources\FuneralServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFuneralServices extends ListRecords
{
    protected static string $resource = FuneralServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
