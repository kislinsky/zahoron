<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqRitualObjectResource\Pages;
use App\Filament\Resources\FaqRitualObjectResource\RelationManagers;
use App\Models\FaqRitualObject;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqRitualObjectResource extends Resource
{
    protected static ?string $model = FaqRitualObject::class;

 protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Вопросы на странице листинга (рит. обьекты)'; // Название в меню
    protected static ?string $navigationGroup = 'Вопросы'; // Указываем группу 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Вопрос')
                ->required(),

                RichEditor::make('content')
                ->label('Ответ')
                ->required(),

                Select::make('type_object')
                    ->label('Тип обьекта')
                    ->options([
                        'cemetery' => 'Кладбища',
                        'сolumbarium' => 'Колумбарии',
                        'crematorium' => 'Крематории',
                        'mortuary' => 'Морги',
                        'churche' => 'Церкви',
                        'mosque' => 'Мечети',
                    ])
                    ->default(0)
                    ->required(),
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

                    TextColumn::make('type_object')
                    ->label('Тип обьекта')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cemetery' => 'Кладбища',
                        'сolumbarium' => 'Колумбарии',
                        'crematorium' => 'Крематории',
                        'mortuary' => 'Морги',
                        'churche' => 'Церкви',
                        'mosque' => 'Мечети',
                    }),
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
            'index' => Pages\ListFaqRitualObjects::route('/'),
            'create' => Pages\CreateFaqRitualObject::route('/create'),
            'edit' => Pages\EditFaqRitualObject::route('/{record}/edit'),
        ];
    }
}
