<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductPriceList;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductPriceListResource\Pages;
use App\Filament\Resources\ProductPriceListResource\RelationManagers;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\PriceProductPriceListRelationManager;

class ProductPriceListResource extends Resource
{
    protected static ?string $model = ProductPriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Продукты прйс-листа'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Название')
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
            PriceProductPriceListRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductPriceLists::route('/'),
            'create' => Pages\CreateProductPriceList::route('/create'),
            'edit' => Pages\EditProductPriceList::route('/{record}/edit'),
        ];
    }
}
