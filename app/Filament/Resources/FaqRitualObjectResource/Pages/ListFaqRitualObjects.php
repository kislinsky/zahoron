<?php

namespace App\Filament\Resources\FaqRitualObjectResource\Pages;

use App\Filament\Resources\FaqRitualObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqRitualObjects extends ListRecords
{
    protected static string $resource = FaqRitualObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
