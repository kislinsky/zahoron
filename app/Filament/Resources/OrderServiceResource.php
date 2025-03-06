<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderServiceResource\Pages;
use App\Filament\Resources\OrderServiceResource\RelationManagers;
use App\Models\OrderService;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderServiceResource extends Resource
{
    protected static ?string $model = OrderService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Заказы услуг'; // Название в меню
    protected static ?string $navigationGroup = 'Заказы'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Захоронение
                Forms\Components\Select::make('burial_id')
                    ->label('Захоронение')
                    ->relationship('burial', 'id') // предполагается, что у вас есть модель Burial
                    ->required(),
                    
                    Select::make('services_id')
                    ->label('Услуги')
                    ->relationship('services', 'title') // предполагается, что у вас есть модель Service
                    ->multiple() // Множественный выбор
                    ->preload() // Предзагрузка данных
                    ->searchable() // Поиск услуг
                    ->required()
                    ->options(\App\Models\Service::pluck('title', 'id')) // Загрузка всех услуг
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Service $service) => $service->title) // Отображение названия услуги
                    ->afterStateHydrated(function (Select $component, $state) {
                        // Преобразуем JSON в массив для отображения выбранных услуг
                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }
                        $component->state($state);
                    })
                    ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                // Пользователь
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // предполагается, что у вас есть модель User
                    ->required(),

                // Услуги
            

                // Статус
                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    ])
                    ->required()
                    ->default(0),

                // Размер
                Forms\Components\TextInput::make('size')
                    ->label('Размер')
                    ->required(),

                // Дата оплаты
                Forms\Components\TextInput::make('date_pay')
                    ->label('Дата оплаты')
                    ->nullable(),

                // Изображения
                Forms\Components\Textarea::make('imgs')
                    ->label('Изображения')
                    ->nullable(),

                // Комментарий клиента
                Forms\Components\Textarea::make('customer_comment')
                    ->label('Комментарий клиента')
                    ->nullable(),

                // Работник
                Forms\Components\Select::make('worker_id')
                    ->label('Работник')
                    ->relationship('worker', 'id') // предполагается, что у вас есть модель User для работников
                    ->nullable(),

                // Кладбище
                Forms\Components\Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title') // предполагается, что у вас есть модель Cemetery
                    ->nullable(),

                // Цена
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required(),

                // Оплачено
                Select::make('paid')
                ->label('Оплачено')
                ->options([
                    0 => 'Не оплачен',
                    1 => 'Оплачен',
                ])
                ->required()
                ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ID
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Захоронение
                Tables\Columns\TextColumn::make('burial.id')
                    ->label('Захоронение')
                    ->sortable(),

                // Пользователь
                Tables\Columns\TextColumn::make('user.id')
                    ->label('Пользователь')
                    ->sortable(),

               
                // Статус
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    }),

             

                // Дата оплаты
                Tables\Columns\TextColumn::make('date_pay')
                    ->label('Дата оплаты'),

                // Цена
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->sortable(),

     
                TextColumn::make('paid')
                    ->label('Оплачено')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    }),
                // Дата создания
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y'),
            ])
            
            ->filters([
                SelectFilter::make('status')
                ->label('Статус')
                ->options([
                    0 => 'Новый',
                    3 => 'В работе',
                    4 => 'На проверке',
                    5 => 'Завершён',
                ])
                ->default(0) // Значение по умолчанию
                ->attribute('status'), // Поле для фильтрации

            // Фильтр по городу
            SelectFilter::make('city')
                ->label('Город')
                ->relationship('cemetery.city', 'title') // Предполагается, что burial связан с city
                ->searchable() // Поиск по городам
                ->preload(), // Предзагрузка данных

            // Фильтр по кладбищу
            SelectFilter::make('cemetery')
                ->label('Кладбище')
                ->relationship('cemetery', 'title') // Предполагается, что cemetery связан с OrderService
                ->searchable() // Поиск по кладбищам
                ->preload(), // Предзагрузка данных

            // Фильтр по исполнителю
            SelectFilter::make('worker')
                ->label('Исполнитель')
                ->relationship('worker', 'id') // Предполагается, что worker связан с User
                ->searchable() // Поиск по исполнителям
                ->preload(), // Предзагрузка дан
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderServices::route('/'),
            'create' => Pages\CreateOrderService::route('/create'),
            'edit' => Pages\EditOrderService::route('/{record}/edit'),
        ];
    }
}
