<?php

namespace App\Filament\Resources\ActivityCategoryOrganizationResource\Pages;

use App\Filament\Resources\ActivityCategoryOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityCategoryOrganizations extends ListRecords
{
    protected static string $resource = ActivityCategoryOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
