<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

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
                
                Forms\Components\TextInput::make('password')
                ->label('Пароль')
                ->maxLength(255)
                ->required(),

                Forms\Components\TextInput::make('phone')
                ->label('Телефон')
                ->required()
                ->maxLength(255),
                Select::make('role')
                ->label('Роль')
                ->options([
                    'admin' => 'Админ', // Значение 1 с названием "Раз"
                    'decoder' => 'Расшифровщик', // Значение 1 с названием "Раз"
                    'organization' => 'Организация', // Значение 1 с названием "Раз"
                    'organization-provider' => 'Организация-поставщик', // Значение 1 с названием "Раз"
                    'user' => 'Пользователь', // Значение 1 с названием "Раз"
                    'agent' => 'Работник', // Значение 1 с названием "Раз"
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
                ->searchable()
                ->preload(),

            
                Select::make('sms_notifications') // Поле для статуса
                    ->label('Смс оповещения') // Название поля
                    ->options([
                        0 => 'нет', // Значение 1 с названием "Раз"
                        1 => 'да', // Значение 1 с названием "Раз"
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение

                    Select::make('email_notifications') // Поле для статуса
                    ->label('email оповещения') // Название поля
                    ->options([
                        0 => 'нет', // Значение 1 с названием "Раз"
                        1 => 'да', // Значение 1 с названием "Раз"
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение

                    Select::make('theme') // Поле для статуса
                    ->label('Тема') // Название поля
                    ->options([
                        'light' => 'Светлая', // Значение 1 с названием "Раз"
                        'black' => 'Темная', // Значение 1 с названием "Раз"
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
                        'admin' => 'Админ', // Значение 1 с названием "Раз"
                        'decoder' => 'Расшифровщик', // Значение 1 с названием "Раз"
                        'organization' => 'Организация', // Значение 1 с названием "Раз"
                        'organization-provider' => 'Организация-поставщик', // Значение 1 с названием "Раз"
                        'user' => 'Пользователь', // Значение 1 с названием "Раз"
                        'agent' => 'Работник', // Значение 1 с названием "Раз"
                        default => 'Неизвестно',
                    }),
            ])
            ->filters([
                //
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
            //
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
}
