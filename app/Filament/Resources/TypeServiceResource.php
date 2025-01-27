<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeServiceResource\Pages;
use App\Filament\Resources\TypeServiceResource\RelationManagers;
use App\Models\TypeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeServiceResource extends Resource
{
    protected static ?string $model = TypeService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Услуги заявки'; // Название в меню
    protected static ?string $navigationGroup = 'Заявки'; // Указываем группу

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title_ru')
                ->label('Название')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('title')
                ->label('Значение')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('type_application_id')
                ->label('Тип услуги')
                ->relationship('typeApplication', 'title_ru')
                ->required()
                ->searchable()
                ->preload(),
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
                    ->label('Значение')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title_ru')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypeServices::route('/'),
            'create' => Pages\CreateTypeService::route('/create'),
            'edit' => Pages\EditTypeService::route('/{record}/edit'),
        ];
    }
}
