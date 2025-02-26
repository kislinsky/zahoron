<?php

namespace App\Filament\Resources\SeoObjectResource\Pages;

use App\Filament\Resources\SeoObjectResource;
use App\Filament\Resources\SeoObjectResource\Widgets\SeoInfoWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeoObject extends EditRecord
{
    protected static string $resource = SeoObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    
}
