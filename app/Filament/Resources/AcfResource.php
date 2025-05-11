<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcfResource\Pages;
use App\Filament\Resources\AcfResource\RelationManagers;
use App\Models\Acf;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcfResource extends Resource
{
    protected static ?string $model = Acf::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Доп поля страниц'; // Название в меню
    protected static ?string $navigationGroup = 'Страницы сайта'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Название')
                ->required(),

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
                ->disableLabel(false) // Показывать метку
                ->placeholder('Введите HTML-контент здесь...'),

                Forms\Components\Select::make('page_id')
                    ->label('Страница')
                    ->relationship('page', 'title_ru')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                // ->maxLength(255),Forms\Components\TextInput::make('title_ru')
                // ->label('Название')
                // ->required()
                // ->maxLength(255),
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
                ->label('Название')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('page.title_ru')
                ->label('Страница')
                ->searchable()
                ->sortable(),
                // Tables\Columns\TextColumn::make('title_ru')
                // ->label('Название')
                // ->searchable()
                // ->sortable(),
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
            'index' => Pages\ListAcfs::route('/'),
            'create' => Pages\CreateAcf::route('/create'),
            'edit' => Pages\EditAcf::route('/{record}/edit'),
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
