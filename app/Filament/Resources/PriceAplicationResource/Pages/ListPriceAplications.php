<?php

namespace App\Filament\Resources\PriceAplicationResource\Pages;

use App\Filament\Resources\PriceAplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriceAplications extends ListRecords
{
    protected static string $resource = PriceAplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
