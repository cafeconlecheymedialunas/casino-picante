<div class="page-container" x-data="toastManager()" @toast.window="show($event.detail)">
<style>
    /* ── Page header ── */
    .page-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        flex-wrap: wrap; gap: 16px;
        position: sticky; top: 0; z-index: 10;
        background: var(--black);
        padding: 24px 28px 16px; margin: -24px -28px 24px;
        border-bottom: 1px solid var(--line);
    }
    .page-title    { font-family: var(--font-display); font-size: 32px; letter-spacing: 0.03em; margin: 0; }
    .page-subtitle { color: var(--muted-2); font-size: 12px; margin: 4px 0 0; }

    /* ── Stats ── */
    .stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
    .stat-card {
        background: linear-gradient(180deg,#170b0b,#0f0707);
        border: 1px solid var(--line); border-radius: 14px; padding: 18px 20px;
        position: relative; overflow: hidden;
    }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,var(--orange),var(--amber)); }
    .stat-icon  { font-size: 22px; margin-bottom: 8px; }
    .stat-label { font-size: 10px; font-weight: 800; letter-spacing: 0.12em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 4px; }
    .stat-value { font-family: var(--font-display); font-size: 34px; line-height: 1; }
    .stat-sub   { font-size: 11px; color: var(--muted-2); margin-top: 6px; }
    .c-good  { color: var(--good); }
    .c-warn  { color: var(--warn); }
    .c-red   { color: #ff4757; }
    .c-orange{ color: var(--orange); }

    /* ── Table card ── */
    .table-card {
        background: linear-gradient(180deg,#170b0b,#0f0707);
        border: 1px solid var(--line); border-radius: 16px; overflow: hidden;
    }
    .tc-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 12px; padding: 18px 20px;
        border-bottom: 1px solid var(--line);
    }
    .tc-title { font-family: var(--font-display); font-size: 22px; letter-spacing: 0.03em; }
    .tc-filters { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

    .search-wrap { position: relative; }
    .search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px; pointer-events: none; }
    .search-input {
        background: rgba(255,255,255,0.04); border: 1px solid var(--line-2);
        border-radius: 8px; padding: 8px 14px 8px 34px;
        color: var(--white); font-size: 13px; font-family: var(--font-body);
        min-width: 220px; transition: border-color 0.2s;
    }
    .search-input::placeholder { color: var(--muted-2); }
    .search-input:focus { outline: none; border-color: var(--orange); }

    /* filter select is styled by global rules — just size override */
    .filter-select { min-width: 150px; }

    .tc-count { font-size: 11px; color: var(--muted-2); white-space: nowrap; }

    /* ── Table ── */
    .t-head, .t-row {
        display: grid;
        grid-template-columns: 44px 2fr 2fr 120px 110px 110px 110px;
        gap: 12px; align-items: center; padding: 11px 20px;
    }
    .t-head {
        font-size: 10px; font-weight: 800; letter-spacing: 0.1em;
        color: var(--muted-2); text-transform: uppercase;
        border-bottom: 1px solid var(--line);
    }
    .t-row { border-bottom: 1px solid var(--line); font-size: 13px; transition: background 0.15s; }
    .t-row:last-child { border-bottom: none; }
    .t-row:hover { background: rgba(255,106,26,0.04); }

    .t-num { font-family: var(--font-mono); font-size: 11px; color: var(--muted-2); }

    .t-user { display: flex; align-items: center; gap: 10px; }
    .t-avatar {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg,var(--orange),var(--amber));
        color: #190702; font-weight: 800; font-size: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .t-uname   { font-weight: 600; }
    .t-contact { font-size: 10px; color: var(--muted-2); }

    .t-email { color: var(--muted); font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .t-phone { color: var(--muted-2); font-size: 12px; }

    .s-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 999px;
        font-size: 10px; font-weight: 800; white-space: nowrap;
    }
    .s-active  { background: rgba(37,196,107,0.12); color: var(--good); }
    .s-pending { background: rgba(255,179,71,0.12);  color: var(--warn); }
    .s-blocked { background: rgba(255,71,87,0.12);   color: #ff4757; }

    .t-date { font-size: 11px; color: var(--muted-2); }
    .t-date-sub { font-size: 10px; color: var(--muted-2); opacity: 0.7; }

    .t-actions { display: flex; gap: 6px; }
    .btn-icon {
        width: 30px; height: 30px; border-radius: 7px;
        border: 1px solid var(--line); background: rgba(255,255,255,0.03);
        color: var(--muted); cursor: pointer; font-size: 13px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.15s;
    }
    .btn-icon:hover           { background: rgba(255,106,26,0.15); border-color: var(--orange); color: var(--white); }
    .btn-icon.danger:hover    { background: rgba(255,71,87,0.15); border-color: #ff4757; }
    .btn-icon.activate:hover  { background: rgba(37,196,107,0.15); border-color: var(--good); }

    /* ── Pagination ── */
    .tc-footer {
        display: flex; justify-content: space-between; align-items: center;
        padding: 14px 20px; border-top: 1px solid var(--line);
        font-size: 12px; color: var(--muted-2); flex-wrap: wrap; gap: 10px;
    }

    /* Laravel pagination override */
    nav[aria-label] { display: flex; align-items: center; gap: 4px; }
    nav[aria-label] span[aria-current] span,
    nav[aria-label] button {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 8px;
        border-radius: 7px; font-size: 12px; cursor: pointer;
        background: rgba(255,255,255,0.04); border: 1px solid var(--line);
        color: var(--muted); transition: all 0.15s;
    }
    nav[aria-label] button:hover { background: rgba(255,106,26,0.15); border-color: var(--orange); color: #fff; }
    nav[aria-label] span[aria-current] span { background: var(--orange); border-color: var(--orange); color: #190702; font-weight: 800; }
    nav[aria-label] span:not([aria-current]) span { color: var(--muted-2); cursor: default; }

    /* ── Empty ── */
    .empty-state { padding: 56px 24px; text-align: center; color: var(--muted-2); }
    .empty-icon  { font-size: 44px; margin-bottom: 12px; }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.75);
        display: flex; align-items: center; justify-content: center;
        z-index: 200; padding: 20px;
    }
    .modal-box {
        background: linear-gradient(180deg,#1c0e0e,#120909);
        border: 1px solid var(--line-2); border-radius: 18px;
        width: 100%; max-width: 480px; max-height: 92vh; overflow-y: auto;
    }
    .modal-box.modal-lg { max-width: 560px; }
    .modal-head {
        display: flex; justify-content: space-between; align-items: center;
        padding: 18px 22px; border-bottom: 1px solid var(--line);
    }
    .modal-head h3 { font-family: var(--font-display); font-size: 22px; letter-spacing: 0.04em; margin: 0; }
    .modal-close { background: none; border: none; color: var(--muted); font-size: 18px; cursor: pointer; padding: 4px 8px; border-radius: 6px; }
    .modal-close:hover { color: var(--white); background: rgba(255,255,255,0.06); }
    .modal-body { padding: 22px; }

    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; }
    .form-input {
        width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2);
        border-radius: 9px; padding: 10px 14px; color: var(--white);
        font-size: 13px; font-family: var(--font-body); transition: border-color 0.2s;
    }
    .form-input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,0.12); }
    .form-input::placeholder { color: var(--muted-2); }
    .form-input.is-error { border-color: #ff4757; }
    .form-error { font-size: 11px; color: #ff4757; margin-top: 4px; }

    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .form-hint { font-size: 11px; color: var(--muted-2); margin-top: 4px; }

    .modal-foot { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--line); }

    /* ── Detail modal ── */
    .detail-head { display: flex; align-items: center; gap: 16px; margin-bottom: 22px; padding-bottom: 18px; border-bottom: 1px solid var(--line); }
    .detail-avatar {
        width: 60px; height: 60px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg,var(--orange),var(--amber));
        color: #190702; font-weight: 800; font-size: 22px;
        display: flex; align-items: center; justify-content: center;
    }
    .detail-name { font-family: var(--font-display); font-size: 26px; margin: 0 0 6px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 22px; }
    .detail-item label { display: block; font-size: 10px; font-weight: 800; letter-spacing: 0.1em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 4px; }
    .detail-item p { font-size: 13px; color: var(--white); margin: 0; word-break: break-word; }
    .detail-item p.muted { color: var(--muted-2); }

    .detail-status-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .detail-status-row .s-btn {
        padding: 7px 16px; border-radius: 999px; font-size: 12px; font-weight: 700;
        border: 1px solid; cursor: pointer; transition: all 0.15s;
    }
    .s-btn-active  { background: rgba(37,196,107,0.08); border-color: rgba(37,196,107,0.3); color: var(--good); }
    .s-btn-active:hover  { background: rgba(37,196,107,0.2); }
    .s-btn-pending { background: rgba(255,179,71,0.08); border-color: rgba(255,179,71,0.3); color: var(--warn); }
    .s-btn-pending:hover { background: rgba(255,179,71,0.2); }
    .s-btn-blocked { background: rgba(255,71,87,0.08); border-color: rgba(255,71,87,0.3); color: #ff4757; }
    .s-btn-blocked:hover { background: rgba(255,71,87,0.2); }
    .s-btn.is-current { opacity: 0.4; cursor: default; pointer-events: none; }

    /* ── Toast ── */
    .toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 500; display: flex; flex-direction: column; gap: 8px; }
    .toast-item {
        padding: 13px 20px; border-radius: 11px; font-size: 13px; font-weight: 600;
        box-shadow: 0 8px 24px rgba(0,0,0,0.4);
        animation: toastIn 0.25s ease;
    }
    .toast-success { background: var(--good); color: #002b14; }
    .toast-danger  { background: #ff4757; color: #fff; }
    .toast-info    { background: var(--orange); color: #190702; }
    @keyframes toastIn { from { transform: translateX(60px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

{{-- PAGE HEADER --}}
<div class="page-header">
    <div>
        <h1 class="page-title">CLIENTES</h1>
        <p class="page-subtitle">Gestión completa de usuarios registrados en la plataforma</p>
    </div>
    <button wire:click="openCreateModal" class="btn-primary">+ Nuevo cliente</button>
</div>

{{-- STATS --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Total registrados</div>
        <div class="stat-value">{{ number_format($metrics['total']) }}</div>
        <div class="stat-sub">
            +{{ $metrics['todayNew'] }} hoy · +{{ $metrics['weekNew'] }} esta semana
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Clientes activos</div>
        <div class="stat-value c-good">{{ number_format($metrics['active']) }}</div>
        <div class="stat-sub">{{ round($metrics['active'] / max($metrics['total'],1) * 100) }}% del total</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-label">Nuevos este mes</div>
        <div class="stat-value c-orange">{{ number_format($metrics['monthNew']) }}</div>
        <div class="stat-sub">
            @if($metrics['growth'] > 0)
                <span class="c-good">▲ {{ $metrics['growth'] }}%</span> vs mes anterior
            @elseif($metrics['growth'] < 0)
                <span class="c-red">▼ {{ abs($metrics['growth']) }}%</span> vs mes anterior
            @else
                Sin variación vs mes anterior
            @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🚫</div>
        <div class="stat-label">Bloqueados</div>
        <div class="stat-value c-red">{{ number_format($metrics['blocked']) }}</div>
        <div class="stat-sub">+{{ $metrics['todayNew'] }} nuevos hoy · +{{ $metrics['weekNew'] }} esta semana</div>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="table-card">
    <div class="tc-header">
        <span class="tc-title">CLIENTES REGISTRADOS</span>
        <div class="tc-filters">
            <div class="search-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre, email o teléfono..."
                    class="search-input">
            </div>
            <select wire:model.live="filterStatus" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="blocked">Bloqueado</option>
            </select>
            <span class="tc-count">{{ $users->total() }} usuario{{ $users->total() !== 1 ? 's' : '' }}</span>
        </div>
    </div>

    @if($users->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No se encontraron usuarios con esos filtros</p>
        </div>
    @else
        <div class="t-head">
            <div>#</div>
            <div>Usuario</div>
            <div>Email</div>
            <div>Teléfono</div>
            <div>Estado</div>
            <div>Registrado</div>
            <div>Acciones</div>
        </div>

        @foreach($users as $i => $user)
        <div class="t-row">
            <div class="t-num">{{ $users->firstItem() + $i }}</div>
            <div class="t-user">
                <div class="t-avatar">{{ strtoupper(substr($user->name,0,2)) }}</div>
                <div>
                    <div class="t-uname">{{ $user->name }}</div>
                    @if($user->contact)
                        <div class="t-contact">{{ $user->contact }}</div>
                    @endif
                </div>
            </div>
            <div class="t-email">{{ $user->email }}</div>
            <div class="t-phone">{{ $user->phone ?? '—' }}</div>
            <div>
                <span class="s-badge {{ $user->status === 'active' ? 's-active' : 's-blocked' }}">
                    {{ $user->status === 'active' ? '● Activo' : '● Bloqueado' }}
                </span>
            </div>
            <div>
                <div class="t-date">{{ $user->created_at->format('d/m/Y') }}</div>
                <div class="t-date-sub">{{ $user->created_at->diffForHumans() }}</div>
            </div>
            <div class="t-actions">
                <button wire:click="openDetailModal({{ $user->id }})" class="btn-icon" title="Ver detalle">👁</button>
                <button wire:click="openEditModal({{ $user->id }})" class="btn-icon" title="Editar">✏️</button>
                @if($user->status === 'active')
                    <button wire:click="setStatus({{ $user->id }},'blocked')" class="btn-icon danger" title="Bloquear">🚫</button>
                @else
                    <button wire:click="setStatus({{ $user->id }},'active')" class="btn-icon activate" title="Activar">✅</button>
                @endif
                <button wire:click="deleteUser({{ $user->id }})"
                    wire:confirm="¿Eliminar al usuario {{ $user->name }}? Esta acción no se puede deshacer."
                    class="btn-icon danger" title="Eliminar">🗑</button>
            </div>
        </div>
        @endforeach

        <div class="tc-footer">
            <span>Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }} usuarios</span>
            {{ $users->links() }}
        </div>
    @endif
</div>

{{-- CREATE / EDIT MODAL --}}
@if($showModal)
<div class="modal-overlay" wire:click.self="closeModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>{{ $editingUserId ? 'EDITAR USUARIO' : 'NUEVO USUARIO' }}</h3>
            <button class="modal-close" wire:click="closeModal">✕</button>
        </div>
        <div class="modal-body">
            <form wire:submit.prevent="saveUser">

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" wire:model="name"
                            class="form-input @error('name') is-error @enderror"
                            placeholder="Nombre completo">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado *</label>
                        <select wire:model="userStatus" class="form-input @error('userStatus') is-error @enderror" style="padding-right:32px;">
                            <option value="active">Activo</option>
                            <option value="blocked">Bloqueado</option>
                        </select>
                        @error('userStatus') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" wire:model="email"
                        class="form-input @error('email') is-error @enderror"
                        placeholder="correo@ejemplo.com">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        {{ $editingUserId ? 'Nueva contraseña (dejar vacío para mantener)' : 'Contraseña *' }}
                    </label>
                    <input type="password" wire:model="password"
                        class="form-input @error('password') is-error @enderror"
                        placeholder="{{ $editingUserId ? '••••••• (opcional)' : 'Mínimo 6 caracteres' }}">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" wire:model="phone"
                            class="form-input"
                            placeholder="+51 999 000 000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contacto adicional</label>
                        <input type="text" wire:model="contact"
                            class="form-input"
                            placeholder="Telegram, WhatsApp…">
                        <div class="form-hint">Usuario de Telegram, alias WhatsApp, etc.</div>
                    </div>
                </div>

                <div class="modal-foot">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editingUserId ? 'Guardar cambios' : 'Crear usuario' }}</span>
                        <span wire:loading>Guardando…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- DETAIL MODAL --}}
@if($showDetailModal && $detailUser)
<div class="modal-overlay" wire:click.self="closeModal">
    <div class="modal-box modal-lg">
        <div class="modal-head">
            <h3>DETALLE DE USUARIO</h3>
            <button class="modal-close" wire:click="closeModal">✕</button>
        </div>
        <div class="modal-body">
            <div class="detail-head">
                <div class="detail-avatar">{{ strtoupper(substr($detailUser->name,0,2)) }}</div>
                <div>
                    <div class="detail-name">{{ $detailUser->name }}</div>
                    <span class="s-badge {{ $detailUser->status === 'active' ? 's-active' : 's-blocked' }}">
                        {{ $detailUser->status === 'active' ? '● Activo' : '● Bloqueado' }}
                    </span>
                </div>
            </div>

            {{-- Status quick-change --}}
            <div style="margin-bottom:18px;">
                <div class="form-label" style="margin-bottom:8px;">Cambiar estado</div>
                <div class="detail-status-row">
                    <button wire:click="setStatus({{ $detailUser->id }},'active')"
                        class="s-btn s-btn-active {{ $detailUser->status==='active' ? 'is-current' : '' }}">
                        ✅ Activar
                    </button>
                    <button wire:click="setStatus({{ $detailUser->id }},'blocked')"
                        class="s-btn s-btn-blocked {{ $detailUser->status==='blocked' ? 'is-current' : '' }}">
                        🚫 Bloquear
                    </button>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <label>Email</label>
                    <p>{{ $detailUser->email }}</p>
                </div>
                <div class="detail-item">
                    <label>Verificado</label>
                    <p>{{ $detailUser->email_verified_at ? '✓ ' . $detailUser->email_verified_at->format('d/m/Y') : '✕ Sin verificar' }}</p>
                </div>
                <div class="detail-item">
                    <label>Teléfono</label>
                    <p class="{{ $detailUser->phone ? '' : 'muted' }}">{{ $detailUser->phone ?? 'No registrado' }}</p>
                </div>
                <div class="detail-item">
                    <label>Contacto adicional</label>
                    <p class="{{ $detailUser->contact ? '' : 'muted' }}">{{ $detailUser->contact ?? 'No registrado' }}</p>
                </div>
                <div class="detail-item">
                    <label>Fecha de registro</label>
                    <p>{{ $detailUser->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="detail-item">
                    <label>Última actualización</label>
                    <p>{{ $detailUser->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="modal-foot">
                <button wire:click="closeModal" class="btn-ghost">Cerrar</button>
                <button wire:click="openEditModal({{ $detailUser->id }})" class="btn-primary">✏️ Editar usuario</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- TOAST --}}
<div class="toast-wrap">
    <template x-for="t in toasts" :key="t.id">
        <div class="toast-item" :class="'toast-' + t.type" x-text="t.message"
             x-transition:enter="transition ease-out duration-200"
             x-transition:leave="transition ease-in duration-150">
        </div>
    </template>
</div>

</div>

<script>
function toastManager() {
    return {
        toasts: [],
        show({ message, type = 'success' }) {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 3500);
        }
    }
}
</script>
