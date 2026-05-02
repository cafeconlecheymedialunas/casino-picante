<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Livewire\Component;

class Tickets extends Component
{
    public $filter = 'open';

    public $selectedTicket = null;

    public $newMessage = '';

    public function selectTicket($id)
    {
        $this->selectedTicket = Ticket::with(['user', 'messages'])->find($id);
    }

    public function sendMessage()
    {
        if (! $this->newMessage || ! $this->selectedTicket) {
            return;
        }

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => auth()->id(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->selectedTicket = $this->selectedTicket->fresh(['messages']);
    }

    public function updateStatus($status)
    {
        if ($this->selectedTicket) {
            $this->selectedTicket->update(['status' => $status]);
            $this->selectedTicket = $this->selectedTicket->fresh();
        }
    }

    public function getTickets()
    {
        $query = Ticket::with('user');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $tickets = $this->getTickets();

        return view('livewire.tickets', compact('tickets'))->extends('layouts.dashboard');
    }
}
