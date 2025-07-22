<?php

namespace App\Filament\Resources\CategoryOurWorkResource\Pages;

use App\Filament\Resources\CategoryOurWorkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryOurWork extends EditRecord
{
    protected static string $resource = CategoryOurWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
