<?php

namespace App\Filament\Resources\ReviewColumbariumResource\Pages;

use App\Filament\Resources\ReviewColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviewColumbaria extends ListRecords
{
    protected static string $resource = ReviewColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
