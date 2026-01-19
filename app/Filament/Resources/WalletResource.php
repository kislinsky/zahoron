<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Filament\Resources\WalletResource\RelationManagers;
use App\Filament\Resources\WalletResource\RelationManagers\TransactionsRelationManager;
use App\Models\User;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationGroup = 'Пользователи';

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    
    protected static ?string $navigationLabel = 'Кошельки';
    
    protected static ?string $modelLabel = 'кошелек';
    
    protected static ?string $pluralModelLabel = 'кошельки';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return User::query()
                                    ->select(['id', 'name', 'email','phone'])
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [$user->id => "{$user->name} ({$user->email}) ({$user->phone})" ];
                                    })
                                    ->toArray();
                            })
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('current', 1); // Сделать активным по умолчанию
                                    }
                                }
                            })
                            ->helperText('Выберите пользователя для кошелька'),
                        
                        Forms\Components\Toggle::make('current')
                            ->label('Активный кошелек')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Основной кошелек пользователя'),
                        
                        Forms\Components\TextInput::make('balance')
                            ->label('Баланс')
                            ->required()
                            ->numeric()
                            ->prefix('₽')
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Текущий баланс в рублях'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Операции с балансом')
                    ->description('Быстрые операции пополнения/списания')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('deposit_amount')
                                    ->label('Сумма пополнения')
                                    ->numeric()
                                    ->prefix('+')
                                    ->suffix('₽')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Сумма для пополнения баланса'),
                                
                                Forms\Components\TextInput::make('withdraw_amount')
                                    ->label('Сумма списания')
                                    ->numeric()
                                    ->prefix('-')
                                    ->suffix('₽')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Сумма для списания с баланса'),
                            ])
                            ->columns(2),
                        
                        Forms\Components\Textarea::make('operation_description')
                            ->label('Описание операции')
                            ->rows(2)
                            ->placeholder('Например: Пополнение через банковскую карту')
                            ->helperText('Причина пополнения или списания'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Wallet $record): string => $record->user->phone ?? '')
                    ->url(fn (Wallet $record) => $record->user_id 
                        ? \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $record->user_id])
                        : null)
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('balance')
                    ->label('Баланс')
                    ->sortable()
                    ->money('RUB')
                    ->alignEnd()
                    ->color(fn (Wallet $record): string => $record->balance > 0 ? 'success' : 'gray')
                    ->weight('bold')
                    ->description(fn (Wallet $record): string => $record->current ? 'Активный' : 'Не активный'),
                
                Tables\Columns\IconColumn::make('current')
                    ->label('Активный')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('Транзакции')
                    ->counts('transactions')
                    ->sortable()
                    ->alignCenter()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлен')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('balance', 'desc')
            ->filters([
                Tables\Filters\Filter::make('has_balance')
                    ->label('Только с балансом')
                    ->query(fn (Builder $query): Builder => $query->where('balance', '>', 0))
                    ->toggle(),
                
                Tables\Filters\Filter::make('zero_balance')
                    ->label('С нулевым балансом')
                    ->query(fn (Builder $query): Builder => $query->where('balance', '=', 0))
                    ->toggle(),
                
                Tables\Filters\SelectFilter::make('current')
                    ->label('Статус кошелька')
                    ->options([
                        '1' => 'Активные',
                        '0' => 'Неактивные',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Созданы с'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Созданы до'),
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Созданы с ' . \Carbon\Carbon::parse($data['created_from'])->format('d.m.Y');
                        }
                        
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Созданы до ' . \Carbon\Carbon::parse($data['created_until'])->format('d.m.Y');
                        }
                        
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('deposit')
                        ->label('Пополнить')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Сумма пополнения')
                                ->required()
                                ->numeric()
                                ->minValue(0.01)
                                ->step(0.01)
                                ->prefix('₽'),
                            
                            Forms\Components\Textarea::make('description')
                                ->label('Описание')
                                ->rows(2)
                                ->placeholder('Например: Пополнение через администратора'),
                        ])
                        ->action(function (Wallet $record, array $data) {
                            $amount = floatval($data['amount']);
                            
                            $transaction = $record->deposit($amount, [
                                'admin_id' => auth()->id(),
                                'operation_type' => 'manual_deposit',
                            ], $data['description'] ?? null);
                            
                            if ($transaction) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Баланс пополнен')
                                    ->body("Сумма: " . Number::currency($amount, 'RUB'))
                                    ->success()
                                    ->send();
                            }
                        })
                        ->modalHeading('Пополнение баланса')
                        ->modalWidth('md'),
                    
                    Tables\Actions\Action::make('withdraw')
                        ->label('Списать')
                        ->icon('heroicon-o-minus-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Сумма списания')
                                ->required()
                                ->numeric()
                                ->minValue(0.01)
                                ->step(0.01)
                                ->prefix('₽')
                                ->rule(function () {
                                    return function (string $attribute, $value, $fail) {
                                        $record = request()->route('record');
                                        if ($record && $value > $record->balance) {
                                            $fail('Сумма списания превышает баланс кошелька');
                                        }
                                    };
                                }),
                            
                            Forms\Components\Textarea::make('description')
                                ->label('Описание')
                                ->rows(2)
                                ->placeholder('Например: Списание за услуги'),
                        ])
                        ->action(function (Wallet $record, array $data) {
                            $amount = floatval($data['amount']);
                            
                            $transaction = $record->withdraw($amount, [
                                'admin_id' => auth()->id(),
                                'operation_type' => 'manual_withdrawal',
                            ], $data['description'] ?? null);
                            
                            if ($transaction) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Средства списаны')
                                    ->body("Сумма: " . Number::currency($amount, 'RUB'))
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Ошибка списания')
                                    ->body('Недостаточно средств на балансе')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalHeading('Списание средств')
                        ->modalWidth('md'),
                    
                    Tables\Actions\Action::make('set_active')
                        ->label('Сделать активным')
                        ->icon('heroicon-o-check-circle')
                        ->color('warning')
                        ->hidden(fn (Wallet $record): bool => $record->current)
                        ->action(function (Wallet $record) {
                            // Деактивируем все кошельки пользователя
                            Wallet::where('user_id', $record->user_id)
                                ->update(['current' => 0]);
                            
                            // Активируем текущий
                            $record->update(['current' => 1]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Кошелек активирован')
                                ->body('Этот кошелек теперь основной для пользователя')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Активация кошелька')
                        ->modalDescription('Этот кошелек станет основным для пользователя. Другие кошельки будут деактивированы.'),
                    
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удалить выбранные кошельки')
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('activate_wallets')
                        ->label('Активировать кошельки')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                // Деактивируем все кошельки пользователя
                                Wallet::where('user_id', $record->user_id)
                                    ->update(['current' => 0]);
                                
                                // Активируем текущий
                                $record->update(['current' => 1]);
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Кошельки активированы')
                                ->body('Выбранные кошельки стали основными для пользователей')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Массовая активация кошельков')
                        ->modalDescription('Выбранные кошельки станут основными для соответствующих пользователей. Существующие активные кошельки будут деактивированы.'),
                    
                    Tables\Actions\BulkAction::make('reset_balance')
                        ->label('Обнулить баланс')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Причина обнуления')
                                ->required()
                                ->rows(3)
                                ->placeholder('Укажите причину обнуления баланса'),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                if ($record->balance > 0) {
                                    // Создаем транзакцию списания полной суммы
                                    $record->withdraw($record->balance, [
                                        'admin_id' => auth()->id(),
                                        'operation_type' => 'balance_reset',
                                        'reason' => $data['reason'],
                                    ], 'Обнуление баланса администратором: ' . $data['reason']);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Балансы обнулены')
                                ->body('Транзакции списания созданы')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Обнуление балансов')
                        ->modalDescription('С выбранных кошельков будут списаны все средства. Это действие создаст транзакции списания.'),
                ]),
            ])
            ->emptyStateHeading('Нет кошельков')
            ->emptyStateDescription('Создайте первый кошелек для пользователя')
            ->emptyStateIcon('heroicon-o-wallet')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать кошелек')
                    ->modalHeading('Новый кошелек'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}