<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Livewire\Component;

class Tickets extends Component
{
    public $filter = 'open';

    public $search = '';

    public $selectedTicket = null;

    private function getCurrentAgentId(): ?int
    {
        return session('active_agent_id');
    }

    public $newMessage = '';

    public function selectTicket($id)
    {
        $this->selectedTicket = Ticket::with(['user', 'messages.agent'])->find($id);
    }

    public function closeDetail()
    {
        $this->selectedTicket = null;
    }

    public function sendMessage()
    {
        if (! $this->newMessage || ! $this->selectedTicket) {
            return;
        }

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->selectedTicket = $this->selectedTicket->fresh(['messages']);
    }

    public function quickAction($type)
    {
        if (! $this->selectedTicket) {
            return;
        }

        $messages = [
            'resolved' => '✅ Ticket resuelto',
            'waiting' => '⏳ Esperando respuesta del usuario',
        ];

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => $messages[$type] ?? $type,
        ]);

        if ($type === 'resolved') {
            $this->selectedTicket->update(['status' => 'closed']);
        }

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

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getMetrics()
    {
        return [
            'open' => Ticket::where('status', 'open')->count(),
            'progress' => Ticket::where('status', 'progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];
    }

    public function render()
    {
        $tickets = $this->getTickets();
        $metrics = $this->getMetrics();

        return view('livewire.tickets', compact('tickets', 'metrics'))->layout('layouts.dashboard');
    }
}
