<?php

namespace App\Filament\Resources\ReviewProductPriceListResource\Pages;

use App\Filament\Resources\ReviewProductPriceListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewProductPriceList extends EditRecord
{
    protected static string $resource = ReviewProductPriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
