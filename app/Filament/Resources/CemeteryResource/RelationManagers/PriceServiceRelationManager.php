<?php

namespace App\Filament\Resources\CemeteryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceServiceRelationManager extends RelationManager
{
    protected static string $relationship = 'priceService';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'title')
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
            ->recordTitleAttribute('priceService')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->label('Цена')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('service.title')
                ->label('Услуга')
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
