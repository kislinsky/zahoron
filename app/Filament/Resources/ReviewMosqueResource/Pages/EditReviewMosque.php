<?php

namespace App\Filament\Resources\ReviewMosqueResource\Pages;

use App\Filament\Resources\ReviewMosqueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewMosque extends EditRecord
{
    protected static string $resource = ReviewMosqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
