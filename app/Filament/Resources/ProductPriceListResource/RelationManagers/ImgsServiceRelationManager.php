<?php

namespace App\Filament\Resources\ProductPriceListResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ImgsServiceRelationManager extends RelationManager
{
    protected static string $relationship = 'imgsService';
    protected static ?string $title = 'Фото';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 FileUpload::make('title')
                ->label('Картинка') // Название поля
                ->directory('/uploads_product_price_list') // Директория для сохранения
                ->image() // Только изображения (jpg, png и т.д.)
                ->maxSize(2048) // Максимальный размер файла в КБ
                ->required()
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('imgsService')
            ->columns([
                 Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
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
