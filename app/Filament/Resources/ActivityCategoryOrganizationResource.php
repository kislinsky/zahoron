<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityCategoryOrganizationResource\Pages;
use App\Filament\Resources\ActivityCategoryOrganizationResource\RelationManagers;
use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProduct;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rap2hpoutre\FastExcel\FastExcel;

class ActivityCategoryOrganizationResource extends Resource
{
    protected static ?string $model = ActivityCategoryOrganization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Цены';
    protected static ?string $navigationGroup = 'Организации';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('organization_id')
                    ->label('ID Организации')
                    ->required()
                    ->numeric(),

                Select::make('category_main_id')
                    ->label('Категория')
                    ->options(function () {
                        return CategoryProduct::whereNull('parent_id')->pluck('title', 'id');
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('category_children_id', null);
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record && $record->categoryProduct && $record->categoryProduct->parent_id) {
                            $component->state($record->categoryProduct->parent_id);
                        }
                    }),

                Select::make('category_children_id')
                    ->label('Подкатегория')
                    ->options(function ($get) {
                        $parentId = $get('category_main_id');
                        if (!$parentId) {
                            return [];
                        }
                        return CategoryProduct::where('parent_id', $parentId)->pluck('title', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record && $record->categoryProduct) {
                            $component->state($record->categoryProduct->id);
                        }
                    }),

                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->default(0)
                    ->prefix('₽'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('organization.title')
                    ->label('Организация')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('organization.city.area.edge.title')
                    ->label('Край')
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.city.area.title')
                    ->label('Округ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.city.title')
                    ->label('Город')
                    ->sortable(),

                Tables\Columns\TextColumn::make('categoryMain.title')
                    ->label('Основная категория')
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('categoryProduct.title')
                    ->label('Подкатегория')
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->formatStateUsing(fn ($state) => $state == 0 ? '-' : $state . ' ₽')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                SelectFilter::make('category_main_id')
                ->label('Основная категория')
                ->relationship('categoryMain', 'title', function (Builder $query) {
                    $query->whereNull('parent_id'); // Проверяем parent_id именно в таблице категорий
                })
                ->searchable()
                ->preload(),
        
            // Фильтр по подкатегории
            SelectFilter::make('category_children_id')
                ->label('Подкатегория')
                ->relationship('categoryProduct', 'title',function (Builder $query) {
                    $query->whereNotNull('parent_id'); // Проверяем parent_id именно в таблице категорий
                }) // Используем отношение category
                ->searchable()
                ->preload(),



             
                    
                SelectFilter::make('edge_id')
                    ->label('Край')
                    ->relationship('organization.city.area.edge', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('area_id')
                    ->label('Округ')
                    ->relationship('organization.city.area', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('organization.city', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('has_price')
                    ->label('Наличие цены')
                    ->options([
                        'with_price' => 'С ценами',
                        'without_price' => 'Без цен (0)',
                    ])
                    ->query(function ( $query, $state) {
                        if ($state['value'] === 'with_price') {
                            $query->where('price', '>', 0);
                        } elseif ($state['value'] === 'without_price') {
                            $query->where('price', 0);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Экспорт в Excel')
                    ->action(function (HasTable $livewire) {
                        $query = ActivityCategoryOrganization::query();
                
                        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
                            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                                if (!empty($filterValue['value'])) {
                                    $state = $filterValue['value'];
                
                                    switch ($filterName) {
                                        case 'city_id':
                                            $query->whereHas('organization.city', fn($q) => $q->where('id', $state));
                                            break;
                                        case 'area_id':
                                            $query->whereHas('organization.city.area', fn($q) => $q->where('id', $state));
                                            break;
                                        case 'edge_id':
                                            $query->whereHas('organization.city.area.edge', fn($q) => $q->where('id', $state));
                                            break;
                                        case 'category_main_id':
                                            $query->where('category_main_id', $state);
                                            break;
                                        case 'category_children_id':
                                            $query->where('category_children_id', $state);
                                            break;
                                        case 'has_price':
                                            $state === 'with_price' 
                                                ? $query->where('price', '>', 0) 
                                                : $query->where('price', 0);
                                            break;
                                    }
                                }
                            }
                        }
                
                        if (property_exists($livewire, 'tableSortColumn') && $livewire->tableSortColumn) {
                            $query->orderBy($livewire->tableSortColumn, $livewire->tableSortDirection ?? 'asc');
                        }
                
                        $prices = $query->with([
                                'organization.city.area.edge',
                                'organization.city.area',
                                'organization.city',
                                'categoryMain',
                                'categoryProduct'
                            ])
                            ->get()
                            ->map(function ($price) {
                                return [
                                    'ID' => (string)$price->id,
                                    'Организация' => $price->organization->title ?? 'Не указано',
                                    'Край' => $price->organization->city->area->edge->title ?? 'Не указано',
                                    'Округ' => $price->organization->city->area->title ?? 'Не указано',
                                    'Город' => $price->organization->city->title ?? 'Не указано',
                                    'Основная категория' => $price->categoryMain->title ?? 'Не указано',
                                    'Подкатегория' => $price->categoryProduct->title ?? 'Не указано',
                                    'Цена' => $price->price == 0 ? 'Нет' : $price->price . ' ₽',
                                    'Дата создания' => $price->created_at?->format('d.m.Y H:i'),
                                    'Дата обновления' => $price->updated_at?->format('d.m.Y H:i'),
                                ];
                            });
                
                        if ($prices->isEmpty()) {
                            $prices = ActivityCategoryOrganization::query()
                                ->with([
                                    'organization.city.area.edge',
                                    'organization.city.area',
                                    'organization.city',
                                    'categoryMain',
                                    'categoryProduct'
                                ])
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->map(fn($price) => [
                                    'ID' => (string)$price->id,
                                    'Организация' => $price->organization->title ?? 'Не указано',
                                    'Край' => $price->organization->city->area->edge->title ?? 'Не указано',
                                    'Округ' => $price->organization->city->area->title ?? 'Не указано',
                                    'Город' => $price->organization->city->title ?? 'Не указано',
                                    'Основная категория' => $price->categoryMain->title ?? 'Не указано',
                                    'Подкатегория' => $price->categoryProduct->title ?? 'Не указано',
                                    'Цена' => $price->price == 0 ? 'Нет' : $price->price . ' ₽',
                                    'Дата создания' => $price->created_at?->format('d.m.Y H:i'),
                                    'Дата обновления' => $price->updated_at?->format('d.m.Y H:i'),
                                ]);
                        }
                
                        return (new FastExcel($prices))->download('prices_export_' . date('Y-m-d') . '.xlsx');
                    })
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
            'index' => Pages\ListActivityCategoryOrganizations::route('/'),
            'create' => Pages\CreateActivityCategoryOrganization::route('/create'),
            'edit' => Pages\EditActivityCategoryOrganization::route('/{record}/edit'),
        ];
    }
}