<?php

namespace App\Filament\Resources\FaqServiceResource\Pages;

use App\Filament\Resources\FaqServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqServices extends ListRecords
{
    protected static string $resource = FaqServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
