<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    protected static ?string $title = 'История переписки';

    protected static ?string $recordTitleAttribute = 'message';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(auth()->id()),
                
                Forms\Components\Textarea::make('message')
                    ->label('Сообщение')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                
                Forms\Components\Toggle::make('is_internal')
                    ->label('Внутренний комментарий')
                    ->helperText('Виден только сотрудникам поддержки')
                    ->default(false),
                
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Дата создания')
                    ->displayFormat('d.m.Y H:i')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(100)
                    ->html()
                    ->formatStateUsing(fn ($state) => nl2br(e($state))),
                
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Внутренний')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-chat-bubble-left'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_internal')
                    ->label('Тип сообщения')
                    ->options([
                        '0' => 'Публичные',
                        '1' => 'Внутренние',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить ответ')
                    ->icon('heroicon-o-plus')
                    ->after(function ($record) {
                        $ticket = $this->getOwnerRecord();
                        
                        Log::info('=== FILAMENT REPLY CREATED ===');
                        Log::info('Reply ID: ' . $record->id);
                        Log::info('Reply User ID: ' . $record->user_id);
                        Log::info('Ticket User ID: ' . $ticket->user_id);
                        Log::info('Ticket Assigned To: ' . $ticket->assigned_to);
                        Log::info('Is Internal: ' . $record->is_internal);
                        
                        try {
                            // Если это не внутреннее сообщение
                            if (!$record->is_internal) {
                                // Если ответил не автор тикета - уведомляем автора
                                if ($record->user_id != $ticket->user_id) {
                                    \App\Models\Notification::create([
                                        'user_id' => $ticket->user_id,
                                        'organization_id' => null,
                                        'type' => 'ticket_reply',
                                        'title' => 'Новый ответ в тикете',
                                        'message' => "Новый ответ в тикете: {$ticket->subject}",
                                        'is_read' => false
                                    ]);
                                    Log::info('Notification created for ticket author');
                                }
                                // Если автор тикета ответил, уведомляем назначенного сотрудника
                                elseif ($record->user_id == $ticket->user_id && $ticket->assigned_to) {
                                    \App\Models\Notification::create([
                                        'user_id' => $ticket->assigned_to,
                                        'organization_id' => null,
                                        'type' => 'ticket_reply',
                                        'title' => 'Новый ответ клиента',
                                        'message' => "Клиент ответил в тикете: {$ticket->subject}",
                                        'is_read' => false
                                    ]);
                                    Log::info('Notification created for assigned staff');
                                }
                            } 
                            // Если это внутреннее сообщение
                            else {
                                // Уведомляем назначенного сотрудника (если ответил не он)
                                if ($ticket->assigned_to && $record->user_id != $ticket->assigned_to) {
                                    \App\Models\Notification::create([
                                        'user_id' => $ticket->assigned_to,
                                        'organization_id' => null,
                                        'type' => 'ticket_internal_reply',
                                        'title' => 'Внутренний комментарий',
                                        'message' => "Новый внутренний комментарий в тикете: {$ticket->subject}",
                                        'is_read' => false
                                    ]);
                                    Log::info('Internal notification created for assigned staff');
                                }
                                
                                // Уведомляем автора тикета (если это не он)
                                if ($record->user_id != $ticket->user_id) {
                                    \App\Models\Notification::create([
                                        'user_id' => $ticket->user_id,
                                        'organization_id' => null,
                                        'type' => 'ticket_internal_reply',
                                        'title' => 'Внутренний комментарий',
                                        'message' => "Новый внутренний комментарий в вашем тикете: {$ticket->subject}",
                                        'is_read' => false
                                    ]);
                                    Log::info('Internal notification created for ticket author');
                                }
                            }
                            
                            Log::info('Notifications created successfully');
                        } catch (\Exception $e) {
                            Log::error('Error creating notification: ' . $e->getMessage());
                            Log::error('Trace: ' . $e->getTraceAsString());
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}