<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use App\Models\CategoryProductPriceList;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeatificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'beatifications';
    protected static ?string $title = 'Pop up облогораживание';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // Предполагается, что у вас есть модель User
                    ->required(),

                    
                    Select::make('products_id')
                    ->label('Услуги')
                    ->options(CategoryProductPriceList::where('parent_id','!=',null)->pluck('title', 'id')) // Загрузка всех услуг
                    ->multiple() // Множественный выбор
                    ->preload() // Предзагрузка данных
                    ->searchable() // Поиск услуг
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (CategoryProductPriceList $service) => $service->title) // Отображение названия услуги
                    ->afterStateHydrated(function (Select $component, $state) {
                        // Преобразуем JSON в массив для отображения выбранных услуг
                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }
                        $component->state($state);
                    })
                    ->dehydrateStateUsing(fn ($state) => json_encode($state)), // Сохраняем в виде JSON



                // Кладбище
                Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->searchable() // Поиск услуг

                    ->relationship('cemetery', 'title') // Предполагается, что у вас есть модель Cemetery
                    ->nullable(),

                // Статус
                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                        4 => 'Архив',
                    ])
                    ->default(0)
                    ->required(),

                // Город
                Select::make('city_id')
                ->searchable() // Поиск услуг

                    ->label('Город')
                    ->relationship('city', 'title') // Предполагается, что у вас есть модель City
                    ->required(),

                // Время звонка
                TextInput::make('call_time')
                    ->label('Время звонка')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('beatifications')
            ->columns([
                TextColumn::make('id')
                ->label('ID')
                ->sortable(),


            // Пользователь
            TextColumn::make('user.name')
                ->label('Пользователь')
                ->sortable(),

          
            // Кладбище
            TextColumn::make('cemetery.title')
                ->label('Кладбище')
                ->sortable(),

            // Статус
            TextColumn::make('status')
                ->label('Статус')
                ->formatStateUsing(fn (int $state): string => match ($state) {
                    0 => 'Новый',
                    1 => 'В работе',
                    2 => 'Завершён',
                    4 => 'Архив',
                }),

            // Город
            TextColumn::make('city.title')
                ->label('Город')
                ->sortable(),

            
            // Дата создания
            TextColumn::make('created_at')
                ->label('Дата создания')
                ->dateTime('d.m.Y'),
        ])
        ->filters([
            // Фильтр по статусу
            Tables\Filters\SelectFilter::make('status')
                ->label('Статус')
                ->options([
                    0 => 'Новый',
                    1 => 'В работе',
                    2 => 'Завершён',
                    4 => 'Архив',
                ]),

            // Фильтр по городу
            Tables\Filters\SelectFilter::make('city_id')
                ->label('Город')
                ->relationship('city', 'title'),

            // Фильтр по кладбищу
            Tables\Filters\SelectFilter::make('cemetery_id')
                ->label('Кладбище')
                ->relationship('cemetery', 'title'),

          
            ])
            ->headerActions([
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
