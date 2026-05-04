<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">AGENTES</h1>
            <p class="page-subtitle">Gestión de agentes del sistema</p>
        </div>
        <div class="header-actions">
            <input type="text" placeholder="Buscar agentes..." wire:model="search" class="search-input">
            <button wire:click="openCreateModal" class="btn-primary">
                <span>+</span> Crear agente
            </button>
        </div>
    </div>

    <div class="filter-bar">
        <button class="filter-btn {{ $statusFilter === 'all' ? 'active' : '' }}" wire:click="$set('statusFilter', 'all')">Todos</button>
        <button class="filter-btn {{ $statusFilter === 'active' ? 'active' : '' }}" wire:click="$set('statusFilter', 'active')">Activos</button>
        <button class="filter-btn {{ $statusFilter === 'inactive' ? 'active' : '' }}" wire:click="$set('statusFilter', 'inactive')">Inactivos</button>
    </div>

    <div class="stats-row">
        <div class="stat-item">
            <span class="stat-value">{{ $metrics['total'] }}</span>
            <span class="stat-label">Total</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $metrics['active'] }}</span>
            <span class="stat-label">Activos</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $metrics['inactive'] }}</span>
            <span class="stat-label">Inactivos</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $metrics['with_lines'] }}</span>
            <span class="stat-label">Con líneas</span>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="flash-message">{{ session('message') }}</div>
    @endif

    <div class="agents-grid">
        @forelse($agents as $agent)
        <div class="agent-card {{ $agent->status === 'inactive' ? 'inactive' : '' }}">
            <div class="agent-header">
                <div class="agent-avatar">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
                <div class="agent-info">
                    <div class="agent-name">{{ $agent->name }}</div>
                    <div class="agent-email">{{ $agent->email }}</div>
                </div>
                <button class="toggle-status {{ $agent->status === 'active' ? 'active' : '' }}" wire:click="toggleStatus({{ $agent->id }})" title="{{ $agent->status === 'active' ? 'Desactivar' : 'Activar' }}">
                    {{ $agent->status === 'active' ? '●' : '○' }}
                </button>
            </div>
            @if($agent->phone)
            <div class="agent-phone">📞 {{ $agent->phone }}</div>
            @endif
            <div class="agent-lines">
                @forelse($agent->activeLines as $line)
                <span class="line-badge">{{ $line->name }}</span>
                @empty
                <span class="no-lines">Sin líneas asignadas</span>
                @endforelse
            </div>
            <div class="agent-actions">
                <button class="btn-edit" wire:click="openEditModal({{ $agent->id }})">Editar</button>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <p>No hay agentes</p>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingAgent ? 'EDITAR AGENTE' : 'NUEVO AGENTE' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveAgent">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" placeholder="Nombre del agente" wire:model="name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" placeholder="correo@ejemplo.com" wire:model="email" required>
                </div>
                <div class="form-group">
                    <label>Contraseña {{ $editingAgent ? '(dejar vacío para mantener)' : '*' }}</label>
                    <input type="password" placeholder="••••••••" wire:model="password" {{ $editingAgent ? '' : 'required' }}>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" placeholder="+54 9 11 9999 9999" wire:model="phone">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select wire:model="status" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
                <div class="modal-actions">
                    @if($editingAgent)
                    <button type="button" class="btn-delete" wire:click="deleteAgent({{ $editingAgent->id }})" wire:confirm="¿Eliminar este agente?">Eliminar</button>
                    @endif
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingAgent ? 'Guardar' : 'Crear' }}</button>
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
            margin: -24px -28px 24px -28px;
            padding: 24px 28px 16px;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--black);
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
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding-top: 12px;
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

        .filter-bar {
            display: flex;
            gap: 8px;
            padding: 0 28px 20px;
        }
        .filter-btn {
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: transparent;
            color: var(--muted);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-btn.active {
            background: var(--orange);
            color: #190702;
            border-color: var(--orange);
            font-weight: 700;
        }
        .filter-btn:hover:not(.active) {
            border-color: var(--orange);
            color: var(--orange);
        }

        .stats-row {
            display: flex;
            gap: 16px;
            padding: 0 28px 20px;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 10px;
            padding: 12px 20px;
        }
        .stat-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--orange);
        }
        .stat-label {
            font-size: 12px;
            color: var(--muted);
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--good);
            color: #000;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 700;
            z-index: 2000;
        }

        .agents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            padding: 0 28px 40px;
        }

        .agent-card {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 14px;
            padding: 18px;
            transition: all 0.2s;
        }
        .agent-card:hover {
            border-color: var(--orange);
        }
        .agent-card.inactive {
            opacity: 0.5;
        }

        .agent-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .agent-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--orange);
            color: #190702;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            flex-shrink: 0;
        }
        .agent-info {
            flex: 1;
            min-width: 0;
        }
        .agent-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .agent-email {
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .toggle-status {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: transparent;
            color: var(--muted);
            font-size: 14px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .toggle-status.active {
            color: var(--good);
            border-color: var(--good);
        }
        .toggle-status:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        .agent-phone {
            font-size: 13px;
            color: var(--white);
            margin-bottom: 12px;
        }

        .agent-lines {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 16px;
            min-height: 28px;
        }
        .line-badge {
            background: rgba(255,106,26,0.15);
            color: var(--orange);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
        .no-lines {
            font-size: 12px;
            color: var(--muted);
        }

        .agent-actions {
            display: flex;
            gap: 8px;
        }
        .btn-edit {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: transparent;
            color: var(--white);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-edit:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            color: var(--muted);
            padding: 40px;
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
            max-width: 480px;
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
            color: var(--white);
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 20px;
            cursor: pointer;
        }
        .modal-close:hover {
            color: var(--orange);
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
        .form-group input {
            width: 100%;
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--white);
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--orange);
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .btn-ghost {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: transparent;
            color: var(--white);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-ghost:hover {
            border-color: var(--orange);
            color: var(--orange);
        }
        .btn-primary {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            background: var(--orange);
            color: #190702;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--amber);
        }
        .btn-delete {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid #ff4757;
            background: transparent;
            color: #ff4757;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            margin-right: auto;
        }
        .btn-delete:hover {
            background: #ff4757;
            color: white;
        }
    </style>
</div>