<div>
    {{-- ── Header ─────────────────────────────────────────────────────── --}}
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
                    @if($line->whatsapp) <span class="ld-contact">💬 {{ $line->whatsapp }}</span> @endif
                    @if($line->telegram) <span class="ld-contact">✈️ {{ $line->telegram }}</span> @endif
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
        {{-- ── Agents table ──────────────────────────────────────────────── --}}
        <div class="ld-section">
            <div class="ld-section-header">
                <h2 class="ld-section-title">AGENTES ASIGNADOS</h2>
                @if($this->hasLinePermission('agent.assign'))
                <button class="btn-primary" style="padding:8px 16px;font-size:12px;" wire:click="openAssignModal">
                    + Asignar agente
                </button>
                @endif
            </div>

            @if($lineAgents->isEmpty())
            <div class="ld-empty">No hay agentes asignados a esta línea.</div>
            @else
            <div class="agents-table">
                <div class="agents-table-head">
                    <div>Agente</div>
                    <div>Rol</div>
                    <div>Estado</div>
                    <div>Permisos</div>
                    <div>Acciones</div>
                </div>
                @foreach($lineAgents as $la)
                <div class="agents-table-row {{ $editingPermAgentId === $la->agent_id ? 'row-editing' : '' }}">
                    <div class="agent-cell-info">
                        <div class="agent-avatar">{{ strtoupper(substr($la->agent->name, 0, 2)) }}</div>
                        <div>
                            <div class="agent-name">{{ $la->agent->name }}</div>
                            <div class="agent-email">{{ $la->agent->email }}</div>
                        </div>
                    </div>
                    <div>
                        @if($this->hasLinePermission('agent.update'))
                        <select class="role-select" wire:change="changeAgentRole({{ $la->agent_id }}, $event.target.value)">
                            <option value="manager" {{ $la->role === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="agent" {{ $la->role === 'agent' ? 'selected' : '' }}>Agente</option>
                        </select>
                        @else
                        <span class="role-badge role-{{ $la->role }}">{{ ucfirst($la->role) }}</span>
                        @endif
                    </div>
                    <div>
                        <button class="toggle-btn {{ $la->is_active ? 'toggle-on' : 'toggle-off' }}"
                            @if($this->hasLinePermission('agent.update'))
                            wire:click="toggleAgentActive({{ $la->agent_id }})"
                            @endif>
                            {{ $la->is_active ? 'Activo' : 'Inactivo' }}
                        </button>
                    </div>
                    <div class="perms-preview">
                        @forelse(array_slice($la->permissionsList, 0, 4) as $p)
                        <span class="perm-chip">{{ $p }}</span>
                        @empty
                        <span style="color:var(--muted-2);font-size:11px;">Sin permisos</span>
                        @endforelse
                        @if(count($la->permissionsList) > 4)
                        <span class="perm-chip perm-more">+{{ count($la->permissionsList) - 4 }}</span>
                        @endif
                    </div>
                    <div class="agent-actions">
                        @if($this->hasLinePermission('agent.permissions'))
                        <button class="btn-ghost action-btn" wire:click="openPermissions({{ $la->agent_id }})">
                            🔐 Permisos
                        </button>
                        @endif
                        @if($this->hasLinePermission('agent.assign'))
                        <button class="btn-ghost action-btn" style="color:#ff4757;border-color:#ff4757;"
                            wire:click="removeAgent({{ $la->agent_id }})"
                            wire:confirm="¿Remover a {{ $la->agent->name }} de esta línea?">
                            ✕
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Permissions panel ─────────────────────────────────────────── --}}
        @if($editingPermAgentId && $editingAgent)
        <div class="ld-section perm-panel">
            <div class="ld-section-header">
                <h2 class="ld-section-title">
                    PERMISOS — {{ strtoupper($editingAgent->name) }}
                </h2>
                <button class="btn-ghost" style="padding:6px 14px;font-size:12px;" wire:click="closePermissions">✕ Cerrar</button>
            </div>
            <p class="perm-note">
                Solo se muestran los permisos que vos tenés (regla de delegación). No podés otorgar lo que no tenés.
            </p>
            <div class="perm-grid">
                @foreach($permCatalog as $resource => $actions)
                @php $hasAny = false; @endphp
                @foreach($actions as $action)
                    @php $key = "{$resource}.{$action}"; @endphp
                    @if(array_key_exists($key, $editingPerms)) @php $hasAny = true; @endphp @endif
                @endforeach
                @if($hasAny)
                <div class="perm-group">
                    <div class="perm-group-label">{{ strtoupper($resource) }}</div>
                    @foreach($actions as $action)
                    @php $key = "{$resource}.{$action}"; @endphp
                    @if(array_key_exists($key, $editingPerms))
                    <label class="perm-check">
                        <input type="checkbox" wire:model="editingPerms.{{ $key }}">
                        <span>{{ $action }}</span>
                    </label>
                    @endif
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>
            <div class="perm-actions">
                <button class="btn-ghost" wire:click="closePermissions">Cancelar</button>
                <button class="btn-primary" wire:click="savePermissions">Guardar permisos</button>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Edit Line Modal ────────────────────────────────────────────── --}}
    @if($showEditModal)
    <div class="modal-overlay" wire:click="$set('showEditModal', false)">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>EDITAR LÍNEA</h3>
                <button class="modal-close" wire:click="$set('showEditModal', false)">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveLineEdit">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" wire:model="editName">
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select wire:model="editType" style="width:100%;">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="telegram">Telegram</option>
                        <option value="phone">Teléfono</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" wire:model="editWhatsapp">
                    </div>
                    <div class="form-group">
                        <label>Telegram</label>
                        <input type="text" wire:model="editTelegram">
                    </div>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" wire:model="editPhone">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select wire:model="editStatus" style="width:100%;">
                        <option value="active">Activa</option>
                        <option value="inactive">Inactiva</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-ghost" wire:click="$set('showEditModal', false)">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ── Assign Agent Modal ─────────────────────────────────────────── --}}
    @if($showAssignModal)
    <div class="modal-overlay" wire:click="$set('showAssignModal', false)">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>ASIGNAR AGENTE</h3>
                <button class="modal-close" wire:click="$set('showAssignModal', false)">✕</button>
            </div>
            <div class="modal-form">
                <div class="form-group">
                    <label>Buscar agente</label>
                    <input type="text" wire:model.live="assignAgentSearch" placeholder="Nombre o email...">
                    @if($this->searchAgents->isNotEmpty())
                    <div class="search-dropdown">
                        @foreach($this->searchAgents as $ag)
                        <div class="search-item" wire:click="selectAssignAgent({{ $ag->id }})">
                            <strong>{{ $ag->name }}</strong>
                            <span>{{ $ag->email }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label>Rol en la línea</label>
                    <select wire:model="assignRole" style="width:100%;">
                        <option value="agent">Agente</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-ghost" wire:click="$set('showAssignModal', false)">Cancelar</button>
                    <button class="btn-primary" wire:click="confirmAssign" @if(!$assignAgentId) disabled @endif>
                        Asignar
                    </button>
                </div>
            </div>
        </div>
    </div>
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
        .ld-section { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 20px; }
        .ld-section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .ld-section-title { font-family: var(--font-display); font-size: 18px; letter-spacing: 0.06em; color: var(--orange); margin: 0; }
        .ld-empty { color: var(--muted); font-size: 13px; padding: 20px 0; text-align: center; }

        /* Agents table */
        .agents-table { display: grid; gap: 2px; }
        .agents-table-head {
            display: grid; grid-template-columns: 2fr 1fr 1fr 2fr 1fr;
            gap: 12px; padding: 8px 12px;
            font-size: 10px; font-weight: 800; letter-spacing: 0.12em;
            color: var(--muted-2); text-transform: uppercase;
        }
        .agents-table-row {
            display: grid; grid-template-columns: 2fr 1fr 1fr 2fr 1fr;
            gap: 12px; align-items: center; padding: 12px;
            border-radius: 8px; background: rgba(255,255,255,0.02);
            border: 1px solid transparent; transition: all 0.2s;
        }
        .agents-table-row:hover { background: rgba(255,255,255,0.04); border-color: var(--line); }
        .row-editing { border-color: var(--orange) !important; background: rgba(255,106,26,0.05) !important; }

        .agent-cell-info { display: flex; align-items: center; gap: 10px; }
        .agent-avatar {
            width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--orange), var(--amber));
            display: flex; align-items: center; justify-content: center;
            color: #190702; font-weight: 800; font-size: 11px;
        }
        .agent-name { font-size: 13px; font-weight: 700; }
        .agent-email { font-size: 11px; color: var(--muted); font-family: var(--font-mono); }

        .role-select { background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); border-radius: 6px; padding: 4px 8px; color: var(--white); font-size: 12px; cursor: pointer; }
        .role-badge { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 999px; }
        .role-manager { background: rgba(255,106,26,0.15); color: var(--orange); }
        .role-agent { background: rgba(255,255,255,0.06); color: var(--muted); }

        .toggle-btn { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: none; cursor: pointer; transition: all 0.2s; }
        .toggle-on { background: rgba(37,196,107,0.15); color: var(--good); }
        .toggle-off { background: rgba(255,255,255,0.06); color: var(--muted); }

        .perms-preview { display: flex; flex-wrap: wrap; gap: 4px; }
        .perm-chip { font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.1); color: var(--orange); border: 1px solid rgba(255,106,26,0.2); font-family: var(--font-mono); }
        .perm-more { background: rgba(255,255,255,0.06); color: var(--muted); border-color: var(--line); }

        .agent-actions { display: flex; gap: 6px; }
        .action-btn { padding: 5px 10px; font-size: 11px; border-radius: 6px; }

        /* Permissions panel */
        .perm-panel { border-color: rgba(255,106,26,0.5); }
        .perm-note { font-size: 12px; color: var(--muted); margin-bottom: 16px; padding: 8px 12px; background: rgba(255,106,26,0.06); border-radius: 6px; border-left: 3px solid var(--orange); }
        .perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .perm-group { display: flex; flex-direction: column; gap: 6px; }
        .perm-group-label { font-size: 10px; font-weight: 800; letter-spacing: 0.14em; color: var(--orange); margin-bottom: 4px; }
        .perm-check { display: flex; align-items: center; gap: 8px; font-size: 12px; cursor: pointer; color: var(--muted); transition: color 0.2s; }
        .perm-check:hover { color: var(--white); }
        .perm-check input[type=checkbox] { accent-color: var(--orange); width: 14px; height: 14px; cursor: pointer; }
        .perm-actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--line); }

        /* Assign search dropdown */
        .search-dropdown { margin-top: 4px; background: #1c0d0a; border: 1px solid var(--line-warm); border-radius: 8px; overflow: hidden; }
        .search-item { padding: 10px 12px; cursor: pointer; display: flex; justify-content: space-between; font-size: 13px; transition: background 0.15s; }
        .search-item:hover { background: rgba(255,106,26,0.1); }
        .search-item span { font-size: 11px; color: var(--muted); font-family: var(--font-mono); }

        /* Modal */
        .modal-overlay { position: fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); display:flex; align-items:center; justify-content:center; z-index:1000; padding:20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 480px; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600; }
        .form-group input, .form-group textarea { width:100%; background:linear-gradient(180deg,#1c0d0a,#120909); border:1px solid var(--line-warm); border-radius:10px; padding:12px 16px; color:var(--white); font-size:14px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .modal-actions { display:flex; gap:12px; justify-content:flex-end; margin-top:24px; }
    </style>
</div>
