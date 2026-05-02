<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">ROLES & PERMISOS</h1>
            <p class="page-subtitle">Crea roles personalizados y asigna permisos granulares</p>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <span>+</span> Nuevo Rol
        </button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-label">TOTAL ROLES</div>
            <div class="stat-value">{{ $metrics['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">ROLES ACTIVOS</div>
            <div class="stat-value" style="color: var(--good);">{{ $metrics['active'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">AGENTES CON ROL</div>
            <div class="stat-value" style="color: var(--orange);">{{ $metrics['agents'] }}</div>
        </div>
    </div>

    <div class="search-bar">
        <input type="text" placeholder="Buscar roles..." wire:model="search" class="search-input">
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content modal-lg" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingRole ? 'EDITAR ROL' : 'NUEVO ROL' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveRole">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre del rol *</label>
                        <input type="text" placeholder="Ej: Supervisor, Editor, Viewer" wire:model="name" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select wire:model="is_active" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea placeholder="Descripción del rol..." wire:model="description" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;min-height:60px;"></textarea>
                </div>

                <div class="perm-section-label">PERMISOS</div>
                <div class="perm-matrix">
                    <div class="matrix-header">
                        <div style="text-align:left">Sección</div>
                        <div>∅</div>
                        <div>👁</div>
                        <div>+</div>
                        <div>✎</div>
                        <div>✕</div>
                    </div>
                    @foreach($availableSections as $section => $label)
                    <div class="matrix-row">
                        <div>{{ $label }}</div>
                        @foreach(array_keys($permissionLevels) as $level)
                        <div class="matrix-cell">
                            <div class="matrix-check {{ ($permissions[$section] ?? 'none') === $level ? 'selected' : '' }}" 
                                 wire:click="togglePermission('{{ $section }}', '{{ $level }}')">
                                {{ $level === 'none' ? '∅' : ($level === 'read' ? '👁' : ($level === 'create' ? '+' : ($level === 'edit' ? '✎' : '✕'))) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>

                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingRole ? 'Guardar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(session()->has('message'))
    <div class="toast toast-success">
        {{ session('message') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="toast toast-error">
        {{ session('error') }}
    </div>
    @endif

    <div class="roles-grid">
        @forelse($roles as $role)
        <div class="role-card {{ !$role->is_active ? 'inactive' : '' }}">
            <div class="role-header">
                <div class="role-avatar">{{ strtoupper(substr($role->name, 0, 2)) }}</div>
                <div class="role-info">
                    <h3>{{ $role->name }}</h3>
                    <span class="role-status {{ $role->is_active ? 'active' : 'inactive' }}">
                        {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="role-actions">
                    <button class="btn-action" wire:click="openEditModal({{ $role->id }})" title="Editar">✏️</button>
                    <button class="btn-action" wire:click="toggleStatus({{ $role->id }})" title="{{ $role->is_active ? 'Desactivar' : 'Activar' }}">
                        {{ $role->is_active ? '🚫' : '✅' }}
                    </button>
                    <button class="btn-action btn-delete" wire:click="deleteRole({{ $role->id }})" title="Eliminar">🗑️</button>
                </div>
            </div>
            @if($role->description)
            <p class="role-desc">{{ $role->description }}</p>
            @endif
            <div class="role-perms">
                <span class="perm-count">{{ count($role->permissions ?? []) }} permisos</span>
                <div class="perm-badges">
                    @foreach($role->permissions ?? [] as $section => $level)
                    @if($level !== 'none')
                    <span class="perm-badge">{{ $section }}</span>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="role-meta">
                {{ $role->agents()->count() }} agentes · Creado {{ $role->created_at->format('d/m/Y') }}
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p>No hay roles creados</p>
            <button wire:click="openCreateModal" class="btn-primary" style="margin-top:16px;">Crear primer rol</button>
        </div>
        @endforelse
    </div>

    </div>

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
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; padding: 0 28px; }
        .stat-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line); border-radius: 14px; padding: 18px; }
        .stat-icon { font-size: 20px; margin-bottom: 8px; }
        .stat-label { font-size: 10px; color: var(--muted); font-weight: 700; letter-spacing: 0.1em; }
        .stat-value { font-family: var(--font-display); font-size: 32px; color: var(--white); margin-top: 4px; }
        
        .search-bar { padding: 0 28px; margin-bottom: 20px; }
        .search-input { width: 100%; max-width: 400px; padding: 12px 20px; border-radius: 999px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 13px; color: var(--white); }
        .search-input:focus { outline: none; border-color: var(--orange); }
        
        .roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; padding: 0 28px 28px; }
        .role-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line); border-radius: 14px; padding: 20px; }
        .role-card.inactive { opacity: 0.6; }
        .role-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .role-avatar { width: 44px; height: 44px; border-radius: 10px; background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; color: #190702; font-weight: 800; font-size: 16px; }
        .role-info { flex: 1; }
        .role-info h3 { font-size: 16px; font-weight: 700; margin: 0; }
        .role-status { font-size: 11px; font-weight: 600; }
        .role-status.active { color: var(--good); }
        .role-status.inactive { color: var(--muted); }
        .role-actions { display: flex; gap: 6px; }
        .btn-action { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); cursor: pointer; font-size: 14px; }
        .btn-action:hover { background: var(--orange); }
        .btn-delete:hover { background: #ff4757; }
        
        .role-desc { font-size: 13px; color: var(--muted); margin-bottom: 12px; }
        .role-perms { margin-bottom: 12px; }
        .perm-count { font-size: 11px; color: var(--orange); font-weight: 700; }
        .perm-badges { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px; }
        .perm-badge { font-size: 10px; padding: 2px 8px; border-radius: 4px; background: rgba(255,106,26,0.15); color: var(--orange); }
        .role-meta { font-size: 11px; color: var(--muted); }
        
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--muted); }
        .empty-icon { font-size: 40px; margin-bottom: 12px; }
        
        .toast { position: fixed; top: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; font-weight: 700; z-index: 2000; }
        .toast-success { background: var(--good); color: #000; }
        .toast-error { background: #ff4757; color: #fff; }
        
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }
        
        .perm-section-label { font-size: 12px; color: var(--orange); font-weight: 700; letter-spacing: 0.1em; margin: 16px 0 12px; }
        .perm-matrix { border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
        .matrix-header { display: grid; grid-template-columns: 1fr 40px 40px 40px 40px 40px; gap: 4px; padding: 10px 12px; background: rgba(255,255,255,0.04); font-size: 11px; color: var(--muted); text-align: center; }
        .matrix-row { display: grid; grid-template-columns: 1fr 40px 40px 40px 40px 40px; gap: 4px; padding: 8px 12px; border-top: 1px solid var(--line); align-items: center; font-size: 12px; }
        .matrix-cell { text-align: center; }
        .matrix-check { width: 28px; height: 28px; border-radius: 6px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; transition: all 0.2s; }
        .matrix-check:hover { background: rgba(255,106,26,0.2); }
        .matrix-check.selected { background: var(--orange); color: #190702; border: none; }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .roles-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .matrix-header, .matrix-row { grid-template-columns: 1fr 32px 32px 32px 32px 32px; font-size: 10px; }
        }
    </style>
</div>