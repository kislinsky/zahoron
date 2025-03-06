<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeadApplicationResource\Pages;
use App\Filament\Resources\DeadApplicationResource\RelationManagers;
use App\Models\DeadApplication;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeadApplicationResource extends Resource
{
    protected static ?string $model = DeadApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Заявки на захоронение'; // Название в меню
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

                // ФИО
                TextInput::make('fio')
                    ->label('ФИО')
                    ->required(),

                // Морг
                Select::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title') // Предполагается, что у вас есть модель Mortuary
                    ->nullable(),

                // Пользователь
                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // Предполагается, что у вас есть модель User
                    ->required(),

                // Организация
                Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title') // Предполагается, что у вас есть модель Organization
                    ->nullable(),

                // Время звонка
                TextInput::make('call_time')
                    ->label('Время звонка')
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

                // ФИО
                TextColumn::make('fio')
                    ->label('ФИО')
                    ->sortable(),

                // Морг
                TextColumn::make('mortuary.title')
                    ->label('Морг')
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

                // Фильтр по моргу
                Tables\Filters\SelectFilter::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title'),

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
            // Отношения (можно добавить позже)
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
}
