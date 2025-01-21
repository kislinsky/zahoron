<?php

namespace App\Filament\Resources\ReviewCrematoriumResource\Pages;

use App\Filament\Resources\ReviewCrematoriumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewCrematorium extends EditRecord
{
    protected static string $resource = ReviewCrematoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
