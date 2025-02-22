<?php

namespace App\Filament\Resources\FaqCemeteryResource\Pages;

use App\Filament\Resources\FaqCemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqCemetery extends EditRecord
{
    protected static string $resource = FaqCemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
