<?php

namespace App\Filament\Resources\FaqOrganizationResource\Pages;

use App\Filament\Resources\FaqOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqOrganizations extends ListRecords
{
    protected static string $resource = FaqOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
