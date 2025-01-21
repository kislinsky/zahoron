<?php

namespace App\Filament\Resources\ReviewCemeteryResource\Pages;

use App\Filament\Resources\ReviewCemeteryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewCemetery extends EditRecord
{
    protected static string $resource = ReviewCemeteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
