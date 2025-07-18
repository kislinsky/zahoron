<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Filament\Resources\OrganizationResource\RelationManagers\ActivityCategoriesRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\BeatificationsRelationManager;
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
        
        if (auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager' ) {
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
            ->required(fn ($get) => intval($get('href_main_img')) === 1)
            ->hidden(fn ($get) => intval($get('href_main_img')) === 0),

        FileUpload::make('img_main_file')
            ->label('Загрузить главное изображение')
            ->directory('/uploads_organization')
            ->image()
            ->maxSize(2048)
            ->reactive()
            ->required(fn ($get) => intval($get('href_main_img')) === 0)
            ->hidden(fn ($get) => intval($get('href_main_img')) === 1),

            View::make('image')
            ->label('Текущий логотип')
            ->view('filament.forms.components.custom-image-organization')
            ->extraAttributes(['class' => 'custom-image-class'])
            ->columnSpan('full')
            ->hidden(fn ($get) => intval($get('href_main_img')) === 0), 
           

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
            ->required(fn ($get) => intval($get('href_img')) === 1)
            ->hidden(fn ($get) => intval($get('href_img')) === 0),

        FileUpload::make('img_file')
            ->label('Загрузить логоитип')
            ->directory('/uploads_organization')
            ->image()
            ->maxSize(2048)
            ->reactive()
            ->required(fn ($get) => intval($get('href_img')) === 0)
            ->hidden(fn ($get) => intval($get('href_img')) === 1),

        View::make('image')
            ->label('Текущий логотип')
            ->view('filament.forms.components.custom-image')
            ->extraAttributes(['class' => 'custom-image-class'])
            ->columnSpan('full')
            ->hidden(fn ($get) => intval($get('href_img')) === 0), 

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
            ->formatStateUsing(fn ($state) => slug($state))
            ->dehydrateStateUsing(fn ($state, $get) => generateUniqueSlug($state, Organization::class, $get('id'))),


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
            ->url(fn ($state, $livewire) => route('organization.single', $livewire->getRecord()->slug))
            ->openUrlInNewTab()
            ->visible(fn ($state, $livewire) => filled($livewire->getRecord()?->slug))
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
                            return '/'.selectCity()->slug.'/admin/users/'.$record->user_id.'/edit';
                        })
                        ->openUrlInNewTab()
                    )->hidden(fn (?Organization $record) => is_null($record)),

                Placeholder::make('created_at')
                ->label('Дата создания')
                ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
            
                Forms\Components\TextInput::make('rating')
                ->label('Рейтинг')
                ->required()
                ,

                Select::make('cemetery_ids')
                ->label('Кладбища, на которых работает организация')
                ->options(fn ($get) => getCemeteriesOptions($get))
                ->multiple()
                ->searchable()
                ->required()
                ->formatStateUsing(function ($state) {
                    return $state ? array_map('intval', explode(',', trim($state, ','))) : [];
                })
                ->preload()
                ->dehydrateStateUsing(fn ($state) => implode(',', (array) $state).','),

                Forms\Components\Textarea::make('comment_admin')
                ->label('Комментарий админа'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->role === 'deputy-admin'|| auth()->user()->role === 'manager') {
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
                    return $record->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $record->phone) : null;
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
                            $query->whereHas('activityCategories', function($q) use ($state) {
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
                            $query->whereHas('activityCategories', function($q) use ($state) {
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('export')
                    ->label('Экспорт в Excel')
                    ->action(function (HasTable $livewire) {
                        $query = Organization::query();
                
                        if (auth()->user()->role === 'deputy-admin'|| auth()->user()->role === 'manager') {
                            $userCityIds = json_decode(auth()->user()->city_ids);
                            $query->whereIn('city_id', $userCityIds);
                        }
                
                        if (property_exists($livewire, 'tableFilters') && !empty($livewire->tableFilters)) {
                            foreach ($livewire->tableFilters as $filterName => $filterValue) {
                                if (!empty($filterValue['value'])) {
                                    $filterValue = $filterValue['value'];
                
                                    switch ($filterName) {
                                        case 'city_id':
                                            $query->whereHas('city', function ($q) use ($filterValue) {
                                                $q->where('id', $filterValue);
                                            });
                                            break;
                                        case 'area_id':
                                            $query->whereHas('city.area', function ($q) use ($filterValue) {
                                                $q->where('id', $filterValue);
                                            });
                                            break;
                                        case 'edge_id':
                                            $query->whereHas('city.area.edge', function ($q) use ($filterValue) {
                                                $q->where('id', $filterValue);
                                            });
                                            break;
                                        case 'cemetery_id':
                                            $query->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$filterValue]);
                                            break;
                                        case 'user_id':
                                            if ($filterValue === 'yes') {
                                                $query->whereNotNull('user_id');
                                            } elseif ($filterValue === 'no') {
                                                $query->whereNull('user_id');
                                            }
                                            break;
                                        default:
                                            if (!empty($filterValue)) {
                                                $query->where($filterName, $filterValue);
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                
                        if (property_exists($livewire, 'tableSortColumn') && $livewire->tableSortColumn) {
                            $query->orderBy($livewire->tableSortColumn, $livewire->tableSortDirection ?? 'asc');
                        }
                
                        $organizations = $query->with(['city.area.edge', 'user'])
                            ->get()
                            ->map(function ($organization) {
                                return [
                                    'ID' => (string)$organization->id,
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
                
                        if ($organizations->isEmpty()) {
                            $organizations = Organization::query()
                                ->when(auth()->user()->role === 'deputy-admin' ?? auth()->user()->role === 'manager', function ($query) {
                                    $userCityIds = json_decode(auth()->user()->city_ids);
                                    $query->whereIn('city_id', $userCityIds);
                                })
                                ->with(['city.area.edge', 'user'])
                                ->orderBy('title')
                                ->get()
                                ->map(function ($organization) {
                                    return [
                                        'ID' => (string)$organization->id,
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
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