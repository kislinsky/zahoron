<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderProductResource\Pages;
use App\Filament\Resources\OrderProductResource\RelationManagers;
use App\Models\OrderProduct;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Заказы товаров'; // Название в меню
    protected static ?string $navigationGroup = 'Заказы'; // Указываем группу

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Дополнительная информация
                TextInput::make('additional')
                    ->label('Дополнительно')
                    ->nullable(),

                // Товар
                Select::make('product_id')
                    ->label('Товар')
                    ->relationship('product', 'title') // Предполагается, что у вас есть модель Product
                    ->required(),

                // Пользователь
                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name') // Предполагается, что у вас есть модель User
                    ->required(),

                // Комментарий клиента
                Textarea::make('customer_comment')
                    ->label('Комментарий клиента')
                    ->nullable(),

                // Количество
                TextInput::make('count')
                    ->label('Количество')
                    ->numeric()
                    ->required(),

                // Цена
                TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required(),

                // Размер
                TextInput::make('size')
                    ->label('Размер')
                    ->nullable(),

                // Статус
                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                    ])
                    ->default(0)
                    ->required(),

                // Кладбище
                Select::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title') // Предполагается, что у вас есть модель Cemetery
                    ->nullable(),

                // Дата
                TextInput::make('date')
                    ->label('Дата')
                    ->type('date')
                    ->nullable(),

                // Время
                TextInput::make('time')
                    ->label('Время')
                    ->nullable(),

                // Морг
                Select::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title') // Предполагается, что у вас есть модель Mortuary
                    ->nullable(),

                // Город отправления
                TextInput::make('city_from')
                    ->label('Город отправления')
                    ->nullable(),

                // Город назначения
                TextInput::make('city_to')
                    ->label('Город назначения')
                    ->nullable(),


                // Организация
                Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title') // Предполагается, что у вас есть модель Organization
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ID
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Товар
                TextColumn::make('product.title')
                    ->label('Товар')
                    ->sortable(),

                // Пользователь
                TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable(),

                
                // Цена
                TextColumn::make('price')
                    ->label('Цена')
                    ->sortable(),

                // Статус
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Новый',
                        1 => 'В работе',
                        2 => 'Завершён',
                    }),

              

                // Дата
                TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y'),



                // Организация
                TextColumn::make('organization.title')
                    ->label('Организация')
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
                    ]),

                // Фильтр по кладбищу
                Tables\Filters\SelectFilter::make('cemetery_id')
                    ->label('Кладбище')
                    ->relationship('cemetery', 'title'),

                // Фильтр по моргу
                Tables\Filters\SelectFilter::make('mortuary_id')
                    ->label('Морг')
                    ->relationship('mortuary', 'title'),

                // Фильтр по организации
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderProducts::route('/'),
            'create' => Pages\CreateOrderProduct::route('/create'),
            'edit' => Pages\EditOrderProduct::route('/{record}/edit'),
        ];
    }
}
