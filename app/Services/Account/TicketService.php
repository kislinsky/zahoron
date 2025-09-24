<?php

namespace App\Services\Account;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function createTicket(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function updateTicket(Ticket $ticket, array $data): bool
    {
        return $ticket->update($data);
    }

    public function deleteTicket(Ticket $ticket): bool
    {
        return $ticket->delete();
    }

    public function closeTicket(Ticket $ticket): bool
    {
        return $ticket->update(['closed_at' => now()]);
    }

    public function reopenTicket(Ticket $ticket): bool
    {
        return $ticket->update(['closed_at' => null]);
    }

    public function addReply(Ticket $ticket, array $data): TicketReply
    {
        return $ticket->replies()->create($data);
    }

    public function getTicketsByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Ticket::where('user_id', $userId)
            ->with(['category', 'priority', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAllTickets(int $perPage = 15): LengthAwarePaginator
    {
        return Ticket::with(['user', 'category', 'priority', 'status', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}