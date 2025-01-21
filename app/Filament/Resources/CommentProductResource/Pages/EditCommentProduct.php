<?php

namespace App\Filament\Resources\CommentProductResource\Pages;

use App\Filament\Resources\CommentProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommentProduct extends EditRecord
{
    protected static string $resource = CommentProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
