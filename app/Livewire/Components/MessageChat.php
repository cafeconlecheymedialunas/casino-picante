<?php

namespace App\Livewire\Components;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\LineAgent;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use Livewire\Component;

class MessageChat extends Component
{
    use HasLinePermissions;

    public ?int $userId = null;

    public ?int $agentId = null;

    public bool $isAgent = false;

    public bool $allChats = false;

    public ?int $singleChatId = null;

    public ?int $selectedChatId = null;

    public string $subject = '';

    public string $newChatMessage = '';

    public string $reply = '';

    public function mount(?int $userId = null, ?int $agentId = null, bool $isAgent = false, bool $allChats = false, ?int $singleChatId = null): void
    {
        $this->userId = $userId;
        $this->isAgent = $isAgent;
        $this->agentId = $isAgent ? (session('active_agent_id') ?: $agentId) : $agentId;
        $this->allChats = $allChats;
        $this->singleChatId = $singleChatId;
        $this->authorizeChatAccess();
        $this->selectedChatId = $singleChatId ?: $this->chats()->first()?->id;
    }

    public function selectChat(int $chatId): void
    {
        $this->authorizeChatAccess();
        $chat = $this->chats()->firstWhere('id', $chatId);

        if ($chat) {
            $this->selectedChatId = $chat->id;
        }
    }

    public function createChat(): void
    {
        $this->authorizeChatAccess();

        if ($this->isAgent || ! $this->userId) {
            return;
        }

        $this->validate([
            'subject' => 'required|min:3|max:140',
            'newChatMessage' => 'required|min:2|max:2000',
        ]);

        $chat = Chat::create([
            'user_id' => $this->userId,
            'subject' => trim($this->subject),
            'status' => 'open',
            'context_type' => 'perfil',
            'context_name' => 'Mi perfil',
        ]);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $this->userId,
            'message' => trim($this->newChatMessage),
        ]);

        $this->subject = '';
        $this->newChatMessage = '';
        $this->selectedChatId = $chat->id;
        session()->flash('chat_message', 'Mensaje enviado correctamente.');
    }

    public function sendReply(): void
    {
        $this->authorizeChatAccess();
        $chat = $this->selectedChat();

        if (! $chat) {
            return;
        }

        $this->validate([
            'reply' => 'required|min:2|max:2000',
        ]);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $this->isAgent ? null : $this->userId,
            'agent_id' => $this->isAgent ? $this->agentId : null,
            'message' => trim($this->reply),
        ]);

        if ($chat->status === 'closed') {
            $chat->update(['status' => 'open']);
        }

        $this->reply = '';
        $this->selectedChatId = $chat->id;
    }

    public function closeChat(): void
    {
        $this->authorizeChatAccess();

        if (! $this->isAgent) {
            return;
        }

        $this->selectedChat()?->update(['status' => 'closed']);
    }

    public function render()
    {
        $this->authorizeChatAccess();
        $chats = $this->chats();
        $selectedChat = $this->selectedChat();

        return view('livewire.components.message-chat', compact('chats', 'selectedChat'));
    }

    private function chats()
    {
        $query = Chat::with(['user', 'agent', 'messages.user', 'messages.agent'])
            ->orderBy('updated_at', 'desc');

        $this->scopeChatQuery($query);

        if ($this->singleChatId) {
            return $query->where('id', $this->singleChatId)->get();
        }

        if ($this->allChats) {
            return $query->get();
        }

        if ($this->isAgent) {
            $query->where(function ($inner) {
                $inner->where('agent_id', $this->agentId)
                    ->orWhereNull('agent_id');
            });
        } else {
            $query->where('user_id', $this->userId);
        }

        return $query->get();
    }

    private function selectedChat(): ?Chat
    {
        if (! $this->selectedChatId) {
            return null;
        }

        return $this->chats()->firstWhere('id', $this->selectedChatId);
    }

    private function authorizeChatAccess(): void
    {
        if (! auth()->check()) {
            abort(403, 'Sin sesion activa.');
        }

        if (! $this->isAgent) {
            if (! $this->userId || auth()->id() !== $this->userId) {
                abort(403, 'No podes ver chats de otro usuario.');
            }

            return;
        }

        if (
            ! $this->hasLinePermission(Permissions::TICKET_READ)
            && ! $this->hasLinePermission(Permissions::USER_READ)
            && ! $this->hasLinePermission(Permissions::AGENT_UPDATE)
            && ! $this->hasLinePermission(Permissions::AGENT_ASSIGN)
        ) {
            abort(403, 'Sin permiso para ver chats.');
        }
    }

    private function scopeChatQuery($query): void
    {
        if (! $this->isAgent) {
            return;
        }

        if ($this->isAdminMode()) {
            return;
        }

        $agentId = $this->agentId ?: session('active_agent_id');
        $lineIds = $this->visibleLineIds();

        $query->where(function ($inner) use ($agentId, $lineIds) {
            if ($agentId) {
                $inner->where('agent_id', $agentId);
            }

            if ($lineIds !== []) {
                $inner->orWhere(function ($clientChats) use ($lineIds) {
                    $clientChats->whereNotNull('user_id')
                        ->whereHas('user', function ($user) use ($lineIds) {
                            $user->whereIn('line_id', $lineIds)
                                ->orWhereHas('lines', fn ($line) => $line
                                    ->whereIn('lines.id', $lineIds)
                                    ->where('line_clients.is_active', true));
                        });
                });
            }
        });
    }

    private function visibleLineIds(): array
    {
        $lineId = session('active_line_id');

        if ($lineId) {
            return [(int) $lineId];
        }

        return LineAgent::where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
            ->pluck('line_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
