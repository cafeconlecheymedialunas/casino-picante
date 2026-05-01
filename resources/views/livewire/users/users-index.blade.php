<div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">TOTAL USUARIOS</div>
            <div class="stat-value">{{ $users->total() }}</div>
            <div class="stat-detail">+12% vs mes anterior</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">ACTIVOS</div>
            <div class="stat-value" style="color: var(--good);">1,847</div>
            <div class="stat-detail">Últimos 30 días</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">NUEVOS ESTE MES</div>
            <div class="stat-value">234</div>
            <div class="stat-detail">+8% vs mes anterior</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">VERIFICADOS</div>
            <div class="stat-value">1,623</div>
            <div class="stat-detail">88% del total</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2 class="table-title">USUARIOS REGISTRADOS</h2>
            <div style="display: flex; gap: 12px;">
                <input type="text" wire:model.live="search" placeholder="Buscar usuarios..." class="search-input">
                <select wire:model="status" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="pending">Pendiente</option>
                    <option value="blocked">Bloqueado</option>
                </select>
            </div>
        </div>

        <div class="table-header-row">
            <div>#</div>
            <div>Usuario</div>
            <div>Email</div>
            <div>Línea</div>
            <div>Estado</div>
            <div>Última actividad</div>
            <div>Acciones</div>
        </div>

        @foreach($users as $index => $user)
        <div class="table-row">
            <div style="color: var(--muted); font-family: var(--font-mono);">{{ $index + 1 }}</div>
            <div>
                <div class="row-avatar">{{ substr($user->name, 0, 2) }}</div>
            </div>
            <div class="row-email">{{ $user->email }}</div>
            <div class="row-line"><span>L1</span></div>
            <div class="row-status active">● Activo</div>
            <div class="row-time">Hace 2h</div>
            <div>
                <button class="btn-ghost" style="height: 28px; padding: 0 12px; font-size: 11px;">Ver</button>
            </div>
        </div>
        @endforeach

        <div style="margin-top: 20px;">
            {{ $users->links() }}
        </div>
    </div>
</div>