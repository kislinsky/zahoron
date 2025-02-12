<?php

namespace App\Filament\Resources\CemeteryResource\RelationManagers;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class WorkingHoursRelationManager extends RelationManager
{
    protected static string $relationship = 'workingHours';
    protected static ?string $title = 'Время работы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('day') // Поле для статуса
                ->label('День недели') // Название поля
                ->options([
                   'Monday'    => 'Понедельник',
                    'Tuesday'   => 'Вторник',
                    'Wednesday' => 'Среда',
                    'Thursday'  => 'Четверг',
                    'Friday'    => 'Пятница',
                    'Saturday'  => 'Суббота',
                    'Sunday'    => 'Воскресенье',
                ])
                ->required(), // Значение по умолчанию

                TextInput::make('time_start_work')
                    ->label('Время начала работы'),

                TextInput::make('time_end_work')
                    ->label('Время начала работы'),


                Radio::make('holiday')
                    ->label('Статус')
                    ->options([
                        '1' => 'Выходной',
                        '0' => 'Не выходной',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('workingHours')
            ->columns([
                TextColumn::make('day')
                ->label('День недели')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'Monday'    => 'Понедельник',
                    'Tuesday'   => 'Вторник',
                    'Wednesday' => 'Среда',
                    'Thursday'  => 'Четверг',
                    'Friday'    => 'Пятница',
                    'Saturday'  => 'Суббота',
                    'Sunday'    => 'Воскресенье',
                    default     => 'Неизвестно', // На случай, если значение отсутствует
                }),

                TextColumn::make('time_start_work')
                ->label('Начало работы'),

                TextColumn::make('time_end_work')
                ->label('Конец работы')

            ])
            ->filters([
                //
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
