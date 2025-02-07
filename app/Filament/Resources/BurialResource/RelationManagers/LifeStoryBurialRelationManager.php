<?php

namespace App\Filament\Resources\BurialResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LifeStoryBurialRelationManager extends RelationManager
{
    protected static string $relationship = 'lifeStory';
    protected static ?string $title = 'Информация о человеке';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('content')
                    ->required()
                    ->maxLength(255),
                Select::make('status') // Поле для статуса
                    ->label('Статус') // Название поля
                    ->options([
                        0 => 'В обработке', // Значение 1 с названием "Раз"
                        1 => 'Принят', // Значение 2 с названием "Два"
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1) // Значение
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lifeStory')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('id'),
                
                Tables\Columns\TextColumn::make('user_id')
                    ->label('user_id'),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'В обработке', // Значение 1 с названием "Раз"
                        1 => 'Принят', // Значение 2 с названием "Два"
                        default => 'Неизвестно',
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
