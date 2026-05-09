<div class="message-chat {{ $singleChatId ? 'single-chat' : '' }}">
    <style>
        .message-chat { display: grid; grid-template-columns: 280px minmax(0, 1fr); gap: 14px; }
        .message-chat.single-chat { grid-template-columns: minmax(0, 1fr); }
        .chat-panel { border: 1px solid var(--line); border-radius: 8px; background: linear-gradient(180deg, #170b0b, #0f0707); overflow: hidden; }
        .chat-head { padding: 14px 16px; border-bottom: 1px solid var(--line); }
        .chat-title { font-family: var(--font-display); font-size: 20px; letter-spacing: .03em; }
        .chat-sub { color: var(--muted-2); font-size: 11px; margin-top: 2px; }
        .chat-list { max-height: 420px; overflow-y: auto; }
        .chat-pill { width: 100%; padding: 12px 14px; border: 0; border-bottom: 1px solid var(--line); background: transparent; color: var(--white); text-align: left; cursor: pointer; }
        .chat-pill:hover, .chat-pill.active { background: rgba(255,106,26,.08); }
        .chat-subject { font-weight: 800; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .chat-meta { color: var(--muted-2); font-size: 10px; margin-top: 4px; display: flex; gap: 6px; align-items: center; }
        .status-dot { width: 7px; height: 7px; border-radius: 999px; display: inline-block; background: var(--warn); }
        .status-progress { background: var(--good); }
        .status-closed { background: var(--muted-2); }
        .chat-body { display: flex; flex-direction: column; min-height: 420px; }
        .messages { flex: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
        .bubble { max-width: 78%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: rgba(255,255,255,.04); }
        .bubble.mine { margin-left: auto; border-color: rgba(255,106,26,.45); background: rgba(255,106,26,.12); }
        .bubble-agent { color: var(--orange); font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 4px; }
        .bubble-text { font-size: 13px; line-height: 1.45; white-space: pre-wrap; }
        .bubble-time { color: var(--muted-2); font-size: 10px; margin-top: 6px; }
        .chat-form { border-top: 1px solid var(--line); padding: 12px; display: flex; gap: 8px; align-items: flex-end; }
        .chat-input { flex: 1; min-height: 42px; max-height: 120px; resize: vertical; background: rgba(255,255,255,.05); border: 1px solid var(--line-2); border-radius: 8px; padding: 10px 12px; color: var(--white); font-size: 13px; }
        .chat-input:focus, .new-chat input:focus, .new-chat textarea:focus { outline: none; border-color: var(--orange); }
        .new-chat { padding: 14px; border-top: 1px solid var(--line); display: grid; gap: 8px; }
        .new-chat input, .new-chat textarea { width: 100%; background: rgba(255,255,255,.05); border: 1px solid var(--line-2); border-radius: 8px; padding: 10px 12px; color: var(--white); font-size: 13px; }
        .chat-empty { color: var(--muted-2); text-align: center; padding: 38px 16px; }
        .chat-flash { margin-bottom: 10px; border: 1px solid rgba(37,196,107,.35); background: rgba(37,196,107,.12); color: var(--good); border-radius: 8px; padding: 10px 12px; font-size: 12px; font-weight: 800; }
        @media (max-width: 860px) { .message-chat { grid-template-columns: 1fr; } }
    </style>

    @unless($singleChatId)
    <div class="chat-panel">
        <div class="chat-head">
            <div class="chat-title">Conversaciones</div>
            <div class="chat-sub">{{ $chats->count() }} chat{{ $chats->count() !== 1 ? 's' : '' }}</div>
        </div>
        <div class="chat-list">
            @forelse($chats as $chat)
                <button type="button" wire:click="selectChat({{ $chat->id }})" class="chat-pill {{ $selectedChat?->id === $chat->id ? 'active' : '' }}">
                    <div class="chat-subject">{{ $chat->subject }}</div>
                    <div class="chat-meta">
                        <span class="status-dot status-{{ $chat->status }}"></span>
                        <span>{{ $chat->status }}</span>
                        <span>{{ $chat->messages->count() }} mensajes</span>
                    </div>
                </button>
            @empty
                <div class="chat-empty">Todavia no hay conversaciones.</div>
            @endforelse
        </div>

        @unless($isAgent)
            <form wire:submit.prevent="createChat" class="new-chat">
                @if(session('chat_message')) <div class="chat-flash">{{ session('chat_message') }}</div> @endif
                <input type="text" wire:model="subject" placeholder="Asunto">
                @error('subject') <div class="fe-error">{{ $message }}</div> @enderror
                <textarea
                    wire:model="newChatMessage"
                    rows="3"
                    placeholder="Escribi tu mensaje"
                    onkeydown="if (event.key === 'Enter' && ! event.shiftKey) { event.preventDefault(); this.closest('form').requestSubmit(); }"
                ></textarea>
                @error('newChatMessage') <div class="fe-error">{{ $message }}</div> @enderror
                <button type="submit" class="btn-primary">Nuevo mensaje</button>
            </form>
        @endunless
    </div>
    @endunless

    <div class="chat-panel">
        <div class="chat-head">
            <div class="chat-title">{{ $selectedChat?->subject ?? 'Chat' }}</div>
            <div class="chat-sub">
                @if($selectedChat)
                    <span style="color:var(--muted-2);">Estado: {{ $selectedChat->status }}</span>
                    @if($isAgent)
                        @if($selectedChat->user)
                            &nbsp;·&nbsp;<span style="color:var(--muted);">Cliente: <strong style="color:var(--white);">{{ $selectedChat->user->username ?? $selectedChat->user->name }}</strong></span>
                        @elseif($selectedChat->context_name)
                            &nbsp;·&nbsp;<span style="color:var(--muted);">{{ $selectedChat->context_type }}: <strong style="color:var(--white);">{{ $selectedChat->context_name }}</strong></span>
                        @endif
                    @endif
                @else
                    Selecciona una conversacion
                @endif
            </div>
        </div>

        @if($selectedChat)
            <div class="chat-body">
                <div class="messages">
                    @foreach($selectedChat->messages as $message)
                        @php
                            // Determinar si este mensaje es "mio" (lado derecho)
                            if ($isAgent) {
                                if ($agentId !== null) {
                                    // Agente con identity: solo sus propios mensajes por agent_id
                                    $mine = (int) $message->agent_id === (int) $agentId;
                                } else {
                                    // Admin sin agent record: sus mensajes se guardaron con user_id = auth()->id()
                                    $mine = $message->agent_id === null
                                        && $message->user_id !== null
                                        && (int) $message->user_id === auth()->id();
                                }
                            } else {
                                $mine = (int) $message->user_id === (int) $userId;
                            }

                            // Nombre a mostrar en la burbuja
                            if ($message->agent_id) {
                                $senderName = $message->agent?->name ?? 'Agente';
                                $senderRole = 'agente';
                            } elseif ($message->user_id && (int) $message->user_id !== (int) $userId) {
                                // user_id es del admin (no del cliente del chat)
                                $senderName = $message->user?->name ?? 'Admin';
                                $senderRole = 'admin';
                            } else {
                                $senderName = $message->user?->username ?? $message->user?->name ?? 'Cliente';
                                $senderRole = 'cliente';
                            }
                        @endphp
                        <div class="bubble {{ $mine ? 'mine' : '' }}">
                            <div class="bubble-agent" style="{{ $senderRole === 'cliente' ? 'color:var(--muted)' : ($senderRole === 'admin' ? 'color:var(--amber)' : '') }}">
                                {{ strtoupper($senderRole) }}: {{ $senderName }}
                            </div>
                            <div class="bubble-text">{{ $message->message }}</div>
                            <div class="bubble-time">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endforeach
                </div>

                <form wire:submit.prevent="sendReply" class="chat-form">
                    <textarea
                        wire:model="reply"
                        class="chat-input"
                        rows="2"
                        placeholder="Responder..."
                        onkeydown="if (event.key === 'Enter' && ! event.shiftKey) { event.preventDefault(); this.closest('form').requestSubmit(); }"
                    ></textarea>
                    <button type="submit" class="btn-primary">Enviar</button>
                    @if($isAgent && $selectedChat->status !== 'closed')
                        <button type="button" wire:click="closeChat" class="btn-ghost">Cerrar</button>
                    @endif
                </form>
                @error('reply') <div class="fe-error" style="padding:0 12px 12px;">{{ $message }}</div> @enderror
            </div>
        @else
            <div class="chat-empty">Selecciona o crea una conversacion para empezar.</div>
        @endif
    </div>
</div>
