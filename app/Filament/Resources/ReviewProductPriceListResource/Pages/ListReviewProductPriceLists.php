<?php

namespace App\Filament\Resources\ReviewProductPriceListResource\Pages;

use App\Filament\Resources\ReviewProductPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviewProductPriceLists extends ListRecords
{
    protected static string $resource = ReviewProductPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
