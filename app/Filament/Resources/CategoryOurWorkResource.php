<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryOurWorkResource\Pages;
use App\Filament\Resources\CategoryOurWorkResource\RelationManagers;
use App\Models\CategoryOurWork;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryOurWorkResource extends Resource
{
    protected static ?string $model = CategoryOurWork::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Категории'; // Название в меню
    protected static ?string $navigationGroup = 'Наши работы'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                ->label('Название')
                ->required() ,
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
            'index' => Pages\ListCategoryOurWorks::route('/'),
            'create' => Pages\CreateCategoryOurWork::route('/create'),
            'edit' => Pages\EditCategoryOurWork::route('/{record}/edit'),
        ];
    }
}
