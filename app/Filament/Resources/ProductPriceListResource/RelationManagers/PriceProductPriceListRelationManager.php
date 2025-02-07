<?php

namespace App\Filament\Resources\ProductPriceListResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceProductPriceListRelationManager extends RelationManager
{
    protected static string $relationship = 'priceProductPriceList';
    protected static ?string $title = 'Цены в городах';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title')
                    ->searchable()
                    ->required()
                    ->preload(),

                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->required(),

                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('priceProductPriceList')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->label('Цена')
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
