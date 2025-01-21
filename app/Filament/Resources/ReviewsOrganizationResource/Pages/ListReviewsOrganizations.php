<?php

namespace App\Filament\Resources\ReviewsOrganizationResource\Pages;

use App\Filament\Resources\ReviewsOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviewsOrganizations extends ListRecords
{
    protected static string $resource = ReviewsOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
