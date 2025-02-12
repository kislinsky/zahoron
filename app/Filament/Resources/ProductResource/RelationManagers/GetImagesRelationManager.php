<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


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


                Radio::make('selected')
                    ->label('Статус')
                    ->options([
                        '1' => 'Главная',
                        '0' => 'Обычная',
                    ])
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

                TextColumn::make('selected')
                ->label('Роль фото')
                ->formatStateUsing(fn (int $state): string => match ($state) {
                    0 => 'Обычная',
                    1 => 'Главная',
                }),
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
