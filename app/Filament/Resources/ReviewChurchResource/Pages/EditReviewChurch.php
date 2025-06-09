<?php

namespace App\Filament\Resources\ReviewChurchResource\Pages;

use App\Filament\Resources\ReviewChurchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewChurch extends EditRecord
{
    protected static string $resource = ReviewChurchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
