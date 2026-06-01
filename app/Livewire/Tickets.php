<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\NotificationService;
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

    public string $createCategory = 'atencion';

    public string $createUserId = '';

    public string $createPriority = 'medium';

    public string $createMessage = '';

    public function openCreateModal(): void
    {
        $this->checkLinePermission(Permissions::TICKET_UPDATE);
        $this->createSubject = '';
        $this->createCategory = 'atencion';
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
            'createCategory' => 'required|in:juego,bono,sorteo,atencion,otro',
            'createUserId' => 'required|integer|exists:users,id',
            'createPriority' => 'required|in:low,medium,high',
            'createMessage' => 'required|string|min:1',
        ]);

        $lineId = $this->requireLineIdForScopedCreate();
        $user = $this->findAssignableUserForTicket((int) $this->createUserId, $lineId);
        if (! $user) {
            $this->addError('createUserId', 'Selecciona un cliente activo del vendor y la linea actual.');

            return;
        }

        $ticket = Ticket::create([
            'vendor_id' => $user->vendor_id ?: session('active_vendor_id'),
            'user_id' => $user->id,
            'line_id' => $lineId,
            'subject' => trim($this->createSubject),
            'category' => $this->createCategory,
            'status' => 'open',
            'priority' => $this->createPriority,
        ]);

        TicketMessage::create([
            'vendor_id' => $ticket->vendor_id,
            'ticket_id' => $ticket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => trim($this->createMessage),
        ]);

        $this->showCreateModal = false;
        $this->notify('Ticket creado', "Ticket {$ticket->tracking_code}: {$ticket->subject}", 'tickets', '/tickets', 'success');
        $this->selectTicket($ticket->id);
    }

    public function selectTicket($id)
    {
        $this->checkLinePermission(Permissions::TICKET_READ);
        $ticket = $this->ticketQuery()
            ->with(['user', 'line', 'messages.agent', 'messages.user'])
            ->findOrFail($id);

        if (! $this->ticketIsVisible($ticket)) {
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

        if (! $this->ticketIsVisible($this->selectedTicket)) {
            abort(403, 'No tienes acceso a este ticket.');
        }

        TicketMessage::create([
            'vendor_id' => $this->selectedTicket->vendor_id,
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => $this->newMessage,
        ]);

        // Notificar al cliente
        NotificationService::sendToClient(
            'Nueva respuesta en tu ticket',
            "Tienes una nueva respuesta en tu ticket {$this->selectedTicket->tracking_code}: {$this->selectedTicket->subject}",
            $this->selectedTicket->user_id,
            'info',
            route('client.account', [], false).'?tab=tickets',
            'tickets'
        );

        // Auto-advance to progress when agent first replies
        if ($this->selectedTicket->status === 'open') {
            $this->selectedTicket->update(['status' => 'progress']);
        }

        $this->newMessage = '';
        $this->selectedTicket = $this->ticketQuery()
            ->with(['user', 'line', 'messages.agent', 'messages.user'])
            ->find($this->selectedTicket->id);

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

        if (! $this->ticketIsVisible($this->selectedTicket)) {
            abort(403, 'No tienes acceso a este ticket.');
        }

        $messages = [
            'resolved' => '✅ Ticket resuelto',
            'waiting' => '⏳ Esperando respuesta del usuario',
        ];

        TicketMessage::create([
            'vendor_id' => $this->selectedTicket->vendor_id,
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => $messages[$type] ?? $type,
        ]);

        if ($type === 'resolved') {
            $this->selectedTicket->update(['status' => 'closed']);

            $this->notify('Ticket resuelto', "El ticket {$this->selectedTicket->subject} fue marcado como resuelto.", 'tickets', '/tickets', 'success');

            NotificationService::sendToClient(
                'Ticket resuelto',
                "Tu ticket {$this->selectedTicket->tracking_code} fue marcado como resuelto.",
                $this->selectedTicket->user_id,
                'success',
                route('client.account', [], false).'?tab=tickets',
                'tickets'
            );
        }

        $this->selectedTicket = $this->ticketQuery()
            ->with(['user', 'line', 'messages.agent', 'messages.user'])
            ->find($this->selectedTicket->id);
        $this->dispatch('messageSent');
    }

    public function reopenTicket(): void
    {
        $this->checkLinePermission(Permissions::TICKET_UPDATE);

        if (! $this->selectedTicket) {
            return;
        }

        if (! $this->ticketIsVisible($this->selectedTicket)) {
            abort(403, 'No tienes acceso a este ticket.');
        }

        $this->selectedTicket->update(['status' => 'open']);

        TicketMessage::create([
            'vendor_id' => $this->selectedTicket->vendor_id,
            'ticket_id' => $this->selectedTicket->id,
            'agent_id' => $this->getCurrentAgentId(),
            'message' => '🔄 Ticket reabierto',
        ]);

        $this->selectedTicket = $this->ticketQuery()
            ->with(['user', 'line', 'messages.agent', 'messages.user'])
            ->find($this->selectedTicket->id);
        $this->dispatch('messageSent');
        $this->notify('Ticket reabierto', "El ticket {$this->selectedTicket->subject} fue reabierto.", 'tickets', '/tickets', 'warning');

        NotificationService::sendToClient(
            'Ticket reabierto',
            "Tu ticket {$this->selectedTicket->tracking_code} fue reabierto por un agente.",
            $this->selectedTicket->user_id,
            'warning',
            route('client.account', [], false).'?tab=tickets',
            'tickets'
        );
    }

    public function updateStatus($status)
    {
        $this->checkLinePermission(
            $status === 'closed' ? Permissions::TICKET_CLOSE : Permissions::TICKET_UPDATE
        );

        if ($this->selectedTicket) {
            if (! $this->ticketIsVisible($this->selectedTicket)) {
                abort(403, 'No tienes acceso a este ticket.');
            }

            $oldStatus = $this->selectedTicket->status;
            $this->selectedTicket->update(['status' => $status]);

            if ($oldStatus !== $status) {
                NotificationService::sendToClient(
                    'Estado de ticket actualizado',
                    "El estado de tu ticket {$this->selectedTicket->tracking_code} cambió a: {$status}",
                    $this->selectedTicket->user_id,
                    'info',
                    route('client.account', [], false).'?tab=tickets',
                    'tickets'
                );
            }

            $this->selectedTicket = $this->ticketQuery()
                ->with(['user', 'line', 'messages.agent', 'messages.user'])
                ->find($this->selectedTicket->id);

            $this->notify('Estado de ticket cambiado', "El ticket {$this->selectedTicket->subject} cambió a: {$status}", 'tickets', '/tickets', 'warning');
        }
    }

    public function getTickets()
    {
        $this->checkLinePermission(Permissions::TICKET_READ);

        $query = $this->ticketQuery()->with(['user', 'line']);

        $lineIds = $this->visibleLineIds();
        if ($lineIds !== null) {
            $query->where(fn ($ticket) => $ticket
                ->whereIn('line_id', $lineIds)
                ->orWhereNull('line_id'));
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

        $base = $this->ticketQuery();
        if ($lineIds !== null) {
            $base->where(fn ($ticket) => $ticket
                ->whereIn('line_id', $lineIds)
                ->orWhereNull('line_id'));
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
            ->when(session('active_vendor_id'), fn ($query, $vendorId) => $query->where('vendor_id', (int) $vendorId))
            ->when($lineId, fn ($q) => $q->where(function ($inner) use ($lineId) {
                $inner->where('line_id', $lineId)
                    ->orWhereHas('lines', fn ($l) => $l->where('lines.id', $lineId)->where('line_clients.is_active', true));
            }))
            ->orderBy('name')
            ->get(['id', 'name', 'username']);

        return view('livewire.tickets', compact('tickets', 'metrics', 'assignableUsers'))->layout('layouts.dashboard');
    }

    public function categoryLabel(?string $category): string
    {
        return match ($category) {
            'juego' => 'Juego',
            'bono' => 'Bono',
            'sorteo' => 'Sorteo',
            'atencion' => 'Atencion',
            'otro' => 'Otro',
            default => 'Sin categoria',
        };
    }

    private function ticketIsVisible(Ticket $ticket): bool
    {
        if (($vendorId = session('active_vendor_id')) && (int) $ticket->vendor_id !== (int) $vendorId) {
            return false;
        }

        $visibleLineIds = $this->visibleLineIds();

        return $visibleLineIds === null
            || $ticket->line_id === null
            || in_array((int) $ticket->line_id, $visibleLineIds, true);
    }

    private function ticketQuery()
    {
        return Ticket::withoutGlobalScopes()
            ->when(session('active_vendor_id'), fn ($query, $vendorId) => $query->where('vendor_id', (int) $vendorId));
    }

    private function findAssignableUserForTicket(int $userId, int $lineId): ?User
    {
        return User::whereKey($userId)
            ->where('status', 'active')
            ->when(session('active_vendor_id'), fn ($query, $vendorId) => $query->where('vendor_id', (int) $vendorId))
            ->where(function ($query) use ($lineId) {
                $query->where('line_id', $lineId)
                    ->orWhereHas('lines', fn ($line) => $line
                        ->where('lines.id', $lineId)
                        ->where('line_clients.is_active', true));
            })
            ->first();
    }
}
