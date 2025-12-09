<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderProductResource\Pages;
use App\Filament\Resources\OrderProductResource\RelationManagers;
use App\Models\OrderProduct;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Заказы товаров';
    protected static ?string $navigationGroup = 'Заказы';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::isRestrictedUser()) {
            $cityIds = static::getUserCityIds();

            if (!empty($cityIds)) {
                $query->whereHas('organization', function($q) use ($cityIds) {
                    $q->whereIn('city_id', $cityIds);
                });
            } else {
                $query->whereNull('organization_id');
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
                TextInput::make('additional')
                    ->label('Дополнительно')
                    ->nullable(),

                Select::make('product_id')
                    ->label('Товар')
                    ->relationship('product', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id')
                    ->required()
                    ->disabled($isRestrictedUser),

                Textarea::make('customer_comment')
                    ->label('Комментарий клиента')
                    ->nullable(),

                TextInput::make('count')
                    ->label('Количество')
                    ->numeric()
                    ->required(),

                TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required(),

                TextInput::make('size')
                    ->label('Размер')
                    ->nullable(),

                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                    ])
                    ->default(0)
                    ->required(),

                Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('date')
                    ->label('Дата')
                    ->type('date')
                    ->nullable(),

                TextInput::make('time')
                    ->label('Время')
                    ->nullable(),

                Select::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('city_from')
                    ->label('Город отправления')
                    ->nullable(),

                TextInput::make('city_to')
                    ->label('Город назначения')
                    ->nullable(),

                Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title', function (Builder $query) use ($userCityIds, $isRestrictedUser) {
                        if ($isRestrictedUser) {
                            $query->whereIn('city_id', $userCityIds);
                        }

                        $query->orderBy('title');

                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->disabled($isRestrictedUser),
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

                TextColumn::make('product.title')
                    ->label('Товар')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Цена')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                    })
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('organization.title')
                    ->label('Организация')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('organization.city.title')
                    ->label('Город организации')
                    ->sortable()
                    ->searchable(),

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
                    ]),

                Tables\Filters\SelectFilter::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title')
                    ->searchable()
                    ->preload()
                    ->hidden(static::isRestrictedUser()),
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
            'index' => Pages\ListOrderProducts::route('/'),
            'create' => Pages\CreateOrderProduct::route('/create'),
            'edit' => Pages\EditOrderProduct::route('/{record}/edit'),
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
            return $record->organization && in_array($record->organization->city_id, $userCityIds);
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
