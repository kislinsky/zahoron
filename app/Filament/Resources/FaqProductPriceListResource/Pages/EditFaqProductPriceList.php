<?php

namespace App\Filament\Resources\FaqProductPriceListResource\Pages;

use App\Filament\Resources\FaqProductPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqProductPriceList extends EditRecord
{
    protected static string $resource = FaqProductPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
