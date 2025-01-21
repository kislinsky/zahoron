<?php

namespace App\Filament\Resources\ReviewMortuaryResource\Pages;

use App\Filament\Resources\ReviewMortuaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviewMortuaries extends ListRecords
{
    protected static string $resource = ReviewMortuaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
