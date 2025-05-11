<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ReviewCrematorium;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReviewCrematoriumResource\Pages;
use App\Filament\Resources\ReviewCrematoriumResource\RelationManagers;

class ReviewCrematoriumResource extends Resource
{
    protected static ?string $model = ReviewCrematorium::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Отзывы о крематориях'; // Название в меню
    protected static ?string $navigationGroup = 'Отзывы'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('crematorium_id')
                    ->label('Крематорий')
                    ->relationship('crematorium', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('content')
                    ->label('Контент')
                    ->required()
                    ->maxLength(1000),

                
                Select::make('status') // Поле для статуса
                    ->label('Статус') // Название поля
                    ->options([
                        0 => 'В обработке', // Значение 1 с названием "Раз"
                        1 => 'Принят', // Значение 2 с названием "Два"
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение

                Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
                
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('crematorium.title')
                    ->label('Крематорий')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'В обработке', // Значение 1 с названием "Раз"
                        1 => 'Принят', // Знач
                        default => 'Неизвестно',
                    }),
                    Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
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
            'index' => Pages\ListReviewCrematoria::route('/'),
            'create' => Pages\CreateReviewCrematorium::route('/create'),
            'edit' => Pages\EditReviewCrematorium::route('/{record}/edit'),
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
