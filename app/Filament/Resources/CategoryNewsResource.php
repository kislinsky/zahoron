<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryNewsResource\Pages;
use App\Filament\Resources\CategoryNewsResource\RelationManagers;
use App\Models\CategoryNews;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryNewsResource extends Resource
{
    protected static ?string $model = CategoryNews::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Категории блога'; // Название в меню
    protected static ?string $navigationGroup = 'Категории'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                ->label('Название')
                ->required() ,
            
                Forms\Components\FileUpload::make('icon')
                    ->label('Иконка')
                    ->directory('/uploads_cats_news') // Директория для сохранения
                    ->image()
                    ->nullable(),
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListCategoryNews::route('/'),
            'create' => Pages\CreateCategoryNews::route('/create'),
            'edit' => Pages\EditCategoryNews::route('/{record}/edit'),
        ];
    }
}
