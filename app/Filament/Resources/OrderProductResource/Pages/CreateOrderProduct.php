<?php

namespace App\Filament\Resources\OrderProductResource\Pages;

use App\Filament\Resources\OrderProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderProduct extends CreateRecord
{
    protected static string $resource = OrderProductResource::class;
}
