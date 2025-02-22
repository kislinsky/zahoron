<?php

namespace App\Filament\Resources\FaqCategoryPriceListResource\Pages;

use App\Filament\Resources\FaqCategoryPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqCategoryPriceList extends EditRecord
{
    protected static string $resource = FaqCategoryPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
