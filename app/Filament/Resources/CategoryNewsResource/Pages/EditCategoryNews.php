<?php

namespace App\Filament\Resources\CategoryNewsResource\Pages;

use App\Filament\Resources\CategoryNewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryNews extends EditRecord
{
    protected static string $resource = CategoryNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
