<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\OrganizationsRelationManager;
use App\Models\City;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Пользователи'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\TextInput::make('email')
                ->label('email')
                ->maxLength(255),
                
                TextInput::make('password')
                ->label('Пароль')
                ->required(fn ($context) => $context !== 'edit')
                ->password()
                ->maxLength(255)
                ->dehydrated(fn ($state) => filled($state))
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->nullable(fn ($context) => $context === 'edit'),
                
                Select::make('status') // Поле для статуса
                ->label('Статус') // Название поля
                ->options([
                    0 => 'Заблокирован',
                    1 => 'Разблокирован',
                ])
                ->required() // Поле обязательно для заполнения
                ->default(1), // Значение по умолчанию


             Select::make('city_ids')
    ->label('Города, к которым привязан пользователь (менеджер, зам. админ)')
    ->multiple()
    ->searchable(['async' => true])
    ->getSearchResultsUsing(function (string $search) {
        return City::query()
            ->where('title', 'like', "%{$search}%")
            ->limit(50)
            ->pluck('title', 'id')
            ->toArray(); // max 50 results for performance
    })
    ->getOptionLabelsUsing(function (array $values) {
        return City::whereIn('id', $values)
            ->pluck('title', 'id')
            ->toArray();
    })
    ->default(fn ($record) => (array) json_decode($record?->city_ids, true) ?? [])
    ->dehydrateStateUsing(fn ($state) => json_encode($state ?: []))
    ->afterStateHydrated(function (Select $component, $state) {
        $decoded = is_string($state) ? json_decode($state, true) : $state;
        $component->state((array) $decoded);
    })
    ->columnSpanFull(),

                Forms\Components\TextInput::make('phone')
                ->label('Телефон')
                ->required()
                ->maxLength(255),
                Select::make('role')
                ->label('Роль')
                ->options([
                    'admin' => 'Админ', 
                    'decoder' => 'Расшифровщик', 
                    'organization' => 'Организация', 
                    'organization-provider' => 'Организация-поставщик', 
                    'user' => 'Пользователь', 
                    'agent' => 'Работник', 
                    'deputy-admin' => 'Зам. админ', 
                    'manager' => 'Менеджер', 
                    'seo-specialist' => 'Seo-специалист', 
            ])->default('user'),
                Forms\Components\TextInput::make('name')
                ->label('Имя')
                ->maxLength(255),
                
                Forms\Components\TextInput::make('surname')
                ->label('Фамилия')
                ->maxLength(255),

                Forms\Components\TextInput::make('patronymic')
                ->label('Отчество')
                ->maxLength(255),

                Forms\Components\TextInput::make('adres')
                ->label('Адрес')
                ->maxLength(255),
                
                Forms\Components\TextInput::make('whatsapp')
                ->label('whatsapp')
                ->maxLength(255),

                Forms\Components\TextInput::make('telegram')
                ->label('telegram')
                ->maxLength(255),


                Forms\Components\TextInput::make('inn')
                ->label('ИНН')
                ->maxLength(255),

                
                Forms\Components\TextInput::make('number_cart')
                ->label('Номер карты')
                ->maxLength(255),

                Forms\Components\TextInput::make('bank')
                ->label('Банк')
                ->maxLength(255),

                Forms\Components\TextInput::make('in_face')
                ->label('В лице')
                ->maxLength(255),

                Forms\Components\TextInput::make('regulation')
                ->label('Доверенность')
                ->maxLength(255),



                Forms\Components\TextInput::make('ogrn')
                ->label('ОГРН')
                ->maxLength(255),

                Forms\Components\Select::make('edge_id')
                ->label('Край')
                ->relationship('edge', 'title')
                ->searchable()
                ->preload(),
                
                Forms\Components\Select::make('city_id')
                ->label('Город')
                ->relationship('city', 'title')
                ->searchable(),

            
                Select::make('sms_notifications') // Поле для статуса
                    ->label('Смс оповещения') // Название поля
                    ->options([
                        0 => 'нет', 
                        1 => 'да', 
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение

                    Select::make('email_notifications') // Поле для статуса
                    ->label('email оповещения') // Название поля
                    ->options([
                        0 => 'нет', 
                        1 => 'да', 
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение

                    Select::make('theme') // Поле для статуса
                    ->label('Тема') // Название поля
                    ->options([
                        'light' => 'Светлая', 
                        'black' => 'Темная', 
                    ])
                    ->default(1), // Значение
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
                Tables\Columns\TextColumn::make('email')
                    ->label('email')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('phone')
                    ->label('phone')
                    ->searchable()
                    ->sortable(),

                    TextColumn::make('role')
                    ->label('Роль')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Админ', 
                        'decoder' => 'Расшифровщик', 
                        'organization' => 'Организация', 
                        'organization-provider' => 'Организация-поставщик', 
                        'user' => 'Пользователь', 
                        'agent' => 'Работник', 
                        'deputy-admin' => 'Зам. админ', 
                        'manager' => 'Менеджер', 
                        'seo-specialist' => 'Seo-специалист', 
                        default => 'Неизвестно',
                    }),
            ])
            ->filters([
                SelectFilter::make('role')
                ->label('Роль')
                ->options([
                    'admin' => 'Админ', 
                        'decoder' => 'Расшифровщик', 
                        'organization' => 'Организация', 
                        'organization-provider' => 'Организация-поставщик', 
                        'user' => 'Пользователь', 
                        'agent' => 'Работник', 
                        'deputy-admin' => 'Зам. админ', 
                        'manager' => 'Менеджер', 
                        'seo-specialist' => 'Seo-специалист', 
                ]),
                SelectFilter::make('city_id')
                ->label('Город')
                ->relationship('city', 'title') // Используем вложенное отношение
                ->searchable(),

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
            OrganizationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
