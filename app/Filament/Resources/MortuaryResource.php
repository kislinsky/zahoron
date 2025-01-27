<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Mortuary;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\MortuaryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MortuaryResource\RelationManagers;

class MortuaryResource extends Resource
{
    protected static ?string $model = Mortuary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Морги'; // Название в меню
    protected static ?string $navigationGroup = 'Ритуальные обьекты'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('width')
                    ->label('Ширина')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('longitude')
                    ->label('Долгота')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('district_id')
                    ->label('Район')
                    ->relationship('district', 'title')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('underground')
                    ->label('Метро')
                    ->maxLength(255),

                Forms\Components\TextInput::make('next_to')
                    ->label('Рядом с')
                    ->maxLength(255),

                Forms\Components\TextInput::make('village')
                    ->label('Деревня')
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('email')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->maxLength(255),

        
                RichEditor::make('mini_content') // Поле для редактирования HTML-контента
                    ->label('Краткое описание') // Соответствующая подпись
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
                    ->required() // Опционально: сделать поле обязательным
                    ->disableLabel(false) // Показывать метку
                    ->placeholder('Введите HTML-контент здесь...'),

                RichEditor::make('content') // Поле для редактирования HTML-контента
                    ->label('Описание') // Соответствующая подпись
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
                    ->required() // Опционально: сделать поле обязательным
                    ->disableLabel(false) // Показывать метку
                    ->placeholder('Введите HTML-контент здесь...'),

                    
                FileUpload::make('img')
                    ->label('Картинка') // Название поля
                    ->directory('/uploads_mortuary') // Директория для сохранения
                    ->image() // Только изображения (jpg, png и т.д.)
                    ->maxSize(2048) // Максимальный размер файла в КБ
                    ->required()
                    ->afterStateUpdated(function ($set, $state, $record) {
                        if ($state && $record) {
                            
                            // Получаем только имя файла (без директории)
                            $filename = basename($state);
                            
                            // Обновляем запись в базе данных, сохраняя только имя файла
                            $record->update([
                                'href_img' => 0, // Или любое другое значение
                            ]);
                        }
                    }),
                    Forms\Components\TextInput::make('adres')
                    ->label('Адрес')
                    ->required()
                    ->maxLength(255),
                
                    Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
                
                    Placeholder::make('rating')
                    ->label('Рейтинг')
                    ->content(fn ($state) => $state),
                
                                    
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
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.title')
                    ->label('Город')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMortuaries::route('/'),
            'create' => Pages\CreateMortuary::route('/create'),
            'edit' => Pages\EditMortuary::route('/{record}/edit'),
        ];
    }
}
