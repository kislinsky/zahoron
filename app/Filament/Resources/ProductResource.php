<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryProduct;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\GetParamRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\GetImagesRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\MemorialMenuRelationManager;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Продукты маркетплэйса'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                
                    TextInput::make('map_link')
                    ->label('Ссылка на товар')
                    ->disabled()
                    ->suffixAction(
                        Action::make('open_map')
                            ->button() // Отобразить как кнопку
                            ->label('Страница товара')
                            ->icon('heroicon-s-eye') // Иконка глаза
                            // Текст кнопки
                            ->url(function ($record) {
                                // Используем $record для получения текущего продукта
                                return $record->route(); // Возвращаем URL продукта
                            })
                            ->openUrlInNewTab()
                        ),


                Forms\Components\Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),



                    TextInput::make('price')
                    ->label('Цена')
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Обновляем total_price при изменении price
                        $set('total_price', $get('price'));
                        // Для отладки
                        logger()->info('Price updated. Total price:', ['total_price' => $get('total_price')]);
                    }),
                
                TextInput::make('price_sale')
                    ->label('Цена со скидкой')
                    ->numeric()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        if (!empty($get('price_sale'))) {
                            // Если price_sale не пустое, обновляем total_price
                            $set('total_price', $get('price_sale'));
                        } else {
                            // Если price_sale пустое, возвращаем total_price к значению price
                            $set('total_price', $get('price'));
                        }
                        // Для отладки
                        logger()->info('Price sale updated. Total price:', ['total_price' => $get('total_price')]);
                    }),
                
                TextInput::make('total_price')
                    ->label('Итоговая цена')
                    ->numeric()
                    ->hidden(),



                    Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true) // Игнорировать текущую запись при редактировании
                    ->label('Slug')
                    ->maxLength(255),

                RichEditor::make('content') // Поле для редактирования HTML-контента
                    ->label('Описание') // Соответствующая подпись
                    ->toolbarButtons([
                        'attachFiles', // возможность прикрепить файлы
                        'bold', // жирный текст
                        'italic', // курсив
                        'underline', // подчеркивание
                        'strike', // зачеркнутый текст
                        'link', // вставка ссылок
                        'orderedList', // нумерованный список
                        'bulletList', // маркированный список
                        'blockquote', // цитата
                        'h2', 'h3', 'h4', // заголовки второго, третьего и четвертого уровня
                        'codeBlock', // блок кода
                        'undo', 'redo', // отмена/возврат действия
                    ])
                    ->required() // Опционально: сделать поле обязательным
                    ->disableLabel(false) // Показывать метку
                    ->placeholder('Введите HTML-контент здесь...'),



            Select::make('category_parent_id')
                ->label('Категория')
                ->options(CategoryProduct::all()->pluck('title', 'id')) // Список всех категорий
                ->searchable() // Добавляем поиск по тексту
                ->reactive() // Делаем поле реактивным
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('category_id', null); // Сбрасываем значение подкатегории при изменении категории
                })
                ->afterStateHydrated(function (Select $component, $record) {
                    // Устанавливаем начальное значение для category_id при редактировании
                    if ($record && $record->subcategory) {
                        $component->state($record->subcategory->category_id);
                    }
                }), // Не сохранять значение в базу данных

            Select::make('category_id')
                ->label('Подкатегория')
                ->options(function ($get) {
                    $categoryId = $get('category_parent_id'); // Получаем выбранную категорию

                    if (!$categoryId) {
                        return []; // Если категория не выбрана, возвращаем пустой список
                    }

                    // Возвращаем список подкатегорий, привязанных к выбранной категории
                    return CategoryProduct::where('parent_id', $categoryId)->pluck('title', 'id');
                })
                ->searchable() // Добавляем поиск по тексту
                ->required() // Подкатегория обязательна для выбора
                ->afterStateHydrated(function (Select $component, $record) {
                    // Устанавливаем начальное значение для subcategory_id при редактировании
                    if ($record) {
                        $component->state($record->category_id);
                    }
                }),
        
                Forms\Components\Select::make('city_id')
                ->label('Город')
                ->relationship('city', 'title')
                ->searchable()
                ->preload(),

                Forms\Components\Select::make('cemetery_id')
                ->label('Кладбище')
                ->relationship('cemetery', 'title')
                ->searchable()
                ->preload(),

                Forms\Components\TextInput::make('material')
                    ->label('Материал')
                    ->maxLength(255),

                Forms\Components\TextInput::make('color')
                    ->label('Цвет')
                    ->maxLength(255),

                Forms\Components\TextInput::make('layering')
                    ->label('Тип продукта (облогоражиавние)')
                    ->maxLength(255),

                Forms\Components\TextInput::make('cafe')
                    ->label('Кафе')
                    ->maxLength(255),

                    Forms\Components\TextInput::make('size')
                    ->label('Размеры')
                    ->maxLength(255),


                Forms\Components\TextInput::make('location_width')
                    ->label('Широта')
                    ->maxLength(255),

                Forms\Components\TextInput::make('location_longitude')
                    ->label('Долгота')
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization.title')
                    ->label('Организация')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }




    public static function getRelations(): array
    {
        return [
            GetParamRelationManager::class,
            MemorialMenuRelationManager::class,
            GetImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
