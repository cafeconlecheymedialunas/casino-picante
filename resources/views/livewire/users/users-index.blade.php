<div class="page-container" x-data="toastManager()" @toast.window="show($event.detail)">
@section('header')
<x-livewire.components.page-header title="CLIENTES" subtitle="Gestion de clientes registrados y acceso" />
@endsection

<style>
    .stats-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }

    .table-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    .table-header-left { display: flex; align-items: baseline; gap: 12px; }
    .table-header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .tc-title { font-family: var(--font-display); font-size: 22px; letter-spacing: .03em; }
    .tc-count { font-size: 11px; color: var(--muted-2); white-space: nowrap; }
    .search-input { min-width: 200px; }

    .stat-card {
        background: linear-gradient(180deg, #170b0b, #0f0707);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 18px 20px;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before { content: ''; position: absolute; inset: 0 0 auto; height: 2px; background: linear-gradient(90deg, var(--orange), var(--amber)); }
    .stat-label { font-size: 10px; font-weight: 800; letter-spacing: .12em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 6px; }
    .stat-value { font-family: var(--font-display); font-size: 34px; line-height: 1; }
    .stat-sub { font-size: 11px; color: var(--muted-2); margin-top: 6px; }
    .c-good { color: var(--good); }
    .c-red { color: #ff4757; }
    .c-orange { color: var(--orange); }

    .table-card {
        background: linear-gradient(180deg, #170b0b, #0f0707);
        border: 1px solid var(--line);
        border-radius: 8px;
        overflow: hidden;
    }
    .tc-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 18px 20px; border-bottom: 1px solid var(--line); }
    .tc-title { font-family: var(--font-display); font-size: 22px; letter-spacing: .03em; }
    .tc-filters { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .search-input, .filter-select, .form-input {
        background: rgba(255,255,255,.04);
        border: 1px solid var(--line-2);
        border-radius: 7px;
        padding: 9px 12px;
        color: var(--white);
        font-size: 13px;
        font-family: var(--font-body);
    }
    .search-input { min-width: 250px; }
    .filter-select { min-width: 150px; }
    .search-input:focus, .filter-select:focus, .form-input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,.12); }
    .tc-count { font-size: 11px; color: var(--muted-2); white-space: nowrap; }

    .t-head, .t-row {
        display: grid;
        grid-template-columns: minmax(140px,1.6fr) 1fr 1.4fr auto;
        gap: 12px;
        align-items: center;
        padding: 11px 20px;
        min-width: 640px;
    }
    .col-client { display:flex; align-items:center; gap:10px; min-width:0; }
    .col-client .table-avatar { width:34px; height:34px; border-radius:8px; border:1px solid var(--line); background:rgba(255,255,255,.05); object-fit:cover; flex-shrink:0; }
    .col-client .truncate { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .table-scroll { overflow-x: auto; }
    .t-head { font-size: 10px; font-weight: 800; letter-spacing: .1em; color: var(--muted-2); text-transform: uppercase; border-bottom: 1px solid var(--line); }
    .t-row { border-bottom: 1px solid var(--line); font-size: 13px; transition: background .15s; }
    .t-row:last-child { border-bottom: 0; }
    .t-row:hover { background: rgba(255,106,26,.04); }

    .col-username, .col-email, .col-msg { display:block; }
    .col-username-label, .col-email-label, .col-msg-label { display:none; }
    .toggle-btn {
        position: relative;
        width: 44px;
        height: 24px;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        transition: background .2s;
        padding: 0;
        display: block;
        flex-shrink: 0;
    }
    .toggle-on { background: linear-gradient(135deg, #25c46b, #1fa854); box-shadow: 0 0 8px rgba(37,196,107,.35); }
    .toggle-off { background: rgba(255,255,255,.1); border: 1px solid var(--line-2); }
    .toggle-knob {
        position: absolute;
        top: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.3);
        transition: left .2s;
    }
    .toggle-on .toggle-knob { left: 23px; }
    .toggle-off .toggle-knob { left: 3px; }
    .toggle-btn:hover { opacity: .85; }
    .mono { font-family: var(--font-mono); font-size: 11px; color: var(--muted-2); }
    .strong { font-weight: 700; }
    .muted { color: var(--muted-2); }
    .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .table-avatar { width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--line); background: rgba(255,255,255,.05); object-fit: cover; display: block; }
    .s-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; white-space: nowrap; }
    .s-active { background: rgba(37,196,107,.12); color: var(--good); }
    .s-inactive { background: rgba(255,71,87,.12); color: #ff4757; }
    .action-row { display: flex; gap: 6px; align-items: center; }
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        border: 1px solid var(--line);
        background: rgba(255,255,255,.03);
        color: var(--muted);
        cursor: pointer;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all .15s;
    }
    .btn-icon:hover { background: rgba(255,106,26,.15); border-color: var(--orange); color: var(--white); }
    .btn-icon.danger:hover { background: rgba(255,71,87,.15); border-color: #ff4757; }
    .btn-icon.activate:hover { background: rgba(37,196,107,.15); border-color: var(--good); }
    .mini-icon { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
    .btn-text {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 32px;
        padding: 0 10px;
        border-radius: 7px;
        border: 1px solid var(--line);
        background: rgba(255,255,255,.03);
        color: var(--white);
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .btn-text:hover { border-color: var(--orange); background: rgba(255,106,26,.15); }
    .btn-text.disabled { opacity: .45; pointer-events: none; }
    .tc-footer { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid var(--line); flex-wrap: wrap; gap: 10px; }
    .pg-wrap { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; font-size:12px; color:var(--muted-2); width:100%; }
    .pg-info { white-space:nowrap; }
    .pg-highlight { font-weight:800; color:var(--white); }
    .pg-nav { display:flex; align-items:center; gap:4px; }
    .pg-btn {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:32px; height:32px; padding:0 8px;
        border:1px solid var(--line); border-radius:7px;
        background:rgba(255,255,255,.03); color:var(--muted);
        font-size:12px; font-weight:700; font-family:var(--font-body);
        text-decoration:none;
        cursor:pointer; transition:all .15s;
    }
    button.pg-btn { line-height:1; }
    .pg-btn:hover { background:rgba(255,106,26,.12); border-color:var(--orange); color:var(--white); }
    .pg-btn.active { background:rgba(255,106,26,.18); border-color:var(--orange); color:var(--orange); cursor:default; }
    .pg-btn.disabled { opacity:.3; cursor:default; pointer-events:none; }
    .pg-dots { padding:0 4px; color:var(--muted-2); font-size:13px; }
    .pg-icon { width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .empty-state { padding: 56px 24px; text-align: center; color: var(--muted-2); }
    .s-toggle-btn { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:999px; font-size:10px; font-weight:800; border:none; cursor:pointer; transition:all .15s; white-space:nowrap; }
    .s-toggle-btn.is-active { background:rgba(37,196,107,.15); color:var(--good); }
    .s-toggle-btn.is-active:hover { background:rgba(255,71,87,.15); color:#ff4757; }
    .s-toggle-btn.is-inactive { background:rgba(255,71,87,.12); color:#ff4757; }
    .s-toggle-btn.is-inactive:hover { background:rgba(37,196,107,.15); color:var(--good); }
    .newsletter-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 7px; border-radius:999px; background:rgba(255,106,26,.12); color:var(--orange); font-size:9px; font-weight:900; letter-spacing:.04em; text-transform:uppercase; margin-top:3px; }

    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.75); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 20px; }
    .modal-box { background: linear-gradient(180deg, #1c0e0e, #120909); border: 1px solid var(--line-2); border-radius: 10px; width: min(720px, 100%); max-height: min(86vh, 820px); overflow-y: auto; box-shadow: 0 12px 48px rgba(0,0,0,.5); }
    .modal-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid var(--line); position:sticky; top:0; background:#1c0e0e; z-index:1; }
    .modal-head h3 { font-family: var(--font-display); font-size: 20px; letter-spacing: .04em; margin: 0; }
    .modal-close { background: none; border: 1px solid var(--line); color: var(--muted); font-size: 14px; cursor: pointer; width:30px; height:30px; border-radius:7px; display:flex; align-items:center; justify-content:center; transition:all .15s; flex-shrink:0; }
    .modal-close:hover { color: var(--orange); border-color:var(--orange); background: rgba(255,106,26,.08); }
    .modal-body { padding: 16px 20px 20px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .form-group { margin-bottom: 10px; }
    .form-label { display: block; font-size: 10px; font-weight: 800; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 4px; }
    .form-input { width: 100%; background: rgba(255,255,255,.04); border: 1px solid var(--line-2); border-radius: 7px; padding: 8px 11px; color: var(--white); font-size: 13px; font-family: var(--font-body); }
    .form-input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,.12); }
    .form-input:-webkit-autofill,
    .form-input:-webkit-autofill:hover,
    .form-input:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--white);
        -webkit-box-shadow: 0 0 0 1000px #261313 inset;
        caret-color: var(--white);
        transition: background-color 9999s ease-out;
    }
    .form-input.is-error { border-color: #ff4757; }
    .form-error { font-size: 11px; color: #ff4757; margin-top: 4px; }
    .form-hint { font-size: 11px; color: var(--muted-2); margin-top: 4px; }
    .modal-foot { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; padding-top: 14px; border-top: 1px solid var(--line); }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .detail-item label { display: block; font-size: 9px; font-weight: 800; letter-spacing: .1em; color: var(--muted-2); text-transform: uppercase; margin-bottom: 3px; }
    .detail-item p { margin: 0; font-size: 13px; word-break: break-word; }
    .toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 500; display: flex; flex-direction: column; gap: 8px; }
    .toast-item { padding: 13px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,.4); }
    .toast-success { background: var(--good); color: #002b14; }
    .toast-danger { background: #ff4757; color: #fff; }

    .line-selected { border-color: var(--orange) !important; background: rgba(255,106,26,.12) !important; color: var(--orange); }

    .toggle-btn {
        position: relative;
        width: 44px;
        height: 24px;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        transition: background .2s;
        padding: 0;
        display: block;
    }
    .toggle-on { background: linear-gradient(135deg, #25c46b, #1fa854); box-shadow: 0 0 8px rgba(37,196,107,.35); }
    .toggle-off { background: rgba(255,255,255,.1); border: 1px solid var(--line-2); }
    .toggle-knob {
        position: absolute;
        top: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.3);
        transition: left .2s;
    }
    .toggle-on .toggle-knob { left: 23px; }
    .toggle-off .toggle-knob { left: 3px; }
    .toggle-btn:hover { opacity: .85; }

    @media (max-width: 860px) {
        .stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .form-row-2, .detail-grid { grid-template-columns: 1fr; }
        .search-input { min-width: 100%; }
        .tc-filters { width: 100%; }
        .stat-value { font-size: 26px; }
    }
    {{-- Agent messaging inline icon (hide text) --}}
    .action-row .agent-message { display:inline-flex; }
    .action-row .agent-message .btn-text {
        all:unset; display:inline-flex; align-items:center; justify-content:center;
        width:32px; height:32px; border-radius:7px; border:1px solid var(--line);
        background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer;
        transition:all .15s; box-sizing:border-box;
    }
    .action-row .agent-message .btn-text:hover { background:rgba(255,106,26,.15); border-color:var(--orange); color:var(--white); }
    .action-row .agent-message .btn-text svg { width:15px; height:15px; stroke-width:1.9; }
    .action-row .agent-message .btn-text .btn-text-label { display:none; }

    @media (max-width: 768px) {
        .stats-row { display:none; }
        .tc-footer .pg-wrap { flex-direction:column; align-items:stretch; }
        .tc-footer .pg-info { text-align:center; }
        .tc-footer .pg-nav { justify-content:center; }
        .t-head, .t-row {
            grid-template-columns: 1fr auto;
            gap: 6px;
            padding: 8px 10px;
            min-width: 0;
            overflow: hidden;
        }
        .table-header-left{ display:none; }
        .t-head > *, .t-row > * { min-width:0; }
        .col-username,
        .col-email { display:none !important; }
        .col-client .table-avatar { width:26px; height:26px; border-radius:6px; }
        .col-client { gap:6px; }
        .col-client span { font-size:12px; }
        .action-row { gap:3px; flex-wrap:nowrap; }
        .action-row .btn-icon,
        .action-row .agent-message .btn-text { width:26px; height:26px; min-height:26px; font-size:9px; border-radius:5px; }
        .action-row .agent-message .btn-text svg { width:12px; height:12px; stroke-width:2; }
        .tc-header { padding: 12px 14px; }
        .tc-footer { flex-direction: column; text-align:center; }
        .module-top-bar { padding: 8px 12px; }
        .new-client-btn { width:100%; justify-content:center; }
        .modal-box { width: min(100%, 100vw); max-height: 92vh; }
        .modal-overlay { padding: 12px; }
        .modal-head { padding:12px 14px; }
        .modal-body { padding:12px 14px 16px; }
    }
</style>

<div class="module-top-bar">
    <button type="button" class="new-client-btn" wire:click="openCreateModal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Nuevo cliente
    </button>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total registrados</div>
        <div class="stat-value">{{ number_format($metrics['total']) }}</div>
        <div class="stat-sub">+{{ $metrics['todayNew'] }} hoy · +{{ $metrics['weekNew'] }} esta semana</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Activos</div>
        <div class="stat-value c-good">{{ number_format($metrics['active']) }}</div>
        <div class="stat-sub">{{ round($metrics['active'] / max($metrics['total'], 1) * 100) }}% del total</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Inactivos pausados</div>
        <div class="stat-value c-red">{{ number_format($metrics['inactive']) }}</div>
        <div class="stat-sub">Acceso restringido automaticamente</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Nuevos este mes</div>
        <div class="stat-value c-orange">{{ number_format($metrics['monthNew']) }}</div>
        <div class="stat-sub">
            @if($metrics['growth'] > 0)
                +{{ $metrics['growth'] }}% vs mes anterior
            @elseif($metrics['growth'] < 0)
                {{ $metrics['growth'] }}% vs mes anterior
            @else
                Sin variacion vs mes anterior
            @endif
        </div>
    </div>
</div>

<div class="table-header-row">
    <div class="table-header-left">
        <span class="tc-title">CLIENTES REGISTRADOS</span>
        <span class="tc-count">{{ $users->total() }} cliente{{ $users->total() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="table-header-right">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="search-input">
        <select wire:model.live="filterStatus" class="filter-select">
            <option value="">Todos</option>
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
        </select>
    </div>
</div>

<div class="table-card">
    @if($users->isEmpty())
        <div class="empty-state">No se encontraron clientes con esos filtros.</div>
    @else
        <div class="table-scroll">
            <div class="t-head">
                <div>Cliente</div>
                <div class="col-username">Username</div>
                <div class="col-email">Email</div>
                <div>Acciones</div>
            </div>

            @foreach($users as $user)
                @php($isActive = $user->status === 'active')
                @php($fullName = trim($user->name.' '.($user->apellido ?? '')))
                @php($avatarUrl = \App\Support\AvatarLibrary::url($user->avatar ?? null))
                <div class="t-row">
                    <div class="col-client">
                        <img class="table-avatar" src="{{ $avatarUrl }}" alt="">
                        <span class="truncate">
                            <span class="strong truncate" style="display:block">{{ $fullName ?: '-' }}</span>
                            @if($user->wants_bonus_emails)
                                <span class="newsletter-badge">Bonos/email</span>
                            @endif
                        </span>
                    </div>
                    <div class="strong truncate col-username">{{ $user->username ?? '-' }}</div>
                    <div class="truncate col-email">{{ $user->email }}</div>
                    <div class="action-row">
                        <livewire:components.agent-messaging
                            :target-user-id="$user->id"
                            :target-name="$fullName ?: $user->name"
                            :target-email="$user->email"
                            :target-phone="$user->phone ?? ''"
                            :context-label="$user->preferredLine?->name ?? ''"
                            :key="'client-agent-message-'.$user->id"
                        />
                        @if($isActive)
                            <button wire:click="setStatus({{ $user->id }}, 'inactive')"
                                wire:confirm="¿Desactivar acceso de {{ $fullName }}?"
                                class="btn-icon" title="Activo — desactivar"
                                style="border-color:rgba(37,196,107,.3);color:var(--good);background:rgba(37,196,107,.08);">
                                <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H6v16h4V4ZM18 4h-4v16h4V4Z"/></svg>
                            </button>
                        @else
                            <button wire:click="setStatus({{ $user->id }}, 'active')"
                                wire:confirm="¿Activar acceso de {{ $fullName }}?"
                                class="btn-icon" title="Inactivo — activar"
                                style="border-color:rgba(255,71,87,.3);color:#ff4757;background:rgba(255,71,87,.08);">
                                <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7V5Z"/></svg>
                            </button>
                        @endif
                        <button wire:click="openDetailModal({{ $user->id }})" class="btn-icon" title="Ver">
                            <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><path d="M12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"/></svg>
                        </button>
                        <button wire:click="openEditModal({{ $user->id }})" class="btn-icon" title="Editar">
                            <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>
                        </button>
                        <button wire:click="deleteUser({{ $user->id }})" wire:confirm="¿Eliminar a {{ $fullName ?: $user->name }}?" class="btn-icon danger" title="Eliminar">
                            <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="tc-footer">
            {{ $users->links('vendor.pagination.casino') }}
        </div>
    @endif
</div>

@if($showModal)
<div class="modal-overlay" wire:click.self="closeModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>{{ $editingUserId ? 'EDITAR CLIENTE' : 'NUEVO CLIENTE' }}</h3>
            <button class="modal-close" wire:click="closeModal">x</button>
        </div>
        <div class="modal-body">
            <form wire:submit.prevent="saveUser">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" wire:model="username" class="form-input @error('username') is-error @enderror" placeholder="usuario_cliente">
                        <div class="form-hint">Si queda vacio se genera desde el nombre.</div>
                        @error('username') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Activo</label>
                        <div style="display:flex;align-items:center;gap:12px;padding:8px 0">
                            <button
                                type="button"
                                wire:click="$set('userStatus', userStatus === 'active' ? 'inactive' : 'active')"
                                class="toggle-btn {{ $userStatus === 'active' ? 'toggle-on' : 'toggle-off' }}"
                                x-data="{ userStatus: @entangle('userStatus') }"
                                wire:loading.attr="disabled"
                            >
                                <span class="toggle-knob"></span>
                            </button>
                            <span style="font-size:12px;color:var(--muted-2)">{{ $userStatus === 'active' ? 'Cliente activo' : 'Cliente inactivo' }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" wire:model="name" class="form-input @error('name') is-error @enderror" placeholder="Nombre">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <input type="text" wire:model="apellido" class="form-input @error('apellido') is-error @enderror" placeholder="Apellido">
                        @error('apellido') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <x-avatar-library model="avatar" :selected="$avatar">
                        @error('avatar') <div class="form-error">{{ $message }}</div> @enderror
                    </x-avatar-library>
                </div>

                <div class="form-group">
                    <label class="form-label">Linea preferida</label>
                    <select wire:model="preferredLineId" class="form-input @error('preferredLineId') is-error @enderror">
                        <option value="">Sin linea</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                        @endforeach
                    </select>
                    @error('preferredLineId') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lineas asignadas</label>
                    <div class="multi-select-wrap" style="border:1px solid var(--line-2);border-radius:7px;background:rgba(255,255,255,.04);padding:8px;min-height:44px">
                        @if(empty($selectedLines))
                            <span style="color:var(--muted-2);font-size:12px">Ninguna linea asignada</span>
                        @endif
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                            @foreach($lines as $line)
                                <label style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:6px;border:1px solid var(--line);background:rgba(255,255,255,.03);cursor:pointer;font-size:12px;transition:all .15s"
                                    class="{{ in_array($line->id, $selectedLines) ? 'line-selected' : '' }}">
                                    <input type="checkbox"
                                        value="{{ $line->id }}"
                                        wire:model="selectedLines"
                                        style="accent-color:var(--orange)">
                                    {{ $line->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-hint">Seleccioná una o más líneas para asignarle acceso al cliente.</div>
                    @error('selectedLines') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Telefono</label>
                        <input type="text" wire:model="phone" class="form-input" placeholder="+54 9 11 0000 0000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contacto adicional</label>
                        <input type="text" wire:model="contact" class="form-input" placeholder="Telegram, WhatsApp, alias">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display:flex;gap:8px;align-items:flex-start;color:var(--muted);font-size:12px;font-weight:700;">
                        <input type="checkbox" wire:model="wantsBonusEmails" style="accent-color:var(--orange);margin-top:2px;">
                        Recibe bonos y novedades del blog por email
                    </label>
                    @error('wantsBonusEmails') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">{{ $editingUserId ? 'Nueva contrasena (opcional)' : 'Contrasena *' }}</label>
                    <input type="password" wire:model="password" class="form-input @error('password') is-error @enderror" placeholder="{{ $editingUserId ? 'Dejar vacio para mantener' : 'Minimo 6 caracteres' }}">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="modal-foot">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editingUserId ? 'Guardar cambios' : 'Crear cliente' }}</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($showDetailModal && $detailUser)
<div class="modal-overlay" wire:click.self="closeModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>DETALLE DE CLIENTE</h3>
            <button class="modal-close" wire:click="closeModal">x</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="detail-item"><label>ID</label><p>#{{ $detailUser->id }}</p></div>
                <div class="detail-item"><label>Username</label><p>{{ $detailUser->username ?? '-' }}</p></div>
                <div class="detail-item"><label>Nombre y apellido</label><p>{{ trim($detailUser->name.' '.($detailUser->apellido ?? '')) ?: '-' }}</p></div>
                <div class="detail-item"><label>Email</label><p>{{ $detailUser->email }}</p></div>
                <div class="detail-item"><label>Linea preferida</label><p>{{ $detailUser->preferredLine?->name ?? '-' }}</p></div>
                <div class="detail-item"><label>Telefono</label><p>{{ $detailUser->phone ?? '-' }}</p></div>
                <div class="detail-item"><label>Contacto adicional</label><p>{{ $detailUser->contact ?? '-' }}</p></div>
                <div class="detail-item"><label>Bonos por email</label><p>{{ $detailUser->wants_bonus_emails ? 'Si' : 'No' }}</p></div>
                <div class="detail-item"><label>Estado</label><p>{{ $detailUser->status === 'active' ? 'Activo' : 'Inactivo' }}</p></div>
                <div class="detail-item"><label>Registro</label><p>{{ $detailUser->created_at->format('d/m/Y H:i') }}</p></div>
            </div>

            <div class="modal-foot">
                @if($detailUser->status === 'active')
                    <button wire:click="setStatus({{ $detailUser->id }}, 'inactive')" class="btn-ghost">Pausar acceso</button>
                @else
                    <button wire:click="setStatus({{ $detailUser->id }}, 'active')" class="btn-ghost">Activar acceso</button>
                @endif
                <button wire:click="openEditModal({{ $detailUser->id }})" class="btn-primary">Editar cliente</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="toast-wrap">
    <template x-for="t in toasts" :key="t.id">
        <div class="toast-item" :class="'toast-' + t.type" x-text="t.message"></div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        show({ message, type = 'success' }) {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter((toast) => toast.id !== id);
            }, 3500);
        }
    }
}
</script>
