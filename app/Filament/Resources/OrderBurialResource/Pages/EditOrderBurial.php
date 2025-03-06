<?php

namespace App\Filament\Resources\OrderBurialResource\Pages;

use App\Filament\Resources\OrderBurialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderBurial extends EditRecord
{
    protected static string $resource = OrderBurialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
