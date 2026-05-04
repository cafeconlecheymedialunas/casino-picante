<div>
    {{-- Header --}}
    <div class="ld-header">
        <div class="ld-back-row">
            <a href="{{ route('lineas') }}" wire:navigate class="ld-back">← Líneas & Redes</a>
        </div>
        <div class="ld-title-row">
            <div>
                <h1 class="page-title">{{ strtoupper($line->name) }}</h1>
                <div class="ld-meta">
                    <span class="ld-badge {{ $line->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}
                    </span>
                    <span class="ld-badge-type">{{ strtoupper($line->type ?? 'GENERAL') }}</span>
                    @if($line->contact_links)
                        @foreach($line->contact_links as $link)
                            <span class="ld-contact">@if($link['type'] === 'whatsapp')💬 @elseif($link['type'] === 'telegram')✈️ @elseif($link['type'] === 'instagram')📷 @elseif($link['type'] === 'facebook')📘 @endif {{ $link['value'] }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
            @if($this->hasLinePermission('line.edit.basic'))
            <button class="btn-primary" wire:click="openEditModal">✎ Editar línea</button>
            @endif
        </div>
    </div>

    @if(session()->has('message'))
    <div class="ld-flash">{{ session('message') }}</div>
    @endif

    <div class="ld-body">
        {{-- Info cards --}}
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Nombre</div>
                <div class="info-value">{{ $line->name }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Estado</div>
                <div class="info-value">
                    <span class="ld-badge {{ $line->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">Plataformas</div>
                <div class="info-value" style="display:flex;flex-wrap:wrap;gap:4px;margin-top:2px;">
                    @forelse($line->activePlatforms as $plat)
                        <span class="platform-badge" title="{{ $plat->website_url }}">
                            @if($plat->logo_url)<img src="{{ $plat->logo_url }}" style="width:14px;height:14px;border-radius:2px;vertical-align:middle;margin-right:3px;">@endif{{ $plat->name }}
                        </span>
                    @empty
                        <span style="color:var(--muted);font-size:12px;">Sin plataformas</span>
                    @endforelse
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">Encargado</div>
                <div class="info-value">
                    @if($currentEncargado)
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--orange);color:#190702;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;">{{ strtoupper(substr($currentEncargado->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:700;color:var(--white);font-size:13px;">{{ $currentEncargado->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $currentEncargado->email }}</div>
                            </div>
                        </div>
                    @else
                        <span style="color:var(--muted);">Sin asignar</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contact Links --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">CONTACTOS DE LA LÍNEA</h2>
            </div>
            @if($line->contact_links && count($line->contact_links) > 0)
                <div class="links-grid">
                    @foreach($line->contact_links as $link)
                    @if(!empty($link['value']))
                        <div class="link-card link-card-{{ $link['type'] ?? '' }}">
                            <div class="link-icon">
                                @if(($link['type'] ?? '') === 'whatsapp')💬
                                @elseif(($link['type'] ?? '') === 'telegram')✈️
                                @elseif(($link['type'] ?? '') === 'instagram')📷
                                @elseif(($link['type'] ?? '') === 'facebook')📘
                                @elseif(($link['type'] ?? '') === 'phone')📞
                                @else 🔗
                                @endif
                            </div>
                            <div class="link-info">
                                <div class="link-type">{{ ucfirst($link['type'] ?? '') }}</div>
                                <div class="link-value">{{ $link['value'] }}</div>
                                @if(!empty($link['message']))
                                    <div class="link-message">"{{ $link['message'] }}"</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @endforeach
                </div>
            @else
                <div class="ld-empty">No hay contactos configurados</div>
            @endif
        </div>

        {{-- Platforms with Contacts --}}
        @if($line->activePlatforms->count() > 0)
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">PLATAFORMAS Y CONTACTOS</h2>
            </div>
            <div class="plat-contact-grid">
                @foreach($line->activePlatforms as $plat)
                <div class="plat-contact-card">
                    <div class="plat-contact-header">
                        @if($plat->logo_url)
                        <img src="{{ $plat->logo_url }}" alt="{{ $plat->name }}" style="width:32px;height:32px;border-radius:6px;object-fit:contain;background:rgba(255,255,255,0.05);">
                        @else
                        <div style="width:32px;height:32px;border-radius:6px;background:rgba(255,106,26,0.12);display:flex;align-items:center;justify-content:center;font-size:14px;">🎮</div>
                        @endif
                        <div>
                            <div class="plat-contact-name">{{ $plat->name }}</div>
                            @if($plat->website_url)
                            <a href="{{ $plat->website_url }}" target="_blank" class="plat-contact-url">🌐 Sitio web</a>
                            @endif
                        </div>
                    </div>
                    @if($plat->contacts && count($plat->contacts) > 0)
                    <div class="plat-contacts-list">
                        @foreach($plat->contacts as $c)
                        <div class="plat-contact-row plat-contact-{{ $c['type'] ?? '' }}">
                            <span class="plat-contact-icon">
                                @if(($c['type'] ?? '') === 'whatsapp')💬
                                @elseif(($c['type'] ?? '') === 'telegram')✈️
                                @elseif(($c['type'] ?? '') === 'instagram')📷
                                @elseif(($c['type'] ?? '') === 'facebook')📘
                                @endif
                            </span>
                            <div class="plat-contact-detail">
                                <span class="plat-contact-val">{{ $c['value'] }}</span>
                                @if(!empty($c['message']))
                                <span class="plat-contact-msg">"{{ $c['message'] }}"</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="plat-no-contacts">Sin contactos cargados</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Encargado Selection --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">ENCARGADO DE LA LÍNEA</h2>
            </div>
            <div class="encargado-section">
                <label>Seleccionar encargado (de los agentes)</label>
                <select wire:model="selectedEncargadoId" wire:change="assignEncargado($event.target.value)" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                    <option value="">Sin encargado asignado</option>
                    @foreach($availableAgents as $agent)
                    <option value="{{ $agent->id }}" {{ $line->encargado_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }} ({{ $agent->email }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Plataformas de la Línea --}}
        @if($this->hasLinePermission('line.edit.basic'))
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">PLATAFORMAS DE LA LÍNEA</h2>
            </div>
            <p class="section-desc">Selecciona qué plataformas del catálogo maestro estarán activas en esta línea. Solo las plataformas activas estarán disponibles para su uso en promociones y otros contenidos.</p>
            <div class="platforms-management">
                @foreach($availablePlatforms as $platform)
                @php
                    $isActive = $line->activePlatforms()->where('platform_id', $platform->id)->exists();
                    $pivotData = $isActive ? $line->platforms()->wherePivot('platform_id', $platform->id)->first()->pivot : null;
                    $customMessage = $pivotData && $pivotData->custom_message ? $pivotData->custom_message : '';
                @endphp
                <div class="platform-item {{ $isActive ? 'platform-active' : '' }}" wire:click="togglePlatformActivation({{ $platform->id }})">
                    <div class="platform-info">
                        @if($platform->logo_url)
                        <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" class="platform-logo">
                        @else
                        <div class="platform-logo-fallback">🎮</div>
                        @endif
                        <div>
                            <div class="platform-name">{{ $platform->name }}</div>
                            @if($platform->website_url)
                            <a href="{{ $platform->website_url }}" target="_blank" class="platform-url">🌐 {{ parse_url($platform->website_url, PHP_URL_HOST) }}</a>
                            @endif
                        </div>
                    </div>
                    <div class="platform-toggle">
                        <input type="checkbox" 
                               wire:model="platformToggles.{{ $platform->id }}" 
                               wire:change="togglePlatformActivation({{ $platform->id }})"
                               {{ $isActive ? 'checked' : '' }}>
                        <span class="toggle-label">{{ $isActive ? 'Activa' : 'Inactiva' }}</span>
                    </div>
                    @if($isActive && !empty($customMessage))
                    <div class="platform-custom-message">
                        Mensaje personalizado: "{{ e($customMessage) }}"
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Agentes Section --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">AGENTES ASIGNADOS</h2>
                @if($this->hasLinePermission('agent.assign'))
                <button class="btn-add-agent" wire:click="openAssignModal">+ Agregar agente</button>
                @endif
            </div>
            
            @if($line->lineAgents && $line->lineAgents->count() > 0)
            <div class="agents-grid">
                @foreach($line->lineAgents as $la)
                <div class="agent-card {{ $la->is_active ? '' : 'inactive' }}">
                    <div class="agent-card-header">
                        <div class="agent-avatar">{{ strtoupper(substr($la->agent->name, 0, 1)) }}</div>
                        <div class="agent-info">
                            <div class="agent-name">{{ $la->agent->name }}</div>
                            <div class="agent-email">{{ $la->agent->email }}</div>
                        </div>
                        <div class="agent-role-badge {{ $la->role }}">
                            {{ $la->role === 'manager' ? 'Encargado' : 'Agente' }}
                        </div>
                    </div>
                    <div class="agent-card-actions">
                        <label class="agent-toggle">
                            <input type="checkbox" {{ $la->is_active ? 'checked' : '' }} wire:change="toggleAgentActive({{ $la->agent->id }})">
                            <span>{{ $la->is_active ? 'Activo' : 'Inactivo' }}</span>
                        </label>
                        @if($this->hasLinePermission('agent.permissions'))
                        <button class="btn-perms" wire:click="openPermissions({{ $la->agent->id }})">✎ Permisos</button>
                        @endif
                        @if($this->hasLinePermission('agent.assign'))
                        <button class="btn-remove-agent" wire:click="removeAgent({{ $la->agent->id }})" wire:confirm="¿Remover este agente de la línea?">✕</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="ld-empty">No hay agentes asignados a esta línea</div>
            @endif
        </div>

        {{-- Permisos de Línea --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">PERMISOS DE LA LÍNEA</h2>
            </div>
            <p class="section-desc">Define qué operaciones permite esta línea. El encargado y agentes no podrán tener más permisos que los definidos aquí.</p>
            
            <div class="line-perms-grid">
                @php
                $linePermissions = [
                    'promo.read' => 'Ver promociones',
                    'promo.create' => 'Crear promociones',
                    'promo.edit' => 'Editar promociones',
                    'promo.delete' => 'Eliminar promociones',
                    'novedad.read' => 'Ver novedades',
                    'novedad.create' => 'Crear novedades',
                    'novedad.edit' => 'Editar novedades',
                    'novedad.delete' => 'Eliminar novedades',
                    'ticket.read' => 'Ver tickets',
                    'ticket.create' => 'Crear tickets',
                    'ticket.edit' => 'Editar tickets',
                    'bonus.read' => 'Ver bonos',
                    'bonus.create' => 'Crear bonos',
                    'sorteo.read' => 'Ver sorteos',
                    'sorteo.create' => 'Crear sorteos',
                    'line.edit.basic' => 'Editar línea',
                    'agent.assign' => 'Asignar agentes',
                    'agent.permissions' => 'Gestionar permisos',
                ];
                @endphp
                @foreach($linePermissions as $perm => $label)
                <label class="perm-check {{ in_array($perm, $linePermissionsList ?? []) ? 'active' : '' }}">
                    <input type="checkbox" wire:change="toggleLinePermission('{{ $perm }}')" {{ in_array($perm, $linePermissionsList ?? []) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Permisos por Agente (Panel lateral) --}}
        @if($editingPermAgentId)
        <div class="perm-panel-overlay" wire:click="closePermissions">
            <div class="perm-panel" wire:click.stop>
                <div class="perm-panel-header">
                    <h3>PERMISOS DE AGENTE</h3>
                    <button class="perm-panel-close" wire:click="closePermissions">✕</button>
                </div>
                <p class="perm-panel-desc">Selecciona los permisos para este agente. No puede tener más permisos que el encargado.</p>
                
                <div class="perm-panel-grid">
                    @php
                    $agentPerms = [
                        'promo.read' => 'Ver promociones',
                        'promo.create' => 'Crear promociones',
                        'promo.edit' => 'Editar promociones',
                        'novedad.read' => 'Ver novedades',
                        'novedad.create' => 'Crear novedades',
                        'ticket.read' => 'Ver tickets',
                        'ticket.edit' => 'Editar tickets',
                        'bonus.read' => 'Ver bonos',
                        'sorteo.read' => 'Ver sorteos',
                    ];
                    @endphp
                    @foreach($agentPerms as $perm => $label)
                    <label class="perm-check {{ $editingPerms[$perm] ?? false ? 'active' : '' }}">
                        <input type="checkbox" wire:model="editingPerms.{{ $perm }}">
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                
                <div class="perm-panel-actions">
                    <button class="btn-ghost" wire:click="closePermissions">Cancelar</button>
                    <button class="btn-primary" wire:click="savePermissions">Guardar permisos</button>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Assign Agent Modal --}}
    @if($showAssignModal)

    {{-- Edit Line Modal --}}
    @if($showEditModal)
    <div class="modal-overlay" wire:click="closeEditModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>EDITAR LÍNEA</h3>
                <button class="modal-close" wire:click="closeEditModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveLineEdit">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" wire:model="editName" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo principal</label>
                        <select wire:model="editType" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telegram">Telegram</option>
                            <option value="phone">Teléfono</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select wire:model="editStatus" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                            <option value="active">Activa</option>
                            <option value="inactive">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea wire:model="editDescription" rows="3" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Icono (emoji)</label>
                    <input type="text" wire:model="editIcon" placeholder="🔥, 💰, etc.">
                </div>

                {{-- Contact Links Repeater --}}
                <div class="form-group">
                    <label>Contactos de la línea</label>
                    <div class="repeater">
                        @foreach($editContactLinks as $index => $link)
                        <div class="repeater-row" wire:key="cl-{{ $index }}">
                            <div class="repeater-item">
                                <select wire:model="editContactLinks.{{ $index }}.type" class="repeater-type">
                                    <option value="whatsapp">💬 WhatsApp</option>
                                    <option value="telegram">✈️ Telegram</option>
                                    <option value="instagram">📷 Instagram</option>
                                    <option value="facebook">📘 Facebook</option>
                                    <option value="phone">📞 Teléfono</option>
                                </select>
                                <input type="text" wire:model="editContactLinks.{{ $index }}.value" placeholder="Número, usuario o URL..." class="repeater-value">
                                <button type="button" wire:click="removeContactLink({{ $index }})" class="repeater-remove">✕</button>
                            </div>
                            @if(in_array($editContactLinks[$index]['type'] ?? '', ['whatsapp', 'telegram']))
                            <div class="repeater-message">
                                <label class="msg-checkbox-label">
                                    <input type="checkbox" wire:model="editContactLinks.{{ $index }}.has_message" class="msg-checkbox">
                                    <span>Activar mensaje automático</span>
                                </label>
                                @if($editContactLinks[$index]['has_message'] ?? false)
                                <textarea wire:model="editContactLinks.{{ $index }}.message" placeholder="Escribe el mensaje automático..." rows="2" class="repeater-msg-area"></textarea>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endforeach
                        <button type="button" wire:click="addContactLink" class="repeater-add">+ Agregar contacto</button>
                    </div>
                </div>

                {{-- Platforms --}}
                <div class="form-group">
                    <label>Plataformas</label>
                    <div class="platforms-grid">
                        @foreach($availablePlatforms as $platform)
                        @php $isSelected = collect($editPlatforms)->firstWhere('platform_id', $platform->id); @endphp
                        <label class="platform-check {{ $isSelected ? 'platform-check-on' : '' }}" wire:click="togglePlatform({{ $platform->id }})">
                            <span>{{ $isSelected ? '✓' : '' }}</span>
                            <span>{{ $platform->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" wire:click="closeEditModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

    <style>
        .ld-header { padding: 24px 28px 0; }
        .ld-back-row { margin-bottom: 12px; }
        .ld-back { font-size: 12px; color: var(--muted); text-decoration: none; transition: color 0.2s; }
        .ld-back:hover { color: var(--orange); }
        .ld-title-row { display: flex; justify-content: space-between; align-items: flex-start; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .ld-meta { display: flex; align-items: center; gap: 10px; margin-top: 6px; flex-wrap: wrap; }
        .ld-badge { font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; letter-spacing: 0.06em; }
        .badge-active { background: rgba(37,196,107,0.15); color: var(--good); border: 1px solid rgba(37,196,107,0.3); }
        .badge-inactive { background: rgba(255,255,255,0.06); color: var(--muted); border: 1px solid var(--line); }
        .ld-badge-type { font-size: 10px; font-weight: 800; color: var(--orange); letter-spacing: 0.1em; }
        .ld-contact { font-size: 12px; color: var(--muted); font-family: var(--font-mono); }

        .ld-flash { margin: 14px 28px 0; padding: 10px 16px; background: var(--good); color: #000; border-radius: 8px; font-weight: 700; font-size: 13px; }

        .ld-body { padding: 20px 28px 40px; display: flex; flex-direction: column; gap: 20px; }

        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
        .info-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 16px; }
        .info-label { font-size: 11px; color: var(--muted); margin-bottom: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }
        .info-value { font-size: 14px; color: var(--white); }
        .platform-badge { padding: 3px 8px; border-radius: 6px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 10px; font-weight: 700; }

        .ld-section { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 20px; }
        .ld-section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .ld-section-title { font-family: var(--font-display); font-size: 22px; margin: 0; }

        .links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px; }
        .link-card { background: rgba(255,255,255,0.04); border: 1px solid var(--line); border-radius: 10px; padding: 12px; display: flex; gap: 10px; align-items: center; }
        .link-icon { font-size: 20px; }
        .link-info { flex: 1; }
        .link-type { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }
        .link-value { font-size: 13px; color: var(--white); font-family: var(--font-mono); }
        .link-label { font-size: 11px; color: var(--orange); margin-top: 2px; }
        .link-message { font-size: 11px; color: var(--muted-2); margin-top: 4px; font-style: italic; line-height: 1.3; }
        .link-card-whatsapp { border-color: rgba(37,196,107,0.2); }
        .link-card-telegram { border-color: rgba(100,150,255,0.2); }
        .link-card-instagram { border-color: rgba(255,106,180,0.2); }
        .link-card-facebook { border-color: rgba(100,130,255,0.2); }

        .plat-contact-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
        .plat-contact-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 10px; padding: 14px; }
        .plat-contact-header { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
        .plat-contact-name { font-size: 13px; font-weight: 700; color: var(--white); }
        .plat-contact-url { font-size: 10px; color: var(--orange); text-decoration: none; }
        .plat-contacts-list { display: flex; flex-direction: column; gap: 6px; }
        .plat-contact-row { display: flex; gap: 8px; align-items: flex-start; padding: 6px 8px; border-radius: 6px; background: rgba(255,255,255,0.03); }
        .plat-contact-row.plat-contact-whatsapp { background: rgba(37,196,107,0.07); }
        .plat-contact-row.plat-contact-telegram { background: rgba(100,150,255,0.07); }
        .plat-contact-row.plat-contact-instagram { background: rgba(255,106,180,0.07); }
        .plat-contact-row.plat-contact-facebook { background: rgba(100,130,255,0.07); }
        .plat-contact-icon { font-size: 14px; flex-shrink: 0; margin-top: 1px; }
        .plat-contact-detail { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .plat-contact-val { font-size: 12px; color: var(--white); font-family: var(--font-mono); }
        .plat-contact-msg { font-size: 10px; color: var(--muted-2); font-style: italic; line-height: 1.3; }
        .plat-no-contacts { font-size: 11px; color: var(--muted-2); font-style: italic; padding: 4px 0; }

        .encargado-section label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }

        .ld-empty { text-align: center; color: var(--muted); padding: 20px; font-size: 12px; }

        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.8); display: flex;
            align-items: center; justify-content: center; z-index: 1000; padding: 20px;
        }
        .modal-content {
            background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%);
            border: 1px solid var(--line); border-radius: 20px;
            width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto;
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 24px; border-bottom: 1px solid var(--line);
        }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }
        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--amber));
            color: #190702; border: none; padding: 10px 20px;
            border-radius: 999px; font-size: 12px; font-weight: 800;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-ghost {
            background: transparent; color: var(--muted);
            border: 1px solid var(--line-2); padding: 10px 20px;
            border-radius: 999px; font-size: 12px; font-weight: 700;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-ghost:hover { border-color: var(--orange); color: var(--orange); }

        .repeater { display: flex; flex-direction: column; gap: 0; }
        .repeater-row { display: flex; flex-direction: column; margin-top: 8px; }
        .repeater-item {
            display: flex; gap: 8px; align-items: center;
            padding: 8px; background: rgba(255,255,255,0.03);
            border: 1px solid var(--line); border-radius: 8px 8px 0 0;
        }
        .repeater-message {
            padding: 8px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--line); border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .msg-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--muted);
            cursor: pointer;
            margin-bottom: 8px;
        }
        .msg-checkbox {
            width: 14px;
            height: 14px;
            accent-color: var(--orange);
        }
        .repeater-type {
            flex-shrink: 0; width: 145px;
            background: linear-gradient(180deg,#1c0d0a,#120909);
            border: 1px solid var(--line-warm); border-radius: 6px;
            padding: 7px 10px; color: var(--white); font-size: 12px;
        }
        .repeater-value {
            flex: 1; background: linear-gradient(180deg,#1c0d0a,#120909);
            border: 1px solid var(--line-warm); border-radius: 6px;
            padding: 7px 10px; color: var(--white); font-size: 12px;
        }
        .repeater-remove {
            flex-shrink: 0; width: 30px; height: 30px; padding: 0;
            background: rgba(255,71,87,0.15); border: 1px solid rgba(255,71,87,0.4);
            color: #ff4757; border-radius: 6px; cursor: pointer; font-size: 12px;
        }
        .repeater-msg-area {
            width: 100%; background: linear-gradient(180deg,#1c0d0a,#120909);
            border: 1px solid var(--line); border-radius: 6px;
            padding: 8px 10px; color: var(--muted); font-size: 12px;
            font-family: var(--font-body); resize: none; margin-top: 6px;
        }
        .repeater-add {
            margin-top: 10px; padding: 7px 14px; align-self: flex-start;
            background: rgba(255,106,26,0.12); border: 1px solid rgba(255,106,26,0.35);
            color: var(--orange); border-radius: 6px; cursor: pointer;
            font-size: 12px; font-weight: 700; transition: all 0.2s;
        }
        .repeater-add:hover { background: rgba(255,106,26,0.2); }
        .platforms-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .platform-check {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 12px; background: rgba(255,255,255,0.04);
            border: 1px solid var(--line); border-radius: 8px;
            cursor: pointer; font-size: 12px; color: var(--muted);
            transition: all 0.2s; user-select: none;
        }
        .platform-check:hover { border-color: var(--orange); color: var(--orange); }
        .platform-check.platform-check-on { background: rgba(255,106,26,0.2); border-color: var(--orange); color: var(--orange); }

        /* Agentes */
        .btn-add-agent { padding: 8px 16px; border-radius: 8px; border: 1px solid var(--orange); background: transparent; color: var(--orange); font-size: 12px; font-weight: 700; cursor: pointer; }
        .btn-add-agent:hover { background: rgba(255,106,26,0.1); }
        .agents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
        .agent-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 12px; padding: 14px; }
        .agent-card.inactive { opacity: 0.5; }
        .agent-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .agent-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--orange); color: #190702; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0; }
        .agent-info { flex: 1; min-width: 0; }
        .agent-name { font-weight: 700; font-size: 14px; color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .agent-email { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .agent-role-badge { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .agent-role-badge.manager { background: rgba(255,106,26,0.2); color: var(--orange); }
        .agent-role-badge.agent { background: rgba(255,255,255,0.1); color: var(--muted); }
        .agent-card-actions { display: flex; gap: 8px; align-items: center; }
        .agent-toggle { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--muted); cursor: pointer; }
        .agent-toggle input { accent-color: var(--orange); }
        .btn-perms { padding: 6px 12px; border-radius: 6px; border: 1px solid var(--line); background: transparent; color: var(--white); font-size: 11px; cursor: pointer; }
        .btn-perms:hover { border-color: var(--orange); color: var(--orange); }
        .btn-remove-agent { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--line); background: transparent; color: var(--muted); font-size: 10px; cursor: pointer; }
        .btn-remove-agent:hover { border-color: #ff4757; color: #ff4757; }

        /* Permisos de línea */
        .section-desc { font-size: 12px; color: var(--muted); margin-bottom: 16px; }
        .line-perms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; }
        .perm-check { display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 8px; font-size: 12px; color: var(--muted); cursor: pointer; transition: all 0.2s; }
        .perm-check:hover { border-color: var(--orange); }
        .perm-check.active { background: rgba(255,106,26,0.15); border-color: var(--orange); color: var(--orange); }
        .perm-check input { display: none; }

        /* Permiso Panel */
        .perm-panel-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .perm-panel { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 500px; max-height: 80vh; overflow-y: auto; }
        .perm-panel-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .perm-panel-header h3 { font-family: var(--font-display); font-size: 18px; margin: 0; color: var(--white); }
        .perm-panel-close { background: none; border: none; color: var(--muted); font-size: 18px; cursor: pointer; }
        .perm-panel-desc { font-size: 12px; color: var(--muted); padding: 16px 24px; margin: 0; }
        .perm-panel-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; padding: 0 24px 20px; }
        .perm-panel-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid var(--line); }
        .perm-panel-actions .btn-ghost { padding: 10px 20px; border-radius: 8px; border: 1px solid var(--line); background: transparent; color: var(--white); font-size: 12px; cursor: pointer; }
        .perm-panel-actions .btn-primary { padding: 10px 24px; border-radius: 8px; border: none; background: var(--orange); color: #190702; font-size: 12px; font-weight: 700; cursor: pointer; }

        .ld-empty { text-align: center; color: var(--muted); padding: 20px; font-size: 12px; }
    </style>
</div>
