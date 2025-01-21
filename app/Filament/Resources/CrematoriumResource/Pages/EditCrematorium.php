<?php

namespace App\Filament\Resources\CrematoriumResource\Pages;

use App\Filament\Resources\CrematoriumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrematorium extends EditRecord
{
    protected static string $resource = CrematoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
