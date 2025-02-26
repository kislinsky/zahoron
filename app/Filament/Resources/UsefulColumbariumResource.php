<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsefulColumbariumResource\Pages;
use App\Filament\Resources\UsefulColumbariumResource\RelationManagers;
use App\Models\UsefulColumbarium;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsefulColumbariumResource extends Resource
{
    protected static ?string $model = UsefulColumbarium::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Полезное колумбарии'; // Название в меню
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
            'index' => Pages\ListUsefulColumbaria::route('/'),
            'create' => Pages\CreateUsefulColumbarium::route('/create'),
            'edit' => Pages\EditUsefulColumbarium::route('/{record}/edit'),
        ];
    }
}
