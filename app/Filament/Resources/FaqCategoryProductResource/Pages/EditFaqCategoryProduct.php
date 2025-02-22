<?php

namespace App\Filament\Resources\FaqCategoryProductResource\Pages;

use App\Filament\Resources\FaqCategoryProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqCategoryProduct extends EditRecord
{
    protected static string $resource = FaqCategoryProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
