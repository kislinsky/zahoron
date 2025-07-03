<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OurWorkResource\Pages;
use App\Filament\Resources\OurWorkResource\RelationManagers;
use App\Models\OurWork;
use App\Models\CategoryOurWork;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OurWorkResource extends Resource
{
    protected static ?string $model = OurWork::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Наши работы';
    protected static ?string $modelLabel = 'Работа';
    protected static ?string $pluralModelLabel = 'Наши работы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'title') // Assuming 'category' is the relationship name and 'name' is the column to display
                    ->required()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('title')
                            ->label('Название категории')
                            ->required(),
                    ]),
                
                Forms\Components\FileUpload::make('img_before')
                    ->label('Изображение "До"')
                    ->required()
                    ->directory('/uploads_our_works')
                    ->image()
                    ->imageEditor(),
                    
                Forms\Components\FileUpload::make('img_after')
                    ->label('Изображение "После"')
                    ->required()
                    ->directory('/uploads_our_works')
                    ->image()
                    ->imageEditor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('img_before')
                    ->label('До')
                    ->size(100),
                
                Tables\Columns\ImageColumn::make('img_after')
                    ->label('После')
                    ->size(100),
                
                Tables\Columns\TextColumn::make('category.title')
                    ->label('Категория')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'title')
                    ->searchable()
                    ->preload(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOurWorks::route('/'),
            'create' => Pages\CreateOurWork::route('/create'),
            'edit' => Pages\EditOurWork::route('/{record}/edit'),
        ];
    }
}