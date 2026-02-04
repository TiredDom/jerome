<?php

namespace App\Http\Controllers;

use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TicketController extends Controller
{
    protected TicketService $service;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'nullable|string|max:100',
            ]);

            $ticket = Ticket::create([
                'user_id' => Auth::id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'] ?? null,
                'status' => 'open',
                'priority' => 'low',
            ]);

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'from_status' => null,
                'to_status' => 'open',
            ]);

            return Response::json($ticket, 201);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to create ticket: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return Response::json(['error' => 'Unauthenticated'], 401);
            }
            $tickets = Ticket::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            return Response::json($tickets);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to load tickets: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorizeView($request->user(), $ticket);
        $ticket->load('logs.user');
        return Response::json($ticket);
    }

    public function adminQueue()
    {
        $this->ensureAdmin();
        $tickets = Ticket::ordered()->whereIn('status', ['open', 'in_progress'])->get();
        return Response::json($tickets);
    }

    public function adminDashboardStats()
    {
        $this->ensureAdmin();

        $total = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $open = Ticket::where('status', 'open')->count();
        $inProgress = Ticket::where('status', 'in_progress')->count();

        $resolvedToday = TicketLog::whereDate('created_at', today())
            ->where('to_status', 'resolved')
            ->distinct('ticket_id')
            ->count('ticket_id');

        $recentTickets = Ticket::ordered()
            ->whereIn('status', ['open', 'in_progress'])
            ->limit(5)
            ->get();

        return Response::json([
            'total' => $total,
            'open' => $open,
            'in_progress' => $inProgress,
            'resolved_today' => $resolvedToday,
            'recent_tickets' => $recentTickets
        ]);
    }

    public function adminShow($id)
    {
        $this->ensureAdmin();
        $ticket = Ticket::with('logs.user')->findOrFail($id);
        return Response::json($ticket);
    }

    public function adminChangeStatus(Request $request, $id)
    {
        $this->ensureAdmin();
        $to = $request->validate(['to_status' => 'required|string'])['to_status'];
        $ticket = Ticket::findOrFail($id);
        $updated = $this->service->changeStatus($ticket, $to, $request->user());
        return Response::json($updated);
    }

    public function adminSetPriority(Request $request, $id)
    {
        $this->ensureAdmin();
        $data = $request->validate(['priority' => 'required|in:low,medium,high']);
        $ticket = Ticket::findOrFail($id);
        $oldPriority = $ticket->priority;
        $ticket->priority = $data['priority'];
        $ticket->save();

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'from_status' => 'priority:' . $oldPriority,
            'to_status' => 'priority:' . $data['priority'],
        ]);

        return Response::json($ticket);
    }

    public function exportLogs()
    {
        $this->ensureAdmin();
        $rows = TicketLog::with('ticket', 'user')->orderBy('created_at', 'desc')->get();

        $csv = "ticket_id,user_id,user_name,from_status,to_status,timestamp\n";
        foreach ($rows as $r) {
            $csv .= implode(',', [
                $r->ticket_id,
                $r->user_id ?? '',
                '"' . ($r->user->name ?? '') . '"',
                $r->from_status ?? '',
                $r->to_status,
                $r->created_at,
            ]) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ticket_logs.csv"'
        ]);
    }

    protected function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user || ($user->is_admin != 1 && $user->is_admin !== true)) {
            abort(403, 'Admin access required');
        }
    }

    protected function authorizeView($user, Ticket $ticket)
    {
        if ($user->id !== $ticket->user_id) {
            if ($user->is_admin != 1 && $user->is_admin !== true) {
                abort(403, 'You can only view your own tickets');
            }
        }
    }
}
