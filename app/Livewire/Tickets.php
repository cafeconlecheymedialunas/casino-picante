<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Livewire\Component;

class Tickets extends Component
{
    use HasLinePermissions, SendsNotifications;

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
        $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($id);
        $this->dispatch('ticketSelected');
    }

    public function closeDetail()
    {
        $this->selectedTicket = null;
    }

    public function sendMessage()
    {
        $this->checkLinePermission('ticket.update');

        $this->validate([
            'newMessage' => 'required|string|min:1',
        ]);

        if (! $this->selectedTicket) {
            return;
        }

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($this->selectedTicket->id);

        $this->notify('Nuevo mensaje en ticket', "Se envió un mensaje en el ticket: {$this->selectedTicket->subject}", 'tickets', '/tickets', 'info');

        $this->dispatch('messageSent');
    }

    public function quickAction($type)
    {
        $this->checkLinePermission('ticket.update');

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

            $this->notify('Ticket resuelto', "El ticket {$this->selectedTicket->subject} fue marcado como resuelto.", 'tickets', '/tickets', 'success');
        }

        $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($this->selectedTicket->id);
        $this->dispatch('messageSent');
    }

    public function updateStatus($status)
    {
        if ($this->selectedTicket) {
            $this->selectedTicket->update(['status' => $status]);
            $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($this->selectedTicket->id);

            $this->notify('Estado de ticket cambiado', "El ticket {$this->selectedTicket->subject} cambió a: {$status}", 'tickets', '/tickets', 'warning');
        }
    }

    public function getTickets()
    {
        $this->checkLinePermission('ticket.read');

        $query = Ticket::with(['user', 'line']);

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
