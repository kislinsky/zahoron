<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketStatusResource\Pages;
use App\Models\TicketStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;

class TicketStatusResource extends Resource
{
    protected static ?string $model = TicketStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Техподдержка';
    protected static ?string $pluralModelLabel = 'Статусы тикетов';
    protected static ?string $modelLabel = 'статус тикетов';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Название статуса')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Введите название статуса'),
                
                ColorPicker::make('color')
                    ->label('Цвет')
                    ->default('#3498db')
                    ->required(),
                
                Toggle::make('is_closed')
                    ->label('Закрытый статус')
                    ->helperText('Если включено, тикет с этим статусом считается закрытым')
                    ->default(false)
                    ->inline(false),
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
                
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Закрытый')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                
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
            ->filters([
                Tables\Filters\Filter::make('is_closed')
                    ->label('Только закрытые статусы')
                    ->query(fn ($query) => $query->where('is_closed', true)),
                
                Tables\Filters\Filter::make('is_open')
                    ->label('Только открытые статусы')
                    ->query(fn ($query) => $query->where('is_closed', false)),
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
                    
                    Tables\Actions\BulkAction::make('mark_as_closed')
                        ->label('Пометить как закрытые')
                        ->action(fn ($records) => $records->each->update(['is_closed' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('mark_as_open')
                        ->label('Пометить как открытые')
                        ->action(fn ($records) => $records->each->update(['is_closed' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать статус'),
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
            'index' => Pages\ListTicketStatuses::route('/'),
            'create' => Pages\CreateTicketStatus::route('/create'),
            'edit' => Pages\EditTicketStatus::route('/{record}/edit'),
        ];
    }
}