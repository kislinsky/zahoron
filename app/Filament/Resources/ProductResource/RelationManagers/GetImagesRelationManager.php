<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ImageProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GetImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'getImages';
    protected static ?string $title = 'Фотографии';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\FileUpload::make('title')
                ->label('Фотография')
                ->directory('uploads_product')
                ->image()
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->recordTitleAttribute('getImages')
            ->columns([
                Tables\Columns\ImageColumn::make('title') // Используйте ImageColumn для отображения изображений
                ->label('Фотография')
                ->size(100), // Размер изображения
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
