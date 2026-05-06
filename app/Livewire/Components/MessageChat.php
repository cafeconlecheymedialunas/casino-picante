<?php

namespace App\Livewire\Components;

use App\Models\Chat;
use App\Models\ChatMessage;
use Livewire\Component;

class MessageChat extends Component
{
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
        $this->agentId = $agentId ?: session('active_agent_id');
        $this->isAgent = $isAgent;
        $this->allChats = $allChats;
        $this->singleChatId = $singleChatId;
        $this->selectedChatId = $singleChatId ?: $this->chats()->first()?->id;
    }

    public function selectChat(int $chatId): void
    {
        $chat = $this->chats()->firstWhere('id', $chatId);

        if ($chat) {
            $this->selectedChatId = $chat->id;
        }
    }

    public function createChat(): void
    {
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
        if (! $this->isAgent) {
            return;
        }

        $this->selectedChat()?->update(['status' => 'closed']);
    }

    public function render()
    {
        $chats = $this->chats();
        $selectedChat = $this->selectedChat();

        return view('livewire.components.message-chat', compact('chats', 'selectedChat'));
    }

    private function chats()
    {
        $query = Chat::with(['user', 'agent', 'messages.user', 'messages.agent'])
            ->orderBy('updated_at', 'desc');

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
}
