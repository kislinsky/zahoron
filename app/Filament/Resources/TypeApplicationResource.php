<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeApplicationResource\Pages;
use App\Filament\Resources\TypeApplicationResource\RelationManagers;
use App\Models\TypeApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeApplicationResource extends Resource
{
    protected static ?string $model = TypeApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Типы заявок'; // Название в меню
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title_ru')
                ->label('Название')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('title')
                ->label('Значение')
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
                ->label('Значение')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('title_ru')
                ->label('Название')
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
            'index' => Pages\ListTypeApplications::route('/'),
            'create' => Pages\CreateTypeApplication::route('/create'),
            'edit' => Pages\EditTypeApplication::route('/{record}/edit'),
        ];
    }
}
