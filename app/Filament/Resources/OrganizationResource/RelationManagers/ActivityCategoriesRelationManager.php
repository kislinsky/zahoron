<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryProduct;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ActivityCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'activityCategories';
    protected static ?string $title = 'Чем занимается';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_main_id')
                ->label('Категория')
                ->options(CategoryProduct::where('parent_id',null)->pluck('title', 'id')) // Список всех категорий
                ->searchable() // Добавляем поиск по тексту
                ->reactive() // Делаем поле реактивным
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('category_children_id', null); // Сбрасываем значение подкатегории при изменении категории
                })
                ->afterStateHydrated(function (Select $component, $record) {
                    // Устанавливаем начальное значение для category_id при редактировании
                    if ($record && $record->subcategory) {
                        $component->state($record->subcategory->category_id);
                    }
                }), 

            Select::make('category_children_id')
                ->label('Подкатегория')
                ->options(function ($get) {
                    $categoryId = $get('category_main_id'); // Получаем выбранную категорию

                    if (!$categoryId) {
                        return []; // Если категория не выбрана, возвращаем пустой список
                    }

                    // Возвращаем список подкатегорий, привязанных к выбранной категории
                    return CategoryProduct::where('parent_id', $categoryId)->pluck('title', 'id');
                })
                ->searchable() // Добавляем поиск по тексту
                ->required() // Подкатегория обязательна для выбора
                ->afterStateHydrated(function (Select $component, $record) {
                    // Устанавливаем начальное значение для subcategory_id при редактировании
                    if ($record) {
                        $component->state($record->category_children_id);
                    }
                }),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->maxLength(255),
                
             
    
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('activityCategories')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('categoryProduct.title')
                ->label('Категория')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('price')
                ->label('Цена')
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
