<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers\PriceServiceRelationManager;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Услуги'; // Название в меню

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Основные данные')->schema([
                    TextInput::make('title')
                        ->label('Название')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('price')
                        ->label('Цена')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('content')
                        ->label('Описание')
                        ->required(),

                  

                    
                ]),

                Section::make('Дополнительная информация')->schema([
                    Textarea::make('text_under_title')
                        ->label('Текст под заголовком')
                        ->nullable(),

                    FileUpload::make('video_1')
                        ->label('Видео 1')
                        ->nullable(),

                    Textarea::make('text_under_video_1')
                        ->label('Текст под видео 1')
                        ->nullable(),

                    Textarea::make('text_under_img')
                        ->label('Текст под изображением')
                        ->nullable(),

                    Textarea::make('text_sale')
                        ->label('Текст скидки')
                        ->nullable(),

                    Textarea::make('text_stages')
                        ->label('Текст этапов')
                        ->nullable(),

                    FileUpload::make('video_2')
                        ->label('Видео 2')
                        ->nullable(),

                    FileUpload::make('img_structure')
                        ->label('Изображение структуры')
                        ->nullable()
                        ->disk('public')
                        ->directory('services/images')
                        ->maxSize(1024) // 1MB
                        ->image(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                TextColumn::make('title')
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
            PriceServiceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
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
