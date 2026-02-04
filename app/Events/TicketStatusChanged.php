<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public ?string $from;
    public string $to;
    public $actor;

    public function __construct(Ticket $ticket, ?string $from, string $to, $actor = null)
    {
        $this->ticket = $ticket;
        $this->from = $from;
        $this->to = $to;
        $this->actor = $actor;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('tickets');
    }

    public function broadcastWith()
    {
        return [
            'ticket_id' => $this->ticket->id,
            'from' => $this->from,
            'to' => $this->to,
            'actor' => $this->actor ? ['id' => $this->actor->id, 'name' => $this->actor->name] : null,
        ];
    }
}
