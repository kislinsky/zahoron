<?php

namespace App\Filament\Resources\OrderServiceResource\Pages;

use App\Filament\Resources\OrderServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderService extends EditRecord
{
    protected static string $resource = OrderServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
