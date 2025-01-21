<?php

namespace App\Filament\Resources\ReviewColumbariumResource\Pages;

use App\Filament\Resources\ReviewColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewColumbarium extends EditRecord
{
    protected static string $resource = ReviewColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
