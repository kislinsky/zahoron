<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Services\Account\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ) {}

    /**
     * Display a listing of the user's tickets.
     */
    public function index(Request $request): View
    {
        $tickets = $this->ticketService->getTicketsByUser(auth()->id());
        
        return view('account.user.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create(): View
    {
        $categories = TicketCategory::where('is_active', true)->get();
        $priorities = TicketPriority::all();
        
        return view('account.user.tickets.create', compact('categories', 'priorities'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority_id' => 'required|exists:ticket_priorities,id',
        ]);

        $ticket = $this->ticketService->createTicket([
            ...$validated,
            'user_id' => auth()->id(),
            'status_id' => 1 // Open status
        ]);

        return redirect()->route('account.tickets.show', $ticket)
            ->with('success', 'Тикет успешно создан!');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket): View
    {
        // Проверяем, что пользователь может просматривать только свои тикеты
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['replies.user', 'category', 'priority', 'status']);
        
        return view('account.user.tickets.show', compact('ticket'));
    }

    /**
     * Add a reply to the ticket.
     */
    public function addReply(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $this->ticketService->addReply($ticket, [
            'message' => $validated['message'],
            'user_id' => auth()->id(),
            'is_internal' => false
        ]);

        return redirect()->back()
            ->with('success', 'Ответ добавлен!');
    }

    /**
     * Close the specified ticket.
     */
    public function close(Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $this->ticketService->closeTicket($ticket);

        return redirect()->back()
            ->with('success', 'Тикет закрыт!');
    }

    /**
     * Reopen the specified ticket.
     */
    public function reopen(Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $this->ticketService->reopenTicket($ticket);

        return redirect()->back()
            ->with('success', 'Тикет открыт заново!');
    }
}