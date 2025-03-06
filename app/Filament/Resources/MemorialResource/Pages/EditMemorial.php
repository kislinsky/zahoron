<?php

namespace App\Filament\Resources\MemorialResource\Pages;

use App\Filament\Resources\MemorialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemorial extends EditRecord
{
    protected static string $resource = MemorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
