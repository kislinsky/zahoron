<?php

namespace App\Filament\Resources\OrderServiceResource\Pages;

use App\Filament\Resources\OrderServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderServices extends ListRecords
{
    protected static string $resource = OrderServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
