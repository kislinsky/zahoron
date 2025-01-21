<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Burial;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BurialResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BurialResource\RelationManagers;
use App\Filament\Resources\RelationManagers\ImageMemorialsRelationManager;
use App\Filament\Resources\RelationManagers\ImagePersonalsRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\WordsMemoryRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\ImageMonumentRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\ImagePersonalRelationManager;
use App\Filament\Resources\BurialResource\RelationManagers\LifeStoryBurialRelationManager;

class BurialResource extends Resource
{
    protected static ?string $model = Burial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Захоронения'; // Название в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('surname')
                    ->label('Фамилия')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('patronymic')
                    ->label('Отчество')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('date_death')
                    ->label('Дата смерти')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('date_birth')
                    ->label('дата рождения')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title')
                    ->searchable()
                    ->required()
                    ->preload(),

                

                Forms\Components\TextInput::make('location_death')
                    ->label('место смерти')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('who')
                    ->label('Вид захоронения')
                    ->required()
                    ->maxLength(255),
                    

                Forms\Components\TextInput::make('information')
                    ->label('Информация')
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique()
                    ->label('slug')
                    ->maxLength(255),
                    
                Select::make('status') // Поле для статуса
                    ->label('Статус') // Название поля
                    ->options([
                        0 => 'Не распознан', // Значение 1 с названием "Раз"
                        1 => 'Распознан', // Значение 2 с названием "Два"
                        2 => 'Отправлен на проверку', // Значение 3 с названием "Три"
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1) // Значение
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
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cemetery.title')
                    ->label('Кладбище')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('who')
                    ->label('Вид захоронения')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не распознан',
                        1 => 'Распознан',
                        2 => 'Отправлен на проверку',
                        default => 'Неизвестно',
                    }),
                    
            ])
            ->filters([
                SelectFilter::make('status')
                ->label('Статус')
                ->options([
                    0 => 'Не распознан',
                    1 => 'Распознан',
                    2 => 'Отправлен на проверку',
                ]),
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
            // Добавляем связи для вывода фотографий
            ImagePersonalRelationManager::class,
            WordsMemoryRelationManager::class,
            ImageMonumentRelationManager::class,
            LifeStoryBurialRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBurials::route('/'),
            'create' => Pages\CreateBurial::route('/create'),
            'edit' => Pages\EditBurial::route('/{record}/edit'),
        ];
    }
}
