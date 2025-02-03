<?php

namespace App\Filament\Resources\ProductPriceListResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StagesRelationManager extends RelationManager
{
    protected static string $relationship = 'stages';
    protected static ?string $title = 'Этапы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Название')
                ->required()
                ->maxLength(255),
                
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
                ->directory('/uploads_product_price_list') // Директория для сохранения
                ->image() // Только изображения (jpg, png и т.д.)
                ->maxSize(2048) // Максимальный размер файла в КБ
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('stages')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
