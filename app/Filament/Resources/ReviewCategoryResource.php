<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewCategoryResource\Pages;
use App\Models\ReviewCategory;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ReviewCategoryResource extends Resource
{
    protected static ?string $model = ReviewCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Категории отзывов общие';
    protected static ?string $navigationGroup = 'Отзывы общие';
    protected static ?string $modelLabel = 'Категория отзывов';
    protected static ?string $pluralModelLabel = 'Категории отзывов';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название категории')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('URL-адрес (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Автоматически генерируется из названия'),

                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Активная категория')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Дополнительно')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Дата создания')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i:s') ?? '-')
                            ->hidden(fn ($record) => $record === null),

                        Placeholder::make('updated_at')
                            ->label('Дата обновления')
                            ->content(fn ($record) => $record?->updated_at?->format('d.m.Y H:i:s') ?? '-')
                            ->hidden(fn ($record) => $record === null),

                        Placeholder::make('reviews_count')
                            ->label('Количество отзывов')
                            ->content(fn ($record) => $record?->reviews_count ?? ($record?->reviews()->count() ?? 0))
                            ->hidden(fn ($record) => $record === null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL-адрес')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('reviews_count')
                    ->label('Кол-во отзывов')
                    ->counts('reviews')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                ToggleColumn::make('is_active')
                    ->label('Активна')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Сортировка')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные')
                    ->placeholder('Все')
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                Tables\Actions\ForceDeleteAction::make()->iconButton(),
                Tables\Actions\RestoreAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListReviewCategories::route('/'),
            'create' => Pages\CreateReviewCategory::route('/create'),
            'edit' => Pages\EditReviewCategory::route('/{record}/edit'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}