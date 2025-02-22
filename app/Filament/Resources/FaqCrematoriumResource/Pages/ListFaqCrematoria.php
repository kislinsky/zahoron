<?php

namespace App\Filament\Resources\FaqCrematoriumResource\Pages;

use App\Filament\Resources\FaqCrematoriumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqCrematoria extends ListRecords
{
    protected static string $resource = FaqCrematoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
