<?php

namespace App\Filament\Resources\FaqRitualObjectResource\Pages;

use App\Filament\Resources\FaqRitualObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqRitualObject extends EditRecord
{
    protected static string $resource = FaqRitualObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
