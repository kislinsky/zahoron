<?php

namespace App\Filament\Resources\FaqCategoryPriceListResource\Pages;

use App\Filament\Resources\FaqCategoryPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqCategoryPriceLists extends ListRecords
{
    protected static string $resource = FaqCategoryPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
