<?php
namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use App\Models\Area;
use App\Models\City;
use App\Models\Edge;
use Filament\Tables;
use App\Models\Cemetery;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\CemeteryResource\Pages;
use App\Filament\Resources\CemeteryResource\RelationManagers\PriceServiceRelationManager;

class CemeteryResource extends Resource
{
    protected static ?string $model = Cemetery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Кладбища'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                
                Forms\Components\TextInput::make('width')
                    ->label('Ширина')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('longitude')
                    ->label('Долгота')
                    ->required()
                    ->maxLength(255),



                    Select::make('edge_id')
                    ->label('Край')
                    ->options(Edge::all()->pluck('title', 'id')) // Список всех краёв
                    ->searchable() // Добавляем поиск по тексту
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('area_id', null); // Сбрасываем значение района при изменении края
                        $set('city_id', null); // Сбрасываем значение города при изменении края
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        // Устанавливаем начальное значение для edge_id при редактировании
                        if ($record && $record->city && $record->city->area) {
                            $component->state($record->city->area->edge_id);
                        }
                    })
                    ->dehydrated(false), // Не сохранять значение в базу данных
                
                Select::make('area_id')
                    ->label('Район')
                    ->options(function ($get) {
                        $edgeId = $get('edge_id'); // Получаем выбранный край
                
                        if (!$edgeId) {
                            return []; // Если край не выбран, возвращаем пустой список
                        }
                
                        // Возвращаем список районов, привязанных к выбранному краю
                        return Area::where('edge_id', $edgeId)->pluck('title', 'id');
                    })
                    ->searchable() // Добавляем поиск по тексту
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('city_id', null); // Сбрасываем значение города при изменении района
                    })
                    ->afterStateHydrated(function (Select $component, $record) {
                        // Устанавливаем начальное значение для area_id при редактировании
                        if ($record && $record->city) {
                            $component->state($record->city->area_id);
                        }
                    })
                    ->dehydrated(false), // Не сохранять значение в базу данных
                
                Select::make('city_id')
                    ->label('Город')
                    ->options(function ($get) {
                        $areaId = $get('area_id'); // Получаем выбранный район
                
                        if (!$areaId) {
                            return []; // Если район не выбран, возвращаем пустой список
                        }
                
                        // Возвращаем список городов, привязанных к выбранному району
                        return City::where('area_id', $areaId)->pluck('title', 'id');
                    })
                    ->searchable() // Добавляем поиск по тексту
                    ->required() // Город обязателен для выбора
                    ->afterStateHydrated(function (Select $component, $record) {
                        // Устанавливаем начальное значение для city_id при редактировании
                        if ($record) {
                            $component->state($record->city_id);
                        }
                    }),







                    Forms\Components\TextInput::make('adres')
                    ->label('Ориентир')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('price_burial_location')
                    ->label('Цена за геопозицию')
                    ->numeric()
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('square')
                    ->label('Площадь')
                    ->maxLength(255),

                Forms\Components\TextInput::make('responsible')
                    ->label('Отвественный')
                    ->maxLength(255),


                Forms\Components\TextInput::make('cadastral_number')
                    ->label('кадастровый номер')
                    ->maxLength(1000),


                    Forms\Components\TextInput::make('cost_sponsorship_call')
                    ->label('Стоимость спонсорства звонка с кладбища')
                    ->maxLength(1000),
                    

               


               

                Forms\Components\TextInput::make('email')
                    ->label('email')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->maxLength(255),

            
                    FileUpload::make('img')
                    ->label('Картинка') // Название поля
                    ->directory('/uploads_cemeteries') // Директория для сохранения
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
            PriceServiceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCemeteries::route('/'),
            'create' => Pages\CreateCemetery::route('/create'),
            'edit' => Pages\EditCemetery::route('/{record}/edit'),
        ];
    }
}