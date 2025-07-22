<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use App\Models\Acf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AcfsRelationManager extends RelationManager
{
    protected static string $relationship = 'acfs';
    protected static ?string $title = 'Дополнительные поля страницы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название поля')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('type')
                            ->label('Тип поля')
                            ->options([
                                'text' => 'Текст (Rich Editor)',
                                'file' => 'Файл',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => request()->session()->forget('acf_type_change')),
                    ])
                    ->columns(1),
                    
                Forms\Components\Section::make('Содержимое поля')
                    ->schema([
                        // Поле для текста
                        Forms\Components\RichEditor::make('content')
                            ->label('Текстовое содержимое')
                            ->toolbarButtons([
                                'attachFiles', 'bold', 'italic', 'underline', 'strike',
                                'link', 'orderedList', 'bulletList', 'blockquote',
                                'h2', 'h3', 'h4', 'codeBlock', 'undo', 'redo',
                            ])
                            ->columnSpanFull()
                            ->required(fn (Forms\Get $get) => $get('type') === 'text')
                            ->hidden(fn (Forms\Get $get) => $get('type') !== 'text')
                            ->helperText('Для текстовых полей используйте это поле'),
                            
                        // Поле для файлов
                        Forms\Components\FileUpload::make('file')
                            ->label('Файл')
                            ->preserveFilenames()
                            ->directory('/uploads')
                            ->downloadable()
                            ->columnSpanFull()
                            ->required(fn (Forms\Get $get) => $get('type') === 'file')
                            ->hidden(fn (Forms\Get $get) => $get('type') !== 'file')
                            ->helperText('Загрузите файл для этого поля'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
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
                    ->label('Фильтр по типу')
                    ->options([
                        'text' => 'Текст',
                        'file' => 'Файл',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить поле'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Удалить')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Нет дополнительных полей')
            ->emptyStateDescription('Нажмите "Добавить поле", чтобы создать новое')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить поле'),
            ]);
    }
}