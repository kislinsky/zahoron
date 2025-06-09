<?php

namespace App\Filament\Resources\ChurchResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ViewsRelationManager extends RelationManager
{
    protected static string $relationship = 'views';
    protected static ?string $title = 'Просмотры';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
               
                
                TextInput::make('user_id')->label('ID пользователя')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('session_id')->label('ID сессии')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('source')->label('Источник')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('ip_address')->label('IP адрес')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('device')->label('Устройство')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')->label('Местоположение')
                    ->required()
                    ->maxLength(255),

                TextInput::make('created_at')->label('Дата просмотра')
                    ->required()
                    ->maxLength(255),
        
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('views')
            ->columns([
                Tables\Columns\TextColumn::make('user_id')->label('ID пользователя'),
                Tables\Columns\TextColumn::make('session_id')->label('ID сессии'),
                Tables\Columns\TextColumn::make('source')->label('Источник'),
                Tables\Columns\TextColumn::make('ip_address')->label('IP адрес'),
                Tables\Columns\TextColumn::make('device')->label('Устройство'),
                Tables\Columns\TextColumn::make('location')->label('Местоположение'),
                Tables\Columns\TextColumn::make('created_at')->label('Дата просмотра')->dateTime(),
            ])
            ->filters([
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
