<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqProductPriceListResource\Pages;
use App\Filament\Resources\FaqProductPriceListResource\RelationManagers;
use App\Models\FaqProductPriceList;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqProductPriceListResource extends Resource
{
    protected static ?string $model = FaqProductPriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Вопросы продуктам прайс листа'; // Название в меню
    protected static ?string $navigationGroup = 'Вопросы'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Вопрос')
                ->required()
                ->maxLength(255),

                Textarea::make('content')
                ->label('Ответ')
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
                    ->label('Вопрос')
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
            'index' => Pages\ListFaqProductPriceLists::route('/'),
            'create' => Pages\CreateFaqProductPriceList::route('/create'),
            'edit' => Pages\EditFaqProductPriceList::route('/{record}/edit'),
        ];
    }
}
