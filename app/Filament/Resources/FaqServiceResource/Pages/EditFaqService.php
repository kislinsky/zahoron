<?php

namespace App\Filament\Resources\FaqServiceResource\Pages;

use App\Filament\Resources\FaqServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqService extends EditRecord
{
    protected static string $resource = FaqServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
