<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoObjectResource\Pages;
use App\Filament\Resources\SeoObjectResource\Widgets\SeoInfoWidget;
use App\Models\SeoObject;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class SeoObjectResource extends Resource
{
    protected static ?string $model = SeoObject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'SEO'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ru_title')
                    ->label('Значение'),
                TextInput::make('title')
                    ->label('Название'),

                    Repeater::make('SEO')
                    ->relationship('SEO') // Указываем название связи
                    ->schema([
                         TextInput::make('title')
                    ->label('Название'),
                        TextInput::make('name')
                            ->label('Meta Title')
                            ->required(),

                        Textarea::make('content')
                            ->label('Meta Description')
                            ->rows(3),
                    ])
                    ->columns(1) // Количество столбцов для полей
                    ->createItemButtonLabel('Добавить SEO данные'),

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
                TextColumn::make('ru_title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Удалить продукт

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

    public static function getWidgets(): array
    {
        return [
            SeoInfoWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoObjects::route('/'),
            'create' => Pages\CreateSeoObject::route('/create'),
            'edit' => Pages\EditSeoObject::route('/{record}/edit'),
        ];
    }


    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || 
               auth()->user()->role === 'seo-specialist';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}
