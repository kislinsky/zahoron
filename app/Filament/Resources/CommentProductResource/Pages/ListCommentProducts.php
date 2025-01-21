<?php

namespace App\Filament\Resources\CommentProductResource\Pages;

use App\Filament\Resources\CommentProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommentProducts extends ListRecords
{
    protected static string $resource = CommentProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
