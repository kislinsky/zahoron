<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ReviewChurch;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReviewChurchResource\Pages;
use App\Filament\Resources\ReviewChurchResource\RelationManagers;

class ReviewChurchResource extends Resource
{
    protected static ?string $model = ReviewChurch::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Отзывы о храмах'; // Название в меню
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
                    
                Forms\Components\Select::make('church_id')
                    ->label('Храм')
                    ->relationship('church', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('content')
                    ->label('Контент')
                    ->required()
                    ->maxLength(1000),

                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'В обработке',
                        1 => 'Принят',
                    ])
                    ->required()
                    ->default(1),

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
                Tables\Columns\TextColumn::make('church.title')
                    ->label('Храм')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'В обработке',
                        1 => 'Принят',
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
            'index' => Pages\ListReviewChurches::route('/'),
            'create' => Pages\CreateReviewChurch::route('/create'),
            'edit' => Pages\EditReviewChurch::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}