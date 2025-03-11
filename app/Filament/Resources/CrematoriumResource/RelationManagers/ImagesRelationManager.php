<?php

namespace App\Filament\Resources\CrematoriumResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'Галерея';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('href_img')
            ->label('Выберите источник изображения')
            ->options([
                0 => 'Файл на сайте',
                1 => 'Ссылка (URL)'
            ])
            ->inline()
            ->live(), // Автоматически обновляет форму при изменении

        // Поле для ссылки (отображается только если выбран вариант "Ссылка")
        TextInput::make('img_url')
            ->label('Ссылка на изображение')
            ->placeholder('https://example.com/image.jpg')
            ->reactive()
            ->required(fn ($get) => intval($get('href_img')) === 1)
            ->hidden(fn ($get) => intval($get('href_img')) === 0), // Скрыто, если выбрано "Файл"

        // Поле для загрузки файла (отображается только если выбран вариант "Файл на сайте")
        FileUpload::make('img_file')
            ->label('Загрузить изображение')
            ->directory('/uploads_crematorium') // Директория для хранения файлов
            ->image()
            ->maxSize(2048)
            ->reactive()
            ->required(fn ($get) => intval($get('href_img')) === 0)
            ->hidden(fn ($get) => intval($get('href_img')) === 1), // Скрыто, если выбрано "Ссылка"

        // Отображение текущего изображения (если запись уже существует)
        View::make('image')
            ->label('Текущее изображение')
            ->view('filament.forms.components.custom-image') // Указываем путь к Blade-шаблону
            ->extraAttributes(['class' => 'custom-image-class'])
            ->columnSpan('full')
            ->hidden(fn ($get) => intval($get('href_img')) === 0), 

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('images')
            ->columns([
                Tables\Columns\ImageColumn::make('image') // Используем ImageColumn
                ->label('Фотография')
                ->size(100)
                ->getStateUsing(function ($record) {
                    // Если href_img равен 1, используем img_url
                    if ($record->href_img == 1) {
                        return $record->img_url; // Возвращаем URL изображения
                    }
                    // Иначе используем img_file (путь к файлу)
                    return $record->img_file; // Возвращаем путь к файлу
                }),
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
