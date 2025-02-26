<?php

namespace App\Filament\Resources\UsefulMortuaryResource\Pages;

use App\Filament\Resources\UsefulMortuaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsefulMortuary extends EditRecord
{
    protected static string $resource = UsefulMortuaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
