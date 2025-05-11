<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderPorductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderPorducts';
    protected static ?string $title = 'Заказы с маркетплэйса';

    public function form(Form $form): Form
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


                TextInput::make('map_link')
                    ->label('Ссылка на товар')
                    ->disabled()
                    ->suffixAction(
                        Action::make('open_map')
                            ->button() // Отобразить как кнопку
                            ->label('Страница товара')
                            ->icon('heroicon-s-eye') // Иконка глаза
                            // Текст кнопки
                            ->url(function ($record) {
                                // Используем $record для получения текущего продукта
                                return '/'.selectCity()->slug."/admin/products/$record->product_id/edit"; // Возвращаем URL продукта
                            })
                            ->openUrlInNewTab()
                        ),

                // Пользователь
                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // Предполагается, что у вас есть модель User
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


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('orderPorducts')
            ->columns([
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
