<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsefulMortuaryResource\Pages;
use App\Filament\Resources\UsefulMortuaryResource\RelationManagers;
use App\Models\UsefulMortuary;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsefulMortuaryResource extends Resource
{
    protected static ?string $model = UsefulMortuary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Полезное морги'; // Название в меню
    protected static ?string $navigationGroup = 'Полезные советы'; // Указываем группу
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                ->label('Заголовок')
                ->required()
                ->maxLength(255),

                Textarea::make('content')
                ->label('Описание')
                ->required()
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsefulMortuaries::route('/'),
            'create' => Pages\CreateUsefulMortuary::route('/create'),
            'edit' => Pages\EditUsefulMortuary::route('/{record}/edit'),
        ];
    }
}
