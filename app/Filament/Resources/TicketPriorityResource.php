<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketPriorityResource\Pages;
use App\Models\TicketPriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class TicketPriorityResource extends Resource
{
    protected static ?string $model = TicketPriority::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Техподдержка';
    protected static ?string $pluralModelLabel = 'Приоритеты тикетов';
    protected static ?string $modelLabel = 'приоритет тикетов';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Название приоритета')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Введите название приоритета'),
                
                ColorPicker::make('color')
                    ->label('Цвет')
                    ->default('#95a5a6')
                    ->required(),
                
                Select::make('level')
                    ->label('Уровень приоритета')
                    ->required()
                    ->options([
                        1 => 'Низкий (1)',
                        2 => 'Средний (2)', 
                        3 => 'Высокий (3)',
                        4 => 'Критический (4)',
                        5 => 'Экстренный (5)'
                    ])
                    ->default(1)
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\ColorColumn::make('color')
                    ->label('Цвет')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('level')
                    ->label('Уровень')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => match($state) {
                        1 => 'Низкий (1)',
                        2 => 'Средний (2)',
                        3 => 'Высокий (3)',
                        4 => 'Критический (4)',
                        5 => 'Экстренный (5)',
                        default => "Уровень {$state}"
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        1 => 'success',
                        2 => 'warning',
                        3 => 'danger',
                        4 => 'danger',
                        5 => 'danger',
                        default => 'gray'
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлен')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('level', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->label('Уровень приоритета')
                    ->options([
                        1 => 'Низкий (1)',
                        2 => 'Средний (2)',
                        3 => 'Высокий (3)',
                        4 => 'Критический (4)',
                        5 => 'Экстренный (5)'
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
                
                Tables\Actions\DeleteAction::make()
                    ->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать приоритет'),
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
            'index' => Pages\ListTicketPriorities::route('/'),
            'create' => Pages\CreateTicketPriority::route('/create'),
            'edit' => Pages\EditTicketPriority::route('/{record}/edit'),
        ];
    }
}