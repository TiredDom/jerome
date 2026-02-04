<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketLog;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketLog::with(['ticket', 'user'])
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('ticket', function($tq) use ($request) {
                    $tq->where('title', 'like', "%{$request->search}%");
                })
                ->orWhere('from_status', 'like', "%{$request->search}%")
                ->orWhere('to_status', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        $logs = $query->paginate(20)->withQueryString()->through(function ($log) {
            return [
                'id' => $log->id,
                'ticket_id' => $log->ticket_id,
                'from_status' => $log->from_status,
                'to_status' => $log->to_status,
                'created_at' => $log->created_at->toISOString(),
                'created_at_human' => $log->created_at->diffForHumans(),
                'ticket' => $log->ticket ? [
                    'id' => $log->ticket->id,
                    'title' => $log->ticket->title,
                    'status' => $log->ticket->status,
                    'priority' => $log->ticket->priority,
                ] : null,
                'user' => $log->user ? [
                    'id' => $log->user->id,
                    'name' => $log->user->name,
                    'email' => $log->user->email,
                ] : null,
            ];
        });

        $stats = [
            'total' => TicketLog::count(),
            'today' => TicketLog::whereDate('created_at', today())->count(),
            'this_week' => TicketLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => TicketLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['date_from', 'date_to', 'search', 'ticket_id']),
            'stats' => $stats,
        ]);
    }

    public function export(Request $request)
    {
        $query = TicketLog::with(['ticket', 'user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('ticket', function($tq) use ($request) {
                    $tq->where('title', 'like', "%{$request->search}%");
                })
                ->orWhere('from_status', 'like', "%{$request->search}%")
                ->orWhere('to_status', 'like', "%{$request->search}%");
            });
        }

        $logs = $query->limit(5000)->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Time', 'Ticket ID', 'Ticket Title', 'From Status', 'To Status', 'User', 'User Email']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d'),
                    $log->created_at->format('H:i:s'),
                    $log->ticket_id,
                    $log->ticket ? $log->ticket->title : 'N/A',
                    $log->from_status ?? 'none',
                    $log->to_status,
                    $log->user ? $log->user->name : 'System',
                    $log->user ? $log->user->email : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
