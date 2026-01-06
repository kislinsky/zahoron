<?php

namespace App\Filament\Widgets;

use App\Models\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class NotificationsTable extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Новые уведомления';
    protected static ?string $description = 'Непрочитанные системные уведомления';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Notification::query()
                    ->where(function ($query) {
                        $query->where('type', 'like', '%_admin')
                            ->orWhereNull('type');
                    })
                    ->where('is_read', false)
                    ->latest()
            )
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('title')
                            ->label('')
                            ->searchable()
                            ->weight('semibold')
                            ->color('gray-900')
                            ->grow(false)
                            ->description(function (Notification $record) {
                                return Str::limit($record->message, 120);
                            })
                            ->extraAttributes(['class' => 'cursor-pointer'])
                            ->action(function (Notification $record) {
                                $this->markAsReadAndView($record);
                            }),
                    ])->space(1),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('created_at')
                            ->label('')
                            ->dateTime('d.m.Y H:i')
                            ->color('gray-500')
                            ->size('sm')
                            ->alignEnd()
                            ->grow(false),
                        
                        Tables\Columns\IconColumn::make('type')
                            ->label('')
                            ->icon(fn ($state) => match($state) {
                                'call_admin' => 'heroicon-o-phone',
                                'system_admin' => 'heroicon-o-cog',
                                'alert_admin' => 'heroicon-o-exclamation-triangle',
                                default => 'heroicon-o-bell'
                            })
                            ->color(fn ($state) => match($state) {
                                'call_admin' => 'blue',
                                'system_admin' => 'orange',
                                'alert_admin' => 'red',
                                default => 'gray'
                            })
                            ->alignEnd()
                            ->grow(false)
                            ->size('sm')
                            ->tooltip(fn ($state) => str_replace('_admin', '', $state)),
                    ])->space(1),
                ]),
            ])
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsRead')
                        ->label('Отметить как прочитанные')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_read' => true]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Уведомления отмечены как прочитанные'),

                    Tables\Actions\BulkAction::make('delete')
                        ->label('Удалить выбранные')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->delete())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Уведомления удалены'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('markAllAsRead')
                    ->label('Прочитать все')
                    ->icon('heroicon-o-check-circle')
                    ->color('gray')
                    ->action(function () {
                        Notification::where('is_read', false)
                            ->where(function ($query) {
                                $query->where('type', 'like', '%_admin')
                                    ->orWhereNull('type');
                            })
                            ->update(['is_read' => true]);
                    })
                    ->successNotificationTitle('Все уведомления отмечены как прочитанные'),
            ])
            ->emptyStateHeading('Нет новых уведомлений')
            ->emptyStateDescription('Когда появятся новые уведомления, они отобразятся здесь.')
            ->emptyStateIcon('heroicon-o-bell')            
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->poll('10s')
            ->deferLoading();
    }

    

    private function getNotificationColorClass(?string $type): string
    {
        return match($type) {
            'call_admin' => 'bg-blue-100 text-blue-600',
            'system_admin' => 'bg-orange-100 text-orange-600',
            'alert_admin' => 'bg-red-100 text-red-600',
            default => 'bg-gray-100 text-gray-600'
        };
    }

    private function getNotificationIcon(?string $type): string
    {
        return match($type) {
            'call_admin' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>',
            'system_admin' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'alert_admin' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
            default => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
        };
    }

    private function getNotificationBadgeClass(?string $type): string
    {
        return match($type) {
            'call_admin' => 'bg-blue-100 text-blue-800',
            'system_admin' => 'bg-orange-100 text-orange-800',
            'alert_admin' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    private function getNotificationTypeLabel(?string $type): string
    {
        if (!$type) return 'Общее';
        
        $type = str_replace('_admin', '', $type);
        return match($type) {
            'call' => 'Звонок',
            'system' => 'Система',
            'alert' => 'Тревога',
            default => ucfirst($type)
        };
    }
}