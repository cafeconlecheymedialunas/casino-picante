<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Support\Permissions;
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

    public bool $showCreateModal = false;
    public string $createSubject = '';
    public string $createUserId = '';
    public string $createPriority = 'medium';
    public string $createMessage = '';

    public function openCreateModal(): void
    {
        $this->checkLinePermission(Permissions::TICKET_UPDATE);
        $this->createSubject = '';
        $this->createUserId = '';
        $this->createPriority = 'medium';
        $this->createMessage = '';
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function createTicket(): void
    {
        $this->checkLinePermission(Permissions::TICKET_UPDATE);

        $this->validate([
            'createSubject' => 'required|string|min:3|max:255',
            'createUserId'  => 'required|integer|exists:users,id',
            'createPriority' => 'required|in:low,medium,high',
            'createMessage' => 'required|string|min:1',
        ]);

        $lineId = session('active_line_id');

        $ticket = Ticket::create([
            'user_id'  => (int) $this->createUserId,
            'line_id'  => $lineId,
            'subject'  => trim($this->createSubject),
            'status'   => 'open',
            'priority' => $this->createPriority,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'agent_id'  => $this->getCurrentAgentId(),
            'message'   => trim($this->createMessage),
        ]);

        $this->showCreateModal = false;
        $this->notify('Ticket creado', "Ticket {$ticket->tracking_code}: {$ticket->subject}", 'tickets', '/tickets', 'success');
        $this->selectTicket($ticket->id);
    }

    public function selectTicket($id)
    {
        $this->checkLinePermission(Permissions::TICKET_READ);
        $ticket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->findOrFail($id);

        $visibleLineIds = $this->visibleLineIds();
        if ($visibleLineIds !== null && ! in_array($ticket->line_id, $visibleLineIds)) {
            abort(403, 'No tienes acceso a este ticket.');
        }

        $this->selectedTicket = $ticket;
        $this->dispatch('ticketSelected');
    }

    public function closeDetail()
    {
        $this->selectedTicket = null;
    }

    public function sendMessage()
    {
        $this->checkLinePermission(Permissions::TICKET_UPDATE);

        $this->validate([
            'newMessage' => 'required|string|min:1',
        ]);

        if (! $this->selectedTicket) {
            return;
        }

        $visibleLineIds = $this->visibleLineIds();
        if ($visibleLineIds !== null && ! in_array($this->selectedTicket->line_id, $visibleLineIds)) {
            abort(403, 'No tienes acceso a este ticket.');
        }

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => $this->newMessage,
        ]);

        // Auto-advance to progress when agent first replies
        if ($this->selectedTicket->status === 'open') {
            $this->selectedTicket->update(['status' => 'progress']);
        }

        $this->newMessage = '';
        $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($this->selectedTicket->id);

        $this->notify('Nuevo mensaje en ticket', "Se envió un mensaje en el ticket: {$this->selectedTicket->subject}", 'tickets', '/tickets', 'info');

        $this->dispatch('messageSent');
    }

    public function quickAction($type)
    {
        $this->checkLinePermission(
            $type === 'resolved' ? Permissions::TICKET_CLOSE : Permissions::TICKET_UPDATE
        );

        if (! $this->selectedTicket) {
            return;
        }

        $visibleLineIds = $this->visibleLineIds();
        if ($visibleLineIds !== null && ! in_array($this->selectedTicket->line_id, $visibleLineIds)) {
            abort(403, 'No tienes acceso a este ticket.');
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

    public function reopenTicket(): void
    {
        $this->checkLinePermission(Permissions::TICKET_UPDATE);

        if (! $this->selectedTicket) {
            return;
        }

        $this->selectedTicket->update(['status' => 'open']);

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'agent_id'  => $this->getCurrentAgentId(),
            'message'   => '🔄 Ticket reabierto',
        ]);

        $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($this->selectedTicket->id);
        $this->dispatch('messageSent');
        $this->notify('Ticket reabierto', "El ticket {$this->selectedTicket->subject} fue reabierto.", 'tickets', '/tickets', 'warning');
    }

    public function updateStatus($status)
    {
        $this->checkLinePermission(
            $status === 'closed' ? Permissions::TICKET_CLOSE : Permissions::TICKET_UPDATE
        );

        if ($this->selectedTicket) {
            $visibleLineIds = $this->visibleLineIds();
            if ($visibleLineIds !== null && ! in_array($this->selectedTicket->line_id, $visibleLineIds)) {
                abort(403, 'No tienes acceso a este ticket.');
            }

            $this->selectedTicket->update(['status' => $status]);
            $this->selectedTicket = Ticket::with(['user', 'line', 'messages.agent', 'messages.user'])->find($this->selectedTicket->id);

            $this->notify('Estado de ticket cambiado', "El ticket {$this->selectedTicket->subject} cambió a: {$status}", 'tickets', '/tickets', 'warning');
        }
    }

    public function getTickets()
    {
        $this->checkLinePermission(Permissions::TICKET_READ);

        $query = Ticket::with(['user', 'line']);

        $lineIds = $this->visibleLineIds();
        if ($lineIds !== null) {
            $query->whereIn('line_id', $lineIds);
        }

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%'.$this->search.'%')
                    ->orWhere('tracking_code', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('username', 'like', '%'.$this->search.'%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getMetrics()
    {
        $lineIds = $this->visibleLineIds();

        $base = Ticket::query();
        if ($lineIds !== null) {
            $base->whereIn('line_id', $lineIds);
        }

        $open = (clone $base)->where('status', 'open')->count();
        $progress = (clone $base)->where('status', 'progress')->count();
        $closed = (clone $base)->where('status', 'closed')->count();

        return compact('open', 'progress', 'closed');
    }

    public function render()
    {
        $tickets = $this->getTickets();
        $metrics = $this->getMetrics();
        $lineId = session('active_line_id');
        $assignableUsers = User::where('status', 'active')
            ->when($lineId, fn ($q) => $q->where(function ($inner) use ($lineId) {
                $inner->where('line_id', $lineId)
                    ->orWhereHas('lines', fn ($l) => $l->where('lines.id', $lineId)->where('line_clients.is_active', true));
            }))
            ->orderBy('name')
            ->get(['id', 'name', 'username']);

        return view('livewire.tickets', compact('tickets', 'metrics', 'assignableUsers'))->layout('layouts.dashboard');
    }
}
