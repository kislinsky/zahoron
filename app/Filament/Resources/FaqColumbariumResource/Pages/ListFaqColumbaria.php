<?php

namespace App\Filament\Resources\FaqColumbariumResource\Pages;

use App\Filament\Resources\FaqColumbariumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqColumbaria extends ListRecords
{
    protected static string $resource = FaqColumbariumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
