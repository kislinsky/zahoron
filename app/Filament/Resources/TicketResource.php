<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Техподдержка';

    protected static ?string $modelLabel = 'тикет';

    protected static ?string $pluralModelLabel = 'Тикеты';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Информация о тикете')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Тема')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->readOnly(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->options(
                                User::all()
                                    ->mapWithKeys(fn ($user) => [
                                        $user->id => $user->name ?? 'Пользователь #' . $user->id
                                    ])
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Select::make('category_id')
                            ->label('Категория')
                            ->options(TicketCategory::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('priority_id')
                            ->label('Приоритет')
                            ->options(TicketPriority::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('status_id')
                            ->label('Статус')
                            ->options(TicketStatus::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('assigned_to')
                            ->label('Назначен')
                            ->options(
                                User::whereIn('role', ['admin', 'manager', 'support'])
                                    ->get()
                                    ->mapWithKeys(fn ($user) => [
                                        $user->id => $user->name ?? 'Сотрудник #' . $user->id
                                    ])
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Создан')
                            ->displayFormat('d.m.Y H:i')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label('Закрыт')
                            ->nullable(),
                    ])
                    ->columns(2),
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
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('subject')
                    ->label('Тема')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->subject),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?? 'Не указан'),
                
                Tables\Columns\BadgeColumn::make('category.name')
                    ->label('Категория')
                    ->color('primary')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('priority.name')
                    ->label('Приоритет')
                    ->color(fn ($state) => match($state) {
                        'Низкий' => 'success',
                        'Средний' => 'warning', 
                        'Высокий' => 'danger',
                        default => 'gray'
                    })
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('status.name')
                    ->label('Статус')
                    ->color(fn ($state) => match($state) {
                        'Открыт' => 'primary',
                        'В работе' => 'warning',
                        'Решен' => 'success',
                        'Закрыт' => 'gray',
                        default => 'gray'
                    })
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Назначен')
                    ->placeholder('Не назначен')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('closed_at')
                    ->label('Закрыт')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Активен')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Категория'),
                
                Tables\Filters\SelectFilter::make('priority_id')
                    ->relationship('priority', 'name')
                    ->label('Приоритет'),
                
                Tables\Filters\SelectFilter::make('status_id')
                    ->relationship('status', 'name')
                    ->label('Статус'),
                
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Назначен')
                    ->options(
                        User::whereIn('role', ['admin', 'manager', 'support'])
                            ->get()
                            ->mapWithKeys(fn ($user) => [
                                $user->id => $user->name ?? 'Сотрудник #' . $user->id
                            ])
                            ->toArray()
                    )
                    ->searchable(),
                
                Tables\Filters\Filter::make('closed')
                    ->label('Только закрытые')
                    ->query(fn (Builder $query) => $query->whereNotNull('closed_at')),
                
                Tables\Filters\Filter::make('open')
                    ->label('Только открытые')
                    ->query(fn (Builder $query) => $query->whereNull('closed_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Просмотр'),
                
                Tables\Actions\EditAction::make()
                    ->label('Редактировать')
                    ->after(function ($record, $data) {
                        // Уведомление при изменении статуса
                        if (isset($data['status_id']) && $record->wasChanged('status_id')) {
                            $status = $record->status;
                            \App\Models\Notification::create([
                                'user_id' => $record->user_id,
                                'organization_id' => null,
                                'type' => 'ticket_status',
                                'title' => 'Статус тикета изменен',
                                'message' => "Статус вашего тикета '{$record->subject}' изменен",
                                'is_read' => false
                            ]);
                        }
                        
                        // Уведомление при изменении назначенного сотрудника
                        if (isset($data['assigned_to']) && $record->wasChanged('assigned_to')) {
                            if ($record->assigned_to) {
                                // Уведомляем нового назначенного сотрудника
                                \App\Models\Notification::create([
                                    'user_id' => $record->assigned_to,
                                    'organization_id' => null,
                                    'type' => 'ticket_assigned',
                                    'title' => 'Тикет назначен',
                                    'message' => "Вам назначен тикет: {$record->subject}",
                                    'is_read' => false
                                ]);
                            }
                            
                            // Уведомляем автора об изменении назначения
                            \App\Models\Notification::create([
                                'user_id' => $record->user_id,
                                'organization_id' => null,
                                'type' => 'ticket_assigned',
                                'title' => 'Назначен исполнитель',
                                'message' => "Для вашего тикета '{$record->subject}' назначен исполнитель",
                                'is_read' => false
                            ]);
                        }
                    }),
                
                Tables\Actions\Action::make('close')
                    ->label('Закрыть')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->action(function (Ticket $record) {
                        $record->update(['closed_at' => now()]);
                        
                        // Уведомление о закрытии тикета
                        \App\Models\Notification::create([
                            'user_id' => $record->user_id,
                            'organization_id' => null,
                            'type' => 'ticket_closed',
                            'title' => 'Тикет закрыт',
                            'message' => "Ваш тикет '{$record->subject}' был закрыт",
                            'is_read' => false
                        ]);
                    })
                    ->hidden(fn (Ticket $record) => $record->closed_at !== null)
                    ->requiresConfirmation(),
                
                Tables\Actions\Action::make('reopen')
                    ->label('Открыть')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->action(function (Ticket $record) {
                        $record->update(['closed_at' => null]);
                        
                        // Уведомление об открытии тикета
                        \App\Models\Notification::create([
                            'user_id' => $record->user_id,
                            'organization_id' => null,
                            'type' => 'ticket_reopened',
                            'title' => 'Тикет открыт',
                            'message' => "Ваш тикет '{$record->subject}' был открыт",
                            'is_read' => false
                        ]);
                    })
                    ->hidden(fn (Ticket $record) => $record->closed_at === null)
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('closeSelected')
                        ->label('Закрыть выбранные')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['closed_at' => now()]);
                                
                                // Уведомление о закрытии тикета
                                \App\Models\Notification::create([
                                    'user_id' => $record->user_id,
                                    'organization_id' => null,
                                    'type' => 'ticket_closed',
                                    'title' => 'Тикет закрыт',
                                    'message' => "Ваш тикет '{$record->subject}' был закрыт",
                                    'is_read' => false
                                ]);
                            }
                        })
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('reopenSelected')
                        ->label('Открыть выбранные')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['closed_at' => null]);
                                
                                // Уведомление об открытии тикета
                                \App\Models\Notification::create([
                                    'user_id' => $record->user_id,
                                    'organization_id' => null,
                                    'type' => 'ticket_reopened',
                                    'title' => 'Тикет открыт',
                                    'message' => "Ваш тикет '{$record->subject}' был открыт",
                                    'is_read' => false
                                ]);
                            }
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'category', 'priority', 'status', 'assignedTo'])
            ->withoutGlobalScopes();
    }

    public static function canCreate(): bool
    {
        return false;
    }
}