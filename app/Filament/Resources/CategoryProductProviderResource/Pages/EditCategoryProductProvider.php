<?php

namespace App\Filament\Resources\CategoryProductProviderResource\Pages;

use App\Filament\Resources\CategoryProductProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryProductProvider extends EditRecord
{
    protected static string $resource = CategoryProductProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
