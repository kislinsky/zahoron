<?php

namespace App\Filament\Resources\WalletResource\RelationManagers;

use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'История транзакций';

    protected static ?string $modelLabel = 'транзакцию';

    protected static ?string $pluralModelLabel = 'транзакции';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Тип операции')
                    ->required()
                    ->options([
                        'deposit' => 'Пополнение',
                        'withdrawal' => 'Списание',
                        'refund' => 'Возврат',
                        'payment' => 'Оплата',
                        'transfer' => 'Перевод',
                    ])
                    ->default('deposit'),
                
                Forms\Components\TextInput::make('amount')
                    ->label('Сумма')
                    ->required()
                    ->numeric()
                    ->prefix('₽')
                    ->minValue(0.01)
                    ->step(0.01),
                
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->required()
                    ->options([
                        'pending' => 'В ожидании',
                        'completed' => 'Завершена',
                        'failed' => 'Неудачная',
                        'cancelled' => 'Отменена',
                    ])
                    ->default('completed'),
                
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Описание транзакции'),
                
                Forms\Components\KeyValue::make('meta')
                    ->label('Метаданные')
                    ->keyLabel('Ключ')
                    ->valueLabel('Значение')
                    ->addable()
                    ->deletable()
                    ->editableKeys()
                    ->editableValues(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->sortable()
                    ->money('RUB')
                    ->alignEnd()
                    ->color(fn (Transaction $record): string => 
                        $record->type === 'deposit' ? 'success' : 'danger'),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'deposit' => 'Пополнение',
                        'withdrawal' => 'Списание',
                        'refund' => 'Возврат',
                        'payment' => 'Оплата',
                        'transfer' => 'Перевод',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'deposit',
                        'danger' => 'withdrawal',
                        'warning' => 'refund',
                        'info' => 'payment',
                        'primary' => 'transfer',
                    ])
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'В ожидании',
                        'completed' => 'Завершена',
                        'failed' => 'Неудачная',
                        'cancelled' => 'Отменена',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'cancelled',
                    ])
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Transaction $record): string {
                        return $record->description ?? '';
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип операции')
                    ->options([
                        'deposit' => 'Пополнение',
                        'withdrawal' => 'Списание',
                        'refund' => 'Возврат',
                        'payment' => 'Оплата',
                        'transfer' => 'Перевод',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'В ожидании',
                        'completed' => 'Завершена',
                        'failed' => 'Неудачная',
                        'cancelled' => 'Отменена',
                    ])
                    ->multiple(),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('С'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить транзакцию')
                    ->modalHeading('Новая транзакция'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Редактировать транзакцию'),
                
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Удалить транзакцию'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удалить выбранные транзакции'),
                ]),
            ]);
    }
}