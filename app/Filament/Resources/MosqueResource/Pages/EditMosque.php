<?php

namespace App\Filament\Resources\MosqueResource\Pages;

use App\Filament\Resources\MosqueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMosque extends EditRecord
{
    protected static string $resource = MosqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
