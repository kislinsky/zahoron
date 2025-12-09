<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($reply) {
            $ticket = $reply->ticket;
            
            // Если это не внутреннее сообщение
            if (!$reply->is_internal) {
                // Если ответил не автор тикета - уведомляем автора
                if ($reply->user_id != $ticket->user_id) {
                    \App\Models\Notification::create([
                        'user_id' => $ticket->user_id,
                        'organization_id' => null,
                        'type' => 'ticket_reply',
                        'title' => 'Новый ответ в тикете',
                        'message' => "Новый ответ в тикете: {$ticket->subject}",
                        'is_read' => false
                    ]);
                }
                // Если автор тикета ответил, уведомляем назначенного сотрудника
                elseif ($reply->user_id == $ticket->user_id && $ticket->assigned_to) {
                    \App\Models\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'organization_id' => null,
                        'type' => 'ticket_reply',
                        'title' => 'Новый ответ клиента',
                        'message' => "Клиент ответил в тикете: {$ticket->subject}",
                        'is_read' => false
                    ]);
                }
            } 
            // Если это внутреннее сообщение
            else {
                // Уведомляем назначенного сотрудника (если ответил не он)
                if ($ticket->assigned_to && $reply->user_id != $ticket->assigned_to) {
                    \App\Models\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'organization_id' => null,
                        'type' => 'ticket_internal_reply',
                        'title' => 'Внутренний комментарий',
                        'message' => "Новый внутренний комментарий в тикете: {$ticket->subject}",
                        'is_read' => false
                    ]);
                }
                
                // Уведомляем автора тикета (если это не он)
                if ($reply->user_id != $ticket->user_id) {
                    \App\Models\Notification::create([
                        'user_id' => $ticket->user_id,
                        'organization_id' => null,
                        'type' => 'ticket_internal_reply',
                        'title' => 'Внутренний комментарий',
                        'message' => "Новый внутренний комментарий в вашем тикете: {$ticket->subject}",
                        'is_read' => false
                    ]);
                }
            }
        });
    }
}