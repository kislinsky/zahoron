<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EdgeResource\Pages;
use App\Filament\Resources\EdgeResource\RelationManagers;
use App\Models\Edge;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EdgeResource extends Resource
{
    protected static ?string $model = Edge::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Края'; // Название в меню
    protected static ?string $navigationGroup = 'Cубъекты'; // Указываем группу
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Название')
                ->required()
                ->maxLength(255),

                Radio::make('is_show')
                ->label('Отображать на сайте')
                ->options([
                    0 => 'Нет',
                    1 => 'Да'
                ]),

                 Forms\Components\TextInput::make('limit_calls')
                    ->label('Лимит звонков')
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
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт

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
            'index' => Pages\ListEdges::route('/'),
            'create' => Pages\CreateEdge::route('/create'),
            'edit' => Pages\EditEdge::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' ;
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}
