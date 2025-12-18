<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Review;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Models\ReviewCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\ReviewResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReviewResource\RelationManagers;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Отзывы общие';
    protected static ?string $navigationGroup = 'Отзывы общие';
    protected static ?string $modelLabel = 'Отзыв ';
    protected static ?string $pluralModelLabel = 'Отзывы общие';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Имя клиента')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('review_category_id')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Выберите категорию'),

                        Forms\Components\Textarea::make('content')
                            ->label('Текст отзыва')
                            ->required()
                            ->maxLength(2000)
                            ->rows(4)
                            ->columnSpanFull(),

                      

                       
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Изображения')
                    ->schema([
                        FileUpload::make('img_before')
                            ->label('Фото "До"')
                            ->directory('uploads/reviews')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->nullable()
                            ->columnSpan(1)
                            ->helperText('Максимальный размер: 2MB'),

                        FileUpload::make('img_after')
                            ->label('Фото "После"')
                            ->directory('uploads/reviews')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->nullable()
                            ->columnSpan(1)
                            ->helperText('Максимальный размер: 2MB'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                       

                        Placeholder::make('created_at')
                            ->label('Дата создания')
                            ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? '-')
                            ->hidden(fn ($record) => $record === null),

                        Placeholder::make('updated_at')
                            ->label('Дата обновления')
                            ->content(fn (?Model $record): string => $record?->updated_at?->format('d.m.Y H:i:s') ?? '-')
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

                ImageColumn::make('img_before')
                    ->label('Фото "До"')
                    ->width(50)
                    ->height(50)
                    ->circular()
                    ->defaultImageUrl(url('/images/default-review.jpg')),

                ImageColumn::make('img_after')
                    ->label('Фото "После"')
                    ->width(50)
                    ->height(50)
                    ->circular()
                    ->defaultImageUrl(url('/images/default-review.jpg')),

                TextColumn::make('name')
                    ->label('Имя клиента')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

               

                TextColumn::make('content')
                    ->label('Текст отзыва')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

    
              

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
           
            ->filters([
                Tables\Filters\SelectFilter::make('review_category_id')
                    ->label('Категория')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

              
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                Tables\Actions\ViewAction::make()->iconButton(),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
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