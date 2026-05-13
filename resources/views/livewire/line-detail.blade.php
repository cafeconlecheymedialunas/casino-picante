@php
    $monthNames = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
    $bestMonth        = $this->getBestMonth();
    $bestPlatform     = $this->getBestPlatformThisMonth();
    $totalThisMonth   = $this->getTotalSalesThisMonth();
    $last3Months      = $this->getTotalSalesLast3Months();
    $encargadoEarnings = $this->getEncargadoEarningsThisMonth();
    $salesTotals      = array_column($last3Months, 'total');
    $maxSales         = $salesTotals ? max(max($salesTotals), 1) : 1;
    $lineAgents       = $line->lineAgents()->with('agent')->get();
    $encargadoLA      = $lineAgents->firstWhere('role', 'encargado');
    $contactLinks     = $line->contact_links ?? [];
    $linePlatforms    = $line->platforms()->get();
@endphp

@section('header')
    <x-livewire.components.page-header title="{{ strtoupper($line->name) }}" subtitle="Detalle de línea" />
@endsection

<div class="ld-root" x-data="{ tab: 'info' }">

{{-- ── HERO ─────────────────────────────────────────────────────────────── --}}
<div class="ld-hero">
    <div class="ld-cover">
        @if($line->portada_url)
            <img src="{{ $line->portada_url }}" alt="{{ $line->name }}">
        @endif
        <div class="ld-avatar">
            @if($line->perfil_url)
                <img src="{{ $line->perfil_url }}" alt="">
            @else
                <span>{{ strtoupper(mb_substr($line->name, 0, 2)) }}</span>
            @endif
        </div>
    </div>
    <div class="ld-hero-meta">
        <div>
            <a href="{{ route('lineas') }}" class="ld-back">
                <i class="fa-solid fa-arrow-left"></i> Volver a líneas
            </a>
            <div class="ld-hero-name">{{ strtoupper($line->name) }}</div>
            <div class="ld-hero-sub">
                <span class="ld-mono">#{{ str_pad($line->id, 4, '0', STR_PAD_LEFT) }}</span>
                <span class="status-pill {{ $line->status === 'active' ? 'pill-active' : 'pill-inactive' }}">
                    {{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}
                </span>
                @if($line->description)
                    <span class="ld-desc-inline">{{ $line->description }}</span>
                @endif
            </div>
        </div>
        @if($this->hasLinePermission(\App\Support\Permissions::LINE_EDIT))
        <a href="{{ route('lineas.edit', $line->id) }}" class="btn-edit-hero">
            <i class="fa-solid fa-pen-to-square"></i> Editar línea
        </a>
        @endif
    </div>
</div>

@if(session()->has('message'))
<div class="ld-flash"><i class="fa-solid fa-circle-check"></i> {{ session('message') }}</div>
@endif

{{-- ── TABS ─────────────────────────────────────────────────────────────── --}}
<div class="ld-tabs">
    <button type="button" class="ld-tab" :class="tab==='info'?'ld-tab-active':''" @click="tab='info'">
        <i class="fa-solid fa-circle-info"></i> Info
    </button>
    <button type="button" class="ld-tab" :class="tab==='encargado'?'ld-tab-active':''" @click="tab='encargado'">
        <i class="fa-solid fa-user-tie"></i> Encargado
    </button>
    <button type="button" class="ld-tab" :class="tab==='agentes'?'ld-tab-active':''" @click="tab='agentes'">
        <i class="fa-solid fa-users"></i> Agentes
    </button>
    <button type="button" class="ld-tab" :class="tab==='ventas'?'ld-tab-active':''" @click="tab='ventas'">
        <i class="fa-solid fa-chart-bar"></i> Ventas
    </button>
    <button type="button" class="ld-tab" :class="tab==='canales'?'ld-tab-active':''" @click="tab='canales'">
        <i class="fa-solid fa-comments"></i> Canales
    </button>
    <button type="button" class="ld-tab" :class="tab==='plataformas'?'ld-tab-active':''" @click="tab='plataformas'">
        <i class="fa-solid fa-gamepad"></i> Plataformas
    </button>
    <button type="button" class="ld-tab" :class="tab==='permisos'?'ld-tab-active':''" @click="tab='permisos'">
        <i class="fa-solid fa-shield-halved"></i> Permisos
    </button>
</div>

<div class="ld-tab-body">

    {{-- ── TAB: INFO ──────────────────────────────────────────────────────── --}}
    <div x-show="tab==='info'" x-cloak>
        <div class="info-grid-4">
            <div class="info-box">
                <div class="info-box-label"><i class="fa-solid fa-tag"></i> Estado</div>
                <div class="info-box-value">
                    <span class="status-pill {{ $line->status === 'active' ? 'pill-active' : 'pill-inactive' }}">
                        {{ $line->status === 'active' ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
            <div class="info-box">
                <div class="info-box-label"><i class="fa-brands fa-whatsapp"></i> Tipo</div>
                <div class="info-box-value">{{ ucfirst($line->type ?? 'General') }}</div>
            </div>
            <div class="info-box">
                <div class="info-box-label"><i class="fa-solid fa-user-tie"></i> Encargado</div>
                <div class="info-box-value">
                    @if($encargadoLA)
                        {{ $encargadoLA->agent?->name ?? '—' }}
                        @if($encargadoLA->porcentaje_ganancia)
                            <span class="pct-badge">{{ number_format($encargadoLA->porcentaje_ganancia, 0) }}%</span>
                        @endif
                    @else
                        <span style="color:var(--muted)">Sin asignar</span>
                    @endif
                </div>
            </div>
            <div class="info-box">
                <div class="info-box-label"><i class="fa-solid fa-users"></i> Agentes</div>
                <div class="info-box-value">{{ $lineAgents->count() }}</div>
            </div>
        </div>

        @if($line->description)
        <div class="desc-box">
            <div class="info-box-label" style="margin-bottom:6px"><i class="fa-solid fa-align-left"></i> Descripción</div>
            <p style="margin:0;font-size:13px;color:var(--muted);line-height:1.7">{{ $line->description }}</p>
        </div>
        @endif

        {{-- Imágenes --}}
        @if($line->portada_url || $line->perfil_url)
        <div class="images-row">
            @if($line->portada_url)
            <div class="img-preview-box" style="flex:2">
                <div class="info-box-label" style="margin-bottom:8px"><i class="fa-solid fa-image"></i> Portada</div>
                <img src="{{ $line->portada_url }}" class="img-portada">
            </div>
            @endif
            @if($line->perfil_url)
            <div class="img-preview-box" style="flex:1">
                <div class="info-box-label" style="margin-bottom:8px"><i class="fa-solid fa-circle-user"></i> Perfil</div>
                <img src="{{ $line->perfil_url }}" class="img-perfil">
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- ── TAB: ENCARGADO ─────────────────────────────────────────────────── --}}
    <div x-show="tab==='encargado'" x-cloak>
        @if($encargadoLA)
        <div class="agent-hero-card">
            <div class="agent-hero-avatar">
                {{ strtoupper(mb_substr($encargadoLA->agent?->name ?? 'E', 0, 2)) }}
            </div>
            <div class="agent-hero-info">
                <div class="agent-hero-name">{{ $encargadoLA->agent?->name ?? '—' }} {{ $encargadoLA->agent?->apellido ?? '' }}</div>
                <div class="agent-hero-email">{{ $encargadoLA->agent?->email ?? '' }}</div>
                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <span class="role-badge role-enc"><i class="fa-solid fa-crown"></i> Encargado</span>
                    @if($encargadoLA->porcentaje_ganancia)
                        <span class="role-badge role-pct"><i class="fa-solid fa-percent"></i> {{ number_format($encargadoLA->porcentaje_ganancia, 0) }}% ganancia</span>
                    @endif

                </div>
            </div>
            <div class="agent-hero-stat">
                <div class="info-box-label">Ganancia este mes</div>
                @php $gananciaEnc = $totalThisMonth * (($encargadoLA->porcentaje_ganancia ?? 0) / 100); @endphp
                <div style="font-family:var(--font-display);font-size:28px;color:var(--good)">${{ number_format($gananciaEnc, 2) }}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px">de ${{ number_format($totalThisMonth, 2) }} en ventas</div>
            </div>
        </div>

        {{-- Permisos del encargado --}}
        @php $encPerms = $encargadoLA->permissionsList; @endphp
        @php $currentAgentId = session('active_agent_id') ? (int) session('active_agent_id') : null; @endphp
        @if($this->isAdminMode() || ($encargadoLA && $encargadoLA->agent?->id === $currentAgentId))
            @if(!empty($encPerms))
                <div style="margin-top:16px">
                    <div style="color:var(--muted-2);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px">Permisos asignados</div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:8px">
                        @foreach(\App\Support\Permissions::labels() as $perm => [$icon, $label])
                            @if(in_array($perm, $encPerms))
                            <div class="perm-chip perm-chip-on">
                                <i class="{{ $icon }}"></i>
                                <span>{{ $label }}</span>
                                <i class="fa-solid fa-check perm-check-icon"></i>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div style="margin-top:16px;color:var(--muted-2);font-size:12px;">Sin permisos asignados aún.</div>
            @endif
        @else
            {{-- Regular agents should not see encargado permissions --}}
        @endif

        @else
        <div class="empty-state">
            <i class="fa-solid fa-user-slash" style="font-size:32px;opacity:.3;margin-bottom:12px"></i>
            <div>Sin encargado asignado</div>
        </div>
        @endif
    </div>

    {{-- ── TAB: AGENTES ───────────────────────────────────────────────────── --}}
    <div x-show="tab==='agentes'" x-cloak>
        
        @php $regularAgents = $lineAgents->where('role', '!=', 'encargado'); @endphp
        @if($regularAgents->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-users-slash" style="font-size:32px;opacity:.3;margin-bottom:12px"></i>
            <div>No hay agentes asignados</div>
        </div>
        @else
        <div class="agents-list">
            @foreach($regularAgents as $la)
            <div class="agent-row {{ $la->is_active ? '' : 'agent-row-inactive' }}">
                <div class="agent-row-avatar">
                    {{ strtoupper(mb_substr($la->agent?->name ?? 'A', 0, 2)) }}
                </div>
                <div class="agent-row-info">
                    <div class="agent-row-name">{{ $la->agent?->name ?? '—' }} {{ $la->agent?->apellido ?? '' }}</div>
                    <div class="agent-row-meta">
                        @if($la->agent?->username) <span>&#64;{{ $la->agent->username }}</span> @endif
                        <span class="role-badge {{ $la->role === 'encargado' ? 'role-enc' : 'role-age' }}">
                            @if($la->role === 'encargado')
                                <i class="fa-solid fa-crown"></i> Encargado
                            @else
                                <i class="fa-solid fa-user"></i> Agente
                            @endif
                        </span>
                        @if($la->porcentaje_ganancia)
                            <span class="role-badge role-pct"><i class="fa-solid fa-percent"></i> {{ number_format($la->porcentaje_ganancia, 0) }}</span>
                        @endif
                    </div>
                </div>
                <div class="agent-row-actions">
                    <span class="agent-status-dot {{ $la->is_active ? 'dot-active' : 'dot-inactive' }}"
                          title="{{ $la->is_active ? 'Activo' : 'Inactivo' }}"></span>
                    @if($this->hasLinePermission(\App\Support\Permissions::AGENT_PERMISSIONS))
                    <button type="button" class="btn-icon-sm" wire:click="openPermissions({{ $la->agent->id }})" title="Permisos">
                        <i class="fa-solid fa-shield-halved"></i>
                    </button>
                    @endif
                    @if($this->hasLinePermission(\App\Support\Permissions::AGENT_ASSIGN) && $la->role !== 'encargado')
                    <button type="button" class="btn-icon-sm btn-danger-sm"
                            wire:click="removeAgent({{ $la->agent->id }})"
                            wire:confirm="¿Quitar a {{ $la->agent?->name }} de la línea?"
                            title="Quitar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── TAB: VENTAS ─────────────────────────────────────────────────────── --}}
    <div x-show="tab==='ventas'" x-cloak>
        <div class="kpi-grid">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon-fa"><i class="fa-solid fa-trophy"></i></div>
                <div class="kpi-label">Mejor Mes</div>
                @if($bestMonth)
                    <div class="kpi-val">{{ $monthNames[$bestMonth['mes']] ?? '' }} {{ $bestMonth['anio'] }}</div>
                    <div class="kpi-amt">${{ number_format($bestMonth['total'], 2) }}</div>
                @else
                    <div class="kpi-empty">Sin datos</div>
                @endif
            </div>
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon-fa"><i class="fa-solid fa-gamepad"></i></div>
                <div class="kpi-label">Top Plataforma</div>
                @if($bestPlatform)
                    <div class="kpi-val">{{ $bestPlatform['platform'] }}</div>
                    <div class="kpi-amt">${{ number_format($bestPlatform['total'], 2) }}</div>
                @else
                    <div class="kpi-empty">Sin datos</div>
                @endif
            </div>
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon-fa"><i class="fa-solid fa-calendar-day"></i></div>
                <div class="kpi-label">Ventas este mes</div>
                <div class="kpi-amt" style="color:var(--good)">${{ number_format($totalThisMonth, 2) }}</div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-icon-fa"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <div class="kpi-label">Ganancia encargado</div>
                @if(count($encargadoEarnings) > 0)
                    @foreach($encargadoEarnings as $earning)
                        <div class="kpi-val" style="font-size:12px">{{ $earning['agent']->name ?? '—' }} · {{ $earning['porcentaje'] }}%</div>
                        <div class="kpi-amt">${{ number_format($earning['ganancia'], 2) }}</div>
                    @endforeach
                @else
                    <div class="kpi-empty">Sin encargado</div>
                @endif
            </div>
        </div>

        <div class="months-grid">
            @foreach($last3Months as $i => $month)
            <div class="month-card">
                <div class="month-label">
                    <i class="fa-solid fa-calendar-week" style="color:var(--orange)"></i>
                    @if($i===0) Mes actual @elseif($i===1) Mes pasado @else Mes anterior @endif
                </div>
                <div class="month-name-sm">{{ $monthNames[$month['mes']] ?? '' }} {{ $month['anio'] }}</div>
                <div class="month-total">${{ number_format($month['total'], 2) }}</div>
                <div class="month-bar">
                    <div class="month-bar-fill" style="width:{{ $maxSales > 0 ? round($month['total'] / $maxSales * 100) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── TAB: CANALES ────────────────────────────────────────────────────── --}}
    <div x-show="tab==='canales'" x-cloak>
        @if(empty($contactLinks))
        <div class="empty-state">
            <i class="fa-solid fa-comment-slash" style="font-size:32px;opacity:.3;margin-bottom:12px"></i>
            <div>Sin canales configurados</div>
        </div>
        @else
        <div class="channels-grid">
            @foreach($contactLinks as $link)
            @if(!empty($link['value']))
            @php
                $t = $link['type'] ?? '';
                $n = $link['name'] ?? '';
                $icon = match($t) {
                    'whatsapp'  => 'fa-brands fa-whatsapp',
                    'telegram'  => 'fa-brands fa-telegram',
                    'instagram' => 'fa-brands fa-instagram',
                    'facebook'  => 'fa-brands fa-facebook',
                    'phone'     => 'fa-solid fa-phone',
                    'email'     => 'fa-solid fa-envelope',
                    'web'       => 'fa-solid fa-globe',
                    default     => 'fa-solid fa-link',
                };
                $color = match($t) {
                    'whatsapp'  => '#25d366',
                    'telegram'  => '#2aabee',
                    'instagram' => '#e1306c',
                    'facebook'  => '#1877f2',
                    'phone'     => 'var(--good)',
                    default     => 'var(--orange)',
                };
            @endphp
            <a href="{{ $link['value'] }}" target="_blank" rel="noopener" class="channel-card">
                <div class="channel-icon" style="color:{{ $color }}">
                    <i class="{{ $icon }}"></i>
                </div>
                <div class="channel-info">
                    <div class="channel-type">{{ ucfirst($t) }}</div>
                    @if($n)
                    <div class="channel-name">{{ $n }}</div>
                    @endif
                    <div class="channel-val">{{ $link['value'] }}</div>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square" style="color:var(--muted);font-size:11px;flex-shrink:0"></i>
            </a>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── TAB: PLATAFORMAS ───────────────────────────────────────────────── --}}
    <div x-show="tab==='plataformas'" x-cloak>
        @if($linePlatforms->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-server" style="font-size:32px;opacity:.3;margin-bottom:12px"></i>
            <div>Sin plataformas asignadas</div>
        </div>
        @else
        <div class="platforms-grid">
            @foreach($linePlatforms as $plat)
            <div class="plat-card">
                @if($plat->logo_url)
                    <img src="{{ $plat->logo_url }}" class="plat-logo" alt="{{ $plat->name }}">
                @else
                    <div class="plat-icon"><i class="fa-solid fa-gamepad"></i></div>
                @endif
                <div class="plat-name">{{ $plat->name }}</div>
                @if($plat->website_url)
                    <a href="{{ $plat->website_url }}" target="_blank" class="plat-link">
                        <i class="fa-solid fa-globe"></i> Sitio web
                    </a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── TAB: PERMISOS ──────────────────────────────────────────────────── --}}
    <div x-show="tab==='permisos'" x-cloak>
        @php $permLabels = \App\Support\Permissions::labels(); @endphp

        @php $myPerms = $this->currentLinePermissions(); @endphp
        <div style="margin-bottom:12px;">
            <div class="form-label" style="margin-bottom:6px">Tus permisos en esta línea</div>
            @if(!empty($myPerms))
                <div class="perms-grid">
                    @foreach($permLabels as $perm => [$icon, $label])
                        @if(in_array($perm, $myPerms))
                            <div class="perm-chip perm-chip-on">
                                <i class="{{ $icon }}"></i>
                                <span>{{ $label }}</span>
                                <i class="fa-solid fa-check perm-check-icon"></i>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div style="color:var(--muted-2);font-size:12px">No tenés permisos asignados en esta línea.</div>
            @endif
        </div>
    </div>

