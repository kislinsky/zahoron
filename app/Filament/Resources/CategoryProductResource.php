<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryProductResource\Pages;
use App\Filament\Resources\CategoryProductResource\RelationManagers;
use App\Models\CategoryProduct;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
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

                TextInput::make('title')
                ->label('Название')
                ->required()
                ->live(debounce: 1000) // Задержка автообновления
                ->afterStateUpdated(function ($state, $set, $get) {
                    // Проверяем, если длина title больше 3 символов, обновляем slug
                    if (!empty($state) && strlen($state) > 3) {
                        $set('slug', generateUniqueSlug($state, CategoryProduct::class, $get('id')));
                    }
                }),
            
            TextInput::make('slug')
                ->required()
                ->label('Slug')
                ->maxLength(255)
                ->unique(ignoreRecord: true) // Проверка уникальности
                ->formatStateUsing(fn ($state) => slug($state)) // Форматируем slug
                ->dehydrateStateUsing(fn ($state, $get) => generateUniqueSlug($state, CategoryProduct::class, $get('id'))),

                // Поле для выбора родительской категории
                Forms\Components\Select::make('parent_id')
                    ->label('Родительская категория')
                    ->options(CategoryProduct::whereNull('parent_id')->pluck('title', 'id')) // Только главные категории
                    ->nullable(),

                    Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required(),

                Forms\Components\Select::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship('parent', 'title') // предполагается наличие отношения 'parent'
                    ->nullable(),

                Forms\Components\FileUpload::make('icon')
                    ->label('Иконка')
                    ->directory('/uploads_cats_product') // Директория для сохранения
                    ->nullable(),

                Forms\Components\FileUpload::make('icon_white')
                    ->directory('/uploads_cats_product') // Директория для сохранения
                    ->label('Иконка при выбранной категории')
                    ->nullable(),


                Forms\Components\Textarea::make('content')
                    ->label('Содержимое')
                    ->nullable(),

                Forms\Components\Textarea::make('manual')
                    ->label('Руководство')
                    ->nullable(),

                Forms\Components\TextInput::make('manual_video')
                    ->label('Видео руководство')
                    ->nullable(),

                   
                

                Forms\Components\Toggle::make('choose_admin')
                    ->label('Выбор администратора')
                    ->default(0),

                Forms\Components\FileUpload::make('icon_map')
                    ->label('Иконка на карте')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoryProducts::route('/'),
            'create' => Pages\CreateCategoryProduct::route('/create'),
            'edit' => Pages\EditCategoryProduct::route('/{record}/edit'),
        ];
    }
}
