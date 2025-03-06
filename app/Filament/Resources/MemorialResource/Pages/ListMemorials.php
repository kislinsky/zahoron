<?php

namespace App\Filament\Resources\MemorialResource\Pages;

use App\Filament\Resources\MemorialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemorials extends ListRecords
{
    protected static string $resource = MemorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
