<?php

namespace App\Livewire\Components;

use App\Models\Agent;
use App\Models\Chat;
use Livewire\Component;

class AgentMessaging extends Component
{
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
        $query = Chat::where('agent_id', $agentId);

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
        $agentId = $this->directAgentId();

        if (! $agentId) {
            return null;
        }

        return $this->findExistingChat($agentId) ?: Chat::create([
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
            return $this->targetAgentId;
        }

        if ($this->targetType === 'Agente' && $this->targetEmail !== '') {
            $agentId = Agent::where('email', $this->targetEmail)->value('id');

            if ($agentId) {
                return $agentId;
            }
        }

        if ($this->targetUserId) {
            return session('active_agent_id')
                ?: Agent::where('status', 'active')->orderBy('id')->value('id');
        }

        return null;
    }
}
