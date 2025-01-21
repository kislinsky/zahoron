<?php

namespace App\Filament\Resources\ColumbariumResource\Pages;

use App\Filament\Resources\ColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditColumbarium extends EditRecord
{
    protected static string $resource = ColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
