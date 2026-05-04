<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">ROLES</h1>
            <p class="page-subtitle">Gestión de roles y permisos del sistema</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <input type="text" placeholder="Buscar roles..." wire:model="search" class="search-input">
            <button wire:click="openCreateModal" class="btn-primary">
                <span>+</span> Crear rol
            </button>
        </div>
    </div>

    @if(session()->has('message'))
    <div style="position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;">
        {{ session('message') }}
    </div>
    @endif

    <div class="content-grid">
        <div class="roles-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $metrics['total'] }}</div>
                <div class="stat-label">Total roles</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $metrics['active'] }}</div>
                <div class="stat-label">Roles activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $metrics['agents_with_roles'] }}</div>
                <div class="stat-label">Agentes con rol</div>
            </div>
        </div>

        <div class="roles-grid">
            @forelse($roles as $role)
            <div class="role-card {{ !$role->is_active ? 'role-inactive' : '' }}">
                <div class="role-header">
                    <div class="role-name">{{ $role->name }}</div>
                    <div class="role-actions">
                        <button class="btn-ghost" style="height:30px;padding:0 12px;font-size:11px;" wire:click="openEditModal({{ $role->id }})">Editar</button>
                        <button class="btn-ghost" style="height:30px;padding:0 12px;font-size:11px;color:#ff4757;border-color:#ff4757;" wire:click="deleteRole({{ $role->id }})" wire:confirm="¿Eliminar este rol?">Eliminar</button>
                    </div>
                </div>
                <div class="role-status">
                    <button class="toggle-btn {{ $role->is_active ? 'toggle-on' : 'toggle-off' }}" wire:click="toggleStatus({{ $role->id }})">
                        {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                    </button>
                </div>
                @if($role->description)
                <div class="role-desc">{{ $role->description }}</div>
                @endif
                <div class="role-permissions">
                    @if(is_array($role->permissions))
                        @foreach($role->permissions as $section => $level)
                            @if($level !== 'none')
                            <span class="perm-chip">{{ $section }}: {{ $level }}</span>
                            @endif
                        @endforeach
                    @endif
                </div>
                <div class="role-footer">
                    <span class="role-count">{{ $role->agents->count() }} agentes</span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay roles creados</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingRole ? 'EDITAR ROL' : 'NUEVO ROL' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveRole">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" placeholder="Nombre del rol" wire:model="name" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea wire:model="description" placeholder="Descripción del rol..." rows="3" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select wire:model="is_active" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Permisos</label>
                    <div class="perm-matrix">
                        <div class="matrix-header">
                            <div>Sección</div>
                            <div>Ninguno</div>
                            <div>Lectura</div>
                            <div>Crear</div>
                            <div>Editar</div>
                            <div>Eliminar</div>
                        </div>
                        @foreach($availableSections as $key => $label)
                        <div class="matrix-row">
                            <div class="matrix-label">{{ $label }}</div>
                            @foreach($permissionLevels as $level)
                            <div class="matrix-cell">
                                <button type="button" class="matrix-check {{ ($permissions[$key] ?? 'none') === $level ? 'selected' : '' }}" wire:click="togglePerm('{{ $key }}', '{{ $level }}')">
                                    @if($level === 'none') ∅ @elseif($level === 'read') 👁 @elseif($level === 'create') + @elseif($level === 'edit') ✎ @elseif($level === 'delete') ✕ @endif
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingRole ? 'Guardar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--black);
            margin: -24px -28px 24px -28px;
            padding: 24px 28px 16px;
            border-bottom: 1px solid var(--line);
        }
        .page-title {
            font-family: var(--font-display);
            font-size: 36px;
            color: var(--white);
            margin: 0;
        }
        .page-subtitle {
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px;
        }
        .content-grid {
            padding: 0 28px 28px;
        }
        .roles-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }
        .stat-value {
            font-family: var(--font-display);
            font-size: 28px;
            color: var(--orange);
        }
        .stat-label {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
        }
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }
        .role-card {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 14px;
            padding: 16px;
            transition: all 0.2s;
        }
        .role-card:hover {
            border-color: var(--orange);
        }
        .role-inactive {
            opacity: 0.6;
        }
        .role-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .role-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--white);
        }
        .role-actions {
            display: flex;
            gap: 6px;
        }
        .role-status {
            margin-bottom: 12px;
        }
        .role-desc {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 12px;
        }
        .role-permissions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }
        .perm-chip {
            padding: 3px 8px;
            border-radius: 6px;
            background: rgba(255,106,26,0.12);
            color: var(--orange);
            font-size: 10px;
            font-weight: 700;
        }
        .role-footer {
            padding-top: 12px;
            border-top: 1px solid var(--line);
            font-size: 11px;
            color: var(--muted);
        }
        .toggle-btn {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid var(--line);
            cursor: pointer;
            transition: all 0.2s;
        }
        .toggle-on {
            background: var(--good);
            color: #000;
            border-color: var(--good);
        }
        .toggle-off {
            background: transparent;
            color: var(--muted);
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }
        .modal-content {
            background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%);
            border: 1px solid var(--line);
            border-radius: 20px;
            width: 100%;
            max-width: 700px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--line);
        }
        .modal-header h3 {
            font-family: var(--font-display);
            font-size: 22px;
            margin: 0;
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 20px;
            cursor: pointer;
        }
        .modal-form {
            padding: 24px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--white);
            font-size: 14px;
        }
        .perm-matrix {
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
        }
        .matrix-header {
            display: grid;
            grid-template-columns: 1fr repeat(4, 36px);
            font-size: 9px;
            color: var(--muted);
            padding: 8px 12px;
            background: rgba(255,255,255,0.02);
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-align: center;
        }
        .matrix-row {
            display: grid;
            grid-template-columns: 1fr repeat(4, 36px);
            align-items: center;
            padding: 8px 12px;
            border-top: 1px solid var(--line);
            font-size: 12px;
        }
        .matrix-label {
            color: var(--white);
        }
        .matrix-cell {
            display: flex;
            justify-content: center;
        }
        .matrix-check {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(255,106,26,0.2);
            border: 1px solid var(--line);
            color: var(--orange);
            font-size: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .matrix-check.selected {
            background: var(--orange);
            color: #190702;
            border: none;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .search-input {
            width: 200px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--line-2);
            font-size: 12px;
            color: var(--muted);
        }
        .search-input:focus {
            outline: none;
            border-color: var(--orange);
            color: var(--white);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--amber));
            color: #190702;
            border: none;
            padding: 10px 20px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-ghost {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--line-2);
            padding: 10px 20px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-ghost:hover {
            border-color: var(--orange);
            color: var(--orange);
        }
        .empty-state {
            text-align: center;
            color: var(--muted);
            padding: 40px;
            grid-column: 1 / -1;
        }
    </style>
</div>
