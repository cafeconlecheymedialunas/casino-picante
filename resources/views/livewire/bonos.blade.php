<div class="page-container">
    @livewire(\App\Livewire\Components\PageHeader::class, [
        'title' => 'BONOS Y PROGRAMAS',
        'subtitle' => 'Gestión de bonos y asignaciones a usuarios',
        'buttonText' => 'Nuevo Bono',
        'buttonAction' => 'openCreateModal',
    ])

    @if(session()->has('message'))
    <div class="toast-success">
        {{ session('message') }}
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-label">TOTAL BONOS</div>
            <div class="stat-value">{{ $metrics['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">BONOS ACTIVOS</div>
            <div class="stat-value" style="color: var(--good);">{{ $metrics['active'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">ASIGNACIONES</div>
            <div class="stat-value" style="color: var(--orange);">{{ $metrics['assigned'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✓</div>
            <div class="stat-label">USADOS</div>
            <div class="stat-value" style="color: var(--amber);">{{ $metrics['used'] }}</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2 class="table-title">LISTADO DE BONOS</h2>
            <div class="table-filters">
                <input type="text" wire:model="search" placeholder="Buscar bonos..." class="search-input">
                <select wire:model="filter" class="filter-select">
                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>
        </div>

        @forelse($bonuses as $bonus)
        <div class="bonus-item">
            <div class="bonus-main">
                <div class="bonus-icon">🎁</div>
                <div class="bonus-info">
                    <div class="bonus-title">{{ $bonus->title }}</div>
                    <div class="bonus-meta">
                        @if($bonus->type === 'general')
                        <span class="badge-general">General</span>
                        @else
                        <span class="badge-specific">Específico</span>
                        @endif
                        <span class="badge-status {{ $bonus->status }}">{{ $bonus->status === 'active' ? 'Activo' : 'Inactivo' }}</span>
                    </div>
                    @if($bonus->description)
                    <div class="bonus-desc">{{ $bonus->description }}</div>
                    @endif
                </div>
                <div class="bonus-details">
                    <div class="bonus-amount">
                        @if($bonus->bonus_percent > 0)
                        <span class="percent">{{ $bonus->bonus_percent }}%</span>
                        @endif
                        @if($bonus->bonus_amount > 0)
                        <span class="amount">${{ number_format($bonus->bonus_amount) }}</span>
                        @endif
                    </div>
                    <div class="bonus-dates">
                        {{ $bonus->start_date?->format('d/m/Y') }} - {{ $bonus->end_date?->format('d/m/Y') }}
                    </div>
                </div>
                <div class="bonus-actions">
                    <button class="btn-action" wire:click="openAssignModal({{ $bonus->id }})" title="Asignar a usuario">
                        👤
                    </button>
                    <button class="btn-action" wire:click="openEditModal({{ $bonus->id }})" title="Editar">
                        ✏️
                    </button>
                    <button class="btn-action" wire:click="toggleStatus({{ $bonus->id }})" title="{{ $bonus->status === 'active' ? 'Desactivar' : 'Activar' }}">
                        {{ $bonus->status === 'active' ? '🚫' : '✅' }}
                    </button>
                    <button class="btn-action btn-delete" wire:click="deleteBonus({{ $bonus->id }})" title="Eliminar">
                        🗑️
                    </button>
                </div>
            </div>

            @php $assignments = $this->getAssignments($bonus->id); @endphp
            @if($assignments->count() > 0)
            <div class="assignments-list">
                <div class="assignments-title">Asignaciones ({{ $assignments->count() }})</div>
                @foreach($assignments->take(5) as $assignment)
                <div class="assignment-item">
                    <span class="assignment-user">{{ $assignment->user->name }}</span>
                    <span class="assignment-status {{ $assignment->status }}">
                        @if($assignment->status === 'available') Disponible
                        @elseif($assignment->status === 'used') Usado
                        @else Expirado
                        @endif
                    </span>
                    <div class="assignment-actions">
                        @if($assignment->status === 'available')
                        <button class="btn-small" wire:click="markAsUsed({{ $assignment->id }})">Usar</button>
                        <button class="btn-small" wire:click="markAsExpired({{ $assignment->id }})">Expirar</button>
                        @endif
                        <button class="btn-small btn-delete" wire:click="removeAssignment({{ $assignment->id }})">✕</button>
                    </div>
                </div>
                @endforeach
                @if($assignments->count() > 5)
                <div class="assignments-more">+{{ $assignments->count() - 5 }} más</div>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">🎁</div>
            <p>No hay bonos creados</p>
            <button wire:click="openCreateModal" class="btn-primary" style="margin-top:16px;">Crear primer bono</button>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingBonus ? 'EDITAR BONO' : 'NUEVO BONO' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveBonus">
                <div class="form-group">
                    <label>Título *</label>
                    <input type="text" wire:model="title" placeholder="Ej: Bono de bienvenida 100%" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea wire:model="description" placeholder="Descripción del bono..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select wire:model="type" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                            <option value="general">General (todos los usuarios)</option>
                            <option value="specific">Específico (usuarios seleccionados)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select wire:model="status" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>% Bono</label>
                        <input type="number" wire:model="bonus_percent" placeholder="0" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label>Monto fijo ($)</label>
                        <input type="number" wire:model="bonus_amount" placeholder="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Depósito mínimo</label>
                        <input type="number" wire:model="min_deposit" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Bono máximo</label>
                        <input type="number" wire:model="max_bonus" placeholder="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha inicio</label>
                        <input type="date" wire:model="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha fin</label>
                        <input type="date" wire:model="end_date" required>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingBonus ? 'Guardar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($showAssignModal && $selectedBonus)
    <div class="modal-overlay" wire:click="closeAssignModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>ASIGNAR BONO A USUARIO</h3>
                <button class="modal-close" wire:click="closeAssignModal">✕</button>
            </div>
            <div class="modal-form">
                <p style="color:var(--muted);font-size:13px;margin-bottom:16px;">
                    Asignar "{{ $selectedBonus->title }}" a un usuario específico
                </p>
                <div class="form-group">
                    <label>Seleccionar usuario</label>
                    <select wire:model="selectedUser" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="">Seleccionar usuario...</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeAssignModal" class="btn-ghost">Cancelar</button>
                    <button type="button" wire:click="assignToUser" class="btn-primary" {{ !$selectedUser ? 'disabled' : '' }}>
                        Asignar Bono
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; padding: 0 28px; }
        .stat-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line); border-radius: 14px; padding: 18px; }
        .stat-icon { font-size: 20px; margin-bottom: 8px; }
        .stat-label { font-size: 10px; color: var(--muted); font-weight: 700; letter-spacing: 0.1em; }
        .stat-value { font-family: var(--font-display); font-size: 32px; color: var(--white); margin-top: 4px; }
        
        .table-card { margin: 0 28px 28px; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line); border-radius: 14px; padding: 20px; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .table-title { font-family: var(--font-display); font-size: 18px; margin: 0; }
        .table-filters { display: flex; gap: 10px; }
        .search-input { padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 12px; color: var(--white); width: 200px; }
        .search-input:focus { outline: none; border-color: var(--orange); }
        .filter-select { padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 12px; color: var(--white); }
        
        .bonus-item { border: 1px solid var(--line); border-radius: 12px; margin-bottom: 12px; overflow: hidden; }
        .bonus-main { display: flex; align-items: center; gap: 16px; padding: 16px; background: rgba(255,255,255,0.02); }
        .bonus-icon { font-size: 24px; }
        .bonus-info { flex: 1; }
        .bonus-title { font-size: 14px; font-weight: 700; color: var(--white); }
        .bonus-meta { display: flex; gap: 8px; margin-top: 4px; }
        .badge-general, .badge-specific, .badge-status { font-size: 10px; padding: 2px 8px; border-radius: 999px; font-weight: 600; }
        .badge-general { background: rgba(255,106,26,0.15); color: var(--orange); }
        .badge-specific { background: rgba(156,39,176,0.15); color: #9c27b0; }
        .badge-status.active { background: rgba(37,196,107,0.12); color: var(--good); }
        .badge-status.inactive { background: rgba(255,255,255,0.06); color: var(--muted-2); }
        .bonus-desc { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .bonus-details { text-align: right; }
        .bonus-amount { font-family: var(--font-display); font-size: 20px; color: var(--orange); }
        .bonus-amount .percent { font-size: 14px; }
        .bonus-dates { font-size: 11px; color: var(--muted); margin-top: 4px; }
        .bonus-actions { display: flex; gap: 6px; }
        .btn-action { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); cursor: pointer; font-size: 14px; }
        .btn-action:hover { background: var(--orange); }
        .btn-action.btn-delete:hover { background: #ff4757; }
        
        .assignments-list { padding: 12px 16px; background: rgba(0,0,0,0.2); border-top: 1px solid var(--line); }
        .assignments-title { font-size: 11px; color: var(--muted); font-weight: 700; margin-bottom: 8px; }
        .assignment-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid var(--line); }
        .assignment-item:last-child { border-bottom: none; }
        .assignment-user { flex: 1; font-size: 12px; }
        .assignment-status { font-size: 10px; padding: 2px 8px; border-radius: 999px; font-weight: 600; }
        .assignment-status.available { background: rgba(37,196,107,0.12); color: var(--good); }
        .assignment-status.used { background: rgba(255,106,26,0.12); color: var(--orange); }
        .assignment-status.expired { background: rgba(255,255,255,0.06); color: var(--muted); }
        .assignment-actions { display: flex; gap: 4px; }
        .btn-small { padding: 4px 8px; font-size: 10px; border-radius: 4px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); color: var(--white); cursor: pointer; }
        .btn-small:hover { background: var(--orange); }
        .btn-small.btn-delete:hover { background: #ff4757; }
        .assignments-more { font-size: 11px; color: var(--muted); margin-top: 8px; }
        
        .empty-state { text-align: center; padding: 40px; color: var(--muted); }
        .empty-icon { font-size: 40px; margin-bottom: 12px; }
        
        .toast-success { position: fixed; top: 20px; right: 20px; background: var(--good); color: #000; padding: 12px 20px; border-radius: 8px; font-weight: 700; z-index: 2000; }
        
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 520px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .bonus-main { flex-wrap: wrap; }
            .bonus-details { width: 100%; text-align: left; margin-top: 8px; }
        }
    </style>
</div>