<?php

namespace App\Filament\Resources\FaqProductPriceListResource\Pages;

use App\Filament\Resources\FaqProductPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqProductPriceLists extends ListRecords
{
    protected static string $resource = FaqProductPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
