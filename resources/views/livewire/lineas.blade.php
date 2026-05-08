@section('header')
    <x-livewire.components.page-header title="LINEAS" subtitle="Gestion operativa, encargado, canales, plataformas y ventas" />
@endsection

<div class="page-container lineas-page-root">
    <style>
        /* ── List / cards ───────────────────────────────────────────────────── */
        .dash-shell:has(.lineas-page-root) { height:auto; min-height:100vh; }
        .main:has(.lineas-page-root) { overflow:visible; }
        .main-content:has(.lineas-page-root) { overflow-y:visible; }
        .lines-page { display: flex; flex-direction: column; gap: 18px; }
        .search-input, .filter-select, .form-input { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; font-family:var(--font-body); }
        .search-input { width:280px; }
        .search-input:focus, .filter-select:focus, .form-input:focus { outline:none; border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        .line-section { display:flex; flex-direction:column; gap:12px; }
        .section-title { display:flex; align-items:center; justify-content:space-between; gap:12px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .line-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px; }
        .line-card { overflow:hidden; border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg,#170b0b,#0f0707); }
        .line-card.inactive { opacity:.72; }
        .line-cover { height:150px; background:linear-gradient(135deg,rgba(255,106,26,.16),rgba(255,255,255,.04)); position:relative; overflow:hidden; }
        .line-cover img { width:100%; height:100%; object-fit:cover; display:block; }
        .line-profile { position:absolute; left:16px; bottom:12px; width:64px; height:64px; border-radius:8px; border:2px solid rgba(255,255,255,.75); background:#210f0f; overflow:hidden; box-shadow:0 10px 22px rgba(0,0,0,.35); }
        .line-profile img { width:100%; height:100%; object-fit:cover; }
        .line-profile span { display:flex; width:100%; height:100%; align-items:center; justify-content:center; font-family:var(--font-display); font-size:28px; color:var(--orange); }
        .line-body { padding:14px 16px 16px; }
        .line-top { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:12px; }
        .line-name { font-family:var(--font-display); font-size:25px; line-height:1; letter-spacing:.03em; }
        .line-id { color:var(--muted-2); font-family:var(--font-mono); font-size:11px; margin-top:4px; }
        .status-badge, .chip { display:inline-flex; align-items:center; width:fit-content; border-radius:999px; padding:4px 10px; font-size:10px; font-weight:800; white-space:nowrap; }
        .status-active { color:var(--good); background:rgba(37,196,107,.12); }
        .status-inactive { color:#ff4757; background:rgba(255,71,87,.12); }
        .info-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px; }
        .info-box { border:1px solid var(--line); border-radius:8px; padding:10px; background:rgba(255,255,255,.03); min-width:0; }
        .info-label { color:var(--muted-2); font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; margin-bottom:5px; }
        .info-value { font-size:12px; font-weight:800; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .chip-row { display:flex; gap:6px; flex-wrap:wrap; min-height:24px; }
        .chip { color:var(--white); background:rgba(255,255,255,.06); border:1px solid var(--line); }
        .card-footer { display:flex; justify-content:space-between; align-items:center; gap:12px; padding-top:12px; border-top:1px solid var(--line); }
        .btn-icon, .btn-soft { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; }
        .btn-icon { width:32px; color:var(--muted); }
        .btn-soft { padding:0 10px; font-size:11px; font-weight:800; }
        .btn-icon:hover, .btn-soft:hover { border-color:var(--orange); background:rgba(255,106,26,.15); color:var(--white); }
        .btn-danger:hover { border-color:#ff4757; background:rgba(255,71,87,.15); }
        .mini-icon { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .empty-state { border:1px dashed var(--line-2); border-radius:8px; padding:38px 20px; text-align:center; color:var(--muted-2); background:rgba(255,255,255,.02); }
        .flash-message { border:1px solid rgba(37,196,107,.35); background:rgba(37,196,107,.12); color:var(--good); border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; margin-bottom:16px; }
        .modal-overlay { position:fixed; inset:0; z-index:240; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.78); }
        .modal-panel { width:min(680px,100%); max-height:92vh; overflow-y:auto; border:1px solid var(--line-2); border-radius:8px; background:linear-gradient(180deg,#1c0e0e,#120909); }
        .modal-head { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-head h3 { margin:0; font-family:var(--font-display); font-size:22px; letter-spacing:.03em; }
        .modal-close { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; display:flex;align-items:center;justify-content:center; }
        .modal-form, .modal-body { padding:22px; }

        /* ── Shared form / detail ────────────────────────────────────────────── */
        .form-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-label { display:block; margin-bottom:6px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .form-input { width:100%; }
        .form-error { margin-top:4px; color:#ff4757; font-size:11px; }
        .repeat-list { display:flex; flex-direction:column; gap:8px; }
        .repeat-row { display:grid; grid-template-columns:180px 1fr 36px; gap:8px; align-items:center; }
        .preview-grid { display:grid; grid-template-columns:1.6fr .8fr; gap:12px; }
        .modal-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .line-actions { display:flex; align-items:center; gap:6px; }

        /* ── Detail / edit inline view ──────────────────────────────────────── */
        .detail-view { display:flex; flex-direction:column; gap:0; }
        .detail-edit-view { min-height:calc(100vh - 150px); }
        .detail-edit-view .tab-content { flex:1; min-height:calc(100vh - 490px); }
        .detail-topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
        .detail-back { display:inline-flex; align-items:center; gap:7px; font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); background:none; border:none; cursor:pointer; padding:0; }
        .detail-back:hover { color:var(--white); }
        .detail-hero { border-radius:10px 10px 0 0; overflow:hidden; border:1px solid var(--line); border-bottom:0; }
        .detail-cover { height:200px; background:linear-gradient(135deg,rgba(255,106,26,.22),rgba(255,255,255,.04)); overflow:hidden; position:relative; }
        .detail-cover img { width:100%; height:100%; object-fit:cover; display:block; }
        .detail-avatar { position:absolute; left:24px; bottom:-32px; width:90px; height:90px; border-radius:10px; border:3px solid rgba(255,255,255,.8); background:#210f0f; overflow:hidden; box-shadow:0 12px 30px rgba(0,0,0,.5); z-index:2; }
        .detail-avatar img { width:100%; height:100%; object-fit:cover; }
        .detail-avatar span { display:flex; width:100%; height:100%; align-items:center; justify-content:center; font-family:var(--font-display); font-size:36px; color:var(--orange); }
        .detail-meta { background:linear-gradient(180deg,#1c0e0e,#120909); padding:44px 24px 20px; display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; border:1px solid var(--line); border-top:0; }
        .detail-title { font-family:var(--font-display); font-size:34px; line-height:1; letter-spacing:.03em; }
        .detail-sub { color:var(--muted-2); font-family:var(--font-mono); font-size:11px; margin-top:5px; display:flex; align-items:center; gap:8px; }

        /* ── Tabs ────────────────────────────────────────────────────────────── */
        .detail-tabs { display:flex; gap:2px; background:linear-gradient(180deg,#1c0e0e,#120909); border:1px solid var(--line); border-top:0; padding:0 20px; overflow-x:auto; }
        .tab-btn { padding:12px 16px; font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; background:none; border:none; border-bottom:2px solid transparent; color:var(--muted); cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:color .15s,border-color .15s; white-space:nowrap; flex-shrink:0; }
        .tab-btn:hover { color:var(--white); }
        .tab-btn-active { color:var(--orange); border-bottom-color:var(--orange); }
        .tab-content { background:linear-gradient(180deg,#1c0e0e,#120909); border:1px solid var(--line); border-top:0; border-radius:0 0 10px 10px; padding:26px; }

        /* ── Stats & sales ───────────────────────────────────────────────────── */
        .stat-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
        .stat-box { border:1px solid var(--line); border-radius:8px; padding:14px; background:rgba(255,255,255,.03); }
        .stat-value { font-family:var(--font-display); font-size:26px; line-height:1.1; }
        .sales-table { border:1px solid var(--line); border-radius:8px; overflow:hidden; }
        .sales-row { display:grid; grid-template-columns:90px 1fr 1fr 90px 88px; gap:10px; padding:10px 12px; border-bottom:1px solid var(--line); align-items:center; font-size:12px; }
        .sales-row:last-child { border-bottom:0; }

        /* ── Agents list ────────────────────────────────────────────────────── */
        .agent-list { display:flex; flex-direction:column; gap:10px; }
        .agent-item { border:1px solid var(--line); border-radius:8px; background:rgba(255,255,255,.03); overflow:hidden; }
        .agent-item-head { display:flex; align-items:center; gap:12px; padding:12px 14px; }
        .agent-avatar { width:38px; height:38px; border-radius:8px; background:rgba(255,106,26,.15); border:1px solid rgba(255,106,26,.3); display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:16px; color:var(--orange); flex-shrink:0; }
        .agent-name { font-size:13px; font-weight:700; }
        .agent-role { font-size:10px; color:var(--muted-2); margin-top:2px; }
        .role-badge { display:inline-flex; align-items:center; border-radius:999px; padding:3px 8px; font-size:10px; font-weight:800; }
        .role-encargado { background:rgba(255,106,26,.15); color:var(--orange); border:1px solid rgba(255,106,26,.3); }
        .role-agente { background:rgba(255,255,255,.06); color:var(--muted); border:1px solid var(--line); }

        /* ── Platform picker ────────────────────────────────────────────────── */
        .platform-pick-label { display:flex; align-items:center; gap:10px; padding:12px 14px; border:1px solid var(--line); border-radius:8px; background:rgba(255,255,255,.03); cursor:pointer; transition:border-color .15s,background .15s; }
        .platform-pick-label:has(input:checked) { border-color:var(--orange); background:rgba(255,106,26,.08); }

        /* ── Permission editor ──────────────────────────────────────────────── */
        .perm-editor { background:rgba(0,0,0,.35); border-top:1px solid var(--line); padding:14px; }
        .perm-resource { margin-bottom:12px; }
        .perm-resource-label { font-size:9px; color:var(--muted); font-weight:800; letter-spacing:.1em; text-transform:uppercase; margin-bottom:7px; }
        .perm-checks { display:flex; gap:8px; flex-wrap:wrap; }
        .perm-check { display:flex; align-items:center; gap:5px; font-size:11px; cursor:pointer; padding:5px 10px; border-radius:6px; border:1px solid var(--line); background:rgba(255,255,255,.03); transition:border-color .15s,background .15s; }
        .perm-check:has(input:checked) { border-color:var(--orange); background:rgba(255,106,26,.12); color:var(--orange); }
        .perm-check input { accent-color:var(--orange); }

        @media (max-width:1000px){
            .line-grid,.form-grid,.preview-grid,.stat-grid{ grid-template-columns:1fr; }
            .info-grid{ grid-template-columns:1fr 1fr; }
            .repeat-row,.sales-row{ grid-template-columns:1fr; }
            .search-input{ width:100%; }
        }
    </style>

    @if(session()->has('message'))
        <div class="flash-message">{{ session('message') }}</div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════════
         1. LIST VIEW
         ════════════════════════════════════════════════════════════════════════ --}}
@if(!$showModal && !$showDetailsModal)
        @if($this->hasLinePermission(\App\Support\Permissions::LINE_CREATE))
        <div class="module-top-bar">
            <button type="button" class="btn-primary" wire:click="openCreateModal">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Crear linea
            </button>
        </div>
        @endif

        <div class="lines-page">
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <input type="text" wire:model.live.debounce.300ms="search" class="search-input" placeholder="Buscar linea, encargado o plataforma">
                <select wire:model.live="statusFilter" class="filter-select">
                    <option value="all">Todos los estados</option>
                    <option value="active">Activas</option>
                    <option value="inactive">Inactivas</option>
                </select>
            </div>

            <div class="line-section">
                <div class="section-title"><span>Lineas activas</span><span>{{ $activeLines->count() }}</span></div>
                @if($activeLines->isEmpty())
                    <div class="empty-state">No hay lineas activas para mostrar.</div>
                @else
                    <div class="line-grid">
                        @foreach($activeLines as $line)
                            @include('livewire.partials.line-card', ['line' => $line])
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="line-section">
                <div class="section-title"><span>Lineas inactivas</span><span>{{ $inactiveLines->count() }}</span></div>
                @if($inactiveLines->isEmpty())
                    <div class="empty-state">No hay lineas inactivas.</div>
                @else
                    <div class="line-grid">
                        @foreach($inactiveLines as $line)
                            @include('livewire.partials.line-card', ['line' => $line])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         2. DETAIL VIEW (Ver más — read-only)
         ════════════════════════════════════════════════════════════════════════ --}}
    @elseif($showDetailsModal && $detailLine)
    @php
        $stats      = $this->statsFor($detailLine);
        $dPlatforms = $detailLine->relationLoaded('platforms') ? $detailLine->getRelation('platforms') : $detailLine->platforms()->get();
        $dContacts  = $detailLine->contact_links ?? [];
        $dAgents    = $detailLine->lineAgents->sortByDesc(fn($la) => $la->role === 'encargado');
    @endphp
    <div class="detail-view detail-edit-view">

        <div class="detail-topbar">
            <button type="button" class="detail-back" wire:click="closeDetailsModal">
                <svg class="mini-icon" viewBox="0 0 15 15"><path d="M9 3L4 7.5 9 12"/></svg>
                Volver a lineas
            </button>
            @if($this->canManageLine($detailLine))
            <button type="button" class="btn-primary" wire:click="editFromDetail">
                <svg class="mini-icon" viewBox="0 0 15 15"><path d="M10.5 2.5l2 2-8.5 8.5H2.5v-2l8.5-8.5z"/></svg>
                Editar
            </button>
            @endif
        </div>

        <div class="detail-hero">
            <div class="detail-cover">
                @if($detailLine->portada_url)<img src="{{ $detailLine->portada_url }}" alt="{{ $detailLine->name }}">@endif
                <div class="detail-avatar">
                    @if($detailLine->perfil_url)<img src="{{ $detailLine->perfil_url }}" alt="">
                    @else<span>{{ strtoupper(mb_substr($detailLine->name, 0, 2)) }}</span>@endif
                </div>
            </div>
        </div>
        <div class="detail-meta">
            <div>
                <div class="detail-title">{{ strtoupper($detailLine->name) }}</div>
                <div class="detail-sub">
                    <span style="font-family:var(--font-mono)">#{{ str_pad($detailLine->id,4,'0',STR_PAD_LEFT) }}</span>
                    <span class="status-badge {{ $detailLine->status==='active' ? 'status-active' : 'status-inactive' }}">
                        {{ $detailLine->status==='active' ? '● Activa' : '○ Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="tab-content" style="border-radius:0 0 10px 10px">

            {{-- Stats --}}
            <div class="stat-grid">
                @php
                    $monthNames = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
                @endphp
                <div class="stat-box">
                    <div class="info-label">Mejor mes</div>
                    <div class="stat-value" style="font-size:18px;line-height:1.4">
                        {{ $stats['bestMonth'] ? (($monthNames[(int)$stats['bestMonth']->mes] ?? $stats['bestMonth']->mes).' '.($stats['bestMonth']->anio)) : '—' }}
                    </div>
                    <div class="line-id" style="margin-top:4px">${{ number_format((float)($stats['bestMonth']->total ?? 0),2) }}</div>
                </div>
                <div class="stat-box">
                    <div class="info-label">Mejor plataforma del mes</div>
                    <div class="stat-value" style="font-size:18px;line-height:1.4">{{ $stats['bestPlatform']?->platform?->name ?? '—' }}</div>
                    <div class="line-id" style="margin-top:4px">${{ number_format((float)($stats['bestPlatform']->total ?? 0),2) }}</div>
                </div>
                <div class="stat-box">
                    <div class="info-label">Ventas mes actual</div>
                    <div class="stat-value">${{ number_format((float)$stats['monthTotal'],2) }}</div>
                </div>
                <div class="stat-box">
                    <div class="info-label">Encargado</div>
                    @php $enc = $detailLine->lineAgents->firstWhere('role','encargado'); @endphp
                    @if($enc)
                        <div style="font-size:13px;font-weight:700">{{ $enc->agent?->name ?? '—' }}</div>
                        <div style="color:var(--orange);font-size:11px;font-weight:800;margin-top:2px">{{ number_format($enc->porcentaje_ganancia,0) }}% ganancia</div>
                    @else
                        <div style="color:var(--muted-2);font-size:11px">Sin asignar</div>
                    @endif
                </div>
            </div>

            {{-- Últimos 3 meses + ganancias --}}
            <div class="form-grid" style="margin-bottom:18px">
                <div class="stat-box">
                    <div class="info-label" style="margin-bottom:10px">Últimos 3 meses</div>
                    @forelse($stats['lastMonths'] as $month)
                        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--line);font-size:12px">
                            <span>{{ ($monthNames[(int)$month->mes] ?? $month->mes).' '.$month->anio }}</span>
                            <strong>${{ number_format((float)$month->total,2) }}</strong>
                        </div>
                    @empty
                        <div style="color:var(--muted-2);font-size:11px">Sin ventas registradas</div>
                    @endforelse
                </div>
                <div class="stat-box">
                    <div class="info-label" style="margin-bottom:10px">Ganancia del encargado en el mes</div>
                    @forelse($stats['earnings'] as $earning)
                        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--line);font-size:12px">
                            <span>{{ $earning['name'] }} ({{ $earning['porcentaje'] }}%)</span>
                            <strong style="color:var(--good)">${{ number_format((float)$earning['ganancia'],2) }}</strong>
                        </div>
                    @empty
                        <div style="color:var(--muted-2);font-size:11px">Sin encargado asignado</div>
                    @endforelse
                </div>
            </div>

            {{-- Canales y plataformas --}}
            <div class="form-grid" style="margin-bottom:18px">
                <div class="info-box">
                    <div class="info-label">Canales de atención</div>
                    @if(!empty($dContacts))
                        <div class="chip-row" style="margin-top:6px">
                            @foreach($dContacts as $link)
                            @php $t = $link['type'] ?? ''; $icon = match($t){ 'whatsapp'=>'💬','telegram'=>'✈️','instagram'=>'📷','facebook'=>'📘','web'=>'🌐',default=>'🔗' }; @endphp
                            <a href="{{ $link['value'] ?? '#' }}" target="_blank" rel="noopener" class="chip" title="{{ $t }}" style="text-decoration:none">{{ $icon }} {{ $t }}</a>
                            @endforeach
                        </div>
                    @else
                        <div style="color:var(--muted-2);font-size:11px;margin-top:4px">Sin canales registrados</div>
                    @endif
                </div>
                <div class="info-box">
                    <div class="info-label">Plataformas</div>
                    @if($dPlatforms->isNotEmpty())
                        <div class="chip-row" style="margin-top:6px">
                            @foreach($dPlatforms as $plat)
                                <a href="{{ $plat->website_url ?? '#' }}" target="_blank" rel="noopener" class="chip" style="text-decoration:none">{{ $plat->name }}</a>
                            @endforeach
                        </div>
                    @else
                        <div style="color:var(--muted-2);font-size:11px;margin-top:4px">Sin plataformas</div>
                    @endif
                </div>
            </div>

            {{-- Agentes de la línea --}}
            <div>
                <div class="form-label" style="margin-bottom:12px">Agentes de la línea</div>
                @if($dAgents->isEmpty())
                    <div class="empty-state">No hay agentes asignados.</div>
                @else
                <div class="agent-list">
                    @foreach($dAgents as $la)
                    @php $perms = $la->getPermissionsListAttribute(); @endphp
                    <div class="agent-item">
                        <div class="agent-item-head">
                            <div class="agent-avatar">{{ strtoupper(mb_substr($la->agent?->name ?? 'A', 0, 2)) }}</div>
                            <div style="flex:1;min-width:0">
                                <div class="agent-name">{{ $la->agent?->name ?? '—' }} {{ $la->agent?->apellido ?? '' }}</div>
                                <div style="margin-top:4px">
                                    <span class="role-badge {{ $la->role === 'encargado' ? 'role-encargado' : 'role-agente' }}">
                                        {{ $la->role === 'encargado' ? 'Encargado' : 'Agente' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                @if(!empty($perms))
                                    <div class="chip-row">
                                        @foreach(array_slice($perms, 0, 4) as $p)<span class="chip" style="font-size:9px">{{ $p }}</span>@endforeach
                                        @if(count($perms) > 4)<span class="chip" style="opacity:.6;font-size:9px">+{{ count($perms)-4 }}</span>@endif
                                    </div>
                                @else
                                    <span style="color:var(--muted-2);font-size:11px">Sin permisos</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         3. EDIT / CREATE VIEW  (6 tabs)
         ════════════════════════════════════════════════════════════════════════ --}}
    @elseif($showModal)
    <div class="detail-view">

        <div class="detail-topbar">
            <button type="button" class="detail-back" wire:click="closeModal">
                <svg class="mini-icon" viewBox="0 0 15 15"><path d="M9 3L4 7.5 9 12"/></svg>
                Volver a lineas
            </button>
        </div>

        {{-- Hero (reflects live state) --}}
        <div class="detail-hero">
            <div class="detail-cover">
                @if($portada_url)<img src="{{ $portada_url }}" alt="{{ $name }}">@endif
                <div class="detail-avatar">
                    @if($perfil_url)<img src="{{ $perfil_url }}" alt="">
                    @else<span>{{ strtoupper(mb_substr($name ?: 'N', 0, 2)) }}</span>@endif
                </div>
            </div>
        </div>
        <div class="detail-meta">
            <div>
                <div class="detail-title">{{ $name ? strtoupper($name) : ($editingLineId ? 'LINEA' : 'NUEVA LINEA') }}</div>
                <div class="detail-sub">
                    @if($editingLineId)<span style="font-family:var(--font-mono)">#{{ str_pad($editingLineId,4,'0',STR_PAD_LEFT) }}</span>@endif
                    <span class="status-badge {{ $status==='active' ? 'status-active' : 'status-inactive' }}">
                        {{ $status==='active' ? '● Activa' : '○ Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tab bar --}}
        <div class="detail-tabs">
            <button type="button" wire:click="switchTab('info')" class="tab-btn {{ $editTab==='info' ? 'tab-btn-active' : '' }}">
                <svg class="mini-icon" viewBox="0 0 15 15"><rect x="2" y="2" width="11" height="11" rx="2"/><path d="M5 7.5h5M5 5h3M5 10h4"/></svg>
                Info
            </button>
            <button type="button" wire:click="switchTab('encargado')" class="tab-btn {{ $editTab==='encargado' ? 'tab-btn-active' : '' }}">
                <svg class="mini-icon" viewBox="0 0 15 15"><circle cx="7.5" cy="5" r="2.5"/><path d="M2 13c0-3 2.5-4.5 5.5-4.5s5.5 1.5 5.5 4.5"/></svg>
                Encargado
            </button>
            @if($editingLineId)
            <button type="button" wire:click="switchTab('agentes')" class="tab-btn {{ $editTab==='agentes' ? 'tab-btn-active' : '' }}">
                <svg class="mini-icon" viewBox="0 0 15 15"><circle cx="5" cy="5" r="2"/><path d="M1 13c0-2.5 1.8-3.5 4-3.5M10.5 2.5l2 2-4 4H7v-2l3.5-3.5z"/></svg>
                Agentes
            </button>
            <button type="button" wire:click="switchTab('ventas')" class="tab-btn {{ $editTab==='ventas' ? 'tab-btn-active' : '' }}">
                <svg class="mini-icon" viewBox="0 0 15 15"><rect x="1.5" y="2.5" width="12" height="10" rx="1.5"/><path d="M4.5 6.5h6M4.5 9h4"/></svg>
                Ventas
            </button>
            @endif
            <button type="button" wire:click="switchTab('canales')" class="tab-btn {{ $editTab==='canales' ? 'tab-btn-active' : '' }}">
                <svg class="mini-icon" viewBox="0 0 15 15"><path d="M13 4c0 4-3.5 7.5-6 9-2.5-1.5-6-5-6-9a6 6 0 0112 0z"/><circle cx="7" cy="4" r="1.5" fill="currentColor" stroke="none"/></svg>
                Canales
            </button>
            <button type="button" wire:click="switchTab('plataformas')" class="tab-btn {{ $editTab==='plataformas' ? 'tab-btn-active' : '' }}">
                <svg class="mini-icon" viewBox="0 0 15 15"><rect x="1" y="4" width="13" height="8" rx="1.5"/><path d="M4.5 4V3a2 2 0 014 0v1"/></svg>
                Plataformas
            </button>
        </div>

        {{-- ── TAB: INFO ──────────────────────────────────────────────────── --}}
        @if($editTab === 'info')
        <div class="tab-content">
            <form wire:submit.prevent="saveLine">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">ID</label>
                        <input class="form-input" value="{{ $editingLineId ? '#'.str_pad($editingLineId,4,'0',STR_PAD_LEFT) : 'Automático al guardar' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select wire:model.live="status" class="form-input">
                            <option value="active">Activa</option>
                            <option value="inactive">Inactiva</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de la linea <span style="color:var(--orange)">*</span></label>
                    <input type="text" wire:model.live="name" class="form-input" placeholder="Ej: Linea Principal">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea wire:model="description" class="form-input" rows="2" placeholder="Descripción breve de la línea..."></textarea>
                    @error('description') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                {{-- Images --}}
                @php
                    try { $portadaPreview = $portadaUpload ? $portadaUpload->temporaryUrl() : $portada_url; } catch(\Throwable $e) { $portadaPreview = $portada_url; }
                    try { $perfilPreview  = $perfilUpload  ? $perfilUpload->temporaryUrl()  : $perfil_url;  } catch(\Throwable $e) { $perfilPreview  = $perfil_url;  }
                @endphp
                <div class="preview-grid" style="margin-bottom:14px">
                    <div class="form-group">
                        <label class="form-label">Portada <span style="color:var(--muted-2);font-size:10px;font-weight:400;text-transform:none">851×315 px</span></label>
                        <label style="display:block;cursor:pointer;border:1px dashed var(--line-2);border-radius:8px;overflow:hidden;aspect-ratio:851/315;background:rgba(255,255,255,.03);transition:border-color .15s" onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor=''">
                            @if($portadaPreview)
                                <img src="{{ $portadaPreview }}" style="width:100%;height:100%;object-fit:cover;display:block">
                            @else
                                <div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--muted-2);font-size:12px;padding:16px">
                                    <svg style="width:24px;height:24px;opacity:.4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 16.5l4.5-4.5 3 3 4-5 4 6.5"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                                    Clic para seleccionar portada
                                </div>
                            @endif
                            <input type="file" wire:model="portadaUpload" accept="image/png,image/jpeg,image/webp,image/gif" style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none">
                        </label>
                        <div wire:loading wire:target="portadaUpload" style="color:var(--orange);font-size:11px;margin-top:4px;font-weight:700">Subiendo...</div>
                        @if($portada_url && !$portadaUpload)
                            <button type="button" class="btn-soft btn-danger" wire:click="deleteImage('portada')" style="margin-top:4px;font-size:10px">Borrar portada</button>
                        @endif
                        @error('portadaUpload') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Perfil <span style="color:var(--muted-2);font-size:10px;font-weight:400;text-transform:none">800×800 px</span></label>
                        <label style="display:block;cursor:pointer;border:1px dashed var(--line-2);border-radius:8px;overflow:hidden;aspect-ratio:1;background:rgba(255,255,255,.03);transition:border-color .15s" onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor=''">
                            @if($perfilPreview)
                                <img src="{{ $perfilPreview }}" style="width:100%;height:100%;object-fit:cover;display:block">
                            @else
                                <div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--muted-2);font-size:12px;padding:16px">
                                    <svg style="width:24px;height:24px;opacity:.4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 16.5l4.5-4.5 3 3 4-5 4 6.5"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                                    Clic para seleccionar perfil
                                </div>
                            @endif
                            <input type="file" wire:model="perfilUpload" accept="image/png,image/jpeg,image/webp,image/gif" style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none">
                        </label>
                        <div wire:loading wire:target="perfilUpload" style="color:var(--orange);font-size:11px;margin-top:4px;font-weight:700">Subiendo...</div>
                        @if($perfil_url && !$perfilUpload)
                            <button type="button" class="btn-soft btn-danger" wire:click="deleteImage('perfil')" style="margin-top:4px;font-size:10px">Borrar perfil</button>
                        @endif
                        @error('perfilUpload') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Permisos de la línea --}}
                <div class="form-group">
                    <label class="form-label">Permisos habilitados en esta línea</label>
                    <p style="color:var(--muted-2);font-size:11px;margin:0 0 10px;line-height:1.6">Determinan qué acciones pueden tener los agentes asignados.</p>
                    <div>
                        <div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:10px">
                            @if(!$showLinePermissionsEditor)
                                <button type="button" class="btn-soft" wire:click="openLinePermissionsEditor">Setear permisos</button>
                            @else
                                <button type="button" class="btn-soft" wire:click="closeLinePermissionsEditor">Cerrar</button>
                            @endif
                        </div>
                        @if($showLinePermissionsEditor)
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px">
                        @foreach($permissionCatalog as $resource => $actions)
                            @foreach($actions as $perm)
                            @php $action = str($perm)->after($resource.'.'); @endphp
                            <label class="perm-check" style="font-size:11px">
                                <input type="checkbox" wire:model="linePermissions" value="{{ $perm }}">
                                <span style="color:var(--muted-2);font-size:9px;margin-right:2px">{{ $resource }}</span>{{ $action }}
                            </label>
                            @endforeach
                        @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal-actions" style="justify-content:space-between;border-top:1px solid var(--line);padding-top:18px;margin-top:4px">
                    @if($editingLineId)
                        <button type="button" class="btn-soft btn-danger" wire:click="deleteLine({{ $editingLineId }})" wire:confirm="¿Eliminar esta linea?">Eliminar</button>
                    @else
                        <span></span>
                    @endif
                    <div style="display:flex;gap:10px">
                        <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary">{{ $editingLineId ? 'Guardar cambios' : 'Crear linea' }}</button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- ── TAB: ENCARGADO ─────────────────────────────────────────────── --}}
        @if($editTab === 'encargado')
        <div class="tab-content">
            <form wire:submit.prevent="saveLine">

                <p style="color:var(--muted-2);font-size:12px;margin:0 0 18px;line-height:1.7">
                    Asigna el encargado responsable de esta línea y su porcentaje de ganancia.
                </p>

                <div class="form-group">
                    <label class="form-label">Encargado <span style="color:var(--orange)">*</span></label>
                    <select wire:model="encargadoId" class="form-input">
                        <option value="">Seleccionar encargado</option>
                        @foreach($availableEncargados as $agent)
                            <option value="{{ $agent->id }}">{{ trim($agent->name.' '.($agent->apellido ?? '')) }}</option>
                        @endforeach
                    </select>
                    @error('encargadoId') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Comisión</label>
                    <div style="display:flex;align-items:center;gap:10px">
                        <input type="number" step="0.5" min="0" max="100"
                            wire:model="encargadoPercent"
                            class="form-input" style="max-width:120px;text-align:center" placeholder="0">
                        <span style="color:var(--muted);font-size:16px;font-weight:800">%</span>
                    </div>
                    @error('encargadoPercent') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;margin-top:4px">
                    <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingLineId ? 'Guardar cambios' : 'Crear linea' }}</button>
                </div>
            </form>
        </div>
        @endif

        {{-- ── TAB: AGENTES (solo al editar) ──────────────────────────────── --}}
        @if($editTab === 'agentes' && $editingLineId)
        <div class="tab-content">

            {{-- Select para agregar agente --}}
            <div style="margin-bottom:20px">
                <label class="form-label">Agregar agente a la línea</label>
                @if($availableAgents->isEmpty())
                    <div style="color:var(--muted-2);font-size:12px;padding:10px 0">Todos los agentes activos ya están asignados.</div>
                @else
                <div style="display:flex;gap:8px;align-items:flex-end">
                    <div style="flex:1">
                        <select id="agent-select-add" class="form-input">
                            <option value="">Seleccionar agente...</option>
                            @foreach($availableAgents as $agent)
                                <option value="{{ $agent->id }}">{{ trim($agent->name.' '.($agent->apellido ?? '')) }}{{ $agent->username ? ' — @'.$agent->username : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn-primary"
                        x-on:click="const s=document.getElementById('agent-select-add');if(s.value){$wire.addAgent(parseInt(s.value));s.value='';}">
                        + Agregar
                    </button>
                </div>
                @endif
            </div>

            {{-- Lista de agentes asignados --}}
            @if($editLineAgents->isEmpty())
                <div class="empty-state">No hay agentes asignados a esta línea aún.</div>
            @else
            <div class="agent-list">
                @foreach($editLineAgents as $la)
                @php $laPerms = $la->getPermissionsListAttribute(); @endphp
                <div class="agent-item" wire:key="la-{{ $la->id }}">
                    <div class="agent-item-head">
                        <div class="agent-avatar">{{ strtoupper(mb_substr($la->agent?->name ?? 'A', 0, 2)) }}</div>
                        <div style="flex:1;min-width:0">
                            <div class="agent-name">{{ $la->agent?->name ?? '—' }} {{ $la->agent?->apellido ?? '' }}</div>
                            <div style="margin-top:4px;display:flex;align-items:center;gap:6px">
                                <span class="role-badge {{ $la->role === 'encargado' ? 'role-encargado' : 'role-agente' }}">
                                    {{ $la->role === 'encargado' ? 'Encargado' : 'Agente' }}
                                </span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px">
                            @if(!empty($laPerms))
                                <div class="chip-row">
                                    @foreach(array_slice($laPerms, 0, 3) as $p)<span class="chip" style="font-size:9px">{{ $p }}</span>@endforeach
                                    @if(count($laPerms) > 3)<span class="chip" style="opacity:.6;font-size:9px">+{{ count($laPerms)-3 }}</span>@endif
                                </div>
                            @else
                                <span style="color:var(--muted-2);font-size:11px">Sin permisos</span>
                            @endif
                            <button type="button" class="btn-soft" wire:click="openAgentPermissions({{ $la->id }})">
                                <svg class="mini-icon" viewBox="0 0 15 15"><path d="M10.5 2.5l2 2-8.5 8.5H2.5v-2l8.5-8.5z"/></svg>
                                Permisos
                            </button>
                            @if($la->role !== 'encargado')
                            <button type="button" class="btn-icon btn-danger"
                                wire:click="removeLineAgent({{ $la->id }})"
                                wire:confirm="¿Quitar a {{ $la->agent?->name ?? 'este agente' }} de la línea?"
                                title="Quitar de la línea">
                                <svg class="mini-icon" viewBox="0 0 15 15"><path d="M3 3l9 9M12 3l-9 9"/></svg>
                            </button>
                            @endif
                        </div>
                    </div>

                    {{-- Permission editor (expands inline) --}}
                    @if($editingAgentPermissionsId === $la->id)
                    <div class="perm-editor">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                            <span style="font-size:11px;font-weight:800;color:var(--muted);letter-spacing:.08em;text-transform:uppercase">
                                Permisos de {{ $la->agent?->name ?? 'agente' }}
                            </span>
                            <button type="button" class="btn-soft" wire:click="closeAgentPermissions" style="font-size:10px">Cancelar</button>
                        </div>

                        @if(empty($availablePermissions))
                            <div style="color:var(--muted-2);font-size:12px;margin-bottom:14px">
                                La línea no tiene permisos configurados. Configura permisos en la línea primero.
                            </div>
                        @else
                            @foreach($permissionCatalog as $resource => $actions)
                            @php
                                $resourceAvail = array_filter($availablePermissions, fn($p) => str_starts_with($p, $resource.'.'));
                            @endphp
                            @if(!empty($resourceAvail))
                            <div class="perm-resource">
                                <div class="perm-resource-label">{{ $resource }}</div>
                                <div class="perm-checks">
                                    @foreach($resourceAvail as $perm)
                                    <label class="perm-check">
                                        <input type="checkbox" wire:model="agentPermissions" value="{{ $perm }}">
                                        {{ explode('.', $perm)[1] ?? $perm }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endforeach
                        @endif

                        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px;padding-top:12px;border-top:1px solid var(--line)">
                            <button type="button" class="btn-soft" wire:click="closeAgentPermissions">Cancelar</button>
                            <button type="button" class="btn-primary" wire:click="saveAgentPermissions">Guardar permisos</button>
                        </div>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
            @endif

        </div>
        @endif

        {{-- ── TAB: VENTAS ─────────────────────────────────────────────────── --}}
        @if($editTab === 'ventas' && $editingLineId)
        @php
            $editPlatforms = $editSalesLine
                ? ($editSalesLine->relationLoaded('platforms') ? $editSalesLine->getRelation('platforms') : $editSalesLine->platforms()->get())
                : collect();
        @endphp
        <div class="tab-content">

            <form wire:submit.prevent="saveSale">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                    <label class="form-label" style="margin:0;font-size:13px">{{ $editingSaleId ? 'Editando venta' : 'Registrar nueva venta' }}</label>
                    @if($editingSaleId)
                        <button type="button" class="btn-soft" wire:click="resetSalesForm">Cancelar</button>
                    @endif
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Plataforma <span style="color:var(--orange)">*</span></label>
                        <select wire:model="salePlatformId" class="form-input">
                            <option value="0">Elegir plataforma</option>
                            @foreach($editPlatforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                        @if($editPlatforms->isEmpty())
                            <div style="margin-top:4px;color:var(--muted-2);font-size:11px">Seleccioná plataformas en la pestaña "Plataformas" primero.</div>
                        @endif
                        @error('salePlatformId') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha <span style="color:var(--orange)">*</span></label>
                        <input type="date" wire:model="saleDate" class="form-input">
                        @error('saleDate') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción <span style="color:var(--muted-2);font-size:10px;font-weight:400;text-transform:none">(opcional)</span></label>
                    <input type="text" wire:model="saleDescripcion" class="form-input" placeholder="Ej: Recarga fichas mayo, bono bienvenida...">
                    @error('saleDescripcion') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Cantidad <span style="color:var(--orange)">*</span></label>
                    <input type="number" step="0.01" min="0" wire:model="saleMontoFichas" class="form-input" placeholder="0.00">
                    @error('saleMontoFichas') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:flex;justify-content:flex-end;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid var(--line)">
                    <button type="submit" class="btn-primary">{{ $editingSaleId ? 'Actualizar venta' : 'Registrar venta' }}</button>
                </div>
            </form>

            <div>
                <div class="form-label" style="margin-bottom:10px">Historial de ventas</div>
                <div class="sales-table">
                    @if($editSalesLine)
                        @forelse($editSalesLine->sales()->with('platform')->orderByDesc('fecha')->get() as $sale)
                            <div class="sales-row">
                                <div style="color:var(--muted-2);font-family:var(--font-mono);font-size:11px">{{ $sale->fecha->format('d/m/Y') }}</div>
                                <div>{{ $sale->platform?->name ?? '—' }}</div>
                                <div style="color:var(--muted-2);font-size:11px">{{ $sale->descripcion ?? '—' }}</div>
                                <div style="font-weight:700">${{ number_format((float)$sale->monto_fichas,2) }}</div>
                                <div class="line-actions">
                                    <button class="btn-icon" wire:click="openEditSaleInModal({{ $sale->id }})" title="Editar">
                                        <svg class="mini-icon" viewBox="0 0 15 15"><path d="M10.5 2.5l2 2-8.5 8.5H2.5v-2l8.5-8.5z"/></svg>
                                    </button>
                                    <button class="btn-icon btn-danger" wire:click="deleteSale({{ $sale->id }})" wire:confirm="¿Eliminar esta venta?">
                                        <svg class="mini-icon" viewBox="0 0 15 15"><path d="M3 3l9 9M12 3l-9 9"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Todavía no hay ventas cargadas.</div>
                        @endforelse
                    @else
                        <div class="empty-state">Guarda la línea primero para registrar ventas.</div>
                    @endif
                </div>
            </div>

        </div>
        @endif

        {{-- ── TAB: CANALES DE ATENCIÓN ────────────────────────────────────── --}}
        @if($editTab === 'canales')
        <div class="tab-content">
            <form wire:submit.prevent="saveLine">

                <p style="color:var(--muted-2);font-size:12px;margin:0 0 18px;line-height:1.7">
                    Canales de contacto de esta línea (WhatsApp, Telegram, etc.).
                </p>

                <livewire:components.contact-repeater wire:model="channels" />

                <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;margin-top:18px">
                    <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingLineId ? 'Guardar cambios' : 'Crear linea' }}</button>
                </div>
            </form>
        </div>
        @endif

        {{-- ── TAB: PLATAFORMAS ────────────────────────────────────────────── --}}
        @if($editTab === 'plataformas')
        <div class="tab-content">
            <form wire:submit.prevent="saveLine">

                <p style="color:var(--muted-2);font-size:12px;margin:0 0 18px;line-height:1.7">
                    Seleccioná las plataformas disponibles para esta línea del catálogo global.
                </p>

                @if($allPlatforms->isEmpty())
                    <div class="empty-state">No hay plataformas creadas aún. Crealas desde el catálogo global de plataformas.</div>
                @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-bottom:6px">
                    @foreach($allPlatforms as $platform)
                    <label class="platform-pick-label" wire:key="plat-{{ $platform->id }}">
                        <input type="checkbox"
                            wire:model="selectedPlatformIds"
                            value="{{ $platform->id }}"
                            style="width:16px;height:16px;accent-color:var(--orange);flex-shrink:0">
                        <div style="min-width:0">
                            @if($platform->logo_url)
                                <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" style="height:20px;object-fit:contain;display:block;margin-bottom:3px">
                            @endif
                            <div style="font-size:13px;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $platform->name }}</div>
                            @if($platform->website_url)
                                <div style="font-size:10px;color:var(--muted-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $platform->website_url }}</div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <div style="margin-bottom:6px;color:var(--muted-2);font-size:11px">{{ count($selectedPlatformIds) }} plataforma(s) seleccionada(s)</div>
                @endif

                <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;margin-top:18px">
                    <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingLineId ? 'Guardar cambios' : 'Crear linea' }}</button>
                </div>
            </form>
        </div>
        @endif

    </div>{{-- /detail-view edit --}}
    @endif{{-- /state switch --}}


    {{-- ════════════════════════════════════════════════════════════════════════
         MODAL: Ventas rápidas desde la tarjeta
         ════════════════════════════════════════════════════════════════════════ --}}
    @if($showSalesModal && $salesLine)
        <div class="modal-overlay" wire:click.self="closeSalesModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>VENTAS — {{ strtoupper($salesLine->name) }}</h3>
                    <button class="modal-close" wire:click="closeSalesModal">
                        <svg class="mini-icon" viewBox="0 0 15 15"><path d="M3 3l9 9M12 3l-9 9"/></svg>
                    </button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveSale">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Plataforma</label>
                            <select wire:model="salePlatformId" class="form-input">
                                <option value="0">Elegir plataforma</option>
                                @foreach(($salesLine->relationLoaded('platforms') ? $salesLine->getRelation('platforms') : $salesLine->platforms()->get()) as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                            @error('salePlatformId') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha</label>
                            <input type="date" wire:model="saleDate" class="form-input">
                            @error('saleDate') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Descripción <span style="color:var(--muted-2);font-size:10px;font-weight:400;text-transform:none">(opcional)</span></label>
                        <input type="text" wire:model="saleDescripcion" class="form-input" placeholder="Ej: Recarga fichas, bono...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cantidad</label>
                        <input type="number" step="0.01" min="0" wire:model="saleMontoFichas" class="form-input" placeholder="0.00">
                        @error('saleMontoFichas') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                        <button type="button" wire:click="closeSalesModal" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar venta</button>
                    </div>
                </form>
                <div class="modal-body" style="padding-top:0">
                    <div class="sales-table">
                        @forelse($salesLine->sales()->with('platform')->orderByDesc('fecha')->get() as $sale)
                            <div class="sales-row">
                                <div style="color:var(--muted-2);font-family:var(--font-mono);font-size:11px">{{ $sale->fecha->format('d/m/Y') }}</div>
                                <div>{{ $sale->platform?->name ?? '—' }}</div>
                                <div style="color:var(--muted-2);font-size:11px">{{ $sale->descripcion ?? '—' }}</div>
                                <div style="font-weight:700">${{ number_format((float)$sale->monto_fichas,2) }}</div>
                                <div class="line-actions">
                                    <button class="btn-icon" wire:click="openSalesModal({{ $salesLine->id }}, {{ $sale->id }})">
                                        <svg class="mini-icon" viewBox="0 0 15 15"><path d="M10.5 2.5l2 2-8.5 8.5H2.5v-2l8.5-8.5z"/></svg>
                                    </button>
                                    <button class="btn-icon btn-danger" wire:click="deleteSale({{ $sale->id }})">
                                        <svg class="mini-icon" viewBox="0 0 15 15"><path d="M3 3l9 9M12 3l-9 9"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Todavía no hay ventas cargadas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
