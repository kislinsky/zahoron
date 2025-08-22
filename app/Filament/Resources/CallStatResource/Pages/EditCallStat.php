<?php

namespace App\Filament\Resources\CallStatResource\Pages;

use App\Filament\Resources\CallStatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCallStat extends EditRecord
{
    protected static string $resource = CallStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
