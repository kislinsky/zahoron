<?php

namespace App\Filament\Resources\FaqCategoryProductResource\Pages;

use App\Filament\Resources\FaqCategoryProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqCategoryProducts extends ListRecords
{
    protected static string $resource = FaqCategoryProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
