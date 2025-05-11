<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use App\Models\CategoryProduct;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';
    protected static ?string $title = 'Продукты организации';

    public function form(Form $form): Form
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
                        $set('slug', generateUniqueSlug($state, Product::class, $get('id')));
                    }
                }),
            
            TextInput::make('slug')
                ->required()
                ->label('Slug')
                ->maxLength(255)
                ->unique(ignoreRecord: true) // Проверка уникальности
                ->formatStateUsing(fn ($state) => slug($state)) // Форматируем slug
                ->dehydrateStateUsing(fn ($state, $get) => generateUniqueSlug($state, Product::class, $get('id'))),

                Radio::make('view')
            ->label('Отображение товара')
            ->options([
                0 => 'Не показывать',
                1 => 'Показывать'
            ])
            ->inline(),

            
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
                        )->hidden(fn (?Product $record) => is_null($record)),


            
                    TextInput::make('price')
                    ->label('Цена')
                    ->required()
                    ->numeric()
                    ->live() // Делаем поле реактивным
                    ->afterStateUpdated(fn (Get $get, Set $set) => $set('total_price', $get('price_sale') ?: $get('price'))), // Обновляем total_price
                
                TextInput::make('price_sale')
                    ->label('Цена со скидкой')
                    ->numeric()
                    ->live() // Делаем поле реактивным
                    ->afterStateUpdated(fn (Get $get, Set $set) => 
                        $set('total_price', !empty($get('price_sale')) ? $get('price_sale') : $get('price'))
                    ), // Если \price_sale\ указано, берем его, иначе — \price\
                
                TextInput::make('total_price')
                    ->label('Итоговая цена')
                    ->numeric()
                    ->disabled() // Запрещаем редактирование вручную
                    ->dehydrated(),



           
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
                    ->disableLabel(false) // Показывать метку
                    ->placeholder('Введите HTML-контент здесь...'),



            Select::make('category_parent_id')
                ->label('Категория')
                ->options(CategoryProduct::where('parent_id',null)->pluck('title', 'id')) // Список всех категорий
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


                Forms\Components\TextInput::make('material')
                    ->label('Материал')
                    ->maxLength(255),

                Forms\Components\TextInput::make('color')
                    ->label('Цвет')
                    ->maxLength(255),

                Forms\Components\TextInput::make('layering')
                    ->label('Тип продукта (облогоражиавние)')
                    ->maxLength(255),

                

                    Forms\Components\TextInput::make('size')
                    ->label('Размеры')
                    ->maxLength(255),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('total_price')
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
                Tables\Actions\Action::make('view_product')
                ->label('Посмотреть') // Текст кнопки
                ->url(fn ($record) => '/'.selectCity()->slug.'/admin/products/'.$record->id.'/edit') // Ссылка на товар
                ->icon('heroicon-o-eye') // Иконка "глаза"
                ->color('primary') // Цвет кнопки
                ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(), // Редактировать продукт
                Tables\Actions\DeleteAction::make(), // Удалить продукт
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Массовое удаление
            ]);
    }
}
