<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductPriceListResource\Pages;
use App\Filament\Resources\ProductPriceListResource\RelationManagers;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\AdvantagesRelationManager;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\AdvicesRelationManager;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\FaqsRelationManager;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\ImgsServiceRelationManager;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\PriceProductPriceListRelationManager;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\StagesRelationManager;
use App\Filament\Resources\ProductPriceListResource\RelationManagers\VariantsRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\ViewsRelationManager;
use App\Models\ProductPriceList;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductPriceListResource extends Resource
{
    protected static ?string $model = ProductPriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Продукты прйс-листа'; // Название в меню


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Поле "title"
                TextInput::make('title')
                ->label('Название фирмы')
                ->required()
                ->live(debounce: 1000) // Задержка автообновления
                ->afterStateUpdated(function ($state, $set, $get) {
                    // Проверяем, если длина title больше 3 символов, обновляем slug
                    if (!empty($state) && strlen($state) > 3) {
                        $set('slug', generateUniqueSlug($state, ProductPriceList::class, $get('id')));
                    }
                }),
            
            TextInput::make('slug')
                ->required()
                ->label('Slug')
                ->maxLength(255)
                ->unique(ignoreRecord: true) // Проверка уникальности
                ->formatStateUsing(fn ($state) => slug($state)) // Форматируем slug
                ->dehydrateStateUsing(fn ($state, $get) => generateUniqueSlug($state, ProductPriceList::class, $get('id'))),
    
    
                TextInput::make('price')
                ->label('Цена')
                ->required(),
    
                Radio::make('view')
                ->label('Отображение продукта')
                ->options([
                    0 => 'Не показывать',
                    1 => 'Показывать'
                ])
                ->inline(),

                    // Поле "category_id"
                Select::make('category_id')
                ->label('Категория')
                ->required()
                ->relationship('category', 'title'), // Убедитесь, что у вас есть связь "category"

               
    
               
    
                // Поле "excerpt"
                RichEditor::make('excerpt')
                    ->label('Краткое описание')
                    ->required(),
    
                // Поле "content"
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
    
                
    
                // Поле "text_before_video_1"
                RichEditor::make('text_before_video_1')
                    ->label('Текст перед видео 1')
                    ->nullable(),
    
                // Поле "text_after_video_1"
                RichEditor::make('text_after_video_1')
                    ->label('Текст после видео 1')
                    ->nullable(),
    
                // Поле "video_1"
                RichEditor::make('video_1')
                    ->label('Видео 1')
                    ->nullable(),
    
                // Поле "text_before_videos"
                RichEditor::make('text_before_videos')
                    ->label('Текст перед всеми видео')
                    ->nullable(),
    
                // Поле "text_after_videos"
                RichEditor::make('text_after_videos')
                    ->label('Текст после видео')
                    ->nullable(),
    
                // Поле "text_images"
                RichEditor::make('text_images')
                    ->label('Текст изображений')
                    ->nullable(),
    
                // Поле "text_advantages"
                RichEditor::make('text_advantages')
                    ->label('Текст преимуществ')
                    ->nullable(),
    
                // Поле "video_2"
                RichEditor::make('video_2')
                    ->label('Видео 2')
                    ->nullable(),
    
                // Поле "text_how_make"
                RichEditor::make('text_how_make')
                    ->label('Текст "Как сделать"')
                    ->nullable(),
    
                // Поле "title_variants"
                RichEditor::make('title_variants')
                    ->label('Заголовок вариантов')
                    ->nullable(),
    
                // Поле "text_variants"
                RichEditor::make('text_variants')
                    ->label('Текст для вариантов')
                    ->nullable(),
    
                // Поле "title_advice"
                RichEditor::make('title_advice')
                    ->label('Заголовок советов')
                    ->nullable(),
    
                // Поле "icon_white"
                TextInput::make('icon_white')
                    ->label('Белая иконка')
                    ->nullable()
                    ->maxLength(255),
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
            PriceProductPriceListRelationManager::class,
            AdvantagesRelationManager::class,
            AdvicesRelationManager::class,
            FaqsRelationManager::class,
            ImgsServiceRelationManager::class,
            StagesRelationManager::class,
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductPriceLists::route('/'),
            'create' => Pages\CreateProductPriceList::route('/create'),
            'edit' => Pages\EditProductPriceList::route('/{record}/edit'),
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
