<?php

namespace App\Filament\Resources\FaqCrematoriumResource\Pages;

use App\Filament\Resources\FaqCrematoriumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqCrematorium extends EditRecord
{
    protected static string $resource = FaqCrematoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
