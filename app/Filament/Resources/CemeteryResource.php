<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CemeteryResource\Pages;
use App\Filament\Resources\CemeteryResource\RelationManagers\PriceServiceRelationManager;
use App\Filament\Resources\CemeteryResource\RelationManagers\ViewsRelationManager;
use App\Filament\Resources\CemeteryResource\RelationManagers\WorkingHoursRelationManager;
use App\Filament\Resources\CemeteryResource\RelationManagers\WorkingsHoursRelationManager;
use App\Models\Area;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
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

class CemeteryResource extends Resource
{
    protected static ?string $model = Cemetery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Кладбища'; // Название в меню
    protected static ?string $navigationGroup = 'Ритуальные обьекты'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                
                Forms\Components\TextInput::make('width')
                    ->label('Ширина')
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

                                return "https://yandex.ru/maps/?rtext=~{$latitude},{$longitude}";
                            })
                            ->openUrlInNewTab()
                        ),

                    Select::make('edge_id')
                    ->label('Край')
                    ->options(Edge::all()->pluck('title', 'id')) // Список всех краёв
                    ->searchable() // Добавляем поиск по тексту
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('area_id', null); // Сбрасываем значение района при изменении края
                        $set('city_id', null); // Сбрасываем значение города при изменении края
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        // Устанавливаем начальное значение для edge_id при редактировании
                        if ($record && $record->city && $record->city->area) {
                            $component->state($record->city->area->edge_id);
                        }
                    })
                    ->dehydrated(false), // Не сохранять значение в базу данных
                
                Select::make('area_id')
                    ->label('Округ')
                    ->options(function ($get) {
                        $edgeId = $get('edge_id'); // Получаем выбранный край
                
                        if (!$edgeId) {
                            return []; // Если край не выбран, возвращаем пустой список
                        }
                
                        // Возвращаем список районов, привязанных к выбранному краю
                        return Area::where('edge_id', $edgeId)->pluck('title', 'id');
                    })
                    ->searchable() // Добавляем поиск по тексту
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('city_id', null); // Сбрасываем значение города при изменении района
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        // Устанавливаем начальное значение для area_id при редактировании
                        if ($record && $record->city) {
                            $component->state($record->city->area_id);
                        }
                    })
                    ->dehydrated(false), // Не сохранять значение в базу данных
                
                Select::make('city_id')
                    ->label('Город')
                    ->options(function ($get) {
                        $areaId = $get('area_id'); // Получаем выбранный район
                
                        if (!$areaId) {
                            return []; // Если район не выбран, возвращаем пустой список
                        }
                
                        // Возвращаем список городов, привязанных к выбранному району
                        return City::where('area_id', $areaId)->pluck('title', 'id');
                    })
                    ->searchable() // Добавляем поиск по тексту
                    ->required() // Город обязателен для выбора
                    ->afterStateHydrated(function (Select $component, $record) {
                        // Устанавливаем начальное значение для city_id при редактировании
                        if ($record) {
                            $component->state($record->city_id);
                        }
                    }),

                    Forms\Components\TextInput::make('adres')
                    ->label('Ориентир')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('price_burial_location')
                    ->label('Цена за геопозицию')
                    ->numeric()
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('square')
                    ->label('Площадь')
                    ->maxLength(255),

                Forms\Components\TextInput::make('responsible')
                    ->label('Отвественный')
                    ->maxLength(255),


                Forms\Components\TextInput::make('cadastral_number')
                    ->label('кадастровый номер')
                    ->maxLength(1000),


                    Forms\Components\TextInput::make('cost_sponsorship_call')
                    ->label('Стоимость спонсорства звонка с кладбища')
                    ->maxLength(1000),
                    
               

                Forms\Components\TextInput::make('email')
                    ->label('email')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->maxLength(255),

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
                        ->directory('/uploads_cemeteries') // Директория для хранения файлов
                        ->image()
                        ->maxSize(2048)
                        ->reactive()
                        ->required(fn ($get) => intval($get('href_img')) === 0)
                        ->hidden(fn ($get) => intval($get('href_img')) === 1), // Скрыто, если выбрано "Ссылка"

                    // Отображение текущего изображения (если запись уже существует)
                    View::make('image')
                        ->label('Текущее изображение')
                        ->view('filament.forms.components.custom-image') // Указываем путь к Blade-шаблону
                        ->extraAttributes(['class' => 'custom-image-class'])
                        ->columnSpan('full')
                        ->hidden(fn ($get) => intval($get('href_img')) === 0), // Скрыто, если выбрано "Файл"

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

                
                   
                
                    Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
                
                    TextInput::make('rating')
                    ->label('Рейтинг'),

                    TextInput::make('date_foundation')
                    ->label('Год основания'),
                
                                    
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
                Tables\Columns\TextColumn::make('city.title')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('edge_id')
                ->label('Край')
                ->relationship('city.area.edge', 'title') // Вложенное отношение
                ->searchable()
                ->preload(),


             // Фильтр по округу
            SelectFilter::make('area_id')
                ->label('Округ')
                ->relationship('city.area', 'title') // Вложенное отношение
                ->searchable()
                ->preload(),

            // Фильтр по городу
            SelectFilter::make('city_id')
                ->label('Город')
                ->relationship('city', 'title') // Используем вложенное отношение
                ->searchable()
                ->preload(),

                SelectFilter::make('has_phone')
                ->label('Отсутсвует телефон')
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


                SelectFilter::make('has_cadastral')
                ->label('Отсутсвует кадастр')
                ->options([
                    'yes' => 'Да',
                    'no' => 'Нет',
                ])
                ->query(function ($query, $state) {
                    if ($state['value'] === 'no') {
                        $query->whereNotNull('cadastral_number');
                    } elseif ($state['value'] === 'yes') {
                        $query->whereNull('cadastral_number');
                    }
                }),

                // SelectFilter::make('has_phone')
                // ->label('Отсутсвуют координаты')
                // ->options([
                //     'yes' => 'Да',
                //     'no' => 'Нет',
                // ])
                // ->query(function ($query, $state) {
                //     if ($state['value'] === 'no') {
                //         $query->whereNotNull('width')->whereNotNull('longitude');
                //     } elseif ($state['value'] === 'yes') {
                //         $query->whereNull('width')->whereNull('longitude');
                //     }
                // }),

                SelectFilter::make('has_burials')
                ->label('Отсутсвуют захоронения')
                ->options([
                    'yes' => 'Да',
                    'no' => 'Нет',
                ])
                ->query(function ($query, $state) {
                    if ($state['value'] === 'yes') {
                        $query->whereDoesntHave('burials');
                    } elseif ($state['value'] === 'no') {
                        $query->whereHas('burials');
                    }
                }),

                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт

            ])
           
            ->headerActions([
                // Другие действия
                \Filament\Tables\Actions\Action::make('export')
                    ->label('Экспорт в Excel')
                    ->action(function (HasTable $livewire) {
        // Получаем текущий запрос таблицы
        $query = Cemetery::query();

        // Применяем фильтры таблицы, если они есть
        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                if (!empty($filterValue['value'])) {
                    $filterValue=$filterValue['value'];
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
                        case 'has_cadastral':
                            if ($filterValue === 'no') {
                                $query->whereNotNull('cadastral_number');
                            } elseif ($filterValue === 'yes') {
                                $query->whereNull('cadastral_number');
                            }
                            break;
                        case 'has_burials':
                            if ($filterValue === 'yes') {
                                $query->whereDoesntHave('burials');
                            } elseif ($filterValue === 'no') {
                                $query->whereHas('burials');
                            }
                            break;
                    }
                }
            }
        }

        // Применяем сортировку таблицы, если она есть
        if (property_exists($livewire, 'tableSortColumn') && $livewire->tableSortColumn) {
            $query->orderBy($livewire->tableSortColumn, $livewire->tableSortDirection ?? 'asc');
        }

        // Получаем данные с учётом фильтров и сортировки
        $cemeteries = $query->with(['city.area.edge', 'city.area', 'city'])
            ->get()
            ->map(function ($cemetery) {
                return [
                    'ID' => $cemetery->id,
                    'Название' => $cemetery->title,
                    'Ширина' => $cemetery->width,
                    'Долгота' => $cemetery->longitude,
                    'Ссылка на карту' => $cemetery->map_link,
                    'Край' => $cemetery->city->area->edge->title ?? 'Не указано',
                    'Округ' => $cemetery->city->area->title ?? 'Не указано',
                    'Город' => $cemetery->city->title ?? 'Не указано',
                    'Ориентир' => $cemetery->adres,
                    'Цена за геопозицию' => $cemetery->price_burial_location,
                    'Площадь' => $cemetery->square,
                    'Ответственный' => $cemetery->responsible,
                    'Кадастровый номер' => $cemetery->cadastral_number,
                    'Стоимость спонсорства звонка' => $cemetery->cost_sponsorship_call,
                    'Email' => $cemetery->email,
                    'Телефон' => $cemetery->phone,
                    'Рейтинг' => $cemetery->rating,
                    'Год основания' => $cemetery->date_foundation,
                ];
            });

        // Если данные пустые, возвращаем все записи
        if ($cemeteries->isEmpty()) {
            $cemeteries = Cemetery::query()
                ->with(['city.area.edge', 'city.area', 'city'])
                ->orderBy('title')
                ->get()
                ->map(function ($cemetery) {
                    return [
                        'ID' => $cemetery->id,
                        'Название' => $cemetery->title,
                        'Ширина' => $cemetery->width,
                        'Долгота' => $cemetery->longitude,
                        'Ссылка на карту' => $cemetery->map_link,
                        'Край' => $cemetery->city->area->edge->title ?? 'Не указано',
                        'Округ' => $cemetery->city->area->title ?? 'Не указано',
                        'Город' => $cemetery->city->title ?? 'Не указано',
                        'Ориентир' => $cemetery->adres,
                        'Цена за геопозицию' => $cemetery->price_burial_location,
                        'Площадь' => $cemetery->square,
                        'Ответственный' => $cemetery->responsible,
                        'Кадастровый номер' => $cemetery->cadastral_number,
                        'Стоимость спонсорства звонка' => $cemetery->cost_sponsorship_call,
                        'Email' => $cemetery->email,
                        'Телефон' => $cemetery->phone,
                        'Рейтинг' => $cemetery->rating,
                        'Год основания' => $cemetery->date_foundation,
                    ];
                });
        }

        // Экспорт в Excel
        return (new FastExcel($cemeteries))->download('cemeteries.xlsx');
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
            WorkingHoursRelationManager::class,
            PriceServiceRelationManager::class,
            ViewsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCemeteries::route('/'),
            'create' => Pages\CreateCemetery::route('/create'),
            'edit' => Pages\EditCemetery::route('/{record}/edit'),
        ];
    }
}