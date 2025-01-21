<?php

namespace App\Filament\Resources\CategoryProductPriceListResource\Pages;

use App\Filament\Resources\CategoryProductPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryProductPriceLists extends ListRecords
{
    protected static string $resource = CategoryProductPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
