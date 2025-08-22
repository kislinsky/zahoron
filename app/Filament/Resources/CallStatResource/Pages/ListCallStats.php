<?php

namespace App\Filament\Resources\CallStatResource\Pages;

use App\Filament\Resources\CallStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCallStats extends ListRecords
{
    protected static string $resource = CallStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
