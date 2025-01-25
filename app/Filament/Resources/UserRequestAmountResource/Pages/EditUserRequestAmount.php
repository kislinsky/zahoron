<?php

namespace App\Filament\Resources\UserRequestAmountResource\Pages;

use App\Filament\Resources\UserRequestAmountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserRequestAmount extends EditRecord
{
    protected static string $resource = UserRequestAmountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
