<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderServiceResource\Pages;
use App\Models\OrderService;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderServiceResource extends Resource
{
    protected static ?string $model = OrderService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Заказы услуг';
    protected static ?string $navigationGroup = 'Заказы';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (static::isRestrictedUser()) {
            $cityIds = static::getUserCityIds();
            
            if (!empty($cityIds)) {
                $query->whereHas('cemetery', function($q) use ($cityIds) {
                    $q->whereIn('city_id', $cityIds);
                });
            } else {
                $query->whereNull('cemetery_id');
            }
        }
        
        return $query;
    }

    protected static function isRestrictedUser(): bool
    {
        return in_array(auth()->user()->role, ['deputy-admin', 'manager']);
    }

    protected static function getUserCityIds(): array
    {
        $user = auth()->user();
        $cityIds = [];
        
        if (!empty($user->city_ids)) {
            $decoded = json_decode($user->city_ids, true);
            
            if (is_array($decoded)) {
                $cityIds = $decoded;
            } else {
                $cityIds = array_filter(explode(',', trim($user->city_ids, '[],"')));
            }
            
            $cityIds = array_map('intval', array_filter($cityIds));
        }
        
        return $cityIds;
    }

    public static function form(Form $form): Form
    {
        $isRestrictedUser = static::isRestrictedUser();
        $userCityIds = $isRestrictedUser ? static::getUserCityIds() : [];
        
        return $form
            ->schema([
                Forms\Components\Select::make('burial_id')
                    ->label('Захоронение')
                    ->relationship('burial', 'id')
                    ->searchable()
                    ->required(),
                    
                // Заменяем relationship на обычный Select с кастомной логикой
                Forms\Components\Select::make('services_id')
                    ->label('Услуги')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->options(Service::pluck('title', 'id'))
                    ->getOptionLabelsUsing(function ($values) {
                        if (empty($values)) return [];
                        
                        return Service::whereIn('id', $values)->pluck('title', 'id');
                    })
                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                        if (is_string($state)) {
                            $state = json_decode($state, true) ?? [];
                        }
                        $component->state($state);
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return json_encode($state ?? []);
                    }),

                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->disabled($isRestrictedUser),

                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Не принят',
                        3 => 'Принят',
                        4 => 'На проверке',
                        5 => 'Выполнен',
                    ])
                    ->required()
                    ->default(0),

                Forms\Components\TextInput::make('size')
                    ->label('Размер')
                    ->required(),

                Forms\Components\DatePicker::make('date_pay')
                    ->label('Дата оплаты')
                    ->nullable(),

                Forms\Components\Textarea::make('imgs')
                    ->label('Изображения')
                    ->nullable(),

                Forms\Components\Textarea::make('customer_comment')
                    ->label('Комментарий клиента')
                    ->nullable(),

                Forms\Components\Select::make('worker_id')
                    ->label('Работник')
                    ->relationship('worker', 'name')
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->preload()
                    ->options(function() use ($userCityIds, $isRestrictedUser) {
                        $query = \App\Models\Cemetery::query();
                        
                        if ($isRestrictedUser && !empty($userCityIds)) {
                            $query->whereIn('city_id', $userCityIds);
                        }
                        
                        return $query->pluck('title', 'id');
                    })
                    ->nullable()
                    ->disabled($isRestrictedUser),

                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('paid')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('burial.id')
                    ->label('ID захоронения')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не принят',
                        1 => 'Принят',
                        2 => 'Выполнен',
                        default => 'Неизвестно',
                    })
                    ->sortable(),

                // Колонка для отображения услуг
                Tables\Columns\TextColumn::make('services_id')
                    ->label('Услуги')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return 'Нет услуг';
                        
                        $serviceIds = is_string($state) ? json_decode($state, true) : $state;
                        if (empty($serviceIds)) return 'Нет услуг';
                        
                        $services = Service::whereIn('id', $serviceIds)->pluck('title')->toArray();
                        return implode(', ', $services);
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_pay')
                    ->label('Дата оплаты')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid')
                    ->label('Оплачено')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                        default => 'Неизвестно',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('cemetery.title')
                    ->label('Кладбище')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cemetery.city.title')
                    ->label('Город')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Не принят',
                        1 => 'Принят',
                        2 => 'Выполнен',
                    ]),

                Tables\Filters\SelectFilter::make('city')
                    ->label('Город')
                    ->relationship('cemetery.city', 'title')
                    ->searchable()
                    ->hidden(static::isRestrictedUser()),

                Tables\Filters\SelectFilter::make('cemetery')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('worker')
                    ->label('Исполнитель')
                    ->relationship('worker', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderServices::route('/'),
            'create' => Pages\CreateOrderService::route('/create'),
            'edit' => Pages\EditOrderService::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || 
               in_array(auth()->user()->role, ['deputy-admin', 'manager']);
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }
        
        if (static::isRestrictedUser()) {
            $userCityIds = static::getUserCityIds();
            return $record->cemetery && in_array($record->cemetery->city_id, $userCityIds);
        }
        
        return false;
    }

    public static function canCreate(): bool
    {   
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }
}