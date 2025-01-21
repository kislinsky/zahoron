<?php

namespace App\Filament\Resources\BurialResource\Pages;

use App\Filament\Resources\BurialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBurial extends EditRecord
{
    protected static string $resource = BurialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
