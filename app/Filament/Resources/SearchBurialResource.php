<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SearchBurialResource\Pages;
use App\Models\SearchBurial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SearchBurialResource extends Resource
{
    protected static ?string $model = SearchBurial::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Поиск захоронений';
    protected static ?string $navigationGroup = 'Захоронения';

    protected static ?string $modelLabel = 'заявка на поиск захоронения';

    protected static ?string $pluralModelLabel = 'Заявки на поиск захоронений';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
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
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('date_birth')
                            ->label('Дата рождения')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('date_death')
                            ->label('Дата смерти')
                            ->maxLength(255),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\Textarea::make('location')
                            ->label('Местоположение')
                            ->columnSpanFull(),
                            
                       Forms\Components\Select::make('user_id')
    ->label('Пользователь')
    ->relationship(
        name: 'user',
        titleAttribute: 'name',
        modifyQueryUsing: fn ($query) => $query->whereNotNull('name')
    )
    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? 'Без имени')
    ->required()
    ->searchable()
    ->preload(),
                            
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                0 => 'Новая',
                                1 => 'В обработке',
                                2 => 'Выполнена',
                                3 => 'Отклонена',
                            ])
                            ->default(0)
                            ->required(),
                            
                        Forms\Components\Textarea::make('imgs')
                            ->label('Изображения')
                            ->columnSpanFull(),
                            
                        Forms\Components\Toggle::make('paid')
                            ->label('Оплачено')
                            ->default(false),
                            
                        Forms\Components\TextInput::make('price')
                            ->label('Цена')
                            ->numeric()
                            ->default(0),
                            
                        Forms\Components\Textarea::make('reason_failure')
                            ->label('Причина отказа')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('surname')
                    ->label('Фамилия')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('patronymic')
                    ->label('Отчество')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('date_birth')
                    ->label('Дата рождения')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('date_death')
                    ->label('Дата смерти')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\SelectColumn::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новая',
                        1 => 'В обработке',
                        2 => 'Выполнена',
                        3 => 'Отклонена',
                    ]),
                    
                Tables\Columns\IconColumn::make('paid')
                    ->label('Оплата')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable(),
                    
                    ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новая',
                        1 => 'В обработке',
                        2 => 'Выполнена',
                        3 => 'Отклонена',
                    ]),
                    
                Tables\Filters\Filter::make('paid')
                    ->label('Оплачено')
                    ->query(fn ($query) => $query->where('paid', true)),
                    
                Tables\Filters\Filter::make('not_paid')
                    ->label('Не оплачено')
                    ->query(fn ($query) => $query->where('paid', false)),
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

    public static function getRelations(): array
    {
        return [
            // Добавьте отношения при необходимости
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSearchBurials::route('/'),
            'create' => Pages\CreateSearchBurial::route('/create'),
            'edit' => Pages\EditSearchBurial::route('/{record}/edit'),
        ];
    }
}