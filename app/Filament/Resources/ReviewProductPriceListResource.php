<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ReviewProductPriceList;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReviewProductPriceListResource\Pages;
use App\Filament\Resources\ReviewProductPriceListResource\RelationManagers;

class ReviewProductPriceListResource extends Resource
{
    protected static ?string $model = ReviewProductPriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Отзывы о товарах прайс-листа'; // Название в меню
    protected static ?string $navigationGroup = 'Отзывы'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\Select::make('product_price_list_id')
                    ->label('Товар')
                    ->relationship('product_price_lists', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                    Forms\Components\Select::make('user_id')
                    ->label('id пользователя')
                    ->relationship('user', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                    Forms\Components\Textarea::make('content')
                    ->label('Контент')
                    ->required()
                    ->maxLength(1000),

   
                    Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
                

                    FileUpload::make('img_before')
                    ->label('Фото до')
                    ->directory('/uploads_product_price_list')
                    ->image()
                    ->maxSize(2048)
                    ->required() ,

                    FileUpload::make('img_after')
                    ->label('Фото после')
                    ->directory('/uploads_product_price_list')
                    ->image()
                    ->maxSize(2048)
                    ->required() ,

               

             
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
                Tables\Columns\TextColumn::make('user_id')
                    ->label('id пользователя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_price_lists.title')
                    ->label('Товар')
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
            'index' => Pages\ListReviewProductPriceLists::route('/'),
            'create' => Pages\CreateReviewProductPriceList::route('/create'),
            'edit' => Pages\EditReviewProductPriceList::route('/{record}/edit'),
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
