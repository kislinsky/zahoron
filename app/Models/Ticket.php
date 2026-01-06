<?php

namespace App\Models;

use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';

    protected $fillable = [
        'subject',
        'description',
        'user_id',
        'category_id',
        'priority_id',
        'status_id',
        'assigned_to',
        'closed_at'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($ticket) {

            // Для админа
            Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'new_ticket_admin',
                'title' => 'Новый тикет',
                'message' => "Создан новый тикет",
                'is_read' => false
            ]);

            
            // Уведомление для назначенного сотрудника о новом тикете
            if ($ticket->assigned_to) {
                \App\Models\Notification::create([
                    'user_id' => $ticket->assigned_to,
                    'organization_id' => null, // Не привязываем к организации
                    'type' => 'new_ticket',
                    'title' => 'Новый тикет',
                    'message' => "Вам назначен новый тикет: {$ticket->subject}",
                    'is_read' => false
                ]);
            }
            
            // Уведомление для автора тикета
            \App\Models\Notification::create([
                'user_id' => $ticket->user_id,
                'organization_id' => null,
                'type' => 'ticket_created',
                'title' => 'Тикет создан',
                'message' => "Ваш тикет '{$ticket->subject}' успешно создан",
                'is_read' => false
            ]);
        });
        
        static::updated(function ($ticket) {
            // Если тикет был закрыт
            if ($ticket->isDirty('closed_at') && $ticket->closed_at) {
                // Уведомление для пользователя о закрытии тикета
                \App\Models\Notification::create([
                    'user_id' => $ticket->user_id,
                    'organization_id' => null,
                    'type' => 'ticket_closed',
                    'title' => 'Тикет закрыт',
                    'message' => "Ваш тикет '{$ticket->subject}' был закрыт",
                    'is_read' => false
                ]);
            }
            
            // При изменении статуса
            if ($ticket->isDirty('status_id')) {
                $status = $ticket->status;
                \App\Models\Notification::create([
                    'user_id' => $ticket->user_id,
                    'organization_id' => null,
                    'type' => 'ticket_status',
                    'title' => 'Статус тикета изменен',
                    'message' => "Статус вашего тикета '{$ticket->subject}' изменен",
                    'is_read' => false
                ]);
                
                // Если есть назначенный сотрудник, уведомляем и его
                if ($ticket->assigned_to) {
                    \App\Models\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'organization_id' => null,
                        'type' => 'ticket_status',
                        'title' => 'Статус тикета изменен',
                        'message' => "Статус тикета '{$ticket->subject}' изменен",
                        'is_read' => false
                    ]);
                }
            }
            
            // При изменении назначенного сотрудника
            if ($ticket->isDirty('assigned_to')) {
                // Уведомляем нового назначенного сотрудника
                if ($ticket->assigned_to) {
                    \App\Models\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'organization_id' => null,
                        'type' => 'ticket_assigned',
                        'title' => 'Тикет назначен',
                        'message' => "Вам назначен тикет: {$ticket->subject}",
                        'is_read' => false
                    ]);
                }
                
                // Уведомляем автора об изменении назначения
                \App\Models\Notification::create([
                    'user_id' => $ticket->user_id,
                    'organization_id' => null,
                    'type' => 'ticket_assigned',
                    'title' => 'Назначен исполнитель',
                    'message' => "Для вашего тикета '{$ticket->subject}' назначен исполнитель",
                    'is_read' => false
                ]);
            }
        });
    }
}