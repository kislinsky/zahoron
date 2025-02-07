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

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Варинаты продукта';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\TextInput::make('title')
                ->label('Название')
                ->required()
                ->maxLength(255),
                
                FileUpload::make('img')
                ->label('Картинка') // Название поля
                ->directory('/uploads_product_price_list') // Директория для сохранения
                ->image() // Только изображения (jpg, png и т.д.)
                ->maxSize(2048) // Максимальный размер файла в КБ
                ->required() ,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('variants')
            ->columns([
                 Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
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
