<?php

namespace App\Filament\Resources\MosqueResource\Pages;

use App\Filament\Resources\MosqueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMosques extends ListRecords
{
    protected static string $resource = MosqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
