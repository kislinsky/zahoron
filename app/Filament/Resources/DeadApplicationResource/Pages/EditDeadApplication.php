<?php

namespace App\Filament\Resources\DeadApplicationResource\Pages;

use App\Filament\Resources\DeadApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeadApplication extends EditRecord
{
    protected static string $resource = DeadApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