</div>{{-- /ld-tab-body --}}

{{-- ── MODAL: ASIGNAR AGENTE ──────────────────────────────────────────────── --}}


{{-- ── MODAL: PERMISOS DE AGENTE ───────────────────────────────────────────── --}}


<style>
[x-cloak] { display: none !important; }

/* ── Layout ──────────────────────────────────────────────────── */
.ld-root { min-height: 100%; min-width: 0; }

/* ── Hero ────────────────────────────────────────────────────── */
.ld-hero { margin-bottom: 0; }
.ld-cover { height: 200px; background: linear-gradient(135deg,rgba(255,106,26,.22),rgba(255,255,255,.04)); position: relative; overflow: hidden; border-radius: 10px 10px 0 0; border: 1px solid var(--line); border-bottom: 0; }
.ld-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
.ld-avatar { position: absolute; left: 24px; bottom: -32px; width: 90px; height: 90px; border-radius: 10px; border: 3px solid rgba(255,255,255,.8); background: #210f0f; overflow: hidden; box-shadow: 0 12px 30px rgba(0,0,0,.5); z-index: 2; }
.ld-avatar img { width: 100%; height: 100%; object-fit: cover; }
.ld-avatar span { display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 36px; color: var(--orange); }
.ld-hero-meta { background: linear-gradient(180deg,#1c0e0e,#120909); padding: 44px 24px 20px; display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; border: 1px solid var(--line); border-top: 0; min-width: 0; }
.ld-hero-meta > div { min-width: 0; max-width: 100%; }
.ld-back { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); text-decoration: none; margin-bottom: 8px; }
.ld-back:hover { color: var(--orange); }
.ld-hero-name { font-family: var(--font-display); font-size: 34px; line-height: 1; letter-spacing: .03em; overflow-wrap: anywhere; }
.ld-hero-sub { display: flex; align-items: center; gap: 8px; margin-top: 6px; flex-wrap: wrap; }
.ld-mono { font-family: var(--font-mono); font-size: 11px; color: var(--muted-2); }
.ld-desc-inline { font-size: 12px; color: var(--muted); overflow-wrap: anywhere; }
.btn-edit-hero { display: inline-flex; align-items: center; gap: 7px; height: 36px; padding: 0 16px; background: rgba(255,106,26,.15); border: 1px solid rgba(255,106,26,.5); border-radius: 8px; color: var(--orange); font-size: 12px; font-weight: 800; text-decoration: none; transition: all .15s; cursor: pointer; }
.btn-edit-hero:hover { background: rgba(255,106,26,.25); }

/* ── Pills ───────────────────────────────────────────────────── */
.status-pill { display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 10px; font-size: 10px; font-weight: 800; white-space: nowrap; }
.pill-active { background: rgba(37,196,107,.12); color: var(--good); border: 1px solid rgba(37,196,107,.3); }
.pill-inactive { background: rgba(255,71,87,.1); color: #ff4757; border: 1px solid rgba(255,71,87,.25); }
.pct-badge { background: rgba(255,106,26,.15); color: var(--orange); border: 1px solid rgba(255,106,26,.3); border-radius: 999px; padding: 2px 8px; font-size: 10px; font-weight: 800; margin-left: 4px; }

/* ── Flash ───────────────────────────────────────────────────── */
.ld-flash { margin: 12px 0; padding: 11px 16px; background: rgba(37,196,107,.12); border: 1px solid rgba(37,196,107,.35); color: var(--good); border-radius: 8px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; }

/* ── Tabs ────────────────────────────────────────────────────── */
.ld-tabs { display: flex; gap: 2px; background: linear-gradient(180deg,#1c0e0e,#120909); border: 1px solid var(--line); border-top: 0; padding: 0 20px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
.ld-tabs::-webkit-scrollbar { height: 6px; }
.ld-tab { padding: 12px 16px; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; background: none; border: none; border-bottom: 2px solid transparent; color: var(--muted); cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: color .15s, border-color .15s; white-space: nowrap; flex-shrink: 0; }
.ld-tab:hover { color: var(--white); }
.ld-tab-active { color: var(--orange) !important; border-bottom-color: var(--orange) !important; }
.ld-tab-body { background: linear-gradient(180deg,#1c0e0e,#120909); border: 1px solid var(--line); border-top: 0; border-radius: 0 0 10px 10px; padding: 26px; min-width: 0; }

/* ── Info tab ────────────────────────────────────────────────── */
.info-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.info-box { border: 1px solid var(--line); border-radius: 8px; padding: 14px; background: rgba(255,255,255,.03); min-width: 0; }
.info-box-label { color: var(--muted-2); font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.info-box-value { font-size: 14px; font-weight: 700; overflow-wrap: anywhere; }
.desc-box { border: 1px solid var(--line); border-radius: 8px; padding: 14px; background: rgba(255,255,255,.03); margin-bottom: 16px; }
.images-row { display: flex; gap: 14px; flex-wrap: wrap; }
.img-preview-box { min-width: 0; }
.img-portada { width: 100%; height: 110px; object-fit: cover; border-radius: 8px; border: 1px solid var(--line); display: block; }
.img-perfil { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; border: 1px solid var(--line); display: block; }

/* ── Encargado hero ──────────────────────────────────────────── */
.agent-hero-card { display: flex; align-items: center; gap: 20px; border: 1px solid rgba(255,106,26,.25); border-radius: 12px; background: rgba(255,106,26,.05); padding: 20px; flex-wrap: wrap; }
.agent-hero-avatar { width: 72px; height: 72px; border-radius: 12px; background: rgba(255,106,26,.2); border: 2px solid rgba(255,106,26,.4); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 26px; color: var(--orange); flex-shrink: 0; }
.agent-hero-info { flex: 1; min-width: 0; }
.agent-hero-name { font-size: 20px; font-weight: 800; font-family: var(--font-display); }
.agent-hero-email { font-size: 12px; color: var(--muted); margin-top: 2px; }
.agent-hero-stat { text-align: right; flex-shrink: 0; }
.role-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 3px 10px; font-size: 10px; font-weight: 800; }
.role-enc { background: rgba(255,106,26,.15); color: var(--orange); border: 1px solid rgba(255,106,26,.3); }
.role-age { background: rgba(255,255,255,.06); color: var(--muted); border: 1px solid var(--line); }
.role-pct { background: rgba(37,196,107,.12); color: var(--good); border: 1px solid rgba(37,196,107,.3); }

/* ── Agents list ─────────────────────────────────────────────── */
.agents-list { display: flex; flex-direction: column; gap: 10px; }
.agent-row { display: flex; align-items: center; gap: 14px; border: 1px solid var(--line); border-radius: 10px; background: rgba(255,255,255,.03); padding: 13px 16px; min-width: 0; }
.agent-row-inactive { opacity: .55; }
.agent-row-avatar { width: 42px; height: 42px; border-radius: 10px; background: rgba(255,106,26,.15); border: 1px solid rgba(255,106,26,.3); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 16px; color: var(--orange); flex-shrink: 0; }
.agent-row-info { flex: 1; min-width: 0; }
.agent-row-name { font-size: 14px; font-weight: 700; overflow-wrap: anywhere; }
.agent-row-meta { display: flex; align-items: center; gap: 6px; margin-top: 4px; flex-wrap: wrap; }
.agent-row-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
.agent-status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot-active { background: var(--good); box-shadow: 0 0 6px rgba(37,196,107,.5); }
.dot-inactive { background: var(--muted); }
.btn-icon-sm { width: 30px; height: 30px; border: 1px solid var(--line); border-radius: 7px; background: rgba(255,255,255,.03); color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all .15s; }
.btn-icon-sm:hover { border-color: var(--orange); color: var(--orange); }
.btn-danger-sm:hover { border-color: #ff4757 !important; color: #ff4757 !important; }

/* ── KPIs ────────────────────────────────────────────────────── */
.kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 20px; }
.kpi-card { background: rgba(255,255,255,.03); border: 1px solid var(--line); border-radius: 12px; padding: 18px 16px; text-align: center; min-width: 0; }
.kpi-card.kpi-gold   { border-color: rgba(255,215,0,.3);   }
.kpi-card.kpi-purple { border-color: rgba(147,112,219,.3); }
.kpi-card.kpi-blue   { border-color: rgba(70,130,255,.3);  }
.kpi-card.kpi-green  { border-color: rgba(37,196,107,.3);  }
.kpi-icon-fa { font-size: 26px; margin-bottom: 10px; opacity: .85; }
.kpi-gold   .kpi-icon-fa { color: #ffd700; }
.kpi-purple .kpi-icon-fa { color: #9370db; }
.kpi-blue   .kpi-icon-fa { color: #4682ff; }
.kpi-green  .kpi-icon-fa { color: var(--good); }
.kpi-label { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .1em; font-weight: 800; margin-bottom: 6px; }
.kpi-val { font-size: 15px; color: var(--white); font-weight: 700; margin-bottom: 4px; }
.kpi-amt { font-size: 22px; color: var(--orange); font-weight: 800; font-family: var(--font-display); }
.kpi-empty { font-size: 12px; color: var(--muted-2); padding: 10px 0; }

/* ── Months ──────────────────────────────────────────────────── */
.months-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.month-card { background: rgba(255,255,255,.03); border: 1px solid var(--line); border-radius: 10px; padding: 16px; }
.month-label { font-size: 10px; color: var(--muted); font-weight: 800; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.month-name-sm { font-size: 12px; color: var(--muted-2); margin-bottom: 8px; }
.month-total { font-size: 22px; color: var(--white); font-weight: 800; font-family: var(--font-display); margin-bottom: 10px; }
.month-bar { height: 6px; background: rgba(255,255,255,.06); border-radius: 4px; overflow: hidden; }
.month-bar-fill { height: 100%; background: linear-gradient(90deg, var(--orange), var(--amber)); border-radius: 4px; transition: width .5s; }

/* ── Channels ────────────────────────────────────────────────── */
.channels-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
.channel-card { display: flex; align-items: center; gap: 14px; border: 1px solid var(--line); border-radius: 10px; background: rgba(255,255,255,.03); padding: 14px 16px; text-decoration: none; color: var(--white); transition: border-color .15s, background .15s; min-width: 0; }
.channel-card:hover { border-color: var(--orange); background: rgba(255,106,26,.06); }
.channel-icon { font-size: 26px; flex-shrink: 0; width: 42px; text-align: center; }
.channel-info { flex: 1; min-width: 0; }
.channel-type { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); margin-bottom: 3px; }
.channel-name { font-size: 12px; color: var(--white); margin-bottom: 2px; font-weight: 600; }
.channel-val { font-size: 13px; font-family: var(--font-mono); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* ── Platforms ───────────────────────────────────────────────── */
.platforms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
.plat-card { border: 1px solid var(--line); border-radius: 10px; background: rgba(255,255,255,.03); padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 10px; text-align: center; min-width: 0; }
.plat-logo { width: 48px; height: 48px; object-fit: contain; border-radius: 8px; }
.plat-icon { font-size: 32px; color: var(--orange); }
.plat-name { font-size: 13px; font-weight: 700; overflow-wrap: anywhere; }
.plat-link { font-size: 11px; color: var(--orange); text-decoration: none; display: flex; align-items: center; gap: 5px; }
.plat-link:hover { text-decoration: underline; }

/* ── Permissions ─────────────────────────────────────────────── */
.perms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 8px; }
.perm-chip { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid transparent; min-width: 0; }
.perm-chip span { min-width: 0; overflow-wrap: anywhere; }
.perm-chip-on  { background: rgba(255,106,26,.12); border-color: rgba(255,106,26,.35); color: var(--orange); }
.perm-chip-off { background: rgba(255,255,255,.03); border-color: var(--line); color: var(--muted); }
.perm-chip-edit { cursor: pointer; transition: all .15s; }
.perm-chip-edit:hover { border-color: var(--orange); }
.perm-check-icon { margin-left: auto; font-size: 10px; }

/* ── Empty state ─────────────────────────────────────────────── */
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; border: 1px dashed var(--line-2); border-radius: 10px; color: var(--muted-2); font-size: 13px; text-align: center; }

/* ── Buttons ─────────────────────────────────────────────────── */
.btn-primary { background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; border: none; padding: 9px 20px; border-radius: 8px; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }
.btn-outline { background: transparent; color: var(--orange); border: 1px solid rgba(255,106,26,.5); padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all .15s; }
.btn-outline:hover { background: rgba(255,106,26,.1); }
.btn-ghost { background: rgba(255,255,255,.04); color: var(--muted); border: 1px solid var(--line); padding: 9px 18px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }

/* ── Modal ───────────────────────────────────────────────────── */
.modal-overlay { position: fixed; inset: 0; z-index: 300; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0,0,0,.78); }
.modal-panel { width: min(520px,100%); max-height: 90vh; overflow-y: auto; border: 1px solid var(--line-2); border-radius: 12px; background: linear-gradient(180deg,#1c0e0e,#120909); }
.modal-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 22px; border-bottom: 1px solid var(--line); }
.modal-head h3 { margin: 0; font-family: var(--font-display); font-size: 20px; display: flex; align-items: center; gap: 10px; }
.modal-close { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 7px; background: rgba(255,255,255,.03); color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; }
.modal-body { padding: 22px; }
.form-label { display: block; margin-bottom: 6px; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.form-input { width: 100%; max-width: 100%; background: rgba(255,255,255,.04); border: 1px solid var(--line); border-radius: 8px; padding: 9px 12px; color: var(--white); font-size: 13px; }
.form-group { margin-bottom: 14px; }
.search-results { display: flex; flex-direction: column; gap: 6px; max-height: 220px; overflow-y: auto; margin-top: 8px; }
.search-result-row { display: flex; align-items: center; gap: 12px; padding: 10px; background: rgba(255,255,255,.03); border: 1px solid var(--line); border-radius: 8px; cursor: pointer; transition: border-color .15s; }
.search-result-row:hover { border-color: var(--orange); }
.result-avatar { width: 34px; height: 34px; border-radius: 8px; background: rgba(255,106,26,.2); color: var(--orange); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 13px; flex-shrink: 0; }

@media (max-width: 900px) {
    .info-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .kpi-grid    { grid-template-columns: repeat(2, 1fr); }
    .months-grid { grid-template-columns: 1fr; }
    .images-row { display: grid; grid-template-columns: 1fr; }
    .channels-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .ld-cover { height: 110px; }
    .ld-avatar { width: 58px; height: 58px; left: 16px; bottom: -23px; border-width: 2px; }
    .ld-avatar span { font-size: 22px; }
    .ld-hero-meta { padding: 30px 14px 14px; align-items: stretch; }
    .ld-hero-name { font-size: 24px; }
    .ld-hero-sub { font-size: 11px; }
    .btn-edit-hero { width: 100%; justify-content: center; }
    .ld-tabs { gap: 4px; padding: 8px; background: none; border: 0; }
    .ld-tab { min-height: 34px; padding: 7px 10px; font-size: 10px; gap: 5px; border: 1px solid var(--line); border-radius: 7px; border-bottom: 1px solid var(--line); }
    .ld-tab-active { border-color: var(--orange) !important; background: rgba(255,106,26,.12); }
    .ld-tab-body { padding: 12px; }
    .info-grid-4 { grid-template-columns: 1fr; }
    .kpi-grid    { grid-template-columns: 1fr; }
    .agent-hero-card { flex-direction: column; text-align: center; }
    .agent-hero-stat { text-align: center; }
    .agent-row { align-items: flex-start; flex-wrap: wrap; padding: 12px; }
    .agent-row-actions { width: 100%; justify-content: flex-start; }
    .channels-grid,
    .platforms-grid,
    .perms-grid { grid-template-columns: 1fr; }
    .channel-card { align-items: flex-start; }
    .channel-val { white-space: normal; overflow-wrap: anywhere; }
    .modal-overlay { padding: 12px; align-items: flex-start; overflow-y: auto; }
    .modal-panel { max-height: none; margin: 10px 0; }
    .modal-head { padding: 14px; }
    .modal-body { padding: 14px; }
}
</style>

</div>
