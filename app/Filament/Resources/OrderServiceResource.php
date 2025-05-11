<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderServiceResource\Pages;
use App\Filament\Resources\OrderServiceResource\RelationManagers;
use App\Models\OrderService;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->required(),
                    
                Select::make('services_id')
                    ->label('Услуги')
                    ->relationship('services', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->options(\App\Models\Service::pluck('title', 'id'))
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Service $service) => $service->title)
                    ->afterStateHydrated(function (Select $component, $state) {
                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }
                        $component->state($state);
                    })
                    ->dehydrateStateUsing(fn ($state) => json_encode($state)),

                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // Изменено на отображение имени
                    ->required()
                    ->disabled($isRestrictedUser),

                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    ])
                    ->required()
                    ->default(0),

                Forms\Components\TextInput::make('size')
                    ->label('Размер')
                    ->required(),

                Forms\Components\TextInput::make('date_pay')
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
                    ->relationship('worker', 'id') // Изменено на отображение имени
                    ->nullable(),

                Forms\Components\Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->preload()
                    ->options(function() use ($userCityIds, $isRestrictedUser) {
                        if ($isRestrictedUser) {
                            return \App\Models\Cemetery::whereIn('city_id', $userCityIds)
                                ->pluck('title', 'id');
                        }
                        return \App\Models\Cemetery::pluck('title', 'id');
                    })
                    ->nullable()
                    ->disabled($isRestrictedUser),

                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required(),

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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('burial.id')
                    ->label('ID захоронения')
                    ->sortable(),

                TextColumn::make('user.id')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    })
                    ->sortable(),

                TextColumn::make('date_pay')
                    ->label('Дата оплаты')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Цена')
                    ->sortable(),

                TextColumn::make('paid')
                    ->label('Оплачено')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    })
                    ->sortable(),

                TextColumn::make('cemetery.title')
                    ->label('Кладбище')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cemetery.city.title')
                    ->label('Город')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    ])
                    ->default(0)
                    ->attribute('status'),

                SelectFilter::make('city')
                    ->label('Город')
                    ->relationship('cemetery.city', 'title')
                    ->searchable()
                    ->preload()
                    ->hidden(static::isRestrictedUser()),

                SelectFilter::make('cemetery')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('worker')
                    ->label('Исполнитель')
                    ->relationship('worker', 'id')
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
        return [
            //
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