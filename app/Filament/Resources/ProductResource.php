<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\GetImagesRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\GetParamRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\MemorialMenuRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\ViewsRelationManager;
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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Продукты маркетплэйса'; // Название в меню

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
                SelectFilter::make('category_parent_id')
                ->label('Категория')
                ->relationship('parentCategory', 'title', function (Builder $query) {
                    $query->whereNull('parent_id'); // Проверяем parent_id именно в таблице категорий
                })
                ->searchable()
                ->preload(),
        
            // Фильтр по подкатегории
            SelectFilter::make('category_id')
                ->label('Подкатегория')
                ->relationship('category', 'title',function (Builder $query) {
                    $query->whereNotNull('parent_id'); // Проверяем parent_id именно в таблице категорий
                }) // Используем отношение category
                ->searchable()
                ->preload(),
        
            // Фильтр по городу
            SelectFilter::make('city_id')
                ->label('Город')
                ->relationship('city', 'title') // Используем отношение city
                ->searchable()
                ->preload(),


                SelectFilter::make('cemetery_id')
                ->form([
                    Select::make('cemetery_id')
                        ->label('Кладбище')
                        ->options(fn () => \App\Models\Cemetery::pluck('title', 'id'))
                        ->searchable(),
                ])
                ->query(function (Builder $query, array $data) {
                    if (!isset($data['cemetery_id']) || empty($data['cemetery_id'])) {
                        return;
                    }

                    $query->whereHas('organization', function ($query) use ($data) {
                        $query->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$data['cemetery_id']]);
                    });
                }),
            ])
            ->actions([
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт
                

            ])

            ->headerActions([
                \Filament\Tables\Actions\Action::make('export')
    ->label('Экспорт в Excel')
    ->action(function (HasTable $livewire) {
        // Получаем текущий запрос таблицы
        $query = Product::query();

        // Применяем фильтры таблицы, если они есть
        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                if (!empty($filterValue)) {
                   
                        // Простая фильтрация по значению
                        switch ($filterName) {
                            case 'city_id':
                                // Фильтрация по city_id через отношение city
                                $query->whereHas('city', function ($q) use ($filterValue) {
                                    $q->where('id', $filterValue);
                                });
                                break;
                                
                            case 'category_parent_id':
                                // Фильтрация по родительской категории
                                $query->whereHas('parentCategory', function ($q) use ($filterValue) {
                                    $q->where('id', $filterValue);
                                });
                                break;
                                
                            case 'category_id':
                                // Фильтрация по подкатегории
                                $query->whereHas('category', function ($q) use ($filterValue) {
                                    $q->where('id', $filterValue);
                                });
                                break;
                        
                          
                                
                           
                        }
                    
                }
            }
        }

        // Применяем сортировку таблицы, если она есть
        if (property_exists($livewire, 'tableSortColumn') && $livewire->tableSortColumn) {
            $query->orderBy($livewire->tableSortColumn, $livewire->tableSortDirection ?? 'asc');
        }

        // Получаем данные с учётом фильтров и сортировки (или всю таблицу, если фильтров нет)
        $products = $query->with(['parentCategory', 'category', 'city']) // Предзагрузка отношений
            ->get()
            ->map(function ($product) {
                return [
                    'ID' => $product->id,
                    'Название' => $product->title,
                    'Ссылка на товар' => $product->map_link,
                    'Организация' => $product->organization->title ?? 'Не указано',
                    'Цена' => $product->price,
                    'Цена со скидкой' => $product->price_sale,
                    'Slug' => $product->slug,
                    'Описание' => $product->content,
                    'Категория' => $product->parentCategory->title ?? 'Не указано',
                    'Подкатегория' => $product->category->title ?? 'Не указано',
                    'Город' => $product->city->title ?? 'Не указано',
                    'Материал' => $product->material,
                    'Цвет' => $product->color,
                    'Тип продукта' => $product->layering,
                    'Кафе' => $product->cafe,
                    'Размеры' => $product->size,
                    'Широта' => $product->location_width,
                    'Долгота' => $product->location_longitude,
                ];
            });

        // Если данные пустые, возвращаем сообщение
        if ($products->isEmpty()) {
            $products = Product::query()
                ->with(['parentCategory', 'category', 'city']) // Предзагрузка отношений
                ->orderBy('title') // Сортировка по названию
                ->get()
                ->map(function ($product) {
                    return [
                        'ID' => $product->id,
                        'Название' => $product->title,
                        'Ссылка на товар' => $product->map_link,
                        'Организация' => $product->organization->title ?? 'Не указано',
                        'Цена' => $product->price,
                        'Цена со скидкой' => $product->price_sale,
                        'Slug' => $product->slug,
                        'Описание' => $product->content,
                        'Категория' => $product->parentCategory->title ?? 'Не указано',
                        'Подкатегория' => $product->category->title ?? 'Не указано',
                        'Город' => $product->city->title ?? 'Не указано',
                        'Материал' => $product->material,
                        'Цвет' => $product->color,
                        'Тип продукта' => $product->layering,
                        'Кафе' => $product->cafe,
                        'Размеры' => $product->size,
                        'Широта' => $product->location_width,
                        'Долгота' => $product->location_longitude,
                    ];
                });
        }

        // Экспорт в Excel
        return (new FastExcel($products))->download('products.xlsx');
    }),
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
            ViewsRelationManager::class,

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
