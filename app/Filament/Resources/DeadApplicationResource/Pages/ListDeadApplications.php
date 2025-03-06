<?php

namespace App\Filament\Resources\DeadApplicationResource\Pages;

use App\Filament\Resources\DeadApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeadApplications extends ListRecords
{
    protected static string $resource = DeadApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
