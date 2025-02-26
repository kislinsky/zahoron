<?php

namespace App\Filament\Resources\UsefulColumbariumResource\Pages;

use App\Filament\Resources\UsefulColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsefulColumbarium extends EditRecord
{
    protected static string $resource = UsefulColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
