<?php

namespace App\Filament\Resources\UsefulCemeteryResource\Pages;

use App\Filament\Resources\UsefulCemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsefulCemetery extends EditRecord
{
    protected static string $resource = UsefulCemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
