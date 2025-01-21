<?php

namespace App\Filament\Resources\CemeteryResource\Pages;

use App\Filament\Resources\CemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCemetery extends EditRecord
{
    protected static string $resource = CemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
