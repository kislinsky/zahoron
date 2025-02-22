<?php

namespace App\Filament\Resources\FaqOrganizationResource\Pages;

use App\Filament\Resources\FaqOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaqOrganization extends EditRecord
{
    protected static string $resource = FaqOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
