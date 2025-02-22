<?php

namespace App\Filament\Resources\FaqColumbariumResource\Pages;

use App\Filament\Resources\FaqColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqColumbarium extends EditRecord
{
    protected static string $resource = FaqColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
