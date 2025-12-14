<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Filament\Resources\OrganizationResource\RelationManagers\ActivityCategoriesRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\BeatificationsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\CallStatsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\DeadAplicationsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\FuneralServicesRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\MemorialsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\OrderPorductsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\OrganizationRequestCountRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\ViewsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\WorkingHoursRelationManager;
use App\Models\CategoryProduct;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MultiSelect;
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
    protected static ?string $navigationLabel = 'Список';
    protected static ?string $navigationGroup = 'Организации';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager') {
            $userCityIds = json_decode(auth()->user()->city_ids);
            $query->whereIn('city_id', $userCityIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Radio::make('href_main_img')
                    ->label('Выберите источник изображения')
                    ->options([
                        0 => 'Файл на сайте',
                        1 => 'Ссылка (URL)'
                    ])
                    ->inline()
                    ->live(),

                TextInput::make('img_main_url')
                    ->label('Ссылка на главное изображение')
                    ->placeholder('https://example.com/image.jpg')
                    ->reactive()
                    ->required(fn($get) => intval($get('href_main_img')) === 1)
                    ->hidden(fn($get) => intval($get('href_main_img')) === 0),

                FileUpload::make('img_main_file')
                    ->label('Загрузить главное изображение')
                    ->directory('/uploads_organization')
                    ->image()
                    ->maxSize(2048)
                    ->reactive()
                    ->required(fn($get) => intval($get('href_main_img')) === 0)
                    ->hidden(fn($get) => intval($get('href_main_img')) === 1),

                View::make('image')
                    ->label('Текущий логотип')
                    ->view('filament.forms.components.custom-image-organization')
                    ->extraAttributes(['class' => 'custom-image-class'])
                    ->columnSpan('full')
                    ->hidden(fn($get) => intval($get('href_main_img')) === 0),


                Radio::make('href_img')
                    ->label('Выберите источник логотипа')
                    ->options([
                        0 => 'Файл на сайте',
                        1 => 'Ссылка (URL)'
                    ])
                    ->inline()
                    ->live(),

                TextInput::make('img_url')
                    ->label('Ссылка на логотип')
                    ->placeholder('https://example.com/image.jpg')
                    ->reactive()
                    ->required(fn($get) => intval($get('href_img')) === 1)
                    ->hidden(fn($get) => intval($get('href_img')) === 0),

                FileUpload::make('img_file')
                    ->label('Загрузить логоитип')
                    ->directory('/uploads_organization')
                    ->image()
                    ->maxSize(2048)
                    ->reactive()
                    ->required(fn($get) => intval($get('href_img')) === 0)
                    ->hidden(fn($get) => intval($get('href_img')) === 1),

                View::make('image')
                    ->label('Текущий логотип')
                    ->view('filament.forms.components.custom-image')
                    ->extraAttributes(['class' => 'custom-image-class'])
                    ->columnSpan('full')
                    ->hidden(fn($get) => intval($get('href_img')) === 0),

                TextInput::make('title')
                    ->label('Название фирмы')
                    ->required()
                    ->live(debounce: 1000)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if (!empty($state) && strlen($state) > 3) {
                            $set('slug', generateUniqueSlug($state, Organization::class, $get('id')));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->label('Slug')
                    ->unique(ignoreRecord: true)
                    ->formatStateUsing(fn($state) => slug($state))
                    ->dehydrateStateUsing(fn($state, $get) => generateUniqueSlug($state, Organization::class, $get('id'))),


                Radio::make('priority')
                    ->label('Приоритет')
                    ->options([
                        0 => 'Обычный',
                        2 => 'Средний',
                        1 => 'Высокий'
                    ])
                    ->inline(),


                TextInput::make('route')
                    ->label('Ссылка на организацию')
                    ->disabled()
                    ->suffixAction(
                        Action::make('open_page')
                            ->label('Открыть страницу')
                            ->icon('heroicon-s-map')
                            ->button()
                            ->url(fn($state, $livewire) => route('organization.single', $livewire->getRecord()->slug))
                            ->openUrlInNewTab()
                            ->visible(fn($state, $livewire) => filled($livewire->getRecord()?->slug))
                    ),

                Radio::make('status')
                    ->label('Отображение организации')
                    ->options([
                        0 => 'Не показывать',
                        1 => 'Показывать'
                    ])
                    ->inline(),

                Forms\Components\Select::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title', function ($query) {
                        $user = auth()->user();

                        if ($user->role === 'admin') {
                            return $query->orderBy('title');
                        }

                        $userCityIds = json_decode($user->city_ids ?? '[]');
                        return $query->whereIn('id', $userCityIds)->orderBy('title');
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('width')
                    ->label('Ширина')
                    ->required()
                ,

                Forms\Components\TextInput::make('longitude')
                    ->label('Долгота')
                    ->required()
                ,

                Forms\Components\TextInput::make('underground')
                    ->label('Метро')
                ,

                Forms\Components\TextInput::make('next_to')
                    ->label('Рядом с')
                ,

                Forms\Components\TextInput::make('email')
                    ->label('email')
                ,

                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                ,

                Forms\Components\TextInput::make('inn')
                    ->label('Инн')
                ,
                Forms\Components\TextInput::make('adres')
                    ->label('Адрес')
                    ->required()
                ,

                Forms\Components\TextInput::make('name_type')
                    ->label('Тип организации')
                ,

                Forms\Components\TextInput::make('whatsapp')
                    ->label('whatsapp')
                ,

                Forms\Components\TextInput::make('telegram')
                    ->label('telegram')
                ,

                Forms\Components\TextInput::make('link_website')
                    ->label('Ссылка на сайт организации')
                ,

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
                    ->required()
                    ->disableLabel(false)
                    ->placeholder('Введите HTML-контент здесь...'),

                TextInput::make('map_link')
                    ->label('Страница пользователя')
                    ->disabled()
                    ->suffixAction(
                        Action::make('open_map')
                            ->button()
                            ->label('Страница пользователя')
                            ->icon('heroicon-s-eye')
                            ->url(function ($record) {
                                return '/admin/users/' . $record->user_id . '/edit';
                            })
                            ->openUrlInNewTab()
                    )->hidden(fn(?Organization $record) => is_null($record)),

                Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn(?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),

                Forms\Components\TextInput::make('rating')
                    ->label('Рейтинг')
                    ->required()
                ,

                Select::make('cemetery_ids')
                    ->label('Кладбища, на которых работает организация')
                    ->options(function () {
                        return Cemetery::query()
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->multiple()
                    ->searchable()
                    ->required()
                    ->preload()
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return [];

                        // Если это строка (из БД)
                        if (is_string($state)) {
                            $state = trim($state, ',');
                            if (empty($state)) return [];

                            // Оставляем как строки, не преобразуем в int!
                            return collect(explode(',', $state))
                                ->map(fn($id) => trim($id))
                                ->filter(fn($id) => !empty($id) && is_numeric($id))
                                ->toArray();
                        }

                        // Если уже массив, просто возвращаем
                        return (array)$state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (empty($state)) return '';

                        // Фильтруем пустые значения, оставляем как строки
                        $state = collect($state)
                            ->filter(fn($value) => !empty($value) && is_numeric($value))
                            ->toArray();

                        if (empty($state)) return '';

                        // Преобразуем массив в строку
                        return implode(',', $state);
                    }),
                Forms\Components\Textarea::make('comment_admin')
                    ->label('Комментарий админа'),


                Forms\Components\TextInput::make('calls')
                    ->label('Лимит звонков')
                    ->maxLength(255),


                Forms\Components\TextInput::make('responsible_organization')
                    ->label('Ответственная организация'),
                Forms\Components\TextInput::make('address_responsible_person')
                    ->label('Адрес (ответственного лица)'),
                Forms\Components\TextInput::make('responsible_person_full_name')
                    ->label('Ответственное лицо (ФИО)'),
                Forms\Components\TextInput::make('okved')
                    ->label('Okved')
            ]);
    }

    public static function table(Table $table): Table
    {

        $columnsMap = [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'title' => 'Название',
            'link_website' => 'Сайт организации',
            'img_url' => 'URL картинки',
            'img_file' => 'Файл картинки',
            'img_main_file' => 'Файл главного изображения',
            'img_main_url' => 'URL главного изображения',
            'city_id' => 'Город',
            'district_id' => 'Район',
            'phone' => 'Телефон',
            'adres' => 'Адрес',
            'next_to' => 'Рядом с',
            'nearby' => 'Рядом с',
            'time_start_work' => 'Начало работы',
            'time_end_work' => 'Конец работы',
            'mini_content' => 'Краткое описание',
            'content' => 'Описание',
            'name_type' => 'Тип организации',
            'width' => 'Широта',
            'longitude' => 'Долгота',
            'available_installments' => 'Доступна рассрочка',
            'found_cheaper' => 'Нашли дешевле',
            'conclusion_contract' => 'Заключение договора',
            'state_compensation' => 'Государственная компенсация',
            'status' => 'Статус',
            'underground' => 'Метро',
            'rating' => 'Рейтинг',
            'cemetery_ids' => 'Кладбища, на которых работает организация',
            'awards' => 'Награды',
            'price_list' => 'Прайс-лист',
            'priority' => 'Приоритет',
            'slug' => 'Слаг',
            'href_img' => 'Ссылка на изображение',
            'href_main_img' => 'Ссылка на главное изображение',
            'whatsapp' => 'WhatsApp',
            'telegram' => 'Telegram',
            'email' => 'Email',
            'village' => 'Село/Поселок',
            'two_gis_link' => '2GIS ссылка',
            'time_difference' => 'Часовой пояс',
            'comment_admin' => 'Комментарий администратора',
            'calls' => 'Звонки',
            'inn' => 'ИНН',
            'responsible_organization' => 'Ответственная организация',
            'address_responsible_person' => 'Адрес (ответственного лица)',
            'responsible_person_full_name' => 'Ответственное лицо (ФИО)',
            'okved' => 'ОКВЭД',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];

        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager') {
                    $userCityIds = json_decode(auth()->user()->city_ids);
                    $query->whereIn('city_id', $userCityIds);
                }
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.area.edge.title')
                    ->label('Край')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.area.title')
                    ->label('Округ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.title')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->url(function ($record) {
                        return $record->phone ? 'tel:' . preg_replace('/[^0-9+]/', '', $record->phone) : null;
                    })
                    ->icon('heroicon-o-phone')
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('main_category_id')
                    ->label('Основная категория')
                    ->options(CategoryProduct::whereNull('parent_id')->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $state) {
                        if (!empty($state['value'])) {
                            $query->whereHas('activityCategories', function ($q) use ($state) {
                                $q->where('category_main_id', $state['value']);
                            });
                        }
                    }),

                SelectFilter::make('subcategory_id')
                    ->label('Подкатегория')
                    ->options(CategoryProduct::whereNotNull('parent_id')->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $state) {
                        if (!empty($state['value'])) {
                            $query->whereHas('activityCategories', function ($q) use ($state) {
                                $q->where('category_children_id', $state['value']);
                            });
                        }
                    }),

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
                    ->relationship('city', 'title') // предполагается, что есть связанная модель 'city'
                    ->searchable(),


                SelectFilter::make('cemetery_id')
                    ->label('Работает на кладбище')
                    ->options(Cemetery::pluck('title', 'id'))
                    ->searchable()
                    ->query(function ($query, $state) {
                        if ($state['value'] != null) {
                            $query->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$state]);
                        }
                    }),

                SelectFilter::make('user_id')
                    ->label('Получен доступ')
                    ->options([
                        'yes' => 'Да',
                        'no'  => 'Нет',
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('export_organizations')
                    ->label('Экспорт в Excel')
                    ->action(function ($livewire, array $data) use ($columnsMap) {
                        $fileName = 'organizations_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

                        $query = $livewire->getFilteredTableQuery()->with(['city']);

                        $columns = $data['columns'] ?: array_keys($columnsMap);

                        $collection = $query->get()->map(function ($item) use ($columns, $columnsMap) {
                            return collect($columns)->mapWithKeys(function ($col) use ($item, $columnsMap) {
                                $value = $item->{$col};

                                if ($col === 'id') {
                                    $value = (string) $value;
                                }

                                if ($col === 'city_id') {
                                    $value = optional($item->city)->title;
                                }

                                if ($col === 'district_id') {
                                    $value = optional($item->district)->title;
                                }

                                if ($col === 'user_id') {
                                    $value = optional($item->user)->name;
                                }

                                if ($col === 'status') {
                                    $value = match ($value) {
                                        1 => 'Показывать',
                                        default => 'Не показывать',
                                    };
                                }

                                if ($col === 'cemetery_ids' && $value) {
                                    $ids = explode(',', $value);
                                    $cemeteries = Cemetery::whereIn('id', $ids)->pluck('title')->toArray();
                                    $value = implode(', ', $cemeteries);
                                }

                                if ($col === 'priority') {
                                    $value = match ($value) {
                                        0 => 'Обычный',
                                        1 => 'Высокий',
                                        2 => 'Средний',
                                        default => 'Не задан',
                                    };
                                }

                                if ($value instanceof \Illuminate\Support\Carbon) {
                                    $value = $value->format('d.m.Y H:i:s');
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
            CallStatsRelationManager::class,
            ImagesRelationManager::class,
            ActivityCategoriesRelationManager::class,
            WorkingHoursRelationManager::class,
            OrganizationRequestCountRelationManager::class,
            ProductsRelationManager::class,
            ViewsRelationManager::class,
            BeatificationsRelationManager::class,
            DeadAplicationsRelationManager::class,
            FuneralServicesRelationManager::class,
            MemorialsRelationManager::class,
            OrderPorductsRelationManager::class

        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit'   => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin' ||
            ((auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager') && !empty(json_decode(auth()->user()->city_ids)));
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }

        if (auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager') {
            $userCityIds = json_decode(auth()->user()->city_ids);
            return in_array($record->city_id, $userCityIds);
        }

        return false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'admin' ||
            ((auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager') && !empty(json_decode(auth()->user()->city_ids)));
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }
}
