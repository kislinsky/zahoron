<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryProductResource\Pages;
use App\Filament\Resources\CategoryProductResource\RelationManagers;
use App\Models\CategoryProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryProductResource extends Resource
{
    protected static ?string $model = CategoryProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Категории МК'; // Название в меню
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
                    ->options(CategoryProduct::whereNull('parent_id')->pluck('title', 'id')) // Только главные категории
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoryProducts::route('/'),
            'create' => Pages\CreateCategoryProduct::route('/create'),
            'edit' => Pages\EditCategoryProduct::route('/{record}/edit'),
        ];
    }
}
