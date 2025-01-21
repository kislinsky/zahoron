<?php

namespace App\Filament\Resources\ReviewMortuaryResource\Pages;

use App\Filament\Resources\ReviewMortuaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewMortuary extends EditRecord
{
    protected static string $resource = ReviewMortuaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
