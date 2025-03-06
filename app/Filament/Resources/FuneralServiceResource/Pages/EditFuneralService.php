<?php

namespace App\Filament\Resources\FuneralServiceResource\Pages;

use App\Filament\Resources\FuneralServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFuneralService extends EditRecord
{
    protected static string $resource = FuneralServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
