<?php

namespace App\Filament\Resources\FaqCemeteryResource\Pages;

use App\Filament\Resources\FaqCemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqCemeteries extends ListRecords
{
    protected static string $resource = FaqCemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
