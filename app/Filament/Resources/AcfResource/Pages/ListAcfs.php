<?php

namespace App\Filament\Resources\AcfResource\Pages;

use App\Filament\Resources\AcfResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcfs extends ListRecords
{
    protected static string $resource = AcfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
