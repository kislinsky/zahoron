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
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\MultiSelect;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Продукты маркетплэйса';
    protected static ?string $navigationGroup = 'Организации';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (auth()->user()->role === 'deputy-admin') {
            $cityIds = static::getUserCityIds();
            
            if (!empty($cityIds)) {
                $query->whereHas('organization.city', function($q) use ($cityIds) {
                    $q->whereIn('id', $cityIds);
                });
            } else {
                $query->whereNull('organization_id');
            }
        }
        
        return $query;
    }

    protected static function getUserCityIds(): array
    {
        $user = auth()->user();
        $cityIds = [];
        
        if (!empty($user->city_ids)) {
            $decoded = json_decode($user->city_ids, true);
            
            if (is_array($decoded)) {
                $cityIds = $decoded;
            } else {
                $cityIds = array_filter(explode(',', trim($user->city_ids, '[],"')));
            }
            
            $cityIds = array_map('intval', array_filter($cityIds));
        }
        
        return $cityIds;
    }

    public static function form(Form $form): Form
    {
        $isDeputyAdmin = auth()->user()->role === 'deputy-admin';
        $userCityIds = $isDeputyAdmin ? static::getUserCityIds() : [];
        
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->live(debounce: 1000)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if (!empty($state) && strlen($state) > 3) {
                            $set('slug', generateUniqueSlug($state, Product::class, $get('id')));
                        }
                    }),
                
                TextInput::make('slug')
                    ->required()
                    ->label('Slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->formatStateUsing(fn ($state) => slug($state))
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
                            ->button()
                            ->label('Страница товара')
                            ->icon('heroicon-s-eye')
                            ->url(function ($record) {
                                return $record->route();
                            })
                            ->openUrlInNewTab()
                    )->hidden(fn (?Product $record) => is_null($record)),

                Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function() use ($userCityIds, $isDeputyAdmin) {
                        if ($isDeputyAdmin) {
                            return \App\Models\Organization::whereIn('city_id', $userCityIds)
                                ->pluck('title', 'id');
                        }
                        return \App\Models\Organization::pluck('title', 'id');
                    })
                    ->disabled($isDeputyAdmin),

                TextInput::make('price')
                    ->label('Цена')
                    ->required()
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $set('total_price', $get('price_sale') ?: $get('price'))),
                
                TextInput::make('price_sale')
                    ->label('Цена со скидкой')
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => 
                        $set('total_price', !empty($get('price_sale')) ? $get('price_sale') : $get('price'))
                    ),
                
                TextInput::make('total_price')
                    ->label('Итоговая цена')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),

                RichEditor::make('content')
                    ->label('Описание')
                    ->toolbarButtons([
                        'attachFiles',
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'orderedList',
                        'bulletList',
                        'blockquote',
                        'h2', 'h3', 'h4',
                        'codeBlock',
                        'undo', 'redo',
                    ])
                    ->disableLabel(false)
                    ->placeholder('Введите HTML-контент здесь...'),

                Select::make('category_parent_id')
                    ->label('Категория')
                    ->options(CategoryProduct::where('parent_id',null)->pluck('title', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('category_id', null);
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record && $record->subcategory) {
                            $component->state($record->subcategory->category_id);
                        }
                    }),

                Select::make('category_id')
                    ->label('Подкатегория')
                    ->options(function ($get) {
                        $categoryId = $get('category_parent_id');
                        if (!$categoryId) {
                            return [];
                        }
                        return CategoryProduct::where('parent_id', $categoryId)->pluck('title', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record) {
                            $component->state($record->category_id);
                        }
                    }),

                TextInput::make('material')
                    ->label('Материал')
                    ->maxLength(255),

                TextInput::make('color')
                    ->label('Цвет')
                    ->maxLength(255),

                TextInput::make('layering')
                    ->label('Тип продукта (облогоражиавние)')
                    ->maxLength(255),

                TextInput::make('size')
                    ->label('Размеры')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Карта колонок для экспорта (русские названия)
        $columnsMap = [
            'id' => 'ID',
            'title' => 'Название',
            'slug' => 'Слаг',
            'view' => 'Отображение',
            'organization_id' => 'Организация',
            'price' => 'Цена',
            'price_sale' => 'Цена со скидкой',
            'total_price' => 'Итоговая цена',
            'content' => 'Описание',
            'category_id' => 'Подкатегория',
            'category_parent_id' => 'Категория',
            'material' => 'Материал',
            'color' => 'Цвет',
            'layering' => 'Тип продукта',
            'size' => 'Размеры',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'organization.city.title' => 'Город',
        ];
        
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
                Tables\Columns\TextColumn::make('organization.city.title')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_parent_id')
                    ->label('Категория')
                    ->relationship('parentCategory', 'title', function (Builder $query) {
                        $query->whereNull('parent_id');
                    })
                    ->searchable()
                    ->preload(),
        
                SelectFilter::make('category_id')
                    ->label('Подкатегория')
                    ->relationship('category', 'title', function (Builder $query) {
                        $query->whereNotNull('parent_id');
                    })
                    ->searchable()
                    ->preload(),
        
                SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('organization.city', 'title')
                    ->searchable()
                    ->hidden(auth()->user()->role === 'deputy-admin'),

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
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('export')
                    ->label('Экспорт в Excel')
                    ->action(function ($livewire, array $data) use ($columnsMap) {
                        $fileName = 'products_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                        
                        // Получаем отфильтрованный запрос
                        $query = $livewire->getFilteredTableQuery()
                            ->with(['organization.city', 'parentCategory', 'category']);
                        
                        // Применяем права доступа для deputy-admin
                        if (auth()->user()->role === 'deputy-admin') {
                            $cityIds = static::getUserCityIds();
                            if (!empty($cityIds)) {
                                $query->whereHas('organization.city', function($q) use ($cityIds) {
                                    $q->whereIn('id', $cityIds);
                                });
                            }
                        }
                        
                        // Выбранные колонки или все по умолчанию
                        $columns = $data['columns'] ?: array_keys($columnsMap);
                        
                        $collection = $query->get()->map(function ($item) use ($columns, $columnsMap) {
                            return collect($columns)->mapWithKeys(function ($col) use ($item, $columnsMap) {
                                $value = '';
                                
                                // Обработка специальных полей
                                if ($col === 'id') {
                                    $value = (string) $item->id;
                                } elseif ($col === 'view') {
                                    $value = match ((int)$item->view) {
                                        0 => 'Не показывать',
                                        1 => 'Показывать',
                                        default => 'Не указано',
                                    };
                                } elseif ($col === 'organization_id') {
                                    $value = optional($item->organization)->title;
                                } elseif ($col === 'organization.city.title') {
                                    $value = optional($item->organization)->city ? $item->organization->city->title : '';
                                } elseif ($col === 'category_parent_id') {
                                    $value = optional($item->parentCategory)->title;
                                } elseif ($col === 'category_id') {
                                    $value = optional($item->category)->title;
                                } elseif (in_array($col, ['created_at', 'updated_at'])) {
                                    $value = $item->{$col} ? $item->{$col}->format('d.m.Y H:i:s') : '';
                                } else {
                                    // Обычные поля модели
                                    $value = $item->{$col} ?? '';
                                }
                                
                                return [$columnsMap[$col] ?? $col => $value];
                            })->toArray();
                        });
                        
                        return (new FastExcel($collection))->download($fileName);
                    })
                    ->form([
                        MultiSelect::make('columns')
                            ->label('Выберите колонки для экспорта')
                            ->options($columnsMap)
                            ->helperText('Если ничего не выбрано, будут экспортированы все колонки')
                    ])
                    ->modalAutofocus(false)
                    ->modalSubmitActionLabel('Скачать Excel')
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
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'deputy-admin';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }
        
        if (auth()->user()->role === 'deputy-admin') {
            $userCityIds = static::getUserCityIds();
            return $record->organization && $record->organization->city && in_array($record->organization->city->id, $userCityIds);
        }
        
        return false;
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }
        
        if (auth()->user()->role === 'deputy-admin') {
            return !empty(static::getUserCityIds());
        }
        
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }
}