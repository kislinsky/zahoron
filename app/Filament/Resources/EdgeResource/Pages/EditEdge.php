<?php

namespace App\Filament\Resources\EdgeResource\Pages;

use App\Filament\Resources\EdgeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEdge extends EditRecord
{
    protected static string $resource = EdgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
