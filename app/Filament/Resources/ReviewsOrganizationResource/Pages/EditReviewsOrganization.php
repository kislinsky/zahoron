<?php

namespace App\Filament\Resources\ReviewsOrganizationResource\Pages;

use App\Filament\Resources\ReviewsOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewsOrganization extends EditRecord
{
    protected static string $resource = ReviewsOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
