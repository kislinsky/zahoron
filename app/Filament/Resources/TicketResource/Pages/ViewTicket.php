<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketReply;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function getTitle(): string|Htmlable
    {
        return "Просмотр тикета #{$this->record->id}";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Информация о тикете')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Тема')
                            ->readOnly()
                            ->columnSpanFull(),
                        
                        Textarea::make('description')
                            ->label('Описание')
                            ->readOnly()
                            ->rows(5)
                            ->columnSpanFull(),
                        
                        TextInput::make('user.name')
                            ->label('Пользователь')
                            ->readOnly(),
                        
                        TextInput::make('category.name')
                            ->label('Категория')
                            ->readOnly(),
                        
                        TextInput::make('priority.name')
                            ->label('Приоритет')
                            ->readOnly(),
                        
                        TextInput::make('status.name')
                            ->label('Статус')
                            ->readOnly(),
                        
                        TextInput::make('assignedTo.name')
                            ->label('Назначен')
                            ->placeholder('Не назначен')
                            ->readOnly(),
                        
                        TextInput::make('created_at')
                            ->label('Создан')
                            ->readOnly(),
                        
                        TextInput::make('closed_at')
                            ->label('Закрыт')
                            ->placeholder('Активен')
                            ->readOnly(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Редактировать'),
            
            Actions\Action::make('close')
                ->label('Закрыть тикет')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->action(function (Ticket $record) {
                    $record->update(['closed_at' => now()]);
                    $this->refreshFormData(['closed_at']);
                })
                ->hidden(fn (Ticket $record) => $record->closed_at !== null),
            
            Actions\Action::make('reopen')
                ->label('Открыть тикет')
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->action(function (Ticket $record) {
                    $record->update(['closed_at' => null]);
                    $this->refreshFormData(['closed_at']);
                })
                ->hidden(fn (Ticket $record) => $record->closed_at === null),
            
            Actions\Action::make('addReply')
                ->label('Добавить ответ')
                ->icon('heroicon-o-chat-bubble-left')
                ->color('primary')
                ->form([
                    Textarea::make('message')
                        ->label('Сообщение')
                        ->required()
                        ->rows(4),
                    
                    Toggle::make('is_internal')
                        ->label('Внутренний комментарий')
                        ->default(false),
                ])
                ->action(function (array $data, Ticket $record) {
                    TicketReply::create([
                        'ticket_id' => $record->id,
                        'user_id' => auth()->id(),
                        'message' => $data['message'],
                        'is_internal' => $data['is_internal'],
                    ]);
                    
                    $this->refreshFormData(['replies']);
                    $this->dispatch('refreshRelationManagers');
                }),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            TicketResource\RelationManagers\RepliesRelationManager::class,
        ];
    }
}