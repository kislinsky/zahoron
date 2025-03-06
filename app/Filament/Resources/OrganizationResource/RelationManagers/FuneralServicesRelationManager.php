<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FuneralServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'funeralServices';
    protected static ?string $title = 'Pop up рит. услуги';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('service')
                    ->label('Выберите услугу')
                    ->options([
                        1 => 'Отправка груз 200',
                        2 => 'Организация кремации',
                        3 => 'Организация похорон',
                    ])
                    ->required(),

                Select::make('city_id')
                    ->label('Город отправки')
                    ->relationship('city', 'title') // Предполагается, что у вас есть модель City
                    ->required(),

                Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title') // Предполагается, что у вас есть модель Cemetery
                    ->nullable(),

                Select::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title') // Предполагается, что у вас есть модель Mortuary
                    ->nullable(),

                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // Предполагается, что у вас есть модель User
                    ->required(),

                // Дополнительные поля
                TextInput::make('status_death')
                    ->label('Статус умершего')
                    ->required(),

                TextInput::make('civilian_status_death')
                    ->label('Гражданский статус')
                    ->required(),

                Select::make('funeral_service_church')
                    ->label('Отпевание в церкви')
                    ->nullable(),

                Select::make('farewell_hall')
                    ->label('Прощальный зал')
                    ->nullable(),

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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('funeralServices')
            ->columns([

                TextColumn::make('id')
                ->label('ID')
                ->sortable(),

            // Город
            TextColumn::make('city.title')
                ->label('Город отправки')
                ->sortable(),

            // Услуга
            TextColumn::make('service')
                ->label('Услуга')
                ->formatStateUsing(fn (int $state): string => match ($state) {
                    1 => 'Отправка груз 200',
                    2 => 'Организация кремации',
                    3 => 'Организация похорон',
                    default => 'Неизвестно',
                })
                ->sortable(),

          

            // Пользователь
            TextColumn::make('user.id')
                ->label('Пользователь')
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
            Tables\Filters\SelectFilter::make('mortuary_id')
                ->label('Район')
                ->relationship('mortuary', 'title'),

          
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
