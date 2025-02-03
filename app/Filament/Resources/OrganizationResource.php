<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Organization;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Filament\Resources\OrganizationResource\RelationManagers\ProductsRelationManager;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Организации'; // Название в меню

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
                Forms\Components\TextInput::make('adres')
                ->label('Адрес')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('name_type')
                ->label('Тип организации')
                ->maxLength(255),

            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true) // Игнорировать текущую запись при редактировании
                ->label('Slug')
                ->maxLength(255),


            Forms\Components\TextInput::make('whatsapp')
                ->label('whatsapp')
                ->maxLength(255),

            Forms\Components\TextInput::make('telegram')
                ->label('telegram')
                ->maxLength(255),

            Forms\Components\TextInput::make('applications_funeral_services')
                ->label('Количество заявок на ритуальные услуги')
                ->maxLength(255),
            Forms\Components\TextInput::make('aplications_memorial')
                ->label('Количество заявок на поминки')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('calls_organization')
                ->label('Количество заявок на звонки')
                ->maxLength(255),
            Forms\Components\TextInput::make('product_requests_from_marketplace')
                ->label('Количество заявок на заказы из маркетплэйса')
                ->numeric() // Разрешить только числовые значения
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('applications_improvemen_graves')
                ->label('Количество заявок на облогораживание')
                ->numeric() // Разрешить только числовые значения
                ->required()
                ->maxLength(255),
                
            FileUpload::make('logo')
                ->label('Картинка')
                ->directory('/uploads_organization')
                ->image()
                ->maxSize(2048)
                ->required()
   
                ->afterStateUpdated(function ($set, $state, $record) {
                    if ($state && $record) {
                        // Обновляем запись в базе данных, сохраняя путь к файлу
                        $record->update([
                            'href_img' => $state, // Сохраняем путь к файлу
                        ]);
                    }
                }),

                View::make('image')
                ->label('Текущее изображение')
                ->view('filament.forms.components.custom-image-organization') // Указываем путь к Blade-шаблону
                ->extraAttributes(['class' => 'custom-image-class'])
                ->columnSpan('full'),
    
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

                
                Forms\Components\Select::make('user_id')
                ->label('id пользователя')
                ->relationship('user', 'id')
                ->searchable()
                ->preload(),

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
            ProductsRelationManager::class, // Добавляем RelationManager
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
