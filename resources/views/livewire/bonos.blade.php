<div class="page-container">
<style>
    /* ── Base ── */
    .bonus-page{display:flex;flex-direction:column;gap:18px;}
    .search-input,.filter-select,.form-input{background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body);}
    .search-input{width:280px;}
    .search-input:focus,.filter-select:focus,.form-input:focus{outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12);}
    .form-input{width:100%;}
    textarea.form-input{resize:vertical;min-height:84px;}
    .form-error{margin-top:4px;color:#ff4757;font-size:11px;}
    .form-label{display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;}
    .form-group{margin-bottom:14px;}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
    .form-section-title{font-size:10px;font-weight:800;color:var(--orange);letter-spacing:.1em;text-transform:uppercase;margin:18px 0 10px;padding-bottom:6px;border-bottom:1px solid var(--line);}
    .check-row{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:12px;font-weight:700;margin-top:8px;}
    .type-btn{flex:1;height:36px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:rgba(255,255,255,.04);border:1px solid var(--line-2);color:var(--muted);display:flex;align-items:center;justify-content:center;gap:6px;}
    .type-btn.active{background:rgba(255,106,26,.15);color:var(--orange);border-color:rgba(255,106,26,.5);}
    .input-suffix-wrap{position:relative;}
    .input-suffix-wrap .form-input{padding-right:36px;}
    .input-suffix{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:700;color:var(--muted);pointer-events:none;}

    /* ── Stats ── */
    .stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
    .stat-card{border:1px solid var(--line);border-radius:8px;padding:16px 18px;background:linear-gradient(180deg,#170b0b,#0f0707);position:relative;overflow:hidden;}
    .stat-card::before{content:'';position:absolute;inset:0 0 auto;height:2px;background:linear-gradient(90deg,var(--orange),var(--amber));}
    .stat-label{color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px;}
    .stat-value{font-family:var(--font-display);font-size:32px;line-height:1;}
    .stat-sub{font-size:10px;color:var(--muted);margin-top:4px;}


    /* ── Table ── */
    .table-card{border:1px solid var(--line);border-radius:8px;background:linear-gradient(180deg,#170b0b,#0f0707);overflow:hidden;}
    .table-top{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;padding:16px 18px;border-bottom:1px solid var(--line);}
    .table-title{font-family:var(--font-display);font-size:22px;letter-spacing:.03em;}
    .table-count{color:var(--muted-2);font-size:11px;}
    .table-scroll{overflow-x:hidden;}
    .t-head,.t-row{display:grid;grid-template-columns:2fr 120px 150px auto;gap:10px;align-items:center;padding:11px 18px;}
    .t-head{color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;border-bottom:1px solid var(--line);}
    .t-row{border-bottom:1px solid var(--line);}
    .t-row:hover{background:rgba(255,106,26,.04);}
    .t-bono-code{font-size:10px;font-weight:800;color:var(--orange);letter-spacing:.08em;margin-bottom:2px;}
    .t-bono-name{font-size:13px;font-weight:600;}
    .badge{display:inline-flex;width:fit-content;align-items:center;gap:4px;border-radius:999px;padding:3px 8px;font-size:10px;font-weight:800;}
    .b-active{color:var(--good);background:rgba(37,196,107,.12);}
    .b-upcoming{color:var(--amber);background:rgba(255,179,71,.12);}
    .b-expired{color:#ff4757;background:rgba(255,71,87,.12);}

    /* ── Uso ── */
    .uso-line{font-size:12px;margin-bottom:3px;}
    .uso-disp{font-size:11px;color:var(--muted);}

    /* ── Action buttons ── */
    .action-row{display:flex;align-items:center;gap:6px;}
    .btn-action{height:30px;border-radius:6px;border:1px solid var(--line);background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:0 10px;font-size:11px;font-weight:700;white-space:nowrap;}
    .btn-action:hover{border-color:var(--orange);background:rgba(255,106,26,.1);color:var(--white);}
    .btn-action.active{border-color:var(--orange);background:rgba(255,106,26,.15);color:var(--orange);}
    .btn-action.danger:hover{border-color:#ff4757;background:rgba(255,71,87,.1);color:#ff4757;}
    .btn-action-icon{width:30px;padding:0;}
    .mini-icon{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

    /* ── Modal ── */
    .flash-message{border:1px solid rgba(37,196,107,.35);background:rgba(37,196,107,.12);color:var(--good);border-radius:8px;padding:12px 14px;font-size:13px;font-weight:700;}
    .modal-overlay{position:fixed;inset:0;z-index:240;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.78);}
    .modal-panel{width:min(720px,100%);max-height:92vh;overflow-y:auto;border:1px solid var(--line-2);border-radius:8px;background:linear-gradient(180deg,#1c0e0e,#120909);}
    .modal-head{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:18px 22px;border-bottom:1px solid var(--line);}
    .modal-head h3{margin:0;font-family:var(--font-display);font-size:24px;letter-spacing:.03em;}
    .modal-close{width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;}
    .modal-form{padding:22px;}
    .modal-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
    .btn-soft{height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--white);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:0 10px;font-size:11px;font-weight:800;}
    .btn-soft:hover{border-color:var(--orange);background:rgba(255,106,26,.15);}

    /* ── Multiselect ── */
    .ms-tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px;}
    .ms-tag{display:inline-flex;align-items:center;gap:4px;background:rgba(255,106,26,.15);color:var(--orange);border:1px solid rgba(255,106,26,.4);border-radius:6px;padding:3px 8px;font-size:12px;font-weight:700;}
    .ms-tag-remove{background:none;border:none;color:var(--orange);cursor:pointer;font-size:14px;line-height:1;padding:0 0 0 2px;}
    .ms-dropdown{position:absolute;top:calc(100% + 4px);left:0;right:0;background:#1c0e0e;border:1px solid var(--line-2);border-radius:10px;max-height:140px;overflow-y:auto;z-index:100;box-shadow:0 8px 32px rgba(0,0,0,.5);}
    .ms-option{padding:9px 14px;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .15s;}
    .ms-option:hover{background:rgba(255,255,255,.05);}
    .ms-option.selected{background:rgba(255,106,26,.1);color:var(--orange);}
    .ms-check{color:var(--orange);font-size:11px;width:14px;}
    .ms-empty{padding:14px;text-align:center;color:var(--muted);font-size:13px;}

    /* ── Layout con panel lateral ── */
    .bonos-layout{display:grid;grid-template-columns:1fr;gap:16px;align-items:start;}
    .bonos-layout.with-panel{grid-template-columns:1fr 360px;}

    /* ── Panel de asignaciones ── */
    .ap-panel{background:linear-gradient(180deg,#170b0b,#0f0707);border:1px solid var(--line);border-radius:12px;overflow:hidden;position:sticky;top:20px;}
    .ap-head{padding:16px;border-bottom:1px solid var(--line);}
    .ap-bonus-code{font-size:10px;font-weight:800;color:var(--orange);letter-spacing:.1em;margin-bottom:2px;}
    .ap-bonus-title{font-family:var(--font-display);font-size:16px;letter-spacing:.02em;margin-bottom:10px;}
    .ap-stats{display:flex;gap:0;border:1px solid var(--line);border-radius:8px;overflow:hidden;}
    .ap-stat{flex:1;text-align:center;padding:8px 6px;border-right:1px solid var(--line);}
    .ap-stat:last-child{border-right:none;}
    .ap-stat-num{font-family:var(--font-display);font-size:20px;line-height:1;}
    .ap-stat-label{font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--muted-2);margin-top:2px;}
    .ap-legend{display:flex;gap:10px;flex-wrap:wrap;padding:10px 16px;border-bottom:1px solid var(--line);background:rgba(255,255,255,.02);}
    .ap-legend-item{display:flex;align-items:center;gap:4px;font-size:10px;color:var(--muted);}
    .ap-list{max-height:400px;overflow-y:auto;}
    .ap-row{display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,.04);}
    .ap-avatar{width:32px;height:32px;border-radius:50%;background:rgba(255,106,26,.2);color:var(--orange);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;}
    .ap-info{flex:1;min-width:0;}
    .ap-username{font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .ap-date{font-size:10px;color:var(--muted);margin-top:1px;}
    .ap-right{flex-shrink:0;}
    .ap-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:6px;font-size:11px;font-weight:700;}
    .ap-badge.pending{background:rgba(255,179,71,.12);color:var(--amber);border:1px solid rgba(255,179,71,.3);}
    .ap-badge.claimed{background:rgba(37,196,107,.12);color:var(--good);border:1px solid rgba(37,196,107,.25);}
    .ap-badge.expired{background:rgba(255,255,255,.05);color:var(--muted);border:1px solid var(--line);}
    .ap-claim-btn{height:28px;border-radius:7px;background:rgba(37,196,107,.1);border:1px solid rgba(37,196,107,.3);color:var(--good);cursor:pointer;font-size:11px;font-weight:700;padding:0 10px;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;}
    .ap-claim-btn:hover{background:var(--good);color:#000;}
    .ap-empty{text-align:center;color:var(--muted);padding:40px 20px;font-size:13px;}
    .ap-search{padding:10px 16px;border-bottom:1px solid var(--line);}

    /* ── Empty / flash ── */
    .empty-state{padding:56px 24px;color:var(--muted-2);text-align:center;}
    .bono-flash{border:1px solid rgba(37,196,107,.35);background:rgba(37,196,107,.12);color:var(--good);border-radius:8px;padding:12px 14px;font-size:13px;font-weight:700;margin-bottom:8px;}

    /* ── Otorgar modal info box ── */
    .assign-info-box{background:rgba(255,106,26,.07);border:1px solid rgba(255,106,26,.25);border-radius:8px;padding:12px 14px;margin-bottom:16px;}
    .assign-info-title{font-size:12px;font-weight:800;color:var(--orange);margin-bottom:6px;display:flex;align-items:center;gap:6px;}
    .assign-info-row{display:flex;justify-content:space-between;font-size:12px;padding:2px 0;}
    .assign-info-label{color:var(--muted);}
    .assign-info-val{font-weight:700;}

    /* ── Light theme ── */
    [data-dashboard-theme="light"] .bonus-page .stat-card,
    [data-dashboard-theme="light"] .bonus-page .table-card,
    [data-dashboard-theme="light"] .bonus-page .modal-panel,
    [data-dashboard-theme="light"] .bonus-page .ap-panel,
    [data-dashboard-theme="light"] .bonus-page .flow-banner{background:#fffdf8!important;background-image:none!important;border-color:var(--line)!important;}
    [data-dashboard-theme="light"] .bonus-page .t-row:hover,[data-dashboard-theme="light"] .bonus-page .ap-row:hover{background:rgba(255,106,26,.05)!important;}
    [data-dashboard-theme="light"] .bonus-page .search-input,[data-dashboard-theme="light"] .bonus-page .filter-select,[data-dashboard-theme="light"] .bonus-page .form-input{background:#fff!important;color:var(--white)!important;border-color:var(--line-2)!important;}
    [data-dashboard-theme="light"] .bonus-page .btn-action,[data-dashboard-theme="light"] .bonus-page .btn-soft,[data-dashboard-theme="light"] .bonus-page .type-btn{background:rgba(244,234,220,.78)!important;color:var(--muted)!important;border-color:var(--line)!important;}
    [data-dashboard-theme="light"] .bonus-page .ms-dropdown{background:#fffdf8!important;}

    /* ── Responsive ── */
    @media(max-width:1100px){.bonos-layout.with-panel{grid-template-columns:1fr;}.ap-panel{position:static;}}
    @media(max-width:900px){.stats-grid,.form-grid{grid-template-columns:1fr;}.search-input{width:100%;}.flow-banner{flex-direction:column;}.flow-step{border-right:none;border-bottom:1px solid var(--line);}.flow-step:last-child{border-bottom:none;}}
    @media(max-width:768px){.stats-grid{display:none;}.t-head,.t-row{grid-template-columns:1fr auto;min-width:0;}.t-head{display:none;}.t-row .t-vigencia{display:none!important;}.modal-panel{border-radius:0;width:100%;max-height:100vh;min-height:100vh;}.modal-overlay{padding:0;align-items:flex-end;}}
</style>

@section('header')
    <x-livewire.components.page-header title="BONOS" subtitle="Creación, asignación y seguimiento de bonos por línea" />
@endsection

@if($this->canMutateVendorContext())
<div class="module-top-bar">
    <button type="button" class="btn-primary" wire:click="openCreateModal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Crear bono
    </button>
</div>
@endif

<div class="bonus-page">


    {{-- ── Filtros ── --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <input type="text" wire:model.live.debounce.300ms="search" class="search-input" placeholder="Buscar código, nombre o línea...">
        <select wire:model.live="filter" class="filter-select" style="min-width:160px;">
            <option value="all">Todos los estados</option>
            <option value="active">● Activos</option>
            <option value="upcoming">● Próximos</option>
            <option value="expired">● Vencidos</option>
        </select>
    </div>

    @if(session()->has('message'))
        <div class="bono-flash"><i class="fa-solid fa-circle-check"></i> {{ session('message') }}</div>
    @endif

    {{-- ── Métricas ── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total bonos</div>
            <div class="stat-value">{{ $metrics['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Activos</div>
            <div class="stat-value" style="color:var(--good)">{{ $metrics['active'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Próximos</div>
            <div class="stat-value" style="color:var(--amber)">{{ $metrics['upcoming'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reclamados</div>
            <div class="stat-value" style="color:var(--orange)">{{ $metrics['claimed'] }}</div>
        </div>
    </div>

    {{-- ── Layout tabla + panel ── --}}
    <div class="bonos-layout {{ $showAssignmentsPanel ? 'with-panel' : '' }}">

        {{-- Tabla de bonos --}}
        <div class="table-card">
            <div class="table-top">
                <div>
                    <div class="table-title">LISTA DE BONOS</div>
                    <div class="table-count">{{ $bonuses->count() }} bono{{ $bonuses->count() !== 1 ? 's' : '' }}</div>
                </div>
            </div>

            @if($bonuses->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-ticket" style="font-size:32px;opacity:.3;margin-bottom:12px;display:block"></i>
                    No hay bonos para los filtros seleccionados.
                </div>
            @else
                <div class="table-scroll">
                    <div class="t-head">
                        <div>Bono</div>
                        <div>Vigencia</div>
                        <div>Uso</div>
                        <div>Acciones</div>
                    </div>

                    @foreach($bonuses as $bonus)
                    <div class="t-row">
                        {{-- Bono --}}
                        <div>
                            <div class="t-bono-code">{{ $bonus->code ?? '-' }}</div>
                            <div class="t-bono-name" style="margin-bottom:4px;">{{ $bonus->title }}</div>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;">
                                <span class="badge b-{{ $bonus->status }}">
                                    @if($bonus->status === 'active') ● Activo
                                    @elseif($bonus->status === 'upcoming') ● Próximo
                                    @else ● Vencido @endif
                                </span>
                                @if($bonus->line)
                                <span style="font-size:10px;color:var(--muted)">{{ $bonus->line->name }}</span>
                                @endif
                                @if($bonus->platform)
                                <span style="font-size:10px;color:var(--muted)">· {{ $bonus->platform->name }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Vigencia --}}
                        <div class="t-vigencia" style="font-size:12px;">
                            <div>{{ $bonus->start_date?->format('d/m/y') }}</div>
                            <div style="color:var(--muted);font-size:10px;margin:1px 0">hasta</div>
                            <div>{{ $bonus->end_date?->format('d/m/y') }}</div>
                        </div>

                        {{-- Uso --}}
                        <div>
                            <div class="uso-line">
                                <span style="color:var(--amber);font-weight:700;">{{ $bonus->assigned_count }}</span>
                                <span style="color:var(--muted);"> otorg. · </span>
                                <span style="color:var(--good);font-weight:700;">{{ $bonus->claimed_count }}</span>
                                <span style="color:var(--muted);"> reclam.</span>
                            </div>
                            <div class="uso-disp">
                                {{ is_null($bonus->remaining_quantity) ? 'Ilimitados' : $bonus->remaining_quantity.' disp.' }}
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="action-row">
                            {{-- Ver asignados --}}
                            <button class="btn-action {{ $bonusForAssignments === $bonus->id ? 'active' : '' }}"
                                wire:click="{{ $bonusForAssignments === $bonus->id ? 'closeAssignmentsPanel' : 'openAssignmentsPanel('.$bonus->id.')' }}"
                                title="Ver asignaciones">
                                <i class="fa-solid fa-users" style="font-size:10px"></i>
                                {{ $bonus->assigned_count }}
                            </button>

                            @if($this->canMutateVendorContext())
                                @if($bonus->status === 'active' && $this->hasLinePermission(\App\Support\Permissions::BONO_UPDATE))
                                <button class="btn-action" wire:click="openAssignModal({{ $bonus->id }})" title="Otorgar bono a clientes">
                                    <i class="fa-solid fa-user-plus" style="font-size:10px"></i>
                                    Otorgar
                                </button>
                                @endif

                                @if($this->hasLinePermission(\App\Support\Permissions::BONO_UPDATE))
                                <button class="btn-action btn-action-icon" wire:click="openEditModal({{ $bonus->id }})" title="Editar">
                                    <svg class="mini-icon" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>
                                </button>
                                @endif

                                @if($this->hasLinePermission(\App\Support\Permissions::BONO_DELETE))
                                <button class="btn-action btn-action-icon danger"
                                    wire:click="deleteBonus({{ $bonus->id }})"
                                    wire:confirm="¿Eliminar el bono {{ $bonus->code }}?"
                                    title="Eliminar">
                                    <svg class="mini-icon" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/></svg>
                                </button>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Panel de asignaciones ── --}}
        @if($showAssignmentsPanel && $panelBonus)
        <div class="ap-panel">
            <div class="ap-head">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px;">
                    <div>
                        <div class="ap-bonus-code">{{ $panelBonus->code }}</div>
                        <div class="ap-bonus-title">{{ $panelBonus->title }}</div>
                    </div>
                    <button class="modal-close" wire:click="closeAssignmentsPanel" title="Cerrar"><i class="fa-solid fa-xmark"></i></button>
                </div>

                {{-- Mini stats del bono --}}
                <div class="ap-stats">
                    @php
                        $pendingCount  = $panelAssignments->where('status','active')->count();
                        $claimedCount  = $panelAssignments->where('status','used')->count();
                        $expiredCount  = $panelAssignments->where('status','expired')->count();
                    @endphp
                    <div class="ap-stat">
                        <div class="ap-stat-num" style="color:var(--amber)">{{ $pendingCount }}</div>
                        <div class="ap-stat-label">Pendientes</div>
                    </div>
                    <div class="ap-stat">
                        <div class="ap-stat-num" style="color:var(--good)">{{ $claimedCount }}</div>
                        <div class="ap-stat-label">Reclamados</div>
                    </div>
                    <div class="ap-stat">
                        <div class="ap-stat-num" style="color:var(--muted)">{{ $expiredCount }}</div>
                        <div class="ap-stat-label">Vencidos</div>
                    </div>
                </div>
            </div>

            {{-- Leyenda de estados --}}
            <div class="ap-legend">
                <div class="ap-legend-item"><span style="color:var(--amber)">●</span> Pendiente — bono otorgado, aún no usado</div>
                <div class="ap-legend-item"><span style="color:var(--good)">●</span> Reclamado — el cliente ya lo usó</div>
            </div>

            {{-- Buscador --}}
            <div class="ap-search">
                <input type="text" wire:model.live.debounce.200ms="assignmentsSearch"
                    placeholder="Buscar cliente..." class="form-input" style="font-size:12px;">
            </div>

            {{-- Lista --}}
            <div class="ap-list">
                @forelse($panelAssignments as $a)
                <div class="ap-row">
                    <div class="ap-avatar">{{ strtoupper(substr($a->user?->username ?? $a->user?->email ?? '?', 0, 1)) }}</div>
                    <div class="ap-info">
                        <div class="ap-username">{{ $a->user?->username ?? $a->user?->email ?? '—' }}</div>
                        <div class="ap-date">Otorgado {{ $a->assigned_at?->format('d/m/Y H:i') ?? $a->created_at->format('d/m/Y H:i') }}</div>
                        @if($a->used_at)
                        <div class="ap-date" style="color:var(--good)">Reclamado {{ $a->used_at->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                    <div class="ap-right">
                        @if($a->status === 'used')
                            <span class="ap-badge claimed"><i class="fa-solid fa-check"></i> Reclamado</span>
                        @elseif($a->status === 'expired')
                            <span class="ap-badge expired"><i class="fa-solid fa-clock"></i> Vencido</span>
                        @else
                            @if($this->canMutateVendorContext())
                                <button wire:click="markClaimed({{ $a->id }})"
                                    wire:confirm="¿Confirmar que {{ $a->user?->username ?? 'este cliente' }} ya usó el bono?"
                                    class="ap-claim-btn"
                                    title="Marcar como reclamado">
                                    <i class="fa-solid fa-circle-check"></i> Reclamar
                                </button>
                            @else
                                <span class="ap-badge pending"><i class="fa-solid fa-clock"></i> Pendiente</span>
                            @endif
                        @endif
                    </div>
                </div>
                @empty
                <div class="ap-empty">
                    <i class="fa-solid fa-users-slash" style="font-size:24px;display:block;margin-bottom:8px;opacity:.3"></i>
                    Aún no se otorgó este bono a nadie.<br>
                    <span style="font-size:11px">Usá el botón "Otorgar" en la tabla.</span>
                </div>
                @endforelse
            </div>
        </div>
        @endif

    </div>{{-- /bonos-layout --}}
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- Modal: Crear / Editar bono                                --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($showModal)
<div class="modal-overlay" wire:click.self="closeModal">
    <div class="modal-panel">
        <div class="modal-head">
            <h3>{{ $editingBonusId ? 'EDITAR BONO' : 'CREAR BONO' }}</h3>
            <button class="modal-close" wire:click="closeModal"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="modal-form" wire:submit.prevent="saveBonus">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Código</label>
                    <input type="text" wire:model="code" class="form-input" placeholder="Auto-generado si está vacío">
                    @error('code') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre del bono</label>
                    <input type="text" wire:model="title" class="form-input" placeholder="Ej: Bono Bienvenida 100%">
                    @error('title') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section-title">Período de vigencia</div>
            <div style="background:rgba(255,179,71,.06);border:1px solid rgba(255,179,71,.2);border-radius:7px;padding:10px 12px;margin-bottom:14px;font-size:11px;color:var(--muted);">
                <i class="fa-solid fa-circle-info" style="color:var(--amber);margin-right:5px"></i>
                El estado (Activo / Próximo / Vencido) se calcula automáticamente según estas fechas.
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" wire:model="startDate" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" wire:model="startTime" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" wire:model="endDate" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Hora fin</label>
                    <input type="time" wire:model="endTime" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea wire:model="description" class="form-input" placeholder="Condiciones del bono..."></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Plataforma</label>
                    <select wire:model="platformId" class="form-input">
                        <option value="">Todas las plataformas</option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                        @endforeach
                    </select>
                    @error('platformId') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo</label>
                    <div style="display:flex;gap:6px;">
                        <button type="button" wire:click="$set('bonusType','general')" class="type-btn {{ $bonusType === 'general' ? 'active' : '' }}">
                            <i class="fa-solid fa-globe"></i> General
                        </button>
                        <button type="button" wire:click="$set('bonusType','specific')" class="type-btn {{ $bonusType === 'specific' ? 'active' : '' }}">
                            <i class="fa-solid fa-user"></i> Específico
                        </button>
                    </div>
                </div>
            </div>
            @if($bonusType === 'specific')
            <div class="form-group">
                <label class="form-label">Cliente específico</label>
                <input type="text" wire:model="specificUsername" class="form-input" placeholder="Email o username del cliente">
                @error('specificUsername') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            @endif

            <div class="form-section-title">Valor del bono</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">% de bonificación</label>
                    <div class="input-suffix-wrap">
                        <input type="number" wire:model="bonusPercent" class="form-input" placeholder="Ej: 100" min="0" max="100" step="0.01">
                        <span class="input-suffix">%</span>
                    </div>
                    @error('bonusPercent') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Depósito mínimo</label>
                    <input type="number" wire:model="minDeposit" class="form-input" placeholder="0.00 (sin mínimo)" min="0" step="0.01">
                    @error('minDeposit') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Bonus máximo</label>
                    <input type="number" wire:model="maxBonus" class="form-input" placeholder="0.00 (sin límite)" min="0" step="0.01">
                    @error('maxBonus') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section-title">Disponibilidad</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Cantidad total de bonos</label>
                    <input type="number" wire:model="totalQuantity" class="form-input" placeholder="Cantidad" @disabled($unlimitedQuantity)>
                    <label class="check-row"><input type="checkbox" wire:model.live="unlimitedQuantity"> Sin límite de cantidad</label>
                </div>
                <div class="form-group">
                    <label class="form-label">Veces por cliente</label>
                    <input type="number" wire:model="perUserLimit" class="form-input" placeholder="Ej: 1" @disabled($unlimitedPerUser)>
                    <label class="check-row"><input type="checkbox" wire:model.live="unlimitedPerUser"> Sin límite por cliente</label>
                </div>
            </div>

            <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                <button type="submit" class="btn-primary">{{ $editingBonusId ? 'Guardar cambios' : 'Crear bono' }}</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- Modal: Otorgar bono a clientes                            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($showAssignModal && $selectedBonus)
<div class="modal-overlay" wire:click.self="closeAssignModal">
    <div class="modal-panel"
         x-data="{
            search: '',
            open: false,
            users: @js($this->getUsersForAssign()),
            selected: @entangle('assignUserIds').live,
            get filtered() { return this.search.length < 1 ? this.users : this.users.filter(u => u.label.toLowerCase().includes(this.search.toLowerCase())) },
            toggle(id) { const i = this.selected.indexOf(id); i === -1 ? this.selected.push(id) : this.selected.splice(i, 1); this.open = false; this.search = ''; },
            isSelected(id) { return this.selected.includes(id) },
            labelFor(id) { const u = this.users.find(u => u.id === id); return u ? u.label : id }
         }">
        <div class="modal-head">
            <h3>OTORGAR BONO</h3>
            <button class="modal-close" wire:click="closeAssignModal"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-form">
            {{-- Info del bono --}}
            <div class="assign-info-box">
                <div class="assign-info-title">
                    <i class="fa-solid fa-ticket"></i>
                    {{ $selectedBonus->code }} — {{ $selectedBonus->title }}
                </div>
                <div class="assign-info-row">
                    <span class="assign-info-label">Línea</span>
                    <span class="assign-info-val">{{ $selectedBonus->line->name ?? '—' }}</span>
                </div>
                <div class="assign-info-row">
                    <span class="assign-info-label">Vigencia</span>
                    <span class="assign-info-val">{{ $selectedBonus->start_date?->format('d/m/Y') }} → {{ $selectedBonus->end_date?->format('d/m/Y') }}</span>
                </div>
                <div class="assign-info-row">
                    <span class="assign-info-label">Bonificación</span>
                    <span class="assign-info-val">{{ $selectedBonus->bonus_percent ?? 0 }}%</span>
                </div>
                <div class="assign-info-row">
                    <span class="assign-info-label">Disponibles</span>
                    <span class="assign-info-val">{{ is_null($selectedBonus->remaining_quantity) ? 'Ilimitados' : $selectedBonus->remaining_quantity }}</span>
                </div>
            </div>

            <form wire:submit.prevent="assignToUser">
                <div class="form-group">
                    <label class="form-label">
                        Seleccionar clientes
                        <span x-show="selected.length" x-text="'— ' + selected.length + ' seleccionado' + (selected.length !== 1 ? 's' : '')" style="color:var(--orange);font-weight:800;"></span>
                    </label>

                    <div style="font-size:11px;color:var(--muted);margin-bottom:8px;">
                        Solo aparecen clientes de esta línea que aún pueden recibir el bono.
                    </div>

                    {{-- Tags de seleccionados --}}
                    <div class="ms-tags" x-show="selected.length > 0">
                        <template x-for="id in selected" :key="id">
                            <span class="ms-tag">
                                <span x-text="labelFor(id)"></span>
                                <button type="button" @click="toggle(id)" class="ms-tag-remove">×</button>
                            </span>
                        </template>
                    </div>

                    {{-- Buscador + dropdown --}}
                    <div style="position:relative;" @click.outside="open = false">
                        <input type="text" x-model="search"
                            @focus="open = true" @click="open = true"
                            placeholder="Buscar cliente por nombre o username..."
                            class="form-input" autocomplete="off">
                        <div class="ms-dropdown" x-show="open" x-transition>
                            <template x-if="users.length === 0">
                                <div class="ms-empty">
                                    <i class="fa-solid fa-users-slash" style="display:block;font-size:20px;margin-bottom:6px;opacity:.4"></i>
                                    No hay clientes disponibles para este bono.<br>
                                    <span style="font-size:11px">Todos ya lo tienen asignado o no pertenecen a esta línea.</span>
                                </div>
                            </template>
                            <template x-if="users.length > 0 && filtered.length === 0">
                                <div class="ms-empty">Sin resultados para "<span x-text="search"></span>"</div>
                            </template>
                            <template x-for="user in filtered" :key="user.id">
                                <div class="ms-option" :class="{ selected: isSelected(user.id) }" @click="toggle(user.id)">
                                    <span class="ms-check" x-show="isSelected(user.id)"><i class="fa-solid fa-check"></i></span>
                                    <span x-text="user.label"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('assignUserIds') <div class="form-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                    <button type="button" wire:click="closeAssignModal" class="btn-soft">Cancelar</button>
                    <button type="submit" class="btn-primary" :disabled="selected.length === 0">
                        <i class="fa-solid fa-user-plus"></i>
                        Otorgar a <span x-text="selected.length"></span> cliente<span x-show="selected.length !== 1">s</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
</div>
