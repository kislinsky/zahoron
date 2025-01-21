<?php

namespace App\Filament\Resources\ReviewCemeteryResource\Pages;

use App\Filament\Resources\ReviewCemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviewCemeteries extends ListRecords
{
    protected static string $resource = ReviewCemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
