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
}