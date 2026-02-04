<?php

namespace App\Services;

use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function changeStatus(Ticket $ticket, string $toStatus, $actor = null): Ticket
    {
        $allowed = [
            'open' => ['in_progress'],
            'in_progress' => ['resolved'],
            'resolved' => [],
        ];

        $from = $ticket->status;

        if ($from === $toStatus) {
            return $ticket;
        }

        if (!in_array($toStatus, $allowed[$from] ?? [])) {
            throw new \InvalidArgumentException("Invalid status transition from {$from} to {$toStatus}");
        }

        return DB::transaction(function () use ($ticket, $from, $toStatus, $actor) {
            $ticket->status = $toStatus;
            $ticket->save();

            $log = TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $actor ? $actor->id : null,
                'from_status' => $from,
                'to_status' => $toStatus,
            ]);

            event(new TicketStatusChanged($ticket, $from, $toStatus, $actor));

            return $ticket->fresh();
        });
    }
}
