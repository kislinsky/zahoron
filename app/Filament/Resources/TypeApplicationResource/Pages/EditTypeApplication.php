<?php

namespace App\Filament\Resources\TypeApplicationResource\Pages;

use App\Filament\Resources\TypeApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeApplication extends EditRecord
{
    protected static string $resource = TypeApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
