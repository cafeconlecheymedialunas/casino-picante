<form wire:submit.prevent="saveAll" class="ld-form">
    <div style="margin-bottom: 16px;">
        <a href="{{ route('lineas') }}" wire:navigate class="ld-back" style="font-size: 12px; color: var(--muted); text-decoration: none;">← Líneas & Redes</a>
    </div>

    <x-livewire.components.page-header title="{{ strtoupper($line->name) }}" subtitle="Detalles y configuración de línea" @if($this->hasLinePermission(\App\Support\Permissions::LINE_EDIT_BASIC) && !$isEditing) buttonText="✎ Editar línea" buttonAction="toggleInlineEdit" @endif />

    @if($isEditing)
    <div style="margin-bottom: 16px; display: flex; gap: 10px;">
        <button type="button" class="btn-cancel" wire:click="toggleInlineEdit" style="background: rgba(255,255,255,0.06); border: 1px solid var(--line); color: var(--muted); padding: 8px 16px; border-radius: 8px; font-size: 12px; cursor: pointer;">✕ Cancelar</button>
        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; border: none; padding: 8px 20px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">💾 Guardar cambios</button>
    </div>
    @else
    <div class="ld-meta" style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <span class="ld-badge {{ $line->status === 'active' ? 'badge-active' : 'badge-inactive' }}" style="font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; letter-spacing: 0.06em; background: {{ $line->status === 'active' ? 'rgba(37,196,107,0.15)' : 'rgba(255,255,255,0.06)' }}; color: {{ $line->status === 'active' ? 'var(--good)' : 'var(--muted)' }}; border: 1px solid {{ $line->status === 'active' ? 'rgba(37,196,107,0.3)' : 'var(--line)' }};">
            {{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}
        </span>
        <span class="ld-badge-type" style="font-size: 10px; font-weight: 800; color: var(--orange); letter-spacing: 0.1em;">{{ strtoupper($line->type ?? 'GENERAL') }}</span>
        @if($line->contact_links)
            @foreach($line->contact_links as $link)
                <span class="ld-contact" style="font-size: 12px; color: var(--muted); font-family: var(--font-mono);">
                    @if($link['type'] === 'whatsapp')💬 @elseif($link['type'] === 'telegram')✈️ @elseif($link['type'] === 'instagram')📷 @elseif($link['type'] === 'facebook')📘 @endif {{ $link['value'] }}
                </span>
            @endforeach
        @endif
    </div>
    @endif

    @if(session()->has('message'))
    <div class="ld-flash">{{ session('message') }}</div>
    @endif

    <div class="ld-body">
        {{-- 📝 INFORMACIÓN --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">📝 INFORMACIÓN DE LA LÍNEA</h2>
            </div>

            @if($isEditing)
            <div class="edit-section">
                <div class="edit-section-title">Datos Básicos</div>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Icono</div>
                        <input type="text" wire:model="line.icon" class="edit-field" placeholder="🔥">
                    </div>
                    <div class="info-card">
                        <div class="info-label">Tipo</div>
                        <select wire:model="line.type" class="edit-field">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telegram">Telegram</option>
                            <option value="phone">Teléfono</option>
                        </select>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Estado</div>
                        <select wire:model="line.status" class="edit-field">
                            <option value="active">Activa</option>
                            <option value="inactive">Inactiva</option>
                        </select>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Encargado</div>
                        <select wire:model="line.encargado_id" class="edit-field">
                            <option value="">Sin encargado</option>
                            @foreach($availableAgents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($isEditing)
                    <div class="info-card" style="grid-column: span 2;">
                        <div class="info-label">Descripción</div>
                        <textarea wire:model="line.description" class="edit-field" rows="2" placeholder="Descripción de la línea..."></textarea>
                    </div>
                    @elseif($line->description)
                    <div class="info-card" style="grid-column: span 2;">
                        <div class="info-label">Descripción</div>
                        <div class="info-value">{{ $line->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Icono</div>
                    <div class="info-value">{{ $line->icon ?: '—' }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Tipo</div>
                    <div class="info-value">{{ ucfirst($line->type ?? 'general') }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        <span class="ld-badge {{ $line->status === 'active' ? 'badge-active' : 'badge-inactive' }}">{{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}</span>
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
                @if($line->description)
                <div class="info-card" style="grid-column: span 2;">
                    <div class="info-label">Descripción</div>
                    <div class="info-value">{{ $line->description }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Imágenes --}}
            @if($isEditing)
            <div class="edit-section" style="margin-top: 16px;">
                <div class="edit-section-title">📸 Imágenes</div>
                <div class="info-grid">
                    <div class="info-card">
                        <x-image-uploader label="Portada 851x315" model="portadaUpload" :upload="$portadaUpload" :value="$line->portada_url" remove-action="removeImageField('portada')" variant="wide">
                            @error('portadaUpload') <div class="form-error">{{ $message }}</div> @enderror
                        </x-image-uploader>
                    </div>
                    <div class="info-card">
                        <x-image-uploader label="Perfil 800x800" model="perfilUpload" :upload="$perfilUpload" :value="$line->perfil_url" remove-action="removeImageField('perfil')" variant="square">
                            @error('perfilUpload') <div class="form-error">{{ $message }}</div> @enderror
                        </x-image-uploader>
                    </div>
                </div>
            </div>
            @elseif($line->portada_url || $line->perfil_url)
            <div class="info-grid" style="margin-top: 12px;">
                @if($line->portada_url)
                <div class="info-card">
                    <div class="info-label">Portada</div>
                    <img src="{{ $line->portada_url }}" style="width:100%;height:80px;object-fit:cover;border-radius:6px;">
                </div>
                @endif
                @if($line->perfil_url)
                <div class="info-card">
                    <div class="info-label">Perfil</div>
                    <img src="{{ $line->perfil_url }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;">
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- 📱 CONTACTOS --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">📱 CONTACTOS</h2>
            </div>
            @if($isEditing)
            <div class="edit-section">
                @foreach($line->contact_links as $index => $link)
                @if(!empty($link['value']) || $isEditing)
                <div class="contact-edit-row" wire:key="contact-{{ $index }}">
                    <select wire:model="line.contact_links.{{ $index }}.type" class="contact-type-edit">
                        <option value="whatsapp">💬 WhatsApp</option>
                        <option value="telegram">✈️ Telegram</option>
                        <option value="instagram">📷 Instagram</option>
                        <option value="facebook">📘 Facebook</option>
                        <option value="phone">📞 Teléfono</option>
                    </select>
                    <input type="text" wire:model="line.contact_links.{{ $index }}.value" class="contact-value-edit" placeholder="Número o enlace...">
                    <button type="button" wire:click="removeContactLink({{ $index }})" class="contact-remove">✕</button>
                </div>
                @endif
                @endforeach
                <button type="button" wire:click="addContactLink" class="btn-add-contact">+ Agregar contacto</button>
            </div>
            @elseif($line->contact_links && count($line->contact_links) > 0)
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
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @else
            <div class="ld-empty">No hay contactos configurados</div>
            @endif
        </div>

        {{-- 🎰 PLATAFORMAS (Checkboxes) --}}
        @if($this->hasLinePermission(\App\Support\Permissions::LINE_EDIT_BASIC))
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">🎰 PLATAFORMAS</h2>
            </div>
            <p class="section-desc">Activa o desactiva las plataformas disponibles para esta línea.</p>
            <div class="platforms-toggle-grid">
                @foreach($availablePlatforms as $platform)
                @php $isActive = $line->activePlatforms()->where('platform_id', $platform->id)->exists(); @endphp
                <div class="platform-toggle-card {{ $isActive ? 'active' : '' }}" wire:click="togglePlatformActivation({{ $platform->id }})">
                    <div class="platform-toggle-header">
                        @if($platform->logo_url)
                        <img src="{{ $platform->logo_url }}" class="platform-toggle-logo">
                        @else
                        <div class="platform-toggle-icon">🎮</div>
                        @endif
                        <span class="platform-toggle-name">{{ $platform->name }}</span>
                    </div>
                    <label class="switch-container">
                        <input type="checkbox" {{ $isActive ? 'checked' : '' }} wire:change="togglePlatformActivation({{ $platform->id }})">
                        <span class="switch-slider"></span>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- AGENTES --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">👥 AGENTES ASIGNADOS</h2>
                @if($this->hasLinePermission(\App\Support\Permissions::AGENT_ASSIGN))
                <button type="button" class="btn-add-agent" wire:click="openAssignModal">+ Agregar agente</button>
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
                        @if($this->hasLinePermission(\App\Support\Permissions::AGENT_PERMISSIONS))
                        <button type="button" class="btn-perms" wire:click="openPermissions({{ $la->agent->id }})">✎ Permisos</button>
                        @endif
                        @if($this->hasLinePermission(\App\Support\Permissions::AGENT_ASSIGN))
                        <button type="button" class="btn-remove-agent" wire:click="removeAgent({{ $la->agent->id }})" wire:confirm="¿Remover este agente de la línea?">✕</button>
                        @endif
                    </div>
                    @if($isEditing)
                    <div class="agent-percentage" style="margin-top: 8px;">
                        <label style="font-size:11px;color:var(--muted);">% Ganancia</label>
                        <input type="number" wire:model="line.porcentaje_encargado" class="edit-field" step="0.01" min="0" max="100" placeholder="%">
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="ld-empty">No hay agentes asignados a esta línea</div>
            @endif
        </div>

        {{-- PERMISOS DE LÍNEA --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">🔐 PERMISOS DE LA LÍNEA</h2>
            </div>
            <p class="section-desc">Define qué operaciones permite esta línea.</p>

            <div class="line-perms-grid">
                @php
                $linePermissions = [
            
                    \App\Support\Permissions::NEWS_READ => 'Ver novedades',
                    \App\Support\Permissions::NEWS_CREATE => 'Crear novedades',
                    \App\Support\Permissions::NEWS_UPDATE => 'Editar novedades',
                    \App\Support\Permissions::TICKET_READ => 'Ver tickets',
                    \App\Support\Permissions::TICKET_UPDATE => 'Editar tickets',
                    \App\Support\Permissions::BONO_READ => 'Ver bonos',
                    \App\Support\Permissions::SORTEO_READ => 'Ver sorteos',
                    \App\Support\Permissions::LINE_EDIT_BASIC => 'Editar línea',
                    \App\Support\Permissions::AGENT_ASSIGN => 'Asignar agentes',
                    \App\Support\Permissions::AGENT_PERMISSIONS => 'Gestionar permisos',
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

        {{-- 📊 ESTADÍSTICAS MANUALES --}}
        <div class="ld-section stats-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">📊 ESTADÍSTICAS MANUALES</h2>
                @if($this->hasLinePermission(\App\Support\Permissions::LINE_EDIT_BASIC))
                <span style="font-size:11px;color:var(--muted);">Los valores se ingresan manualmente</span>
                @endif
            </div>

            {{-- KPIs --}}
            <div class="sales-kpis">
                <div class="kpi-card kpi-gold">
                    <div class="kpi-icon">🏆</div>
                    <div class="kpi-label">Mejor Mes</div>
                    @if($line->mejor_mes)
                    <div class="kpi-value">{{ $line->mejor_mes }}</div>
                    <div class="kpi-amount">${{ number_format($line->mejor_mes_total ?? 0, 2) }}</div>
                    @else
                    <div class="kpi-empty">Sin datos</div>
                    @endif
                </div>
                <div class="kpi-card kpi-purple">
                    <div class="kpi-icon">🎯</div>
                    <div class="kpi-label">Top Plataforma</div>
                    @if($line->mejor_plataforma)
                    <div class="kpi-value">{{ $line->mejor_plataforma }}</div>
                    <div class="kpi-amount">${{ number_format($line->mejor_plataforma_total ?? 0, 2) }}</div>
                    @else
                    <div class="kpi-empty">Sin datos</div>
                    @endif
                </div>
                <div class="kpi-card kpi-blue">
                    <div class="kpi-icon">📅</div>
                    <div class="kpi-label">Este Mes</div>
                    <div class="kpi-amount" style="color: var(--good);">${{ number_format($line->ventas_mes_actual ?? 0, 2) }}</div>
                </div>
                <div class="kpi-card kpi-green">
                    <div class="kpi-icon">💵</div>
                    <div class="kpi-label">Ganancia Encargado</div>
                    <div class="kpi-value" style="font-size: 14px;">{{ $line->porcentaje_encargado ?? 0 }}%</div>
                    <div class="kpi-amount">${{ number_format($line->ganancia_encargado ?? 0, 2) }}</div>
                </div>
            </div>

            {{-- Últimos 3 Meses --}}
            <div class="months-grid">
                <div class="month-card">
                    <div class="month-name">Mes Actual</div>
                    <div class="month-total">${{ number_format($line->ventas_mes_actual ?? 0, 2) }}</div>
                    <div class="month-bar">
                        @php $max = max($line->ventas_mes_actual ?? 1, $line->ventas_mes_pasado ?? 1, $line->ventas_mes_antiguo ?? 1); @endphp
                        <div class="month-bar-fill" style="width: {{ (($line->ventas_mes_actual ?? 0) / $max * 100) }}%"></div>
                    </div>
                </div>
                <div class="month-card">
                    <div class="month-name">Mes Pasado</div>
                    <div class="month-total">${{ number_format($line->ventas_mes_pasado ?? 0, 2) }}</div>
                    <div class="month-bar">
                        <div class="month-bar-fill" style="width: {{ (($line->ventas_mes_pasado ?? 0) / $max * 100) }}%"></div>
                    </div>
                </div>
                <div class="month-card">
                    <div class="month-name">Mes Anterior</div>
                    <div class="month-total">${{ number_format($line->ventas_mes_antiguo ?? 0, 2) }}</div>
                    <div class="month-bar">
                        <div class="month-bar-fill" style="width: {{ (($line->ventas_mes_antiguo ?? 0) / $max * 100) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Edit Form (only in edit mode) --}}
            @if($isEditing)
            <div class="edit-section" style="margin-top: 16px;">
                <div class="edit-section-title">📊 Editar Estadísticas</div>
                <div class="stats-edit-grid">
                    <div class="stat-field">
                        <label class="edit-label">Mejor Mes</label>
                        <input type="text" wire:model="line.mejor_mes" class="edit-field" placeholder="Ej: Marzo 2026">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Mejor Mes ($)</label>
                        <input type="number" wire:model="line.mejor_mes_total" class="edit-field" step="0.01" placeholder="0.00">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Mejor Plataforma</label>
                        <input type="text" wire:model="line.mejor_plataforma" class="edit-field" placeholder="Nombre">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Mejor Plataforma ($)</label>
                        <input type="number" wire:model="line.mejor_plataforma_total" class="edit-field" step="0.01" placeholder="0.00">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Ventas Mes Actual ($)</label>
                        <input type="number" wire:model="line.ventas_mes_actual" class="edit-field" step="0.01" placeholder="0.00">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Ventas Mes Pasado ($)</label>
                        <input type="number" wire:model="line.ventas_mes_pasado" class="edit-field" step="0.01" placeholder="0.00">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Ventas Mes Anterior ($)</label>
                        <input type="number" wire:model="line.ventas_mes_antiguo" class="edit-field" step="0.01" placeholder="0.00">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">Ganancia Encargado ($)</label>
                        <input type="number" wire:model="line.ganancia_encargado" class="edit-field" step="0.01" placeholder="0.00">
                    </div>
                    <div class="stat-field">
                        <label class="edit-label">% Encargado</label>
                        <input type="number" wire:model="line.porcentaje_encargado" class="edit-field" step="0.01" min="0" max="100" placeholder="0">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

{{-- MODALS --}}
@if($showAssignModal)
<div class="modal-overlay" wire:click="closeAssignModal">
    <div class="modal-content" wire:click.stop>
        <div class="modal-header">
            <h3>ASIGNAR AGENTE</h3>
            <button class="modal-close" wire:click="closeAssignModal">✕</button>
        </div>
        <div class="modal-form">
            <div class="form-group">
                <label>Buscar agente</label>
                <input type="text" wire:model.live="assignAgentSearch" class="form-input" placeholder="Nombre o email...">
            </div>
            @if($this->searchAgents && $this->searchAgents->count() > 0)
            <div class="agent-search-results">
                @foreach($this->searchAgents as $agent)
                <div class="agent-result" wire:click="selectAssignAgent({{ $agent->id }})">
                    <div class="agent-avatar-sm">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
                    <div>
                        <div style="font-weight:600;color:var(--white);">{{ $agent->name }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $agent->email }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @if($assignAgentId)
            <div class="form-group">
                <label>Rol</label>
                <select wire:model="assignRole" class="form-select">
                    <option value="miembro">Agente</option>
                    <option value="encargado">Encargado</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" wire:click="closeAssignModal" class="btn-ghost">Cancelar</button>
                <button type="button" wire:click="confirmAssign" class="btn-primary">Asignar</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Permisos por Agente Panel --}}
@if($editingPermAgentId)
<div class="perm-panel-overlay" wire:click="closePermissions">
    <div class="perm-panel" wire:click.stop>
        <div class="perm-panel-header">
            <h3>PERMISOS DE AGENTE</h3>
            <button class="perm-panel-close" wire:click="closePermissions">✕</button>
        </div>
        <p class="perm-panel-desc">Selecciona los permisos para este agente.</p>

        <div class="perm-panel-grid">
            @php
            $agentPerms = [
                \App\Support\Permissions::NEWS_READ => 'Ver novedades',
                \App\Support\Permissions::NEWS_CREATE => 'Crear novedades',
                \App\Support\Permissions::TICKET_READ => 'Ver tickets',
                \App\Support\Permissions::TICKET_UPDATE => 'Editar tickets',
                \App\Support\Permissions::BONO_READ => 'Ver bonos',
                \App\Support\Permissions::SORTEO_READ => 'Ver sorteos',
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

    <style>
    .ld-form { margin: 0; padding: 0; }
    .ld-back { font-size: 12px; color: var(--muted); text-decoration: none; }
    .ld-back:hover { color: var(--orange); }
    .ld-badge { font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; letter-spacing: 0.06em; }
    .badge-active { background: rgba(37,196,107,0.15); color: var(--good); border: 1px solid rgba(37,196,107,0.3); }
    .badge-inactive { background: rgba(255,255,255,0.06); color: var(--muted); border: 1px solid var(--line); }
    .ld-badge-type { font-size: 10px; font-weight: 800; color: var(--orange); letter-spacing: 0.1em; }
    .ld-contact { font-size: 12px; color: var(--muted); font-family: var(--font-mono); }

    .ld-flash { margin: 14px 28px 0; padding: 10px 16px; background: var(--good); color: #000; border-radius: 8px; font-weight: 700; font-size: 13px; }

    .ld-body { padding: 20px 28px 40px; display: flex; flex-direction: column; gap: 20px; }

    .ld-section { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 20px; }
    .ld-section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .ld-section-title { font-family: var(--font-display); font-size: 22px; margin: 0; }

    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
    .info-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 10px; padding: 14px; }
    .info-label { font-size: 11px; color: var(--muted); margin-bottom: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }
    .info-value { font-size: 14px; color: var(--white); }

    .edit-section { background: rgba(255,106,26,0.05); border: 1px solid rgba(255,106,26,0.2); border-radius: 10px; padding: 16px; }
    .edit-section-title { font-size: 12px; color: var(--orange); font-weight: 700; margin-bottom: 12px; letter-spacing: 0.05em; }
    .edit-field { width: 100%; background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 8px; padding: 8px 12px; color: var(--white); font-size: 13px; }
    .edit-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; font-weight: 600; }

    .title-input-edit { font-family: var(--font-display); font-size: 36px; color: var(--white); background: transparent; border: none; border-bottom: 2px solid var(--orange); padding: 0; width: 100%; }
    .status-select-edit, .type-select-edit, .icon-input-edit { background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 6px; padding: 6px 10px; color: var(--white); font-size: 12px; }

    .edit-actions { display: flex; gap: 8px; }
    .btn-cancel { background: rgba(255,255,255,0.06); border: 1px solid var(--line); color: var(--muted); padding: 8px 16px; border-radius: 8px; font-size: 12px; cursor: pointer; }
    .btn-save { background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; border: none; padding: 8px 20px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; }

    .section-desc { font-size: 12px; color: var(--muted); margin-bottom: 16px; }

    .links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px; }
    .link-card { background: rgba(255,255,255,0.04); border: 1px solid var(--line); border-radius: 10px; padding: 12px; display: flex; gap: 10px; align-items: center; }
    .link-icon { font-size: 20px; }
    .link-info { flex: 1; }
    .link-type { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }
    .link-value { font-size: 13px; color: var(--white); font-family: var(--font-mono); }

    .contact-edit-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
    .contact-type-edit { width: 150px; background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 6px; padding: 8px; color: var(--white); font-size: 12px; }
    .contact-value-edit { flex: 1; background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 6px; padding: 8px; color: var(--white); font-size: 12px; }
    .contact-remove { width: 30px; height: 30px; background: rgba(255,71,87,0.15); border: 1px solid rgba(255,71,87,0.4); color: #ff4757; border-radius: 6px; cursor: pointer; }
    .btn-add-contact { margin-top: 8px; padding: 8px 14px; background: rgba(255,106,26,0.12); border: 1px solid rgba(255,106,26,0.35); color: var(--orange); border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 700; }

    .platforms-toggle-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
    .platform-toggle-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 10px; padding: 14px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s; }
    .platform-toggle-card.active { border-color: var(--orange); background: rgba(255,106,26,0.08); }
    .platform-toggle-header { display: flex; align-items: center; gap: 10px; }
    .platform-toggle-logo { width: 28px; height: 28px; border-radius: 6px; object-fit: contain; }
    .platform-toggle-icon { font-size: 20px; }
    .platform-toggle-name { font-size: 13px; color: var(--white); font-weight: 600; }

    .switch-container { position: relative; width: 40px; height: 22px; }
    .switch-container input { opacity: 0; width: 0; height: 0; }
    .switch-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: var(--line); border-radius: 22px; transition: 0.2s; }
    .switch-slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background: var(--white); border-radius: 50%; transition: 0.2s; }
    .switch-container input:checked + .switch-slider { background: var(--orange); }
    .switch-container input:checked + .switch-slider:before { transform: translateX(18px); }

    .agents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
    .agent-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 12px; padding: 14px; }
    .agent-card.inactive { opacity: 0.5; }
    .agent-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .agent-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--orange); color: #190702; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; }
    .agent-info { flex: 1; min-width: 0; }
    .agent-name { font-weight: 700; font-size: 14px; color: var(--white); }
    .agent-email { font-size: 11px; color: var(--muted); }
    .agent-role-badge { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; }
    .agent-role-badge.manager { background: rgba(255,106,26,0.2); color: var(--orange); }
    .agent-role-badge.agent { background: rgba(255,255,255,0.1); color: var(--muted); }
    .agent-card-actions { display: flex; gap: 8px; align-items: center; }
    .agent-toggle { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--muted); cursor: pointer; }
    .agent-toggle input { accent-color: var(--orange); }
    .btn-perms { padding: 6px 12px; border-radius: 6px; border: 1px solid var(--line); background: transparent; color: var(--white); font-size: 11px; cursor: pointer; }
    .btn-perms:hover { border-color: var(--orange); color: var(--orange); }
    .btn-remove-agent { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--line); background: transparent; color: var(--muted); font-size: 10px; cursor: pointer; }
    .btn-remove-agent:hover { border-color: #ff4757; color: #ff4757; }

    .btn-add-agent { padding: 8px 16px; border-radius: 8px; border: 1px solid var(--orange); background: transparent; color: var(--orange); font-size: 12px; font-weight: 700; cursor: pointer; }
    .btn-add-agent:hover { background: rgba(255,106,26,0.1); }

    .line-perms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; }
    .perm-check { display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 8px; font-size: 12px; color: var(--muted); cursor: pointer; transition: all 0.2s; }
    .perm-check:hover { border-color: var(--orange); }
    .perm-check.active { background: rgba(255,106,26,0.15); border-color: var(--orange); color: var(--orange); }
    .perm-check input { display: none; }

    .stats-section { border-color: rgba(255,106,26,0.3); }

    .sales-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .kpi-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 12px; padding: 16px; text-align: center; }
    .kpi-card.kpi-gold { border-color: rgba(255,215,0,0.3); }
    .kpi-card.kpi-purple { border-color: rgba(147,112,219,0.3); }
    .kpi-card.kpi-blue { border-color: rgba(70,130,255,0.3); }
    .kpi-card.kpi-green { border-color: rgba(37,196,107,0.3); }
    .kpi-icon { font-size: 28px; margin-bottom: 8px; }
    .kpi-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 4px; }
    .kpi-value { font-size: 16px; color: var(--white); font-weight: 700; margin-bottom: 4px; }
    .kpi-amount { font-size: 20px; color: var(--orange); font-weight: 800; }
    .kpi-empty { font-size: 13px; color: var(--muted); padding: 20px; }

    .months-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .month-card { background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 10px; padding: 14px; }
    .month-name { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 8px; }
    .month-total { font-size: 18px; color: var(--white); font-weight: 700; margin-bottom: 8px; }
    .month-bar { height: 8px; background: rgba(255,255,255,0.06); border-radius: 4px; overflow: hidden; }
    .month-bar-fill { height: 100%; background: linear-gradient(90deg, var(--orange), var(--amber)); border-radius: 4px; transition: width 0.5s; }

    .stats-edit-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }

    .ld-empty { text-align: center; color: var(--muted); padding: 20px; font-size: 12px; }

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

    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.8); display: flex;
        align-items: center; justify-content: center; z-index: 1000; padding: 20px;
    }
    .modal-content {
        background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%);
        border: 1px solid var(--line); border-radius: 20px;
        width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;
    }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 20px 24px; border-bottom: 1px solid var(--line);
    }
    .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; color: var(--white); }
    .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
    .modal-form { padding: 24px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
    .form-input { width: 100%; background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
    .form-select { width: 100%; background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
    .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }

    .agent-search-results { display: flex; flex-direction: column; gap: 6px; max-height: 200px; overflow-y: auto; }
    .agent-result { display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 8px; cursor: pointer; transition: all 0.2s; }
    .agent-result:hover { border-color: var(--orange); }
    .agent-avatar-sm { width: 32px; height: 32px; border-radius: 50%; background: var(--orange); color: #190702; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; }

    .perm-panel-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
    .perm-panel { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 500px; max-height: 80vh; overflow-y: auto; }
    .perm-panel-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
    .perm-panel-header h3 { font-family: var(--font-display); font-size: 18px; margin: 0; color: var(--white); }
    .perm-panel-close { background: none; border: none; color: var(--muted); font-size: 18px; cursor: pointer; }
    .perm-panel-desc { font-size: 12px; color: var(--muted); padding: 16px 24px; margin: 0; }
    .perm-panel-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; padding: 0 24px; }
    .perm-panel-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 20px 24px; border-top: 1px solid var(--line); margin-top: 16px; }

    @media (max-width: 768px) {
        .sales-kpis { grid-template-columns: repeat(2, 1fr); }
        .months-grid { grid-template-columns: 1fr; }
        .stats-edit-grid { grid-template-columns: 1fr; }
    }
</style>
