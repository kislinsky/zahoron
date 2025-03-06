<?php

namespace App\Filament\Resources\BeautificationResource\Pages;

use App\Filament\Resources\BeautificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBeautification extends EditRecord
{
    protected static string $resource = BeautificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
