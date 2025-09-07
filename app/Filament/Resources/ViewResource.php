<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViewResource\Pages;
use App\Filament\Resources\ViewResource\Widgets\ViewStatsOverview;
use App\Filament\Resources\ViewResource\Widgets\ViewsPerDayChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsByEntityTypeChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsBySourceChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsByDeviceChart;
use App\Filament\Resources\ViewResource\Widgets\ViewsByLocationChart;
use App\Filament\Resources\ViewResource\Widgets\TopViewedEntities;
use App\Models\View;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ViewResource extends Resource
{
    protected static ?string $model = View::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationGroup = 'Статистика';
    protected static ?string $navigationLabel = 'Просмотры';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('entity_type')
                    ->label('Тип объекта')
                    ->options([
                        'cemetery' => 'Кладбище',
                        'mortuary' => 'Морг',
                        'organization' => 'Организация',
                        'page' => 'Страница',
                    ])
                    ->required(),

                TextInput::make('entity_id')
                    ->label('ID объекта')
                    ->required()
                    ->numeric(),
                
                TextInput::make('user_id')
                    ->label('ID пользователя')
                    ->numeric()
                    ->nullable(),
                
                TextInput::make('session_id')
                    ->label('ID сессии')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('source')
                    ->label('Источник')
                    ->options([
                        'direct' => 'Прямой переход',
                        'search' => 'Поиск',
                        'social' => 'Социальные сети',
                        'email' => 'Email',
                        'referral' => 'Реферальный',
                    ])
                    ->required(),
                
                TextInput::make('ip_address')
                    ->label('IP адрес')
                    ->required()
                    ->maxLength(45),
                    
                Forms\Components\Select::make('device')
                    ->label('Устройство')
                    ->options([
                        'desktop' => 'Компьютер',
                        'tablet' => 'Планшет',
                        'mobile' => 'Мобильный',
                    ])
                    ->required(),

                TextInput::make('location')
                    ->label('Местоположение')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Дата просмотра')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('views')
            ->columns([
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Тип объекта')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cemetery' => 'success',
                        'mortuary' => 'warning',
                        'organization' => 'info',
                        'page' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cemetery' => 'Кладбище',
                        'mortuary' => 'Морг',
                        'organization' => 'Организация',
                        'page' => 'Страница',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID объекта')
                    ->searchable(),

                Tables\Columns\TextColumn::make('session_id')
                    ->label('ID сессии'),


                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP адрес'),

    

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата просмотра')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Фильтр по типу объекта
                SelectFilter::make('entity_type')
                    ->label('Тип объекта')
                    ->options([
                        'cemetery' => 'Кладбища',
                        'mortuary' => 'Морги',
                        'organization' => 'Организации',
                        'page' => 'Страницы',
                    ])
                    ->multiple(),

                // Фильтр по источнику
                SelectFilter::make('source')
                    ->label('Источник')
                    ->options([
                        'direct' => 'Прямой переход',
                        'search' => 'Поиск',
                        'social' => 'Социальные сети',
                        'email' => 'Email',
                        'referral' => 'Реферальный',
                    ])
                    ->multiple(),

                // Фильтр по устройству
                SelectFilter::make('device')
                    ->label('Устройство')
                    ->options([
                        'desktop' => 'Компьютер',
                        'tablet' => 'Планшет',
                        'mobile' => 'Мобильный',
                    ])
                    ->multiple(),

                // Фильтр по местоположению
                SelectFilter::make('location')
                    ->label('Местоположение')
                    ->options(function () {
                        return View::whereNotNull('location')
                            ->distinct('location')
                            ->pluck('location', 'location')
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple(),

                // Фильтр по дате
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Дата от'),
                        Forms\Components\DatePicker::make('created_until')->label('Дата до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Период просмотров'),

                // Фильтр по пользователям/сессиям
                Filter::make('has_user')
                    ->label('Тип посетителя')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->options([
                                'user' => 'Авторизованные пользователи',
                                'session' => 'Анонимные посетители',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['type'] === 'user',
                                fn (Builder $query): Builder => $query->whereNotNull('user_id'),
                            )
                            ->when(
                                $data['type'] === 'session',
                                fn (Builder $query): Builder => $query->whereNull('user_id'),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListViewStats::route('/'),
            'create' => Pages\CreateView::route('/create'),
            'edit' => Pages\EditView::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ViewStatsOverview::class,
            ViewsPerDayChart::class,
            ViewsByEntityTypeChart::class,
            ViewsByDeviceChart::class,
            TopViewedEntities::class,
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || auth()->user()->role === 'deputy-admin';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}