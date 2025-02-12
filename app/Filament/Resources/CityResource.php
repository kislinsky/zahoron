<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Filament\Resources\CityResource\RelationManagers\PriceProductPriceListRelationManager;
use App\Models\Area;
use App\Models\City;
use App\Models\Edge;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Города'; // Название в меню
    protected static ?string $navigationGroup = 'Cубъекты'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\TextInput::make('title')
                    ->label('Название')
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
                    }), // Не сохранять значение в базу данных
                
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
                    }), // Не сохранять значение в базу данных
                
                Select::make('selected_admin') // Поле для статуса
                    ->label('Выбрать город по умолчанию') // Название поля
                    ->options([
                        0 => 'Нет',
                        1 => 'Да',
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(0), // Значение по умолчанию

                Select::make('selected_form') // Поле для статуса
                    ->label('Выбрать город для отображения в форме поиска города') // Название поля
                    ->options([
                        0 => 'Нет',
                        1 => 'Да',
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(0), // Значение по умолчанию
                
            Forms\Components\TextInput::make('slug')
            ->required()
            ->unique(ignoreRecord: true) // Игнорировать текущую запись при редактировании
            ->label('Slug')
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
                Tables\Columns\TextColumn::make('area.title')
                    ->label('Округ')
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
            PriceProductPriceListRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
