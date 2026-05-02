<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">GESTIÓN DE USUARIOS</h1>
            <p class="page-subtitle">Administra y controla todos los usuarios registrados en la plataforma</p>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <span>+</span> Nuevo Usuario
        </button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">TOTAL USUARIOS</div>
            <div class="stat-value">{{ number_format($metrics['total']) }}</div>
            <div class="stat-detail {{ $metrics['growthPercent'] >= 0 ? 'positive' : 'negative' }}">
                {{ $metrics['growthPercent'] >= 0 ? '↑' : '↓' }} {{ abs($metrics['growthPercent']) }}% vs mes anterior
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">USUARIOS ACTIVOS</div>
            <div class="stat-value" style="color: var(--good);">{{ number_format($metrics['active']) }}</div>
            <div class="stat-detail">{{ round($metrics['active'] / max($metrics['total'], 1) * 100) }}% del total</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-label">NUEVOS ESTE MES</div>
            <div class="stat-value" style="color: var(--orange);">{{ number_format($metrics['newThisMonth']) }}</div>
            <div class="stat-detail">
                Hoy: {{ $metrics['today'] }} | Semana: {{ $metrics['thisWeek'] }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📧</div>
            <div class="stat-label">EMAIL VERIFICADO</div>
            <div class="stat-value" style="color: var(--amber);">{{ number_format($metrics['verified']) }}</div>
            <div class="stat-detail">{{ round($metrics['verified'] / max($metrics['total'], 1) * 100) }}% verificados</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2 class="table-title">USUARIOS REGISTRADOS</h2>
            <div class="table-filters">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" wire:model.live="search" placeholder="Buscar por nombre o email..." class="search-input">
                </div>
                <select wire:model="status" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="pending">Pendiente</option>
                    <option value="blocked">Bloqueado</option>
                </select>
            </div>
        </div>

        @if($users->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>No se encontraron usuarios</p>
            </div>
        @else
            <div class="table-container">
                <div class="table-header-row">
                    <div>#</div>
                    <div>Usuario</div>
                    <div>Email</div>
                    <div>Teléfono</div>
                    <div>Estado</div>
                    <div>Registrado</div>
                    <div>Acciones</div>
                </div>

                @foreach($users as $index => $user)
                <div class="table-row">
                    <div class="row-number">{{ $users->firstItem() + $index }}</div>
                    <div class="row-user">
                        <div class="row-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->name }}</div>
                            @if($user->contact)
                            <div class="user-contact">{{ $user->contact }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row-email">{{ $user->email }}</div>
                    <div class="row-phone">{{ $user->phone ?? '—' }}</div>
                    <div class="row-status">
                        <span class="status-badge status-{{ $user->status }}">
                            @if($user->status === 'active') ✓ Activo
                            @elseif($user->status === 'pending') ⏳ Pendiente
                            @elseif($user->status === 'blocked') ✕ Bloqueado
                            @endif
                        </span>
                    </div>
                    <div class="row-date">
                        <div>{{ $user->created_at->format('d/m/Y') }}</div>
                        <div class="row-time">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="row-actions">
                        <button wire:click="openDetailModal({{ $user->id }})" class="btn-action btn-view" title="Ver detalles">
                            <span>👁</span>
                        </button>
                        <button wire:click="openEditModal({{ $user->id }})" class="btn-action btn-edit" title="Editar">
                            <span>✏️</span>
                        </button>
                        <button wire:click="toggleStatus({{ $user->id }})" 
                                class="btn-action btn-toggle {{ $user->status === 'active' ? 'btn-block' : 'btn-activate' }}"
                                title="{{ $user->status === 'active' ? 'Bloquear' : 'Activar' }}">
                            <span>{{ $user->status === 'active' ? '🚫' : '✅' }}</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="table-footer">
                <div class="pagination-info">
                    Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} de {{ $users->total() }} usuarios
                </div>
                <div class="pagination">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingUser ? 'EDITAR USUARIO' : 'NUEVO USUARIO' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form wire:submit="saveUser" class="modal-form">
                <div class="form-group">
                    <label>Nombre de usuario</label>
                    <input type="text" wire:model="name" placeholder="Ingresa el nombre de usuario" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" wire:model="email" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono (opcional)</label>
                        <input type="text" wire:model="phone" placeholder="+51 999 999 999">
                    </div>
                    <div class="form-group">
                        <label>Contacto adicional (opcional)</label>
                        <input type="text" wire:model="contact" placeholder="Telegram, WhatsApp, etc.">
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ $editingUser ? 'Nueva contraseña (dejar vacío para mantener)' : 'Contraseña' }}</label>
                    <input type="password" wire:model="password" placeholder="{{ $editingUser ? '••••••••' : 'Mínimo 6 caracteres' }}" {{ $editingUser ? '' : 'required' }}>
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingUser ? 'Guardar cambios' : 'Crear usuario' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($showDetailModal && $selectedUser)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content modal-lg" wire:click.stop>
            <div class="modal-header">
                <h3>DETALLES DEL USUARIO</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <div class="user-detail">
                <div class="detail-header">
                    <div class="detail-avatar">{{ strtoupper(substr($selectedUser->name, 0, 2)) }}</div>
                    <div class="detail-info">
                        <h2>{{ $selectedUser->name }}</h2>
                        <span class="status-badge status-{{ $selectedUser->status }}">
                            @if($selectedUser->status === 'active') Activo
                            @elseif($selectedUser->status === 'pending') Pendiente
                            @else Bloqueado
                            @endif
                        </span>
                    </div>
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Email</label>
                        <p>{{ $selectedUser->email }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Teléfono</label>
                        <p>{{ $selectedUser->phone ?? 'No registrado' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Contacto adicional</label>
                        <p>{{ $selectedUser->contact ?? 'No registrado' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Fecha de registro</label>
                        <p>{{ $selectedUser->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Última actualización</label>
                        <p>{{ $selectedUser->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Email verificado</label>
                        <p>{{ $selectedUser->email_verified_at ? '✓ Verificado el ' . $selectedUser->email_verified_at->format('d/m/Y') : '✕ No verificado' }}</p>
                    </div>
                </div>
                <div class="detail-actions">
                    <button wire:click="openEditModal({{ $selectedUser->id }})" class="btn-primary">Editar usuario</button>
                    <button wire:click="toggleStatus({{ $selectedUser->id }}); closeModal()" 
                            class="btn-ghost {{ $selectedUser->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                        {{ $selectedUser->status === 'active' ? 'Bloquear usuario' : 'Activar usuario' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session()->has('message'))
    <div class="toast toast-success">
        {{ session('message') }}
    </div>
    @endif

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 36px;
            color: var(--white);
            margin: 0;
            letter-spacing: 0.02em;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 13px;
            margin: 4px 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        .stat-card {
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--orange), var(--amber));
        }

        .stat-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0.1em;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: 36px;
            color: var(--white);
            line-height: 1;
        }

        .stat-detail {
            font-size: 12px;
            color: var(--muted);
            margin-top: 8px;
        }

        .stat-detail.positive { color: var(--good); }
        .stat-detail.negative { color: #ff4757; }

        .table-card {
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .table-title {
            font-family: var(--font-display);
            font-size: 24px;
            letter-spacing: 0.02em;
            color: var(--white);
        }

        .table-filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
        }

        .search-input {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 10px;
            padding: 10px 16px 10px 38px;
            color: var(--white);
            font-size: 13px;
            min-width: 240px;
        }

        .search-input::placeholder { color: var(--muted-2); }

        .filter-select {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 10px;
            padding: 10px 16px;
            color: var(--white);
            font-size: 13px;
            cursor: pointer;
        }

        .table-container {
            overflow-x: auto;
        }

        .table-header-row {
            display: grid;
            grid-template-columns: 50px 2fr 2fr 1fr 100px 120px 140px;
            gap: 16px;
            font-size: 11px;
            color: var(--muted);
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        @media (max-width: 768px) {
            .table-header-row { display: none; }
            .table-row { flex-direction: column; padding: 16px; }
        }

        .table-row {
            display: grid;
            grid-template-columns: 50px 2fr 2fr 1fr 100px 120px 140px;
            gap: 16px;
            font-size: 13px;
            padding: 16px 0;
            border-bottom: 1px solid var(--line);
            align-items: center;
            transition: background 0.2s;
        }

        .table-row:hover {
            background: rgba(255,106,26,0.05);
        }

        .row-number {
            color: var(--muted-2);
            font-family: var(--font-mono);
            font-size: 12px;
        }

        .row-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .row-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--orange), var(--amber));
            color: #190702;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--white);
        }

        .user-contact {
            font-size: 11px;
            color: var(--muted);
        }

        .row-email {
            color: var(--muted);
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .row-phone {
            color: var(--muted);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-active {
            background: rgba(37,196,107,0.15);
            color: var(--good);
        }

        .status-pending {
            background: rgba(255,179,71,0.15);
            color: var(--warn);
        }

        .status-blocked {
            background: rgba(255,71,87,0.15);
            color: #ff4757;
        }

        .row-date {
            display: flex;
            flex-direction: column;
            color: var(--muted);
        }

        .row-time {
            font-size: 11px;
            color: var(--muted-2);
        }

        .row-actions {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.04);
            color: var(--white);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            background: rgba(255,106,26,0.2);
            border-color: var(--orange);
        }

        .btn-toggle:hover {
            transform: scale(1.05);
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .pagination-info {
            font-size: 12px;
            color: var(--muted);
        }

        .pagination {
            display: flex;
            gap: 4px;
        }

        .pagination button {
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--line);
            color: var(--muted);
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .pagination button:hover {
            background: rgba(255,106,26,0.2);
            color: var(--white);
        }

        .pagination button.active {
            background: var(--orange);
            color: #190702;
            border-color: var(--orange);
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
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
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-lg { max-width: 560px; }

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
            letter-spacing: 0.04em;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
        }

        .modal-close:hover { color: var(--white); }

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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .btn-danger {
            border-color: #ff4757 !important;
            color: #ff4757 !important;
        }

        .btn-success {
            border-color: var(--good) !important;
            color: var(--good) !important;
        }

        .user-detail {
            padding: 24px;
        }

        .detail-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--line);
        }

        .detail-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--orange), var(--amber));
            color: #190702;
            font-weight: 800;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-info h2 {
            font-family: var(--font-display);
            font-size: 28px;
            margin: 0 0 8px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .detail-item label {
            display: block;
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        .detail-item p {
            color: var(--white);
            font-size: 14px;
            margin: 0;
        }

        .detail-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 16px 24px;
            border-radius: 12px;
            font-weight: 600;
            z-index: 1001;
            animation: slideIn 0.3s ease;
        }

        .toast-success {
            background: var(--good);
            color: #190702;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 640px) {
            .page-header { flex-direction: column; }
            .page-title { font-size: 28px; }
            .table-filters { width: 100%; }
            .search-wrapper { width: 100%; }
            .search-input { width: 100%; min-width: auto; }
            .form-row { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .detail-actions { flex-direction: column; }
            .modal-actions { flex-direction: column; }
            .btn-action { width: 28px; height: 28px; font-size: 12px; }
        }
    </style>
</div>