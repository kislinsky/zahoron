<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeadApplicationResource\Pages;
use App\Filament\Resources\DeadApplicationResource\RelationManagers;
use App\Models\DeadApplication;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeadApplicationResource extends Resource
{
    protected static ?string $model = DeadApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Заявки на захоронение';
    protected static ?string $navigationGroup = 'Pop up';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (static::isRestrictedUser()) {
            $cityIds = static::getUserCityIds();
            
            if (!empty($cityIds)) {
                $query->whereIn('city_id', $cityIds);
            } else {
                // Если у пользователя нет назначенных городов, не показываем ничего
                $query->whereNull('city_id');
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
            // Пробуем декодировать JSON
            $decoded = json_decode($user->city_ids, true);
            
            if (is_array($decoded)) {
                $cityIds = $decoded;
            } else {
                // Если это строка с числами через запятую
                $cityIds = array_filter(explode(',', trim($user->city_ids, '[],"')));
            }
            
            // Преобразуем в числа и удаляем пустые значения
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
                Select::make('city_id')
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
                ->required()
                ->disabled($isRestrictedUser),

                TextInput::make('fio')
                    ->label('ФИО')
                    ->required(),

                Select::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title')
                    ->searchable()
                    ->nullable(),

                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id')  // Изменено на отображение имени вместо id
                    ->required()
                    ->disabled($isRestrictedUser),

                Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title')
                    ->searchable()
                    ->nullable()
                    ->disabled($isRestrictedUser),

                TextInput::make('call_time')
                    ->label('Время звонка')
                    ->nullable(),

                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                        4 => 'Архив',
                    ])
                    ->default(0)
                    ->required(),
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

                TextColumn::make('city.title')
                    ->label('Город')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fio')
                    ->label('ФИО')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('mortuary.title')
                    ->label('Морг')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')  // Изменено на отображение имени
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('organization.title')
                    ->label('Организация')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                        4 => 'Архив',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                        4 => 'Архив',
                    ]),

                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title')
                    ->hidden(static::isRestrictedUser())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title')
                    ->searchable(),


                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title')
                    ->searchable(),

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
            'index' => Pages\ListDeadApplications::route('/'),
            'create' => Pages\CreateDeadApplication::route('/create'),
            'edit' => Pages\EditDeadApplication::route('/{record}/edit'),
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
            return in_array($record->city_id, $userCityIds);
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