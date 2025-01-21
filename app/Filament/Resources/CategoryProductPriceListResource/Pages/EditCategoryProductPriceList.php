<?php

namespace App\Filament\Resources\CategoryProductPriceListResource\Pages;

use App\Filament\Resources\CategoryProductPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryProductPriceList extends EditRecord
{
    protected static string $resource = CategoryProductPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
