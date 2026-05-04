<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">LÍNEAS & REDES</h1>
            <p class="page-subtitle">Cada línea opera con su propio juego de redes sociales y mensaje automático</p>
        </div>
        <button class="btn-primary" wire:click="openCreateModal"><span>+</span> Nueva línea</button>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content modal-large" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingLine ? 'EDITAR LÍNEA' : 'NUEVA LÍNEA' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveLine">
                <div class="form-section">
                    <h4 class="form-section-title">DATOS BÁSICOS</h4>
                    <div class="form-group">
                        <label>Nombre de la línea</label>
                        <input type="text" placeholder="Nombre de la línea..." wire:model="name">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tipo principal</label>
                            <select wire:model="type" class="form-select">
                                <option value="whatsapp">WhatsApp</option>
                                <option value="telegram">Telegram</option>
                                <option value="phone">Teléfono</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select wire:model="status" class="form-select">
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Icono (emoji)</label>
                            <input type="text" placeholder="🔥" wire:model="icon">
                        </div>
                        <div class="form-group">
                            <label>Encargado</label>
                            <select wire:model="encargado_id" class="form-select">
                                <option value="">Sin encargado</option>
                                @foreach($availableAgents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea placeholder="Descripción de la línea..." wire:model="description" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="form-section-title">CONTACTOS</h4>
                    <div class="contact-repeater-mini">
                        @foreach($editContactLinks as $index => $link)
                        <div class="contact-row-mini" wire:key="cl-{{ $index }}">
                            <select wire:model="editContactLinks.{{ $index }}.type" class="contact-type-mini">
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="telegram">✈️ Telegram</option>
                                <option value="instagram">📷 Instagram</option>
                                <option value="facebook">📘 Facebook</option>
                                <option value="phone">📞 Teléfono</option>
                            </select>
                            <input type="text" wire:model="editContactLinks.{{ $index }}.value" placeholder="Valor..." class="contact-value-mini">
                            <button type="button" wire:click="removeContactLink({{ $index }})" class="contact-remove-mini">✕</button>
                        </div>
                        @endforeach
                        <button type="button" wire:click="addContactLink" class="contact-add-mini">+ Agregar contacto</button>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="form-section-title">PLATAFORMAS</h4>
                    <div class="platforms-mini">
                        @foreach($availablePlatforms as $platform)
                        @php $isSelected = collect($editPlatforms)->firstWhere('platform_id', $platform->id); @endphp
                        <label class="platform-chip-mini {{ $isSelected ? 'active' : '' }}" wire:click="togglePlatform({{ $platform->id }})">
                            <span class="platform-check-mini">{{ $isSelected ? '✓' : '' }}</span>
                            @if($platform->logo_url)
                            <img src="{{ $platform->logo_url }}" class="platform-logo-mini">
                            @endif
                            <span>{{ $platform->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="modal-actions">
                    @if($editingLine)
                    <button type="button" class="btn-delete" wire:click="deleteLine({{ $editingLine->id }})" wire:confirm="¿Eliminar esta línea?">Eliminar</button>
                    @endif
                    <div class="modal-actions-right">
                        <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                        <button type="submit" class="btn-primary">{{ $editingLine ? 'Guardar' : 'Crear' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(session()->has('message'))
    <div class="flash-message">{{ session('message') }}</div>
    @endif

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
                    </div>
                    @endif
                    @if($line->contact_links)
                        @foreach($line->contact_links as $link)
                        @if(!in_array($link['type'], ['whatsapp', 'telegram']))
                        <div class="line-link">
                            <div class="link-icon">
                                @if($link['type'] === 'instagram')📷@elseif($link['type'] === 'facebook')📘@elseif($link['type'] === 'phone')📞@endif
                            </div>
                            <div class="link-info">
                                <div class="link-label">{{ ucfirst($link['type']) }}</div>
                                <div class="link-value">{{ $link['value'] }}</div>
                            </div>
                            @if(isset($link['has_message']) && $link['has_message'])
                            <span class="link-badge">+ msg auto</span>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    @endif
                </div>
                <div class="line-platforms">
                    @foreach($line->platforms()->wherePivot('is_active', true)->limit(4)->get() as $platform)
                    <span class="platform-chip">{{ $platform->name }}</span>
                    @endforeach
                    @if($line->platforms()->wherePivot('is_active', true)->count() > 4)
                    <span class="platform-more">+{{ $line->platforms()->wherePivot('is_active', true)->count() - 4 }}</span>
                    @endif
                </div>
                <div class="line-footer">
                    <span>Agentes: {{ $agentCounts[$line->id] ?? 0 }}</span>
                    <div class="line-actions">
                        <button class="btn-action" wire:click="openEditModal({{ $line->id }})">✎ Editar</button>
                        <a href="{{ route('lineas.detail', $line->id) }}" wire:navigate class="btn-action btn-action-primary">Ver detalle →</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay líneas creadas</p>
            </div>
            @endforelse
        </div>
    </div>

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

        .line-links { display: grid; gap: 8px; margin-bottom: 12px; }
        .line-link { display: flex; gap: 8px; align-items: center; padding: 8px 10px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); }
        .link-icon { width: 28px; height: 28px; border-radius: 6px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .link-info { flex: 1; }
        .link-label { font-size: 10px; color: var(--muted); }
        .link-value { font-size: 12px; font-family: var(--font-mono); }
        .link-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.12); color: var(--orange); font-weight: 700; }

        .line-platforms { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; min-height: 24px; }
        .platform-chip { background: rgba(255,106,26,0.1); color: var(--orange); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .platform-more { background: rgba(255,255,255,0.1); color: var(--muted); padding: 4px 10px; border-radius: 6px; font-size: 11px; }

        .line-footer { margin-top: 8px; padding-top: 10px; border-top: 1px solid var(--line); font-size: 11px; color: var(--muted); display: flex; justify-content: space-between; align-items: center; }
        .line-actions { display: flex; gap: 8px; }
        .btn-action { padding: 6px 12px; border-radius: 6px; border: 1px solid var(--line); background: transparent; color: var(--muted); font-size: 11px; cursor: pointer; transition: all 0.2s; }
        .btn-action:hover { border-color: var(--orange); color: var(--orange); }
        .btn-action-primary { color: var(--orange); border-color: var(--orange); }

        .empty-state { text-align: center; color: var(--muted); padding: 40px; grid-column: 1 / -1; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 480px; }
        .modal-large { max-width: 640px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; color: var(--white); }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; max-height: 70vh; overflow-y: auto; }
        
        .form-section { margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--line); }
        .form-section:last-of-type { border-bottom: none; }
        .form-section-title { font-size: 12px; color: var(--orange); font-weight: 700; margin: 0 0 16px; letter-spacing: 0.05em; }
        
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-select { width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .form-group input:focus, .form-group textarea:focus, .form-select:focus { outline: none; border-color: var(--orange); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .contact-repeater-mini { display: flex; flex-direction: column; gap: 8px; }
        .contact-row-mini { display: flex; gap: 8px; align-items: center; }
        .contact-type-mini { width: 140px; padding: 10px 12px; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 8px; color: var(--white); font-size: 13px; }
        .contact-value-mini { flex: 1; padding: 10px 14px; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 8px; color: var(--white); font-size: 13px; }
        .contact-remove-mini { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line); background: transparent; color: var(--muted); cursor: pointer; }
        .contact-add-mini { padding: 10px 16px; border-radius: 8px; border: 1px dashed var(--line); background: transparent; color: var(--orange); font-size: 12px; font-weight: 600; cursor: pointer; }

        .platforms-mini { display: flex; flex-wrap: wrap; gap: 8px; }
        .platform-chip-mini { display: flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line); background: transparent; cursor: pointer; font-size: 12px; color: var(--muted); transition: all 0.2s; }
        .platform-chip-mini.active { border-color: var(--orange); background: rgba(255,106,26,0.1); color: var(--orange); }
        .platform-check-mini { width: 16px; font-size: 10px; }
        .platform-logo-mini { width: 16px; height: 16px; border-radius: 4px; }

        .modal-actions { display: flex; gap: 12px; justify-content: space-between; margin-top: 24px; }
        .modal-actions-right { display: flex; gap: 12px; }
        .btn-ghost { padding: 12px 20px; border-radius: 10px; border: 1px solid var(--line); background: transparent; color: var(--white); font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-ghost:hover { border-color: var(--orange); color: var(--orange); }
        .btn-primary { padding: 12px 24px; border-radius: 10px; border: none; background: var(--orange); color: #190702; font-size: 13px; font-weight: 700; cursor: pointer; }
        .btn-primary:hover { background: var(--amber); }
        .btn-delete { padding: 12px 20px; border-radius: 10px; border: 1px solid #ff4757; background: transparent; color: #ff4757; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-delete:hover { background: #ff4757; color: white; }

        .flash-message { position: fixed; top: 20px; right: 20px; background: var(--good); color: #000; padding: 12px 20px; border-radius: 8px; font-weight: 700; z-index: 2000; }
    </style>
</div>