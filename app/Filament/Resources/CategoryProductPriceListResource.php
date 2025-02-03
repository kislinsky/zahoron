<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryProductPriceListResource\Pages;
use App\Filament\Resources\CategoryProductPriceListResource\RelationManagers;
use App\Models\CategoryProductPriceList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryProductPriceListResource extends Resource
{
    protected static ?string $model = CategoryProductPriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Категории Прайс-листа'; // Название в меню
    protected static ?string $navigationGroup = 'Категории'; // Указываем группу

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
                    ->options(CategoryProductPriceList::whereNull('parent_id')->pluck('title', 'id')) // Только главные категории
                    ->nullable(),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true) // Игнорировать текущую запись при редактировании
                    ->label('Slug')
                    ->maxLength(255),

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
            'index' => Pages\ListCategoryProductPriceLists::route('/'),
            'create' => Pages\CreateCategoryProductPriceList::route('/create'),
            'edit' => Pages\EditCategoryProductPriceList::route('/{record}/edit'),
        ];
    }
}
