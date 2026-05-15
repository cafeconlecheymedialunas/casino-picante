<div>
@section('header')
    <x-livewire.components.page-header title="TICKETS" />
@endsection

    <div class="content-grid">
        <div class="ticket-list">
            <div class="ticket-search">
                <input type="text" placeholder="Buscar tickets..." wire:model.live.debounce.200ms="search" class="search-input">
            </div>
            <div class="ticket-filters">
                <button class="btn-primary" style="height:32px;padding:0 14px;font-size:11px;" wire:click="openCreateModal">+ Nuevo ticket</button>
                <button class="ticket-filter {{ $filter === 'all' ? 'active' : '' }}" wire:click="$set('filter', 'all')">Todos</button>
                <button class="ticket-filter {{ $filter === 'open' ? 'active' : '' }}" wire:click="$set('filter', 'open')">Abiertos ({{ $metrics['open'] }})</button>
                <button class="ticket-filter {{ $filter === 'progress' ? 'active' : '' }}" wire:click="$set('filter', 'progress')">En proceso ({{ $metrics['progress'] }})</button>
                <button class="ticket-filter {{ $filter === 'closed' ? 'active' : '' }}" wire:click="$set('filter', 'closed')">Cerrados ({{ $metrics['closed'] }})</button>
            </div>

            @forelse($tickets as $ticket)
            <div class="ticket-item {{ $selectedTicket && $selectedTicket->id === $ticket->id ? 'selected' : '' }}" wire:click="selectTicket({{ $ticket->id }})">
                <div class="ticket-item-header">
                    <div style="display:flex;align-items:center;gap:6px;min-width:0;">
                        <span class="ticket-user">{{ $ticket->user->name ?? 'Usuario' }}</span>
                        @if($ticket->tracking_code)
                            <span class="ticket-code">{{ $ticket->tracking_code }}</span>
                        @endif
                    </div>
                    <span class="ticket-time">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                <div class="ticket-subject">{{ $ticket->subject }}</div>
                <div class="ticket-item-footer">
                    <span class="ticket-category">{{ $this->categoryLabel($ticket->category) }}</span>
                    <span class="ticket-line">{{ $ticket->line->name ?? 'Sin línea' }}</span>
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
                    <div class="conv-meta">
                    @if($selectedTicket->tracking_code)
                        <span style="color:var(--orange);font-weight:800;">{{ $selectedTicket->tracking_code }}</span> ·
                    @endif
                    {{ $this->categoryLabel($selectedTicket->category) }} · {{ $selectedTicket->line->name ?? 'Sin línea' }} · {{ $selectedTicket->status }} · {{ $selectedTicket->priority }} · {{ $selectedTicket->created_at->diffForHumans() }}
                </div>
                </div>
                <div class="conv-actions">
                    @if($selectedTicket->status === 'closed')
                        <button class="btn-ghost" style="height:30px;padding:0 12px;font-size:11px;" wire:click="reopenTicket">🔄 Reabrir</button>
                    @else
                        @if($selectedTicket->status === 'open')
                            <button class="btn-ghost" style="height:30px;padding:0 12px;font-size:11px;" wire:click="updateStatus('progress')">En proceso</button>
                        @endif
                        <button class="btn-primary" style="height:30px;padding:0 12px;font-size:11px;" wire:click="updateStatus('closed')">Cerrar</button>
                    @endif
                </div>
            </div>

            <div class="conv-messages" id="ticketMessages">
                @foreach($selectedTicket->messages as $message)
                @php
                    $isAgent = (bool)$message->agent_id;
                    $sender = $isAgent ? $message->agent : $message->user;
                    $senderName = $sender
                        ? trim(collect([$sender->name, $sender->apellido ?? null])->filter()->join(' '))
                        : ($isAgent ? 'Agente' : 'Usuario');
                    $avatarValue = $sender?->avatar ?: 'avatar_'.\Illuminate\Support\Str::slug($senderName ?: ($isAgent ? 'agente' : 'usuario'), '-');
                @endphp
                <div class="message {{ $isAgent ? 'agent' : '' }}">
                    <div class="message-avatar {{ $isAgent ? 'agent' : '' }}">
                        <img src="{{ \App\Support\AvatarLibrary::url($avatarValue) }}"
                             alt="{{ $senderName }}"
                             style="width: 100%; height: 100%; border-radius: 50%;">
                    </div>
                    <div class="message-bubble">
                        <div class="message-meta">
                            {{ $senderName }} · {{ $message->created_at->format('H:i') }}
                        </div>
                        <div class="message-text">{{ $message->message }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($selectedTicket->status !== 'closed')
            <div class="conv-input">
                <div class="quick-actions">
                    <button class="btn-ghost quick-btn" wire:click="quickAction('resolved')">👍 Resuelto</button>
                    <button class="btn-ghost quick-btn" wire:click="quickAction('waiting')">⏳ Esperando usuario</button>
                </div>
                <div class="input-box">
                    <input type="text" class="input-text" placeholder="Escribí tu respuesta…" wire:model="newMessage" wire:keydown.enter="sendMessage">
                    <button class="btn-primary send-btn" wire:click="sendMessage" wire:loading.attr="disabled">
                        <span wire:loading.remove>Enviar →</span>
                        <span wire:loading>...</span>
                    </button>
                </div>
                @error('newMessage') <span style="color: var(--orange); font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
            </div>
            @else
            <div class="conv-input" style="text-align:center;padding:14px;">
                <span style="font-size:12px;color:var(--muted);">Ticket cerrado ·</span>
                <button class="btn-ghost" style="height:26px;padding:0 10px;font-size:11px;margin-left:6px;" wire:click="reopenTicket">🔄 Reabrir</button>
            </div>
            @endif
            @else
            <div class="conv-empty">
                <p>Selecciona un ticket para ver la conversación</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Modal crear ticket --}}
    @if($showCreateModal)
    <div class="modal-overlay" wire:click.self="$set('showCreateModal', false)">
        <div class="modal-box" style="max-width:480px;">
            <div class="modal-header">
                <div class="modal-title">Nuevo Ticket</div>
                <button class="modal-close" wire:click="$set('showCreateModal', false)">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Usuario</label>
                    <select class="form-input" wire:model="createUserId">
                        <option value="">Seleccionar usuario...</option>
                        @foreach($assignableUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                        @endforeach
                    </select>
                    @error('createUserId') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Asunto</label>
                    <input type="text" class="form-input" wire:model="createSubject" placeholder="Describe el problema...">
                    @error('createSubject') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select class="form-input" wire:model="createCategory">
                        <option value="juego">Juego</option>
                        <option value="bono">Bono</option>
                        <option value="sorteo">Sorteo</option>
                        <option value="atencion">Atencion</option>
                        <option value="otro">Otro</option>
                    </select>
                    @error('createCategory') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Prioridad</label>
                    <select class="form-input" wire:model="createPriority">
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mensaje inicial</label>
                    <textarea class="form-input" wire:model="createMessage" rows="4" placeholder="Descripción detallada..."></textarea>
                    @error('createMessage') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-ghost" wire:click="$set('showCreateModal', false)">Cancelar</button>
                <button class="btn-primary" wire:click="createTicket" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="createTicket">Crear ticket</span>
                    <span wire:loading wire:target="createTicket">Creando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            const scrollToBottom = () => {
                const container = document.getElementById('ticketMessages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };

            Livewire.on('ticketSelected', () => {
                setTimeout(scrollToBottom, 50);
            });

            Livewire.on('messageSent', () => {
                setTimeout(scrollToBottom, 50);
            });
        });
    </script>

    <style>
        .content-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 0; height: calc(100vh - 180px); padding: 0 28px 28px; }
        @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .content-grid { grid-template-columns: 1fr; height: auto; min-height: calc(100vh - 180px); padding: 0 14px 20px; gap: 20px; }
            .ticket-list { border-right: none; padding-right: 0; }
            .ticket-filters { gap: 4px; }
            .ticket-filter { padding: 4px 8px; font-size: 10px; }
            .ticket-item { padding: 10px; border-radius: 10px; }
            .ticket-item-header { flex-wrap: wrap; gap: 4px; }
            .ticket-user { font-size: 12px; }
            .ticket-conversation { padding-left: 0; }
            .conv-header { flex-direction: column; align-items: flex-start; }
            .conv-actions { width: 100%; justify-content: flex-end; }
            .conv-title { font-size: 13px; }
            .conv-meta { font-size: 10px; }
            .message-bubble { max-width: 85%; padding: 8px 12px; font-size: 12px; }
            .message-text { font-size: 12px; }
            .input-box { padding: 8px 10px; }
            .input-text { font-size: 12px; }
            .send-btn { height: 28px; padding: 0 12px; font-size: 11px; }
            .modal-box { max-width:100% !important; margin:0 10px; border-radius:12px; }
            .modal-header { padding:14px 16px; }
            .modal-body { padding:14px 16px; }
            .modal-actions { padding:12px 16px; }
        }

        .ticket-list { border-right: 1px solid var(--line); padding-right: 20px; overflow-y: auto; }
        .ticket-filters { display: flex; gap: 6px; margin-bottom: 12px; flex-wrap: wrap; }
        .ticket-filter { padding: 6px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; background: transparent; color: var(--muted); border: 1px solid var(--line-2); cursor: pointer; }
        .ticket-filter.active { background: var(--orange); color: #190702; border: none; }

        .ticket-item { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 12px; cursor: pointer; margin-bottom: 8px; transition: all 0.2s; }
        .ticket-item:hover { border-color: var(--orange); }
        .ticket-item.selected { border-color: rgba(255,106,26,0.5); background: linear-gradient(180deg, rgba(255,106,26,0.06), rgba(20,8,8,0.85)); }
        .ticket-item-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px; }
        .ticket-user { font-weight: 700; font-size: 13px; }
        .ticket-code { font-size: 9px; font-weight: 800; color: var(--orange); letter-spacing: .06em; background: rgba(255,106,26,.1); padding: 1px 5px; border-radius: 4px; }
        .ticket-time { font-size: 10px; color: var(--muted); white-space: nowrap; }
        .ticket-subject { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
        .ticket-item-footer { display: flex; gap: 6px; align-items: center; }
        .ticket-line { padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 10px; font-weight: 700; }
        .ticket-category { padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.08); color: var(--muted); font-size: 10px; font-weight: 800; }
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
        .ticket-search { margin-bottom: 12px; }
        .search-input { width: 100%; padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 12px; color: var(--muted); }
        .search-input:focus { outline: none; border-color: var(--orange); color: var(--white); }

        .modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,.7);display:flex;align-items:center;justify-content:center;z-index:1000;backdrop-filter:blur(2px); }
        .modal-box { background:linear-gradient(180deg,#1e0d09,#130808);border:1px solid var(--line-warm);border-radius:14px;width:100%;padding:0;overflow:hidden; }
        .modal-header { display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid var(--line); }
        .modal-title { font-family:var(--font-display);font-size:18px;letter-spacing:.04em; }
        .modal-close { background:none;border:none;color:var(--muted);font-size:16px;cursor:pointer;padding:4px; }
        .modal-close:hover { color:var(--white); }
        .modal-body { padding:20px 22px;display:flex;flex-direction:column;gap:14px; }
        .modal-actions { display:flex;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--line); }
        .form-group { display:flex;flex-direction:column;gap:5px; }
        .form-label { font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--muted-2);text-transform:uppercase; }
        .form-input { background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body);width:100%; }
        .form-input:focus { outline:none;border-color:var(--orange); }
        .form-error { font-size:11px;color:#ff5050;margin-top:2px; }
    </style>
</div>
