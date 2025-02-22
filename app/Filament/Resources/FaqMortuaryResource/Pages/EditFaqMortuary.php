<?php

namespace App\Filament\Resources\FaqMortuaryResource\Pages;

use App\Filament\Resources\FaqMortuaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqMortuary extends EditRecord
{
    protected static string $resource = FaqMortuaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
