<?php

namespace App\Livewire\Components;

use App\Models\Agent;
use App\Models\Chat;
use App\Models\LineAgent;
use App\Models\User;
use App\Support\Permissions;
use App\Support\Roles;
use App\Traits\HasLinePermissions;
use Livewire\Component;

class AgentMessaging extends Component
{
    use HasLinePermissions;

    public ?int $targetUserId = null;

    public ?int $targetAgentId = null;

    public string $targetType = 'Cliente';

    public string $targetName = '';

    public string $targetEmail = '';

    public string $targetPhone = '';

    public string $contextLabel = '';

    public bool $open = false;

    public ?int $activeChatId = null;

    public function openPanel(): void
    {
        $this->authorizeMessaging();
        $this->requireVendorContextForWrite();
        $this->activeChatId = $this->findOrCreateDirectChat()?->id;
        $this->open = true;
    }

    public function closePanel(): void
    {
        $this->open = false;
        $this->activeChatId = null;
    }

    public function render()
    {
        return view('livewire.components.agent-messaging');
    }

    private function findExistingChat(int $agentId): ?Chat
    {
        $this->authorizeDirectAgent($agentId);

        $query = Chat::withoutGlobalScopes()->where('agent_id', $agentId);

        if ($this->targetUserId) {
            return $query->where('user_id', $this->targetUserId)->first();
        }

        return $query
            ->whereNull('user_id')
            ->where('context_type', $this->targetType)
            ->where('context_name', $this->targetName)
            ->first();
    }

    private function findOrCreateDirectChat(): ?Chat
    {
        $this->authorizeMessaging();
        $agentId = $this->directAgentId();

        if (! $agentId) {
            return null;
        }
        $this->authorizeDirectAgent($agentId);

        return $this->findExistingChat($agentId) ?: Chat::create([
            'vendor_id' => session('active_vendor_id'),
            'user_id' => $this->targetUserId,
            'agent_id' => $agentId,
            'subject' => "{$this->targetType}: {$this->targetName}",
            'status' => 'open',
            'context_type' => $this->targetType,
            'context_name' => $this->targetName,
            'context_email' => $this->targetEmail ?: null,
            'context_phone' => $this->targetPhone ?: null,
            'context_label' => $this->contextLabel ?: null,
        ]);
    }

    private function directAgentId(): ?int
    {
        if ($this->targetAgentId) {
            $this->authorizeDirectAgent((int) $this->targetAgentId);

            return $this->targetAgentId;
        }

        if ($this->targetType === 'Agente' && $this->targetEmail !== '') {
            $agentId = Agent::where('email', $this->targetEmail)->value('id');

            if ($agentId) {
                return $agentId;
            }
        }

        if ($this->targetUserId) {
            if (session('active_agent_id')) {
                return session('active_agent_id');
            }

            $activeVendorId = session('active_vendor_id');
            $query = Agent::where('status', 'active');

            if ($activeVendorId) {
                $query->where('vendor_id', $activeVendorId);
            }

            return $query->orderBy('id')->value('id');
        }

        return null;
    }

    private function authorizeMessaging(): void
    {
        if ($this->targetUserId) {
            $this->checkLinePermission(Permissions::USER_READ);
            $this->authorizeTargetUser((int) $this->targetUserId);

            return;
        }

        if (
            ! $this->hasLinePermission(Permissions::AGENT_UPDATE)
            && ! $this->hasLinePermission(Permissions::AGENT_ASSIGN)
            && ! $this->hasLinePermission(Permissions::TICKET_READ)
        ) {
            abort(403, 'Sin permiso para enviar mensajes.');
        }

        if ($this->targetAgentId) {
            $this->authorizeDirectAgent((int) $this->targetAgentId);
        }
    }

    private function authorizeTargetUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $activeVendorId = session('active_vendor_id');

        if ($activeVendorId && (int) $user->vendor_id !== (int) $activeVendorId) {
            abort(403, 'No podes enviar mensajes a clientes de otro vendor.');
        }

        if (auth()->user()?->hasRole(Roles::ADMIN) && ! $activeVendorId) {
            return;
        }

        if ($this->isAdminMode()) {
            return;
        }

        $lineIds = $this->visibleLineIds();
        $allowed = ($user->line_id && in_array((int) $user->line_id, $lineIds ?? [], true))
            || $user->lines()->whereIn('lines.id', $lineIds ?? [])->wherePivot('is_active', true)->exists();

        if (! $allowed) {
            abort(403, 'No podes enviar mensajes a clientes fuera de tus lineas.');
        }
    }

    private function authorizeDirectAgent(int $agentId): void
    {
        $agent = Agent::findOrFail($agentId);
        $activeVendorId = session('active_vendor_id');

        if ($activeVendorId && (int) $agent->vendor_id !== (int) $activeVendorId) {
            abort(403, 'No podes enviar mensajes a agentes de otro vendor.');
        }

        if (auth()->user()?->hasRole(Roles::ADMIN) && ! $activeVendorId) {
            return;
        }

        if ($this->isAdminMode()) {
            return;
        }

        $lineIds = $this->visibleLineIds();
        $allowed = LineAgent::withoutGlobalScopes()->where('agent_id', $agentId)
            ->whereIn('line_id', $lineIds ?? [])
            ->where('is_active', true)
            ->exists();

        if (! $allowed) {
            abort(403, 'No podes enviar mensajes a agentes fuera de tus lineas.');
        }
    }
}
