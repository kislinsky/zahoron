<?php

namespace App\Filament\Resources\UsefulCrematoriumResource\Pages;

use App\Filament\Resources\UsefulCrematoriumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsefulCrematorium extends EditRecord
{
    protected static string $resource = UsefulCrematoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
