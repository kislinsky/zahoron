<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChurchResource\Pages;
use App\Filament\Resources\ChurchResource\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\ChurchResource\RelationManagers\ViewsRelationManager;
use App\Filament\Resources\ChurchResource\RelationManagers\WorkingHoursRelationManager;
use App\Models\Area;
use App\Models\Church;
use App\Models\City;
use App\Models\Edge;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Rap2hpoutre\FastExcel\FastExcel;

class ChurchResource extends Resource
{
    protected static ?string $model = Church::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Церкви';
    protected static ?string $navigationGroup = 'Ритуальные обьекты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                Select::make('edge_id')
                    ->label('Край')
                    ->options(Edge::all()->pluck('title', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('area_id', null);
                        $set('city_id', null);
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record && $record->city && $record->city->area) {
                            $component->state($record->city->area->edge_id);
                        }
                    })
                    ->dehydrated(false),
                
                Select::make('area_id')
                    ->label('Округ')
                    ->options(function ($get) {
                        $edgeId = $get('edge_id');
                
                        if (!$edgeId) {
                            return [];
                        }
                
                        return Area::where('edge_id', $edgeId)->pluck('title', 'id');
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('city_id', null);
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record && $record->city) {
                            $component->state($record->city->area_id);
                        }
                    })
                    ->dehydrated(false),
                
                Select::make('city_id')
                    ->label('Город')
                    ->options(function ($get) {
                        $areaId = $get('area_id');
                
                        if (!$areaId) {
                            return [];
                        }
                
                        return City::where('area_id', $areaId)->pluck('title', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record) {
                            $component->state($record->city_id);
                        }
                    }),

                Forms\Components\TextInput::make('latitude')
                    ->label('Широта')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('longitude')
                    ->label('Долгота')
                    ->required()
                    ->maxLength(255),

                TextInput::make('map_link')
                    ->label('Ссылка на карту')
                    ->disabled()
                    ->suffixAction(
                        Action::make('open_map')
                            ->button()
                            ->label('Открыть карту')
                            ->icon('heroicon-s-map')
                            ->url(function ($get) {
                                $latitude = $get('latitude');
                                $longitude = $get('longitude');
                                return "https://yandex.ru/maps/?rtext=~{$latitude},{$longitude}";
                            })
                            ->openUrlInNewTab()
                    ),

                Forms\Components\TextInput::make('underground')
                    ->label('Метро')
                    ->maxLength(255),

                Forms\Components\TextInput::make('next_to')
                    ->label('Рядом с')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->maxLength(255),

                RichEditor::make('mini_content')
                    ->label('Краткое описание')
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
                    ->placeholder('Введите краткое описание...'),

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
                    ->placeholder('Введите описание...'),

                Radio::make('href_img')
                    ->label('Выберите источник изображения')
                    ->options([
                        0 => 'Файл на сайте',
                        1 => 'Ссылка (URL)'
                    ])
                    ->inline()
                    ->live(),

                TextInput::make('img_url')
                    ->label('Ссылка на изображение')
                    ->placeholder('https://example.com/image.jpg')
                    ->reactive()
                    ->required(fn ($get) => intval($get('href_img')) === 1)
                    ->hidden(fn ($get) => intval($get('href_img')) === 0),

                FileUpload::make('img_file')
                    ->label('Загрузить изображение')
                    ->directory('/uploads_church')
                    ->image()
                    ->maxSize(2048)
                    ->reactive()
                    ->required(fn ($get) => intval($get('href_img')) === 0)
                    ->hidden(fn ($get) => intval($get('href_img')) === 1),

                View::make('image')
                    ->label('Текущее изображение')
                    ->view('filament.forms.components.custom-image')
                    ->extraAttributes(['class' => 'custom-image-class'])
                    ->columnSpan('full')
                    ->hidden(fn ($get) => intval($get('href_img')) === 0),

                Forms\Components\TextInput::make('address')
                    ->label('Адрес')
                    ->maxLength(255),
                
                Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
                
                TextInput::make('rating')
                    ->label('Рейтинг')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.title')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('edge_id')
                    ->label('Край')
                    ->relationship('city.area.edge', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('area_id')
                    ->label('Округ')
                    ->relationship('city.area', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title')
                    ->searchable(),

                SelectFilter::make('has_phone')
                    ->label('Отсутствует телефон')
                    ->options([
                        'yes' => 'Да',
                        'no' => 'Нет',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'no') {
                            $query->whereNotNull('phone');
                        } elseif ($state['value'] === 'yes') {
                            $query->whereNull('phone');
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Экспорт в Excel')
                    ->action(function (HasTable $livewire) {
                        $query = Church::query();

                        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
                            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                                if (!empty($filterValue['value'])) {
                                    $filterValue = $filterValue['value'];
                                    switch ($filterName) {
                                        case 'edge_id':
                                            $query->whereHas('city.area.edge', function ($q) use ($filterValue) {
                                                $q->where('id', $filterValue);
                                            });
                                            break;
                                        case 'area_id':
                                            $query->whereHas('city.area', function ($q) use ($filterValue) {
                                                $q->where('id', $filterValue);
                                            });
                                            break;
                                        case 'city_id':
                                            $query->whereHas('city', function ($q) use ($filterValue) {
                                                $q->where('id', $filterValue);
                                            });
                                            break;
                                        case 'has_phone':
                                            if ($filterValue === 'no') {
                                                $query->whereNotNull('phone');
                                            } elseif ($filterValue === 'yes') {
                                                $query->whereNull('phone');
                                            }
                                            break;
                                    }
                                }
                            }
                        }

                        if (property_exists($livewire, 'tableSortColumn') && $livewire->tableSortColumn) {
                            $query->orderBy($livewire->tableSortColumn, $livewire->tableSortDirection ?? 'asc');
                        }

                        $churches = $query->with(['city.area.edge', 'city.area', 'city'])
                            ->get()
                            ->map(function ($church) {
                                return [
                                    'ID' => $church->id,
                                    'Название' => $church->title,
                                    'Широта' => $church->latitude,
                                    'Долгота' => $church->longitude,
                                    'Ссылка на карту' => $church->map_link,
                                    'Край' => $church->city->area->edge->title ?? 'Не указано',
                                    'Округ' => $church->city->area->title ?? 'Не указано',
                                    'Город' => $church->city->title ?? 'Не указано',
                                    'Адрес' => $church->address,
                                    'Телефон' => $church->phone,
                                    'Рейтинг' => $church->rating,
                                ];
                            });

                        if ($churches->isEmpty()) {
                            $churches = Church::query()
                                ->with(['city.area.edge', 'city.area', 'city'])
                                ->orderBy('title')
                                ->get()
                                ->map(function ($church) {
                                    return [
                                        'ID' => $church->id,
                                        'Название' => $church->title,
                                        'Широта' => $church->latitude,
                                        'Долгота' => $church->longitude,
                                        'Ссылка на карту' => $church->map_link,
                                        'Край' => $church->city->area->edge->title ?? 'Не указано',
                                        'Округ' => $church->city->area->title ?? 'Не указано',
                                        'Город' => $church->city->title ?? 'Не указано',
                                        'Адрес' => $church->address,
                                        'Телефон' => $church->phone,
                                        'Рейтинг' => $church->rating,
                                    ];
                                });
                        }

                        return (new FastExcel($churches))->download('churches.xlsx');
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
            ImagesRelationManager::class,
            WorkingHoursRelationManager::class,
            ViewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChurches::route('/'),
            'create' => Pages\CreateChurch::route('/create'),
            'edit' => Pages\EditChurch::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}