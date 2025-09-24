<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketCategoryResource\Pages;
use App\Models\TicketCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;

class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Техподдержка';
    protected static ?string $pluralModelLabel = 'Категории тикетов';
    protected static ?string $modelLabel = 'категорию тикетов';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название категории')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Введите название категории'),
                
                ColorPicker::make('color')
                    ->label('Цвет')
                    ->default('#3498db')
                    ->required(),
                
                Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true)
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
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Статус')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлена')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Только активные')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
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
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Активировать')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Деактивировать')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать категорию'),
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
            'index' => Pages\ListTicketCategories::route('/'),
            'create' => Pages\CreateTicketCategory::route('/create'),
            'edit' => Pages\EditTicketCategory::route('/{record}/edit'),
        ];
    }
}