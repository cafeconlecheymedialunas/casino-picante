<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">TICKETS</h1>
        </div>
    </div>

    <div class="content-grid">
        <div class="ticket-list">
            <div class="ticket-filters">
                <button class="ticket-filter {{ $filter === 'open' ? 'active' : '' }}" wire:click="$set('filter', 'open')">Abiertos</button>
                <button class="ticket-filter {{ $filter === 'progress' ? 'active' : '' }}" wire:click="$set('filter', 'progress')">En proceso</button>
                <button class="ticket-filter {{ $filter === 'closed' ? 'active' : '' }}" wire:click="$set('filter', 'closed')">Cerrados</button>
            </div>

            @forelse($tickets as $ticket)
            <div class="ticket-item {{ $selectedTicket && $selectedTicket->id === $ticket->id ? 'selected' : '' }}" wire:click="selectTicket({{ $ticket->id }})">
                <div class="ticket-item-header">
                    <span class="ticket-user">{{ $ticket->user->name ?? 'Usuario' }}</span>
                    <span class="ticket-time">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                <div class="ticket-subject">{{ $ticket->subject }}</div>
                <div class="ticket-item-footer">
                    <span class="ticket-line">{{ $ticket->line_id ?? 'L1' }}</span>
                    <span class="ticket-status {{ $ticket->status }}">
                        @if($ticket->status === 'open')● Abierto
                        @elseif($ticket->status === 'progress')● En proceso
                        @else● Cerrado
                        @endif
                    </span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay tickets</p>
            </div>
            @endforelse
        </div>

        <div class="ticket-conversation">
            @if($selectedTicket)
            <div class="conv-header">
                <div>
                    <div class="conv-title">{{ $selectedTicket->user->name ?? 'Usuario' }} · {{ $selectedTicket->subject }}</div>
                    <div class="conv-meta">{{ $selectedTicket->line_id ?? 'L1' }} · {{ $selectedTicket->status }} · {{ $selectedTicket->created_at->diffForHumans() }}</div>
                </div>
                <div class="conv-actions">
                    <button class="btn-ghost" style="height: 30px; padding: 0 12px; font-size: 11px;" wire:click="updateStatus('progress')">En proceso</button>
                    <button class="btn-primary" style="height: 30px; padding: 0 12px; font-size: 11px;" wire:click="updateStatus('closed')">Cerrar</button>
                </div>
            </div>

            <div class="conv-messages">
                @foreach($selectedTicket->messages as $message)
                <div class="message {{ $message->agent_id ? 'agent' : '' }}">
                    <div class="message-avatar {{ $message->agent_id ? 'agent' : '' }}">
                        {{ strtoupper(substr($message->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="message-bubble">
                        <div class="message-meta">{{ $message->created_at->format('H:i') }}</div>
                        <div class="message-text">{{ $message->message }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="conv-input">
                <div class="quick-actions">
                    <button class="btn-ghost quick-btn">👍 Resuelto</button>
                    <button class="btn-ghost quick-btn">⏳ Esperando usuario</button>
                </div>
                <div class="input-box">
                    <input type="text" class="input-text" placeholder="Escribí tu respuesta…" wire:model="newMessage" wire:keydown.enter="sendMessage">
                    <button class="btn-primary send-btn" wire:click="sendMessage">Enviar →</button>
                </div>
            </div>
            @else
            <div class="conv-empty">
                <p>Selecciona un ticket para ver la conversación</p>
            </div>
            @endif
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .content-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 0; height: calc(100vh - 180px); padding: 0 28px 28px; }
        @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }

        .ticket-list { border-right: 1px solid var(--line); padding-right: 20px; overflow-y: auto; }
        .ticket-filters { display: flex; gap: 6px; margin-bottom: 12px; flex-wrap: wrap; }
        .ticket-filter { padding: 6px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; background: transparent; color: var(--muted); border: 1px solid var(--line-2); cursor: pointer; }
        .ticket-filter.active { background: var(--orange); color: #190702; border: none; }

        .ticket-item { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 12px; cursor: pointer; margin-bottom: 8px; transition: all 0.2s; }
        .ticket-item:hover { border-color: var(--orange); }
        .ticket-item.selected { border-color: rgba(255,106,26,0.5); background: linear-gradient(180deg, rgba(255,106,26,0.06), rgba(20,8,8,0.85)); }
        .ticket-item-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px; }
        .ticket-user { font-weight: 700; font-size: 13px; }
        .ticket-time { font-size: 10px; color: var(--muted); }
        .ticket-subject { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
        .ticket-item-footer { display: flex; gap: 6px; align-items: center; }
        .ticket-line { padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 10px; font-weight: 700; }
        .ticket-status { font-size: 10px; font-weight: 700; }
        .ticket-status.open { color: var(--good); }
        .ticket-status.progress { color: var(--amber); }

        .ticket-conversation { padding-left: 20px; display: flex; flex-direction: column; }
        .conv-empty { text-align: center; color: var(--muted); padding: 40px; }
        .conv-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 14px; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 10px; }
        .conv-title { font-weight: 700; font-size: 15px; }
        .conv-meta { font-size: 11px; color: var(--muted); }
        .conv-actions { display: flex; gap: 6px; }
        .conv-messages { flex: 1; padding: 18px 0; display: flex; flex-direction: column; gap: 12px; overflow-y: auto; }
        .message { display: flex; gap: 10px; justify-content: flex-start; }
        .message.agent { justify-content: flex-end; }
        .message-avatar { width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.08); color: var(--muted); font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .message-avatar.agent { background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; }
        .message-bubble { max-width: 70%; padding: 10px 14px; border-radius: 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--line); }
        .message.agent .message-bubble { background: linear-gradient(180deg, var(--orange-2), var(--orange)); border: none; color: #190702; }
        .message-meta { font-size: 10px; font-weight: 700; opacity: 0.7; margin-bottom: 2px; }
        .message-text { font-size: 13px; line-height: 1.4; }

        .conv-input { padding-top: 12px; border-top: 1px solid var(--line); }
        .quick-actions { display: flex; gap: 8px; margin-bottom: 8px; flex-wrap: wrap; }
        .quick-btn { height: 26px; padding: 0 10px; font-size: 10px; font-weight: 700; }
        .input-box { display: flex; align-items: center; gap: 8px; padding: 12px 14px; border-radius: 12px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); }
        .input-text { flex: 1; font-size: 13px; color: var(--white); background: transparent; border: none; outline: none; }
        .input-text::placeholder { color: var(--muted); }
        .send-btn { height: 32px; padding: 0 16px; font-size: 12px; }

        .empty-state { text-align: center; color: var(--muted); padding: 40px; }
    </style>
</div>