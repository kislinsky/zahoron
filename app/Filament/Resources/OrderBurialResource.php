<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderBurialResource\Pages;
use App\Filament\Resources\OrderBurialResource\RelationManagers;
use App\Models\OrderBurial;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderBurialResource extends Resource
{
    protected static ?string $model = OrderBurial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Заказы геолокаций'; // Название в меню
    protected static ?string $navigationGroup = 'Заказы'; // Указываем группу
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('burial_id')
                    ->label('Захоронение')
                    ->relationship('burial', 'id') // предполагается, что у вас есть модель Burial
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'id') // предполагается, что у вас есть модель User
                    ->required(),
                    Select::make('status') // Поле для статуса
                    ->label('Статус') // Название поля
                    ->options([
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    ])
                    ->required() // Поле обязательно для заполнения
                    ->default(1), // Значение по умолчанию
                Forms\Components\Textarea::make('customer_comment')
                    ->label('Комментарий')
                    ->nullable(),
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('date_pay')
                    ->label('Дата оплаты')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->sortable(),
                
                

                Tables\Columns\TextColumn::make('burial.id')
                ->label('Захоронение')
                ->sortable(),

                Tables\Columns\TextColumn::make('user.id')
                ->label('Пользователь')
                ->sortable(),


                Tables\Columns\TextColumn::make('price')
                ->label('Цена')
                ->sortable(),

                Tables\Columns\TextColumn::make('date_pay')
                ->label('Дата оплаты')
                ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                ->label('Дата создания')
                ->dateTime('d.m.Y'),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Не оплачен',
                        1 => 'Оплачен',
                    }),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderBurials::route('/'),
            'create' => Pages\CreateOrderBurial::route('/create'),
            'edit' => Pages\EditOrderBurial::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'deputy-admin' || auth()->user()->role === 'manager' ;
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function canCreate(): bool
    {   
        return false;
    }
}
