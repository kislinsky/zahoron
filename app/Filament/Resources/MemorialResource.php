<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemorialResource\Pages;
use App\Filament\Resources\MemorialResource\RelationManagers;
use App\Models\Memorial;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemorialResource extends Resource
{
    protected static ?string $model = Memorial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Поминки'; // Название в меню
    protected static ?string $navigationGroup = 'Pop up'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Город
                Select::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title') // Предполагается, что у вас есть модель City
                    ->required(),

                // Район
                Select::make('district_id')
                    ->label('Район')
                    ->relationship('district', 'title') // Предполагается, что у вас есть модель District
                    ->required(),

                // Дата бронирования
                DatePicker::make('date')
                    ->label('Дата бронирования')
                    ->required(),

                // Время бронирования
                TextInput::make('time')
                    ->label('Время бронирования')
                    ->required(),

                // Количество участников
                TextInput::make('count')
                    ->label('Количество участников')
                    ->numeric()
                    ->required(),

                // Пользователь
                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name') // Предполагается, что у вас есть модель User
                    ->required(),

                // Организация
                Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title') // Предполагается, что у вас есть модель Organization
                    ->nullable(),

                // Статус
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

                // Количество времени
                TextInput::make('count_time')
                    ->label('Количество времени')
                    ->numeric()
                    ->required(),

                // Время звонка
                TextInput::make('call_time')
                    ->label('Время звонка')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ID
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Город
                TextColumn::make('city.title')
                    ->label('Город')
                    ->sortable(),

                // Район
                TextColumn::make('district.title')
                    ->label('Район')
                    ->sortable(),

                // Пользователь
                TextColumn::make('user.id')
                    ->label('Пользователь')
                    ->sortable(),

                // Организация
                TextColumn::make('organization.title')
                    ->label('Организация')
                    ->sortable(),

                // Статус
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                        4 => 'Архив',
                    }),

                

                // Дата создания
                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y'),
            ])
            ->filters([
                // Фильтр по статусу
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                        4 => 'Архив',
                    ]),

                // Фильтр по городу
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title'),

                // Фильтр по району
                Tables\Filters\SelectFilter::make('district_id')
                    ->label('Район')
                    ->relationship('district', 'title'),

                // Фильтр по организации
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title'),
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
            'index' => Pages\ListMemorials::route('/'),
            'create' => Pages\CreateMemorial::route('/create'),
            'edit' => Pages\EditMemorial::route('/{record}/edit'),
        ];
    }
}
