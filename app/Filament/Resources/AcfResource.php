<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcfResource\Pages;
use App\Models\Acf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class AcfResource extends Resource
{
    protected static ?string $model = Acf::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Дополнительные поля страниц';
    protected static ?string $navigationGroup = 'Страницы сайта';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название поля')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('page_id')
                            ->label('Связанная страница')
                            ->relationship('page', 'title_ru')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('type')
                            ->label('Тип поля')
                            ->options([
                                'text' => 'Текст (Rich Editor)',
                                'file' => 'Файл',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => request()->session()->forget('acf_type_change')),
                    ])->columns(1),
                    
                Forms\Components\Section::make('Содержимое поля')
                    ->schema([
                        
Forms\Components\Toggle::make('is_plain_text')
    ->label('Обычный текст (без форматирования)')
    ->hidden(fn (Forms\Get $get) =>  $get('type') !== 'text')
    ->live(),
Forms\Components\Fieldset::make('Содержимое')
    ->schema([
        Forms\Components\RichEditor::make('content_html')
            ->label('Форматированный текст')
            ->columnSpanFull()
            ->hidden(fn (Forms\Get $get) => $get('is_plain_text') || $get('type') !== 'text'),
            
        Forms\Components\Textarea::make('content_plain')
            ->label('Обычный текст')
            ->columnSpanFull()
            ->hidden(fn (Forms\Get $get) => !$get('is_plain_text') || $get('type') !== 'text'),
    ])
    ->hidden(fn (Forms\Get $get) => $get('type') !== 'text'),
                            
                        Forms\Components\FileUpload::make('file') // Изменили на file_path
                            ->label('Файл')
                            ->preserveFilenames()
                            ->directory('/uploads') // Более структурированный путь
                            ->downloadable()
                            ->columnSpanFull()
                            ->required(fn (Forms\Get $get) => $get('type') === 'file')
                            ->hidden(fn (Forms\Get $get) => $get('type') !== 'file'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('page.title_ru')
                    ->label('Страница')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'info',
                        'file' => 'warning',
                        default => 'gray',
                    }),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип поля')
                    ->options([
                        'text' => 'Текст',
                        'file' => 'Файл',
                    ]),
                    
                Tables\Filters\SelectFilter::make('page_id')
                    ->label('Страница')
                    ->relationship('page', 'title_ru')
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // При необходимости добавьте отношения
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcfs::route('/'),
            'create' => Pages\CreateAcf::route('/create'),
            'edit' => Pages\EditAcf::route('/{record}/edit'),
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