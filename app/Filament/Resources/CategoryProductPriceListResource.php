<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryProductPriceListResource\Pages;
use App\Models\CategoryProductPriceList;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


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
                TextInput::make('title')
                ->label('Название')
                ->required()
                ->live(debounce: 1000) // Задержка автообновления
                ->afterStateUpdated(function ($state, $set, $get) {
                    // Проверяем, если длина title больше 3 символов, обновляем slug
                    if (!empty($state) && strlen($state) > 3) {
                        $set('slug', generateUniqueSlug($state, CategoryProductPriceList::class, $get('id')));
                    }
                }),
            
            TextInput::make('slug')
                ->required()
                ->label('Slug')
                ->maxLength(255)
                ->unique(ignoreRecord: true) // Проверка уникальности
                ->formatStateUsing(fn ($state) => slug($state)) // Форматируем slug
                ->dehydrateStateUsing(fn ($state, $get) => generateUniqueSlug($state, CategoryProductPriceList::class, $get('id'))),

                // Поле для выбора родительской категории
                Forms\Components\Select::make('parent_id')
                    ->label('Родительская категория')
                    ->options(CategoryProductPriceList::whereNull('parent_id')->pluck('title', 'id')) // Только главные категории
                    ->nullable(),


                Forms\Components\FileUpload::make('icon')
                    ->label('Иконка')
                    ->directory('/uploads_cats_product_price_list') // Директория для сохранения
                    ->image()
                    ->nullable(),

                    Forms\Components\FileUpload::make('icon_white')
                    ->label('Иконка при выборе категории')
                    ->directory('/uploads_cats_product_price_list') // Директория для сохранения
                    ->image()
                    ->nullable(),

                Forms\Components\Textarea::make('content')
                    ->label('Контент')
                    ->required(),

                Forms\Components\FileUpload::make('video')
                    ->label('Видео')
                    ->directory('/uploads_cats_product_price_list') // Директория для сохранения
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
            'index' => Pages\ListCategoryProductPriceLists::route('/'),
            'create' => Pages\CreateCategoryProductPriceList::route('/create'),
            'edit' => Pages\EditCategoryProductPriceList::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' ;
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}
