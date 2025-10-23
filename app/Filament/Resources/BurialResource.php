<?php
namespace App\Filament\Resources;

use App\Filament\Resources\BurialResource\Pages;
use App\Filament\Resources\BurialResource\RelationManagers\ImageMonumentRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\ImagePersonalRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\InfoEditBurialRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\LifeStoryBurialRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\ViewsRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\WordsMemoryRelationManager;
use App\Models\Area;
use App\Models\Burial;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Rap2hpoutre\FastExcel\FastExcel;

class BurialResource extends Resource
{
    protected static ?string $model = Burial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Захоронения'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('surname')
                    ->label('Фамилия')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('patronymic')
                    ->label('Отчество')
                    ->required()
                    ->maxLength(255),

            

                Forms\Components\TextInput::make('date_birth')
                    ->label('Дата рождения')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('date_death')
                    ->label('Дата смерти')
                    ->required()
                    ->maxLength(255),
                            
                    Forms\Components\TextInput::make('width')
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
                                ->button() // Отобразить как кнопку
                                ->label('Открыть карту')
                                ->icon('heroicon-s-map') // Иконка кнопки
                                // Текст кнопки
                                ->url(function ($get) {
                                    $latitude = $get('width');
                                    $longitude = $get('longitude');

                                    return "https://yandex.ru/maps/?pt={$latitude},{$longitude}&z=18&l=map";
                                })
                                ->openUrlInNewTab()
                            ),

                         Select::make('edge_id')
    ->label('Край')
    ->options(Edge::all()->pluck('title', 'id'))
    ->searchable()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $set('area_id', null);
        $set('city_id', null); 
        $set('cemetery_id', null);
    })
    ->afterStateHydrated(function (Select $component, $record) {
        // Получаем edge_id через цепочку от кладбища
        if ($record && $record->cemetery_id) {
            $cemetery = Cemetery::with('city.area.edge')->find($record->cemetery_id);
            if ($cemetery && $cemetery->city && $cemetery->city->area && $cemetery->city->area->edge) {
                $component->state($cemetery->city->area->edge_id);
            }
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
        $set('cemetery_id', null);
    })
    ->afterStateHydrated(function (Select $component, $record) {
        // Получаем area_id через цепочку от кладбища
        if ($record && $record->cemetery_id) {
            $cemetery = Cemetery::with('city.area')->find($record->cemetery_id);
            if ($cemetery && $cemetery->city && $cemetery->city->area) {
                $component->state($cemetery->city->area_id);
            }
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
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $set('cemetery_id', null);
    })
    ->afterStateHydrated(function (Select $component, $record) {
        // Получаем city_id от кладбища
        if ($record && $record->cemetery_id) {
            $cemetery = Cemetery::with('city')->find($record->cemetery_id);
            if ($cemetery && $cemetery->city) {
                $component->state($cemetery->city_id);
            }
        }
    })
    ->dehydrated(false),

Select::make('cemetery_id')
    ->label('Кладбище')
    ->options(function ($get) {
        $cityId = $get('city_id');
        
        if (!$cityId) {
            return [];
        }
        
        return Cemetery::where('city_id', $cityId)->pluck('title', 'id');
    })
    ->searchable()
    ->required()
    ->afterStateHydrated(function (Select $component, $record) {
        if ($record) {
            $component->state($record->cemetery_id);
        }
    }),




                        Radio::make('href_img')
                        ->label('Выберите источник изображения')
                        ->options([
                            0 => 'Файл на сайте',
                            1 => 'Ссылка (URL)'
                        ])
                        ->inline()
                        ->live(), // Автоматически обновляет форму при изменении

                    // Поле для ссылки (отображается только если выбран вариант "Ссылка")
                    TextInput::make('img_url')
                        ->label('Ссылка на изображение')
                        ->placeholder('https://example.com/image.jpg')
                        ->reactive()
                        ->required(fn ($get) => intval($get('href_img')) === 1)
                        ->hidden(fn ($get) => intval($get('href_img')) === 0), // Скрыто, если выбрано "Файл"

                    // Поле для загрузки файла (отображается только если выбран вариант "Файл на сайте")
                   FileUpload::make('img_file')
    ->label('Загрузить изображение')
    ->directory('/uploads_burials')
    ->image()
    ->maxSize(2048)
    ->reactive()
    ->required(fn ($get) => intval($get('href_img')) === 0)
    ->hidden(fn ($get) => intval($get('href_img')) === 1),

View::make('image')
    ->label('Текущее изображение')
    ->view('filament.forms.components.custom-image-burial')
    ->extraAttributes(['class' => 'custom-image-class'])
    ->columnSpan('full')
    ->hidden(fn ($get) => intval($get('href_img')) === 0),

                       

                           

                Forms\Components\TextInput::make('location_death')
                    ->label('Место смерти')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('who')
                    ->label('Вид захоронения')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('information')
                    ->label('Информация')
                    ->maxLength(255),

                Forms\Components\TextInput::make('agent_id')
                    ->label('Фотограф')
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true) // Игнорировать текущую запись при редактировании
                    ->label('Slug')
                    
                    ->maxLength(255),

                Select::make('status') // Поле для статуса
                    ->label('Статус') // Название поля
                    ->options([
                        0 => 'Не распознан',
                        1 => 'Распознан',
                        2 => 'Отправлен на проверку',
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение по умолчанию

                  
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
                    Tables\Columns\TextColumn::make('surname')
                    ->label('Фамилия')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('patronymic')
                    ->label('Отчество')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cemetery.title')
                    ->label('Кладбище')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('who')
                    ->label('Вид захоронения')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('date_birth')
                    ->label('Дата рождения')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('date_death')
                    ->label('Дата смерти')
                    ->searchable()
                    ->sortable(),

                    Tables\Columns\TextColumn::make('agent_id')
                    ->label('Фотограф')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не распознан',
                        1 => 'Распознан',
                        2 => 'Отправлен на проверку',
                    }),

            ])

            
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Не распознан',
                        1 => 'Распознан',
                        2 => 'Отправлен на проверку',
                    ]),
                Filter::make('name')
                    ->label('Поиск по имени')
                    ->form([
                        TextInput::make('name')
                            ->label('Имя')
                            ->placeholder('Введите имя'), // Плейсхолдер задаётся здесь
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['name'])) {
                            $query->where('name', 'like', '%' . $data['name'] . '%');
                        }
                    }),
                    Filter::make('surname')
                    ->label('Поиск по фамилии')
                    ->form([
                        TextInput::make('surname')
                            ->label('Фамилия')
                            ->placeholder('Введите фамилию'), // Плейсхолдер задаётся здесь
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['surname'])) {
                            $query->where('surname', 'like', '%' . $data['surname'] . '%');
                        }
                    }),
                    Filter::make('patronymic')
                    ->label('Поиск по отчеству')
                    ->form([
                        TextInput::make('patronymic')
                            ->label('Отчество')
                            ->placeholder('Введите отчество'), // Плейсхолдер задаётся здесь
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['patronymic'])) {
                            $query->where('patronymic', 'like', '%' . $data['patronymic'] . '%');
                        }
                    }),

                    SelectFilter::make('edge_id')
                        ->label('Край')
                        ->relationship('cemetery.city.area.edge', 'title') // Вложенное отношение
                        ->searchable()
                        ->preload(),


                     // Фильтр по округу
                    SelectFilter::make('area_id')
                        ->label('Округ')
                        ->relationship('cemetery.city.area', 'title') // Вложенное отношение
                        ->searchable()
                        ->preload(),

                    // Фильтр по городу
                    SelectFilter::make('city_id')
                        ->label('Город')
                        ->relationship('cemetery.city', 'title') // Используем вложенное отношение
                        ->searchable(),
                    
                    SelectFilter::make('cemetery_id')
                        ->label('Кладбище')
                        ->relationship('cemetery', 'title') // Используем отношение и поле для отображения
                        ->searchable() // Добавляем поиск
                        ->preload(), // Предзагрузка данных
            
             
               
                 
            ])
            ->actions([
                Tables\Actions\Action::make('view_burial')
                ->label('Посмотреть') // Текст кнопки
                ->url(fn ($record) => '/'.selectCity()->slug.'/burial/'.$record->slug) // Ссылка на товар
                ->icon('heroicon-o-eye') // Иконка "глаза"
                ->color('primary') // Цвет кнопки
                ->openUrlInNewTab(),
                

                Tables\Actions\Action::make('Геолокация')
                ->url(fn (Burial $record): string =>  "https://yandex.ru/maps/?rtext=~{$record->width},{$record->longitude}")
                ->openUrlInNewTab(),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('export')
                    ->label('Экспорт в Excel')
                    ->action(function (HasTable $livewire) {
                        // Получаем текущий запрос таблицы
                        $query = Burial::query();

                        // Применяем фильтры таблицы, если они есть
                        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
                            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                                if (!empty($filterValue)) {
                                    // Обработка сложных фильтров (например, диапазонов дат)
                                    if (is_array($filterValue)) {
                                        if (isset($filterValue['start']) && isset($filterValue['end'])) {
                                            // Пример для фильтрации по диапазону дат
                                            $query->whereBetween($filterName, [$filterValue['start'], $filterValue['end']]);
                                        } elseif (isset($filterValue['value'])) {
                                            // Пример для фильтрации по значению
                                            $query->where($filterName, $filterValue['value']);
                                        }
                                    } else {
                                        // Простая фильтрация по значению
                                        switch ($filterName) {
                                            case 'city_id':
                                                // Фильтрация по city_id через отношение cemetery
                                                $query->whereHas('cemetery', function ($q) use ($filterValue) {
                                                    $q->where('city_id', $filterValue);
                                                });
                                                break;
                                            case 'area_id':
                                                // Фильтрация по area_id через отношение cemetery.city
                                                $query->whereHas('cemetery.city', function ($q) use ($filterValue) {
                                                    $q->where('area_id', $filterValue);
                                                });
                                                break;
                                            case 'edge_id':
                                                // Фильтрация по edge_id через отношение cemetery.city.area
                                                $query->whereHas('cemetery.city.area', function ($q) use ($filterValue) {
                                                    $q->where('edge_id', $filterValue);
                                                });
                                                break;
                                            default:
                                                // Простая фильтрация по полям таблицы burials
                                                $query->where($filterName, $filterValue);
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Применяем сортировку таблицы, если она есть
                        if (property_exists($livewire, 'tableSortColumn') && $livewire->tableSortColumn) {
                            $query->orderBy($livewire->tableSortColumn, $livewire->tableSortDirection ?? 'asc');
                        }
                        
                        // Получаем данные с учётом фильтров и сортировки (или всю таблицу, если фильтров нет)
                        $burials = $query->with(['cemetery.city.area.edge']) // Предзагрузка отношений
                            ->get()
                            ->map(function ($burial) {
                                return [
                                    'ID' => $burial->id,
                                    'Имя' => $burial->name,
                                    'Фамилия' => $burial->surname,
                                    'Отчество' => $burial->patronymic,
                                    'Дата рождения' => $burial->date_birth,
                                    'Дата смерти' => $burial->date_death,
                                    'Край' => $burial->cemetery->city->area->edge->title ?? 'Не указано',
                                    'Округ' => $burial->cemetery->city->area->title ?? 'Не указано',
                                    'Город' => $burial->cemetery->city->title ?? 'Не указано',
                                    'Кладбище' => $burial->cemetery->title ?? 'Не указано',
                                    'Вид захоронения' => $burial->who,
                                    'Статус' => match ($burial->status) {
                                        0 => 'Не распознан',
                                        1 => 'Распознан',
                                        2 => 'Отправлен на проверку',
                                        default => 'Неизвестно',
                                    },
                                ];
                            });
                        
                        // Если данные пустые, возвращаем сообщение
                        if ($burials->isEmpty()) {
                            $burials = Burial::query()
                                ->with(['cemetery.city.area.edge']) // Предзагрузка отношений
                                ->orderBy('name') // Сортировка по названию
                                ->get()
                                ->map(function ($burial) {
                                    return [
                                        'ID' => $burial->id,
                                        'Имя' => $burial->name,
                                        'Фамилия' => $burial->surname,
                                        'Отчество' => $burial->patronymic,
                                        'Дата рождения' => $burial->date_birth,
                                        'Дата смерти' => $burial->date_death,
                                        'Край' => $burial->cemetery->city->area->edge->title ?? 'Не указано',
                                        'Округ' => $burial->cemetery->city->area->title ?? 'Не указано',
                                        'Город' => $burial->cemetery->city->title ?? 'Не указано',
                                        'Кладбище' => $burial->cemetery->title ?? 'Не указано',
                                        'Вид захоронения' => $burial->who,
                                        'Статус' => match ($burial->status) {
                                            0 => 'Не распознан',
                                            1 => 'Распознан',
                                            2 => 'Отправлен на проверку',
                                            default => 'Неизвестно',
                                        },
                                    ];
                                });
                        }
                        
                        // Экспорт в Excel
                        return (new FastExcel($burials))->download('burials.xlsx');
                    }),
            ])

            ->paginated([10, 25, 50, 100, 200, 300, 400, 'all']);

    }

    public static function getRelations(): array
    {
        return [
            // Добавляем связи для вывода фотографий
            ImagePersonalRelationManager::class,
            WordsMemoryRelationManager::class,
            InfoEditBurialRelationManager::class,
            ImageMonumentRelationManager::class,
            ViewsRelationManager::class,
            LifeStoryBurialRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBurials::route('/'),
            'create' => Pages\CreateBurial::route('/create'),
            'edit' => Pages\EditBurial::route('/{record}/edit'),
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