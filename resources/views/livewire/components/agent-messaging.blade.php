<div class="agent-message" wire:key="agent-message-{{ md5($targetType.$targetName.$targetEmail.$targetPhone.$contextLabel) }}">
    @once
        <style>
            .agent-message { display: inline-flex; }
            .agent-message .mini-icon { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
            .agent-message .btn-text { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 32px; padding: 0 10px; border-radius: 7px; border: 1px solid var(--line); background: rgba(255,255,255,.03); color: var(--white); text-decoration: none; font-size: 11px; font-weight: 700; white-space: nowrap; cursor: pointer; }
            .agent-message .btn-text:hover { border-color: var(--orange); background: rgba(255,106,26,.15); }
            .agent-message-overlay { position: fixed; inset: 0; z-index: 260; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0,0,0,.72); }
            .agent-message-panel { width: min(760px, 100%); max-height: 88vh; overflow: hidden; border: 1px solid var(--line-2); border-radius: 8px; background: linear-gradient(180deg, #1c0e0e, #120909); box-shadow: 0 24px 70px rgba(0,0,0,.45); }
            .agent-message-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 18px 20px; border-bottom: 1px solid var(--line); }
            .agent-message-head h3 { margin: 0; font-family: var(--font-display); font-size: 22px; letter-spacing: .03em; }
            .agent-message-head p { margin: 4px 0 0; color: var(--muted-2); font-size: 12px; }
            .agent-message-close { width: 30px; height: 30px; border: 1px solid var(--line); border-radius: 7px; color: var(--muted); background: rgba(255,255,255,.03); cursor: pointer; }
            .agent-message-close:hover { color: var(--white); border-color: var(--orange); background: rgba(255,106,26,.15); }
            .agent-message-chat { padding: 14px; max-height: 72vh; overflow: auto; }
            .agent-message-empty { padding: 28px 14px; text-align: center; color: var(--muted-2); font-size: 13px; }
        </style>
    @endonce

    <button type="button" wire:click="openPanel" class="btn-text" title="Enviar mensaje a un agente">
        <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M21 12a8 8 0 0 1-8 8H7l-4 3 1.4-5A8 8 0 1 1 21 12Z"/>
            <path d="M8 10h8M8 14h5"/>
        </svg>
        Mensaje
    </button>

    @if($open)
        <div class="agent-message-overlay" wire:click.self="closePanel">
            <div class="agent-message-panel">
                <div class="agent-message-head">
                    <div>
                        <h3>Mensajeria con agentes</h3>
                        <p>{{ $targetType }}: {{ $targetName ?: 'sin nombre' }}</p>
                    </div>
                    <button type="button" class="agent-message-close" wire:click="closePanel">x</button>
                </div>

                @if($activeChatId)
                    <div class="agent-message-chat">
                        <livewire:components.message-chat
                            :is-agent="true"
                            :all-chats="true"
                            :single-chat-id="$activeChatId"
                            :key="'agent-message-single-chat-'.$activeChatId"
                        />
                    </div>
                @else
                    <div class="agent-message-empty">No se pudo abrir un chat directo para este contacto.</div>
                @endif
            </div>
        </div>
    @endif
</div>
