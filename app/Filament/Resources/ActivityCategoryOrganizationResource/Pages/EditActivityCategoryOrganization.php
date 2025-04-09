<?php

namespace App\Filament\Resources\ActivityCategoryOrganizationResource\Pages;

use App\Filament\Resources\ActivityCategoryOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityCategoryOrganization extends EditRecord
{
    protected static string $resource = ActivityCategoryOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
