<?php

namespace App\Filament\Resources\SearchBurialResource\Pages;

use App\Filament\Resources\SearchBurialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSearchBurial extends EditRecord
{
    protected static string $resource = SearchBurialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
