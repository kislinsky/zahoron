<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Cemetery;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Organization;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\MultiSelectFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Filament\Resources\OrganizationResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\WorkingHoursRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\ActivityCategoriesRelationManager;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Организации'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
        ->schema([

            FileUpload::make('logo')
            ->label('Картинка')
            ->directory('/uploads_organization')
            ->image()
            ->maxSize(2048)

            ->afterStateUpdated(function ($set, $state, $record) {
                if ($state && $record) {
                    // Обновляем запись в базе данных, сохраняя путь к файлу
                    $record->update([
                        'href_img' => 0, // Сохраняем путь к файлу
                    ]);
                }
            }),

            View::make('image')
            ->label('Текущее изображение')
            ->view('filament.forms.components.custom-image-organization') // Указываем путь к Blade-шаблону
            ->extraAttributes(['class' => 'custom-image-class'])
            ->columnSpan('full')->hidden(fn (?Organization $record) => is_null($record)),

            Forms\Components\TextInput::make('title')
                ->label('Название')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('city_id')
                ->label('Город')
                ->relationship('city', 'title')
                ->required()
                ->searchable()
                ->preload(),

                Select::make('cemetery_ids')
                    ->label('Кладбища на которых работает организация')
                    ->options(Cemetery::pluck('title', 'id')->toArray())
                    ->multiple()
                    ->searchable()
                    ->required()
                    ->default(function ($get) {
                        $cemeteryIds = $get('cemetery_ids') ?? '';

                        return array_filter(explode(',', rtrim($cemeteryIds, ',')));
                    })
                    ->afterStateUpdated(function ($state, $set) {
                        $set('cemetery_ids', implode(',', (array) $state));
                    }),

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

            Forms\Components\TextInput::make('village')
                ->label('Деревня')
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

            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true) // Игнорировать текущую запись при редактировании
                ->label('Slug')
                ->maxLength(255),


            Forms\Components\TextInput::make('whatsapp')
                ->label('whatsapp')
                ->maxLength(255),

            Forms\Components\TextInput::make('telegram')
                ->label('telegram')
                ->maxLength(255),

            Forms\Components\TextInput::make('applications_funeral_services')
                ->label('Количество заявок на ритуальные услуги')
                ->maxLength(255),
            Forms\Components\TextInput::make('aplications_memorial')
                ->label('Количество заявок на поминки')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('calls_organization')
                ->label('Количество заявок на звонки')
                ->maxLength(255),
            Forms\Components\TextInput::make('product_requests_from_marketplace')
                ->label('Количество заявок на заказы из маркетплэйса')
                ->numeric() // Разрешить только числовые значения
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('applications_improvemen_graves')
                ->label('Количество заявок на облогораживание')
                ->numeric() // Разрешить только числовые значения
                ->required()
                ->maxLength(255),
                
           
    
            RichEditor::make('mini_content') // Поле для редактирования HTML-контента
                ->label('Краткое описание') // Соответствующая подпись
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

                
                Forms\Components\Select::make('user_id')
                ->label('id пользователя')
                ->relationship('user', 'id')
                ->searchable()
                ->preload(),

                Placeholder::make('created_at')
                ->label('Дата создания')
                ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
            
                Placeholder::make('rating')
                ->label('Рейтинг')
                ->content(fn ($state) => $state),
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
