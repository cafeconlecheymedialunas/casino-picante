<div class="page-container">
    <style>
        .agents-page { display: flex; flex-direction: column; gap: 18px; }
        .header-tools { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .search-input, .filter-select, .form-input {
            background: rgba(255,255,255,.04); border: 1px solid var(--line-2); border-radius: 7px;
            padding: 9px 12px; color: var(--white); font-size: 13px; font-family: var(--font-body);
        }
        .search-input { width: 270px; }
        .filter-select { min-width: 150px; }
        .search-input:focus, .filter-select:focus, .form-input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,.12); }
        .stats-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .stat-card {
            border: 1px solid var(--line); border-radius: 8px; padding: 16px 18px;
            background: linear-gradient(180deg, #170b0b, #0f0707); position: relative; overflow: hidden;
        }
        .stat-card::before { content: ''; position: absolute; inset: 0 0 auto; height: 2px; background: linear-gradient(90deg, var(--orange), var(--amber)); }
        .stat-label { font-size: 10px; font-weight: 800; letter-spacing: .12em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 6px; }
        .stat-value { font-family: var(--font-display); font-size: 32px; line-height: 1; }
        .c-good { color: var(--good); }
        .c-red { color: #ff4757; }
        .c-orange { color: var(--orange); }
        .flash-message {
            border: 1px solid rgba(37,196,107,.35); background: rgba(37,196,107,.12);
            color: var(--good); border-radius: 8px; padding: 12px 14px; font-size: 13px; font-weight: 700;
        }
        .table-card { background: linear-gradient(180deg, #170b0b, #0f0707); border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
        .table-top { display: flex; justify-content: space-between; align-items: flex-start; padding: 16px 18px; border-bottom: 1px solid var(--line); gap: 14px; flex-wrap: wrap; }
        .table-tools { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
        .table-title { font-family: var(--font-display); font-size: 22px; letter-spacing: .03em; }
        .table-count { color: var(--muted-2); font-size: 11px; }
        .table-scroll { overflow-x: auto; }
        .t-head, .t-row {
            display: grid; grid-template-columns: 64px 1fr 1.2fr 128px 1.2fr 122px 150px 170px;
            gap: 12px; align-items: center; min-width: 1080px; padding: 11px 18px;
        }
        .t-head { color: var(--muted-2); font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        .t-row { border-bottom: 1px solid var(--line); font-size: 13px; transition: background .15s; }
        .t-row:last-child { border-bottom: 0; }
        .t-row:hover { background: rgba(255,106,26,.04); }
        .mono { font-family: var(--font-mono); color: var(--muted-2); font-size: 11px; }
        .strong { font-weight: 800; }
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .role-badge, .status-badge, .line-badge {
            display: inline-flex; align-items: center; width: fit-content; max-width: 100%; gap: 5px;
            border-radius: 999px; padding: 4px 10px; font-size: 10px; font-weight: 800; white-space: nowrap;
        }
        .role-super { color: var(--orange); background: rgba(255,106,26,.13); }
        .role-agent { color: var(--muted); background: rgba(255,255,255,.06); }
        .status-active { color: var(--good); background: rgba(37,196,107,.12); }
        .status-inactive { color: #ff4757; background: rgba(255,71,87,.12); }
        .line-badge { color: var(--white); background: rgba(255,255,255,.06); }
        .muted { color: var(--muted-2); }
        .action-row { display: flex; align-items: center; gap: 6px; }
        .btn-icon {
            width: 32px; height: 32px; border-radius: 7px; border: 1px solid var(--line);
            background: rgba(255,255,255,.03); color: var(--muted); cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center; transition: all .15s; text-decoration: none;
        }
        .btn-icon:hover { background: rgba(255,106,26,.15); border-color: var(--orange); color: var(--white); }
        .btn-icon.danger:hover { background: rgba(255,71,87,.15); border-color: #ff4757; color: #fff; }
        .btn-icon.activate:hover { background: rgba(37,196,107,.15); border-color: var(--good); color: var(--good); }
        .mini-icon { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
        .empty-state { padding: 56px 24px; color: var(--muted-2); text-align: center; }
        .table-footer { padding: 14px 18px; border-top: 1px solid var(--line); color: var(--muted-2); font-size: 12px; }
        .modal-overlay { position: fixed; inset: 0; z-index: 240; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0,0,0,.78); }
        .modal-panel { width: min(760px, 100%); max-height: 92vh; overflow-y: auto; border: 1px solid var(--line-2); border-radius: 8px; background: linear-gradient(180deg, #1c0e0e, #120909); }
        .modal-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 22px; border-bottom: 1px solid var(--line); }
        .modal-head h3 { margin: 0; font-family: var(--font-display); font-size: 23px; letter-spacing: .03em; }
        .modal-close { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 7px; background: rgba(255,255,255,.03); color: var(--muted); cursor: pointer; }
        .modal-form { padding: 22px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; margin-bottom: 6px; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .form-input { width: 100%; }
        .form-error { margin-top: 4px; color: #ff4757; font-size: 11px; }
        .line-check-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .line-check {
            display: flex; align-items: center; gap: 9px; min-height: 42px; padding: 10px 12px;
            border: 1px solid var(--line); border-radius: 7px; background: rgba(255,255,255,.03);
            color: var(--white); font-size: 13px; cursor: pointer;
        }
        .line-check input { accent-color: var(--orange); }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; padding-top: 18px; margin-top: 18px; border-top: 1px solid var(--line); }
        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; padding: 22px; }
        .detail-item label { display: block; margin-bottom: 5px; color: var(--muted-2); font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .detail-item p { margin: 0; color: var(--white); font-size: 13px; word-break: break-word; }
        @media (max-width: 860px) {
            .stats-row, .form-grid, .line-check-grid, .detail-grid { grid-template-columns: 1fr; }
            .search-input { width: 100%; }
            .table-tools { width: 100%; justify-content: flex-start; }
        }
    </style>

    <x-livewire.components.page-header title="AGENTES" subtitle="Alta, cargo, estado y asignacion operativa por linea" />

    @if($canCreateAgents)
    <div class="page-action-strip">
        <button type="button" class="btn-primary" wire:click="openCreateModal">+ Crear agente</button>
    </div>
    @endif

    <div class="agents-page">
        <div class="stats-row">
            <div class="stat-card"><div class="stat-label">Total agentes</div><div class="stat-value">{{ $metrics['total'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Activos</div><div class="stat-value c-good">{{ $metrics['active'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Inactivos</div><div class="stat-value c-red">{{ $metrics['inactive'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Con linea</div><div class="stat-value c-orange">{{ $metrics['with_lines'] }}</div></div>
        </div>

        @if(session()->has('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif

        <div class="table-card">
            <div class="table-top">
                <div>
                    <div class="table-title">TABLA DE AGENTES</div>
                    <div class="table-count">Mostrando {{ $agents->count() }} de {{ $agents->total() }}</div>
                </div>
                <div class="table-tools">
                    <input type="text" wire:model.live.debounce.300ms="search" class="search-input" placeholder="Buscar ID, username, nombre o email">
                    <select wire:model.live="statusFilter" class="filter-select">
                        <option value="all">Todos los estados</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                    <select wire:model.live="cargoFilter" class="filter-select">
                        <option value="all">Todos los cargos</option>
                        <option value="super_agente">Encargado</option>
                        <option value="agente">Agente</option>
                    </select>
                </div>
            </div>

            @if($agents->isEmpty())
                <div class="empty-state">No hay agentes para los filtros seleccionados.</div>
            @else
                <div class="table-scroll">
                    <div class="t-head">
                        <div>ID</div>
                        <div>Username</div>
                        <div>Nombre</div>
                        <div>Cargo</div>
                        <div>Linea asignada</div>
                        <div>Estado</div>
                        <div>Enviar mensaje</div>
                        <div>Acciones</div>
                    </div>

                    @foreach($agents as $agent)
                        @php $isActive = $agent->status === 'active'; @endphp
                        <div class="t-row">
                            <div class="mono">#{{ $agent->id }}</div>
                            <div class="strong truncate">{{ $agent->username ?? '-' }}</div>
                            <div class="truncate">{{ trim($agent->name.' '.($agent->apellido ?? '')) }}</div>
                            <div>
                                <span class="role-badge {{ $agent->cargo === 'super_agente' ? 'role-super' : 'role-agent' }}">
                                    {{ $agent->cargo === 'super_agente' ? 'Encargado' : 'Agente' }}
                                </span>
                            </div>
                            <div class="truncate">
                                @forelse($agent->assignedLines as $line)
                                    <span class="line-badge">{{ $line->name }}</span>
                                @empty
                                    <span class="muted">Sin linea</span>
                                @endforelse
                            </div>
                            <div>
                                <span class="status-badge {{ $isActive ? 'status-active' : 'status-inactive' }}">
                                    {{ $isActive ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <div>
                                <livewire:components.agent-messaging
                                    :target-agent-id="$agent->id"
                                    target-type="Agente"
                                    :target-name="trim($agent->name.' '.($agent->apellido ?? ''))"
                                    :target-email="$agent->email"
                                    :target-phone="$agent->phone ?? ''"
                                    :context-label="$agent->cargo === 'super_agente' ? 'Encargado' : 'Agente'"
                                    :key="'agent-table-message-'.$agent->id"
                                />
                            </div>
                            <div class="action-row">
                                @if($isActive)
                                    <button wire:click="toggleStatus({{ $agent->id }})" class="btn-icon danger" title="Pausar agente">
                                        <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H6v16h4V4ZM18 4h-4v16h4V4Z"/></svg>
                                    </button>
                                @else
                                    <button wire:click="toggleStatus({{ $agent->id }})" class="btn-icon activate" title="Activar agente">
                                        <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7V5Z"/></svg>
                                    </button>
                                @endif
                                <button wire:click="openDetailModal({{ $agent->id }})" class="btn-icon" title="Ver detalle">
                                    <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><path d="M12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"/></svg>
                                </button>
                                <button wire:click="openEditModal({{ $agent->id }})" class="btn-icon" title="Editar agente">
                                    <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>
                                </button>
                                <button wire:click="deleteAgent({{ $agent->id }})" wire:confirm="Eliminar al agente {{ trim($agent->name.' '.($agent->apellido ?? '')) }}?" class="btn-icon danger" title="Eliminar agente">
                                    <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="table-footer">
                    {{ $agents->links() }}
                </div>
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>{{ $editingAgentId ? 'EDITAR AGENTE' : 'CREAR AGENTE' }}</h3>
                    <button class="modal-close" wire:click="closeModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveAgent">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nombre *</label>
                            <input type="text" wire:model="name" class="form-input" placeholder="Nombre">
                            @error('name') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Apellido</label>
                            <input type="text" wire:model="apellido" class="form-input" placeholder="Apellido">
                            @error('apellido') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" wire:model="email" class="form-input" placeholder="agente@ejemplo.com">
                            @error('email') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" wire:model="username" class="form-input" placeholder="usuario_agente">
                            @error('username') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ $editingAgentId ? 'Contrasena nueva' : 'Contrasena *' }}</label>
                            <input type="password" wire:model="password" class="form-input" placeholder="{{ $editingAgentId ? 'Dejar vacio para mantener' : 'Minimo 6 caracteres' }}">
                            @error('password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefono</label>
                            <input type="text" wire:model="phone" class="form-input" placeholder="+54 9 11 0000 0000">
                            @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cargo</label>
                            <select wire:model="cargo" class="form-input">
                                <option value="super_agente">Encargado</option>
                                <option value="agente">Agente</option>
                            </select>
                            @error('cargo') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select wire:model="status" class="form-input">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                            @error('status') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lineas asignadas</label>
                        <div class="line-check-grid">
                            @forelse($lines as $line)
                                <label class="line-check">
                                    <input type="checkbox" wire:model="lineIds" value="{{ $line->id }}">
                                    <span>{{ $line->name }}</span>
                                </label>
                            @empty
                                <div class="muted">No hay lineas disponibles.</div>
                            @endforelse
                        </div>
                        @error('lineIds') <div class="form-error">{{ $message }}</div> @enderror
                        @error('lineIds.*') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                        <button type="submit" class="btn-primary">{{ $editingAgentId ? 'Guardar cambios' : 'Crear agente' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showDetailModal && $detailAgent)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-panel" style="width: min(860px, 100%);">
                <div class="modal-head">
                    <h3>DETALLE DE AGENTE</h3>
                    <button class="modal-close" wire:click="closeModal">x</button>
                </div>
                <div class="detail-grid">
                    <div class="detail-item"><label>ID</label><p>#{{ $detailAgent->id }}</p></div>
                    <div class="detail-item"><label>Username</label><p>{{ $detailAgent->username ?? '-' }}</p></div>
                    <div class="detail-item"><label>Nombre</label><p>{{ $detailAgent->name }}</p></div>
                    <div class="detail-item"><label>Apellido</label><p>{{ $detailAgent->apellido ?? '-' }}</p></div>
                    <div class="detail-item"><label>Email</label><p>{{ $detailAgent->email }}</p></div>
                    <div class="detail-item"><label>Telefono</label><p>{{ $detailAgent->phone ?? '-' }}</p></div>
                    <div class="detail-item"><label>Cargo</label><p>{{ $detailAgent->cargo === 'super_agente' ? 'Encargado' : 'Agente' }}</p></div>
                    <div class="detail-item"><label>Estado</label><p>{{ $detailAgent->status === 'active' ? 'Activo' : 'Inactivo' }}</p></div>
                    <div class="detail-item"><label>Alta</label><p>{{ $detailAgent->created_at?->format('d/m/Y H:i') ?? '-' }}</p></div>
                </div>

                {{-- Permisos por linea --}}
                <div style="padding: 0 22px 22px;">
                    <div style="font-size: 11px; font-weight: 800; letter-spacing: .1em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 10px; padding-top: 4px; border-top: 1px solid var(--line);">Permisos por linea</div>

                    @forelse($detailAgent->assignedLines as $assignedLine)
                        <div style="border: 1px solid var(--line); border-radius: 7px; padding: 12px 14px; margin-bottom: 8px; background: rgba(255,255,255,.02);">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 6px;">
                                <div style="font-size: 13px; font-weight: 700;">
                                    {{ $assignedLine->icon ?? '●' }} {{ $assignedLine->name }}
                                </div>
                                @if($permEditAgentId === $detailAgent->id && $permEditLineId === $assignedLine->id)
                                    <button wire:click="closePermissions" class="btn-ghost" style="font-size: 11px; padding: 4px 10px;">Cancelar</button>
                                @else
                                    <button wire:click="openPermissions({{ $detailAgent->id }}, {{ $assignedLine->id }})" class="btn-ghost" style="font-size: 11px; padding: 4px 10px;">Editar permisos</button>
                                @endif
                            </div>

                            @if($permEditAgentId === $detailAgent->id && $permEditLineId === $assignedLine->id)
                                @if(empty($permEditAvailable))
                                    <div style="color: var(--muted-2); font-size: 12px; padding: 6px 0;">Esta linea no tiene permisos configurados. Configuralos en la seccion Lineas.</div>
                                @else
                                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
                                        @foreach($permissionCatalog as $resource => $actions)
                                            @php
                                                $intersect = array_values(array_intersect($actions, $permEditAvailable));
                                            @endphp
                                            @if(!empty($intersect))
                                                <div>
                                                    <div style="font-size: 10px; font-weight: 800; letter-spacing: .08em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 5px;">{{ $resource }}</div>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                                        @foreach($intersect as $perm)
                                                            @php $action = substr(strrchr($perm, '.'), 1); @endphp
                                                            <label style="display: flex; align-items: center; gap: 5px; padding: 5px 10px; border: 1px solid var(--line); border-radius: 5px; cursor: pointer; font-size: 11px; background: rgba(255,255,255,.03);">
                                                                <input type="checkbox" wire:model="permEditSelected" value="{{ $perm }}" style="accent-color: var(--orange);">
                                                                {{ $action }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px;">
                                        <button wire:click="closePermissions" class="btn-ghost" style="font-size: 12px;">Cancelar</button>
                                        <button wire:click="savePermissions" class="btn-primary" style="font-size: 12px;">Guardar permisos</button>
                                    </div>
                                @endif
                            @else
                                @php $agentLinePerms = $detailAgent->linePermissionsFor($assignedLine->id); @endphp
                                @if(!empty($agentLinePerms))
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach($agentLinePerms as $perm)
                                            <span style="padding: 2px 8px; background: rgba(255,106,26,.12); color: var(--orange); border-radius: 4px; font-size: 10px; font-weight: 700;">{{ $perm }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="color: var(--muted-2); font-size: 11px;">Sin permisos asignados</div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <div style="color: var(--muted-2); font-size: 12px;">Sin lineas asignadas.</div>
                    @endforelse
                </div>

                <div class="modal-actions" style="margin: 0 22px 22px;">
                    @if($detailAgent->status === 'active')
                        <button wire:click="toggleStatus({{ $detailAgent->id }})" class="btn-ghost">Pausar agente</button>
                    @else
                        <button wire:click="toggleStatus({{ $detailAgent->id }})" class="btn-ghost">Activar agente</button>
                    @endif
                    <button wire:click="openEditModal({{ $detailAgent->id }})" class="btn-primary">Editar agente</button>
                </div>
            </div>
        </div>
    @endif
</div>
