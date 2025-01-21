<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryProductProviderResource\Pages;
use App\Filament\Resources\CategoryProductProviderResource\RelationManagers;
use App\Models\CategoryProductProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryProductProviderResource extends Resource
{
    protected static ?string $model = CategoryProductProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Категории Поставщиков'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required(),

                // Поле для выбора родительской категории
                Forms\Components\Select::make('parent_id')
                    ->label('Родительская категория')
                    ->options(CategoryProductProvider::whereNull('parent_id')->pluck('title', 'id')) // Только главные категории
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),

                // Отображение родительской категории
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Родительская категория')
                    ->default('Главная категория'), // Если parent_id == null
            ])
            ->filters([
                // Фильтры, если нужны
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Редактирование
                Tables\Actions\DeleteAction::make(), // Удаление
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Массовое удаление
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoryProductProviders::route('/'),
            'create' => Pages\CreateCategoryProductProvider::route('/create'),
            'edit' => Pages\EditCategoryProductProvider::route('/{record}/edit'),
        ];
    }
}
