<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('rub'), // Форматирование цены
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime(),
            ])
            ->filters([
                // Фильтры (опционально)
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Добавить продукт
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Редактировать продукт
                Tables\Actions\DeleteAction::make(), // Удалить продукт
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Массовое удаление
            ]);
    }
}
