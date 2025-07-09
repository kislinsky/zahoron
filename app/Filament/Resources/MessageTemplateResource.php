<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateResource\Pages;
use App\Filament\Resources\MessageTemplateResource\RelationManagers;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Шаблоны сообщений';

    protected static ?string $modelLabel = 'Шаблон сообщения';

    protected static ?string $pluralModelLabel = 'Шаблоны сообщений';

    protected static ?string $navigationGroup = 'Коммуникации';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основные настройки')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название шаблона')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state) {
                                $set('slug', Str::slug($state));
                            }),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Уникальный идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit'),
                            
                        Forms\Components\Select::make('type')
                            ->label('Тип сообщения')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                            ])
                            ->required()
                            ->native(false),
                            
                        Forms\Components\TextInput::make('subject')
                            ->label('Тема письма')
                            ->required(fn ($get) => $get('type') === 'email')
                            ->hidden(fn ($get) => $get('type') !== 'email')
                            ->maxLength(255),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активный шаблон')
                            ->default(true),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Содержание шаблона')
                    ->schema([
                        RichEditor::make('template')
                            ->toolbarButtons([
                                'attachFiles', // возможность прикрепить файлы
                                'bold', // жирный текст
                                'italic', // курсив
                                'underline', // подчеркивание
                                'strike', // зачеркнутый текст
                                'link', // вставка ссылок
                                'orderedList', // нумерованный список
                                'bulletList', // маркированный список
                                'blockquote', // цитата
                                'h2', 'h3', 'h4', // заголовки второго, третьего и четвертого уровня
                                'codeBlock', // блок кода
                                'undo', 'redo', // отмена/возврат действия
                            ])
                            ->label('Текст шаблона')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Используйте {{variable}} для подстановки переменных'),
                            
                       
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Описание шаблона')
                            ->columnSpanFull()
                            ->maxLength(500),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->label('Идентификатор')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'email' => 'Email',
                        'sms' => 'SMS',
                        default => $state
                    })
                    ->color(fn ($state) => match($state) {
                        'email' => 'info',
                        'sms' => 'success',
                        default => 'gray'
                    }),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип шаблона')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Только активные')
                    ->default(true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Отношения при необходимости
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
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