<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceAplicationResource\Pages;
use App\Filament\Resources\PriceAplicationResource\RelationManagers;
use App\Models\PriceAplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceAplicationResource extends Resource
{
    protected static ?string $model = PriceAplication::class;
    protected static ?string $navigationLabel = 'Цены на заявки в городах'; // Название в меню

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type_application_id')
                ->label('Тип заявки')
                ->relationship('typeApplication', 'title_ru')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('type_service_id')
                ->label('Тип услуги')
                ->relationship('typeService', 'title_ru')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('city_id')
                ->label('Город')
                ->relationship('city', 'title')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('price')
                ->label('Цена')
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
            Tables\Columns\TextColumn::make('price')
                ->label('Цена')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('typeApplication.title_ru')
                ->label('Тип заявки')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('typeService.title_ru')
                ->label('Тип услуги')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('city.title')
                ->label('Город')
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
            'index' => Pages\ListPriceAplications::route('/'),
            'create' => Pages\CreatePriceAplication::route('/create'),
            'edit' => Pages\EditPriceAplication::route('/{record}/edit'),
        ];
    }
}
