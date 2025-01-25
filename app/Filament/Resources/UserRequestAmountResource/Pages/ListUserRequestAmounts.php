<?php

namespace App\Filament\Resources\UserRequestAmountResource\Pages;

use App\Filament\Resources\UserRequestAmountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserRequestAmounts extends ListRecords
{
    protected static string $resource = UserRequestAmountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
