<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">LÍNEAS & REDES</h1>
            <p class="page-subtitle">Cada línea opera con su propio juego de redes sociales y mensaje automático</p>
        </div>
        <button class="btn-primary"><span>+</span> Nueva línea</button>
    </div>

    <div class="content">
        <p class="lines-desc">Cada línea opera con su propio juego de redes sociales y mensaje automático. Activá o desactivá líneas y enlaces individuales sin tocar el frontend.</p>
        
        <div class="lines-grid">
            @forelse($lines as $index => $line)
            <div class="line-card">
                <div class="line-glow" style="background: radial-gradient(circle, #ff6a1a55, transparent 70%);"></div>
                <div class="line-header">
                    <div class="line-header-title">
                        <span class="line-icon">{{ $line->icon }}</span>
                        <div>
                            <div class="line-name">{{ strtoupper($line->name) }}</div>
                            <div class="line-sub">{{ $line->description }}</div>
                        </div>
                    </div>
                    <label class="line-toggle">
                        <div class="toggle-switch {{ $line->status === 'active' ? 'active' : '' }}" wire:click="toggleLine({{ $line->id }})">
                            <div class="toggle-knob"></div>
                        </div>
                        {{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}
                    </label>
                </div>
                <div class="line-links">
                    @if($line->whatsapp)
                    <div class="line-link">
                        <div class="link-icon">💬</div>
                        <div class="link-info">
                            <div class="link-label">WhatsApp</div>
                            <div class="link-value">{{ $line->whatsapp }}</div>
                        </div>
                        @if($line->whatsapp_message)
                        <span class="link-badge">+ msg auto</span>
                        @endif
                        <button class="btn-ghost link-edit" wire:click="openEditModal({{ $line->id }})">✎</button>
                    </div>
                    @endif
                    @if($line->telegram)
                    <div class="line-link">
                        <div class="link-icon">✈️</div>
                        <div class="link-info">
                            <div class="link-label">Telegram</div>
                            <div class="link-value">{{ $line->telegram }}</div>
                        </div>
                        @if($line->telegram_message)
                        <span class="link-badge">+ msg auto</span>
                        @endif
                        <button class="btn-ghost link-edit" wire:click="openEditModal({{ $line->id }})">✎</button>
                    </div>
                    @endif
                    @if($line->whatsapp_channel)
                    <div class="line-link">
                        <div class="link-icon">📢</div>
                        <div class="link-info">
                            <div class="link-label">Canal de WhatsApp</div>
                            <div class="link-value">{{ Str::limit($line->whatsapp_channel, 20) }}</div>
                        </div>
                        <button class="btn-ghost link-edit" wire:click="openEditModal({{ $line->id }})">✎</button>
                    </div>
                    @endif
                    @if($line->facebook)
                    <div class="line-link">
                        <div class="link-icon">📘</div>
                        <div class="link-info">
                            <div class="link-label">Facebook</div>
                            <div class="link-value">{{ $line->facebook }}</div>
                        </div>
                        <button class="btn-ghost link-edit" wire:click="openEditModal({{ $line->id }})">✎</button>
                    </div>
                    @endif
                    @if($line->instagram)
                    <div class="line-link">
                        <div class="link-icon">📸</div>
                        <div class="link-info">
                            <div class="link-label">Instagram</div>
                            <div class="link-value">{{ $line->instagram }}</div>
                        </div>
                        <button class="btn-ghost link-edit" wire:click="openEditModal({{ $line->id }})">✎</button>
                    </div>
                    @endif
                    <button class="btn-ghost link-add">+ Agregar enlace</button>
                </div>
                <div class="line-footer">
                    <span>Agentes asignados</span>
                    <span class="line-agents">{{ rand(2, 5) }} agentes</span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay líneas creadas</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($showModal && $editingLine)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>EDITAR LÍNEA</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" value="{{ $editingLine->name }}">
                    </div>
                    <div class="form-group">
                        <label>Icono</label>
                        <input type="text" value="{{ $editingLine->icon }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>WhatsApp</label>
                    <input type="text" value="{{ $editingLine->whatsapp }}">
                </div>
                <div class="form-group">
                    <label>Mensaje automático WhatsApp</label>
                    <textarea class="edit-input tall">{{ $editingLine->whatsapp_message }}</textarea>
                </div>
                <div class="form-group">
                    <label>Telegram</label>
                    <input type="text" value="{{ $editingLine->telegram }}">
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .content { padding: 0 28px 28px; }
        .lines-desc { font-size: 13px; color: var(--muted); margin: 0 0 18px; max-width: 540px; }
        
        .lines-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        @media (max-width: 1200px) { .lines-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .lines-grid { grid-template-columns: 1fr; } }

        .line-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 18px; position: relative; overflow: hidden; }
        .line-glow { position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; border-radius: 50%; }
        .line-header { position: relative; display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
        .line-header-title { display: flex; align-items: center; gap: 8px; }
        .line-icon { font-size: 16px; }
        .line-name { font-family: var(--font-display); font-size: 22px; letter-spacing: 0.04em; }
        .line-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .line-toggle { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--muted); cursor: pointer; }
        .toggle-switch { width: 32px; height: 18px; border-radius: 999px; background: var(--line); position: relative; cursor: pointer; }
        .toggle-switch.active { background: var(--orange); }
        .toggle-knob { position: absolute; top: 2; right: 2; width: 14px; height: 14px; border-radius: 50%; background: #fff; transition: transform 0.2s; }
        .toggle-switch.active .toggle-knob { transform: translateX(-14px); }

        .line-links { display: grid; gap: 8px; }
        .line-link { display: flex; gap: 8px; align-items: center; padding: 8px 10px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); }
        .link-icon { width: 28px; height: 28px; border-radius: 6px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .link-info { flex: 1; }
        .link-label { font-size: 10px; color: var(--muted); }
        .link-value { font-size: 12px; font-family: var(--font-mono); }
        .link-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.12); color: var(--orange); font-weight: 700; }
        .link-edit { width: 28px; height: 28px; padding: 0; font-size: 11px; }
        .link-add { height: 32px; font-size: 11px; font-weight: 700; margin-top: 4px; border-style: dashed; }
        .line-footer { margin-top: 12px; padding-top: 10px; border-top: 1px solid var(--line); font-size: 11px; color: var(--muted); display: flex; justify-content: space-between; }
        .line-agents { color: var(--orange); font-weight: 700; }

        .empty-state { text-align: center; color: var(--muted); padding: 40px; grid-column: 1 / -1; }

        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 480px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .edit-input.tall { min-height: 56px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }
    </style>
</div>