<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
