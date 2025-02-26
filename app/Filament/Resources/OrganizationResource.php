<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Filament\Resources\OrganizationResource\RelationManagers\ActivityCategoriesRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\OrganizationRequestCountRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\WorkingHoursRelationManager;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Organization;
use App\Models\User;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rap2hpoutre\FastExcel\FastExcel;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Организации'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
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
            ->directory('/uploads_mortuaries') // Директория для хранения файлов
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
            ->hidden(fn ($get) => intval($get('href_img')) === 0), 


            TextInput::make('title')
            ->label('Название фирмы')
            ->required()
            ->live(debounce: 1000) // Задержка автообновления
            ->afterStateUpdated(function ($state, $set, $get) {
                // Проверяем, если длина title больше 3 символов, обновляем slug
                if (!empty($state) && strlen($state) > 3) {
                    $set('slug', generateUniqueSlug($state, Organization::class, $get('id')));
                }
            }),
        
        TextInput::make('slug')
            ->required()
            ->label('Slug')
            ->maxLength(255)
            ->unique(ignoreRecord: true) // Проверка уникальности
            ->formatStateUsing(fn ($state) => slug($state)) // Форматируем slug
            ->dehydrateStateUsing(fn ($state, $get) => generateUniqueSlug($state, Organization::class, $get('id'))),

            Radio::make('status')
            ->label('Отображение организации')
            ->options([
                0 => 'Не показывать',
                1 => 'Показывать'
            ])
            ->inline(),

            Forms\Components\Select::make('city_id')
                ->label('Город')
                ->relationship('city', 'title')
                ->required()
                ->searchable()
                ->preload()
                ->live(), // Позволяет обновлять зависимые поля при изменении

               
            Forms\Components\TextInput::make('width')
                ->label('Ширина')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('longitude')
                ->label('Долгота')
                ->required()
                ->maxLength(255),

         

            Forms\Components\TextInput::make('underground')
                ->label('Метро')
                ->maxLength(255),

            Forms\Components\TextInput::make('next_to')
                ->label('Рядом с')
                ->maxLength(255),

         

            Forms\Components\TextInput::make('email')
                ->label('email')
                ->maxLength(255),

            Forms\Components\TextInput::make('phone')
                ->label('Телефон')
                ->maxLength(255),
                Forms\Components\TextInput::make('adres')
                ->label('Адрес')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('name_type')
                ->label('Тип организации')
                ->maxLength(255),

            


            Forms\Components\TextInput::make('whatsapp')
                ->label('whatsapp')
                ->maxLength(255),

            Forms\Components\TextInput::make('telegram')
                ->label('telegram')
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

                
      

                TextInput::make('map_link')
                ->label('Страница пользователя')
                ->disabled()
                ->suffixAction(
                    Action::make('open_map')
                        ->button() // Отобразить как кнопку
                        ->label('Страница пользователя')
                        ->icon('heroicon-s-eye') // Иконка глаза
                        // Текст кнопки
                        ->url(function ($record) {
                            // Используем $record для получения текущего продукта
                            return '/'.selectCity()->slug.'/admin/users/'.$record->user_id.'/edit'; // Возвращаем URL продукта
                        })
                        ->openUrlInNewTab()
                    )->hidden(fn (?Organization $record) => is_null($record)),

                Placeholder::make('created_at')
                ->label('Дата создания')
                ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
            
                Forms\Components\TextInput::make('rating')
                ->label('Рейтинг')
                ->required()
                ->maxLength(255),

                Select::make('cemetery_ids')
                ->label('Кладбища, на которых работает организация')
                ->options(fn ($get) => getCemeteriesOptions($get))
                ->multiple() // Разрешаем выбор нескольких значений
                ->searchable() // Добавляем поиск
                ->required() // Обязательное поле
                ->formatStateUsing(function ($state) {
                    // Если данные уже есть (например, загружены из базы), преобразуем их в массив
                    return $state ? array_map('intval', explode(',', trim($state, ','))) : [];
                })
                ->preload()
                ->dehydrateStateUsing(fn ($state) => implode(',', (array) $state).','), // Преобразуем в строку перед сохранением

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

                    SelectFilter::make('city_id')
                        ->label('Город')
                        ->relationship('city', 'title') // Вложенное отношение
                        ->searchable()
                        ->preload(),

                        SelectFilter::make('cemetery_id')
                        ->label('Работает на кладбище')
                        ->options(Cemetery::pluck('title', 'id'))
                        ->searchable()
                        ->query(function ($query, $state) {
                            if ($state['value']!=null) {
                                $query->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$state]);
                            }
                        }),
                        SelectFilter::make('user_id')
                            ->label('Получен доступ')
                            ->options([
                                'yes' => 'Да',
                                'no' => 'Нет',
                            ])
                            ->query(function ($query, $state) {
                                if ($state['value'] === 'yes') {
                                    $query->whereNotNull('user_id');
                                } elseif ($state['value'] === 'no') {
                                    $query->whereNull('user_id');
                                }
                            }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт

            ])->headerActions([
                \Filament\Tables\Actions\Action::make('export')
    ->label('Экспорт в Excel')
    ->action(function (HasTable $livewire) {
        // Получаем текущий запрос таблицы
        $query = Organization::query();

        // Применяем фильтры таблицы, если они есть
        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                if (!empty($filterValue['value'])) {
                    $filterValue=$filterValue['value'];

                        // Простая фильтрация по значению
                        switch ($filterName) {
                            case 'city_id':

                                // Фильтрация по city_id через отношение city
                                $query->whereHas('city', function ($q) use ($filterValue) {
                                    $q->where('id', $filterValue);
                                });
                                break;
                            case 'area_id':
                                // Фильтрация по area_id через отношение city.area
                                $query->whereHas('city.area', function ($q) use ($filterValue) {
                                    $q->where('id', $filterValue);
                                });
                                break;
                            case 'edge_id':
                                // Фильтрация по edge_id через отношение city.area.edge
                                $query->whereHas('city.area.edge', function ($q) use ($filterValue) {
                                    $q->where('id', $filterValue);
                                });
                                break;
                            case 'cemetery_id':
                                // Фильтрация по cemetery_id через поле cemetery_ids
                                $query->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$filterValue]);
                                break;
                             case 'user_id':

                                    if ($filterValue === 'yes') {

                                        $query->whereNotNull('user_id');
                                    } elseif ($filterValue=== 'no') {
                                        $query->whereNull('user_id');
                                    }
                                    break;
                                default:
                                    if (!empty($filterValue)) { // Проверка, что значение не пустое
                                        $query->where($filterName, $filterValue);
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

        // Получаем данные с учётом фильтров и сортировки (или всю таблицу, если фильтров нет)
        $organizations = $query->with(['city.area.edge', 'user']) // Предзагрузка отношений
            ->get()
            ->map(function ($organization) {
                return [
                    'ID' => $organization->id,
                    'Название' => $organization->title,
                    'Город' => $organization->city->title ?? 'Не указано',
                    'Край' => $organization->city->area->edge->title ?? 'Не указано',
                    'Округ' => $organization->city->area->title ?? 'Не указано',
                    'Кладбища' => $organization->cemetery_ids,
                    'Широта' => $organization->width,
                    'Долгота' => $organization->longitude,
                    'Метро' => $organization->underground,
                    'Рядом с' => $organization->next_to,
                    'Email' => $organization->email,
                    'Телефон' => $organization->phone,
                    'Адрес' => $organization->adres,
                    'Тип организации' => $organization->name_type,
                    'Slug' => $organization->slug,
                    'WhatsApp' => $organization->whatsapp,
                    'Telegram' => $organization->telegram,
                    'Краткое описание' => $organization->mini_content,
                    'Описание' => $organization->content,
                    'ID пользователя' => $organization->user_id,
                    'Дата создания' => $organization->created_at?->format('d.m.Y H:i:s'),
                    'Рейтинг' => $organization->rating,
                ];
            });

        // Если данные пустые, возвращаем сообщение
        if ($organizations->isEmpty()) {
            $organizations = Organization::query()
                ->with(['city.area.edge', 'user']) // Предзагрузка отношений
                ->orderBy('title') // Сортировка по названию
                ->get()
                ->map(function ($organization) {
                    return [
                        'ID' => $organization->id,
                        'Название' => $organization->title,
                        'Город' => $organization->city->title ?? 'Не указано',
                        'Край' => $organization->city->area->edge->title ?? 'Не указано',
                        'Округ' => $organization->city->area->title ?? 'Не указано',
                        'Кладбища' => $organization->cemetery_ids,
                        'Широта' => $organization->width,
                        'Долгота' => $organization->longitude,
                        'Метро' => $organization->underground,
                        'Рядом с' => $organization->next_to,
                        'Email' => $organization->email,
                        'Телефон' => $organization->phone,
                        'Адрес' => $organization->adres,
                        'Тип организации' => $organization->name_type,
                        'Slug' => $organization->slug,
                        'WhatsApp' => $organization->whatsapp,
                        'Telegram' => $organization->telegram,
                        'Краткое описание' => $organization->mini_content,
                        'Описание' => $organization->content,
                        'ID пользователя' => $organization->user_id,
                        'Дата создания' => $organization->created_at?->format('d.m.Y H:i:s'),
                        'Рейтинг' => $organization->rating,
                    ];
                });
        }

        // Экспорт в Excel
        return (new FastExcel($organizations))->download('organizations.xlsx');
    })
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
            ActivityCategoriesRelationManager::class,
            WorkingHoursRelationManager::class,
            OrganizationRequestCountRelationManager::class,
            ProductsRelationManager::class, // Добавляем RelationManager
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
