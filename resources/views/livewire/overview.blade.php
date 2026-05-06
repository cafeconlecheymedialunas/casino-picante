<div class="page-container">
    <x-livewire.components.page-header title="OVERVIEW" subtitle="Resumen operativo y alertas importantes" />

    <style>
    /* Alert bar */
    .alert-bar { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .alert-row {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
    }
    .alert-row.danger  { background: rgba(255,71,87,0.1);  border: 1px solid rgba(255,71,87,0.3);  color: #ff4757; }
    .alert-row.warning { background: rgba(255,179,71,0.08); border: 1px solid rgba(255,179,71,0.28); color: var(--warn); }
    .alert-row.info    { background: rgba(255,106,26,0.07); border: 1px solid rgba(255,106,26,0.2); color: var(--orange-2); }
    .alert-row a { color: inherit; font-weight: 800; text-decoration: underline; margin-left: auto; white-space: nowrap; }

    /* Section header */
    .mod-section {
        display: flex; align-items: center; gap: 10px;
        margin: 28px 0 10px;
    }
    .mod-section-label {
        font-size: 10px; font-weight: 800; letter-spacing: 0.18em;
        color: var(--orange); white-space: nowrap;
    }
    .mod-section-line {
        flex: 1; height: 1px; background: var(--line);
    }
    .mod-section-link {
        font-size: 11px; color: var(--muted-2); text-decoration: none; font-weight: 600;
        white-space: nowrap;
    }
    .mod-section-link:hover { color: var(--orange); }

    /* KPI grid */
    .kpi-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
    .kpi-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }

    .kpi {
        background: linear-gradient(180deg, #170b0b, #0f0707);
        border: 1px solid var(--line); border-radius: 14px;
        padding: 16px 18px; display: flex; flex-direction: column; gap: 4px;
        position: relative; overflow: hidden;
    }
    .kpi.kpi-urgent { border-color: rgba(255,71,87,0.4); }
    .kpi.kpi-good   { border-color: rgba(37,196,107,0.3); }
    .kpi.kpi-warn   { border-color: rgba(255,179,71,0.3); }

    /* module chip in top-right */
    .kpi-mod {
        position: absolute; top: 10px; right: 12px;
        font-size: 9px; font-weight: 800; letter-spacing: 0.1em;
        color: var(--muted-2); background: rgba(255,255,255,0.04);
        padding: 2px 7px; border-radius: 999px; border: 1px solid var(--line);
        text-transform: uppercase;
    }

    .kpi-label {
        font-size: 10px; font-weight: 700; letter-spacing: 0.1em;
        color: var(--muted-2); text-transform: uppercase;
        padding-right: 56px; /* avoid overlap with mod chip */
    }
    .kpi-value {
        font-family: var(--font-display); font-size: 38px; line-height: 1;
        color: var(--white);
    }
    .kpi-value.c-red    { color: #ff4757; }
    .kpi-value.c-orange { color: var(--orange); }
    .kpi-value.c-green  { color: var(--good); }
    .kpi-value.c-warn   { color: var(--warn); }
    .kpi-value.c-muted  { color: var(--muted-2); }

    /* descriptive subline — full sentence */
    .kpi-desc {
        font-size: 11px; color: var(--muted-2); line-height: 1.4;
        margin-top: 2px;
    }
    .kpi-desc .hi   { color: var(--white); font-weight: 700; }
    .kpi-desc .up   { color: var(--good); font-weight: 700; }
    .kpi-desc .down { color: #ff4757; font-weight: 700; }
    .kpi-desc .warn { color: var(--warn); font-weight: 700; }

    /* Tables row */
    .tables-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 4px; }
    .ov-card { background: linear-gradient(180deg, #170b0b, #0f0707); border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }
    .ov-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-bottom: 1px solid var(--line);
    }
    .ov-card-title { font-size: 10px; font-weight: 800; letter-spacing: 0.14em; color: var(--muted); }
    .ov-card-mod   { font-size: 9px; color: var(--muted-2); background: rgba(255,255,255,0.04); padding: 2px 7px; border-radius: 999px; border: 1px solid var(--line); }
    .ov-card-link  { font-size: 11px; color: var(--orange); text-decoration: none; font-weight: 700; }
    .ov-card-link:hover { text-decoration: underline; }

    .row-item {
        display: grid; align-items: center; gap: 10px;
        padding: 9px 16px; border-bottom: 1px solid var(--line); font-size: 12px;
    }
    .row-item:last-child { border-bottom: none; }
    .row-users { grid-template-columns: 30px 1fr 72px; }
    .row-ticket { grid-template-columns: 1fr 64px 66px; }

    .r-avatar {
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--orange), var(--amber));
        display: flex; align-items: center; justify-content: center;
        color: #190702; font-weight: 800; font-size: 10px;
    }
    .r-name  { font-weight: 600; font-size: 12px; }
    .r-meta  { font-size: 10px; color: var(--muted-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .r-time  { font-size: 10px; color: var(--muted-2); text-align: right; }

    .badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; white-space: nowrap; }
    .badge-active   { background: rgba(37,196,107,0.12); color: var(--good); }
    .badge-blocked  { background: rgba(255,71,87,0.12);  color: #ff4757; }
    .badge-pending  { background: rgba(255,179,71,0.12); color: var(--warn); }
    .badge-open     { background: rgba(255,106,26,0.12); color: var(--orange); }
    .badge-stale    { background: rgba(255,71,87,0.12);  color: #ff4757; }

    .t-subject { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .t-user    { font-size: 10px; color: var(--muted-2); }

    .empty-state { padding: 22px 16px; font-size: 12px; color: var(--muted-2); text-align: center; }
</style>

<x-livewire.components.page-header title="DASHBOARD" subtitle="Panel de control · {{ now()->format('d \d\e F Y') }}" />

{{-- ALERTS --}}
@if(count($alerts) > 0)
<div class="alert-bar">
    @foreach($alerts as $a)
    <div class="alert-row {{ $a['type'] }}">
        {{ $a['icon'] }} {{ $a['msg'] }}
        <a href="{{ route($a['route']) }}" wire:navigate>{{ $a['link'] }}</a>
    </div>
    @endforeach
</div>
@endif

{{-- ── USUARIOS ── --}}
<div class="mod-section">
    <span class="mod-section-label">USUARIOS</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('users.index') }}" wire:navigate class="mod-section-link">Ir al módulo →</a>
</div>
<div class="kpi-grid-4">
    <div class="kpi {{ $users['todayNew'] > 0 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Registros hoy</div>
        <div class="kpi-value {{ $users['todayNew'] > 0 ? 'c-green' : 'c-muted' }}">{{ $users['todayNew'] }}</div>
        <div class="kpi-desc">
            @if($users['vsYesterday'] > 0)
                <span class="up">▲ {{ $users['vsYesterday'] }}%</span> más que ayer
                (<span class="hi">{{ $users['yesterdayNew'] }}</span> nuevos usuarios ayer)
            @elseif($users['vsYesterday'] < 0)
                <span class="down">▼ {{ abs($users['vsYesterday']) }}%</span> menos que ayer
                (<span class="hi">{{ $users['yesterdayNew'] }}</span> nuevos usuarios ayer)
            @else
                Igual que ayer — <span class="hi">{{ $users['yesterdayNew'] }}</span> nuevos usuarios
            @endif
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Nuevos esta semana</div>
        <div class="kpi-value c-orange">{{ $users['weekNew'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $users['weekNew'] }}</span> nuevos usuarios esta semana ·
            <span class="hi">{{ $users['monthNew'] }}</span> este mes
            @if($users['vsLastMonth'] > 0)
                (<span class="up">▲ {{ $users['vsLastMonth'] }}%</span> vs mes anterior)
            @elseif($users['vsLastMonth'] < 0)
                (<span class="down">▼ {{ abs($users['vsLastMonth']) }}%</span> vs mes anterior)
            @endif
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Usuarios activos</div>
        <div class="kpi-value c-green">{{ number_format($users['active']) }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ number_format($users['active']) }}</span> usuarios activos
            de <span class="hi">{{ number_format($users['total']) }}</span> registrados en total
        </div>
    </div>

    <div class="kpi {{ $users['blocked'] > 0 ? 'kpi-urgent' : '' }}">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Usuarios bloqueados</div>
        <div class="kpi-value {{ $users['blocked'] > 0 ? 'c-red' : 'c-muted' }}">{{ $users['blocked'] }}</div>
        <div class="kpi-desc">
            <span class="{{ $users['blocked'] > 0 ? 'down' : 'neu' }}">{{ $users['blocked'] }}</span> usuarios bloqueados ·
            <span class="up">{{ number_format($users['active']) }}</span> activos de <span class="hi">{{ number_format($users['total']) }}</span> totales
        </div>
    </div>
</div>

{{-- ── TICKETS ── --}}
<div class="mod-section">
    <span class="mod-section-label">TICKETS DE SOPORTE</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('tickets') }}" wire:navigate class="mod-section-link">Ir al módulo →</a>
</div>
<div class="kpi-grid-4">
    <div class="kpi {{ $tickets['open'] > 0 ? 'kpi-urgent' : 'kpi-good' }}">
        <span class="kpi-mod">tickets</span>
        <div class="kpi-label">Tickets abiertos</div>
        <div class="kpi-value {{ $tickets['open'] > 10 ? 'c-red' : ($tickets['open'] > 0 ? 'c-orange' : 'c-green') }}">{{ $tickets['open'] }}</div>
        <div class="kpi-desc">
            @if($tickets['stale'] > 0)
                <span class="down">⚠ {{ $tickets['stale'] }} ticket{{ $tickets['stale'] > 1 ? 's' : '' }}</span> sin respuesta hace más de 2 horas
            @else
                Todos los tickets abiertos están siendo atendidos a tiempo
            @endif
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">tickets</span>
        <div class="kpi-label">En proceso ahora</div>
        <div class="kpi-value c-orange">{{ $tickets['progress'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $tickets['progress'] }}</span> tickets en proceso ·
            <span class="up">{{ $tickets['closedToday'] }}</span> tickets resueltos hoy
        </div>
    </div>

    <div class="kpi {{ $tickets['openedToday'] > 5 ? 'kpi-warn' : '' }}">
        <span class="kpi-mod">tickets</span>
        <div class="kpi-label">Abiertos hoy</div>
        <div class="kpi-value">{{ $tickets['openedToday'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $tickets['openedToday'] }}</span> tickets nuevos hoy ·
            <span class="hi">{{ $tickets['weekTotal'] }}</span> nuevos esta semana
        </div>
    </div>

    <div class="kpi {{ $tickets['resolutionRate'] >= 70 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">tickets</span>
        <div class="kpi-label">Tasa de resolución</div>
        <div class="kpi-value {{ $tickets['resolutionRate'] >= 70 ? 'c-green' : 'c-warn' }}">
            {{ $tickets['resolutionRate'] }}<span style="font-size:22px;color:var(--muted-2);">%</span>
        </div>
        <div class="kpi-desc">
            <span class="hi">{{ $tickets['closed'] }}</span> tickets cerrados
            de <span class="hi">{{ $tickets['total'] }}</span> totales registrados
        </div>
    </div>
</div>

{{-- ── BONOS & SORTEOS ── --}}
<div class="mod-section">
    <span class="mod-section-label">BONOS Y SORTEOS</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('bonos') }}" wire:navigate class="mod-section-link">Ir a bonos →</a>
</div>
<div class="kpi-grid-4">
    <div class="kpi {{ $bonuses['activeBonuses'] > 0 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">bonuses</span>
        <div class="kpi-label">Bonos vigentes</div>
        <div class="kpi-value {{ $bonuses['activeBonuses'] > 0 ? 'c-green' : 'c-muted' }}">{{ $bonuses['activeBonuses'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $bonuses['activeBonuses'] }}</span> bonos activos vigentes ·
            <span class="hi">{{ $bonuses['pausedBonuses'] }}</span> pausados ·
            <span class="hi">{{ $bonuses['totalBonuses'] }}</span> en total
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">bonus_assignments</span>
        <div class="kpi-label">Asignaciones activas</div>
        <div class="kpi-value c-orange">{{ $bonuses['activeAssign'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $bonuses['activeAssign'] }}</span> usuarios con bono activo ·
            <span class="up">{{ $bonuses['usedAssign'] }}</span> usados ·
            <span class="down">{{ $bonuses['expiredAssign'] }}</span> expirados
        </div>
    </div>

    <div class="kpi {{ $bonuses['conversionRate'] >= 50 ? 'kpi-good' : 'kpi-warn' }}">
        <span class="kpi-mod">bonus_assignments</span>
        <div class="kpi-label">Conversión de bonos (mes)</div>
        <div class="kpi-value {{ $bonuses['conversionRate'] >= 50 ? 'c-green' : 'c-warn' }}">
            {{ $bonuses['conversionRate'] }}<span style="font-size:20px;color:var(--muted-2);">%</span>
        </div>
        <div class="kpi-desc">
            <span class="hi">{{ $bonuses['usedMonth'] }}</span> bonos canjeados este mes
            (de los que vencieron o fueron usados)
        </div>
    </div>

    <div class="kpi {{ $raffles['active'] > 0 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">raffles</span>
        <div class="kpi-label">Sorteos</div>
        <div class="kpi-value {{ $raffles['active'] > 0 ? 'c-green' : 'c-muted' }}">{{ $raffles['active'] + $raffles['upcoming'] }}</div>
        <div class="kpi-desc">
            <span class="{{ $raffles['active'] > 0 ? 'up' : 'hi' }}">{{ $raffles['active'] }}</span> sorteo{{ $raffles['active'] != 1 ? 's' : '' }} activo{{ $raffles['active'] != 1 ? 's' : '' }} ahora ·
            <span class="hi">{{ $raffles['upcoming'] }}</span> próximos ·
            <span class="hi">{{ $raffles['numbersActive'] }}</span> números asignados en sorteo activo
        </div>
    </div>
</div>

{{-- ── PROMOCIONES & CONTENIDO ── --}}
<div class="mod-section">
    <span class="mod-section-label">PROMOCIONES Y CONTENIDO</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('promociones') }}" wire:navigate class="mod-section-link">Ir a promociones →</a>
</div>
<div class="kpi-grid-4">
    <div class="kpi {{ $promos['active'] > 0 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">promotions</span>
        <div class="kpi-label">Promociones activas</div>
        <div class="kpi-value {{ $promos['active'] > 0 ? 'c-green' : 'c-muted' }}">{{ $promos['active'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $promos['active'] }}</span> promociones vigentes ahora ·
            <span class="hi">{{ $promos['upcoming'] }}</span> próximas a activarse
        </div>
    </div>

    <div class="kpi {{ $promos['expiring'] > 0 ? 'kpi-warn' : '' }}">
        <span class="kpi-mod">promotions</span>
        <div class="kpi-label">Por vencer (24h)</div>
        <div class="kpi-value {{ $promos['expiring'] > 0 ? 'c-warn' : 'c-muted' }}">{{ $promos['expiring'] }}</div>
        <div class="kpi-desc">
            @if($promos['expiring'] > 0)
                <span class="warn">{{ $promos['expiring'] }} promoción{{ $promos['expiring'] > 1 ? 'es' : '' }}</span> vence{{ $promos['expiring'] > 1 ? 'n' : '' }} en las próximas 24 horas
            @else
                Ninguna promoción vence en las próximas 24 horas
            @endif
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">posts</span>
        <div class="kpi-label">Publicaciones activas</div>
        <div class="kpi-value c-orange">{{ $content['published'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $content['novedades'] }}</span> novedades ·
            <span class="hi">{{ $content['carrusel'] }}</span> carrusel ·
            <span class="hi">{{ $content['blog'] }}</span> blog publicados
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">agents</span>
        <div class="kpi-label">Equipo de agentes</div>
        <div class="kpi-value">{{ $agents['total'] }}</div>
        <div class="kpi-desc">
            <span class="up">{{ $agents['active'] }}</span> agentes activos ·
            <span class="hi">{{ $agents['parents'] }}</span> principales ·
            <span class="hi">{{ $agents['children'] }}</span> subordinados
        </div>
    </div>
</div>

{{-- ── ESTADÍSTICAS GENERALES ── --}}
<div class="mod-section">
    <span class="mod-section-label">ESTADÍSTICAS GENERALES</span>
    <div class="mod-section-line"></div>
</div>
<div class="kpi-grid-4">
    <div class="kpi">
        <span class="kpi-mod">usuarios</span>
        <div class="kpi-label">Usuarios registrados</div>
        <div class="kpi-value c-orange">{{ number_format($registeredUsersCount) }}</div>
        <div class="kpi-desc">
            Total de usuarios registrados en el sistema
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">agentes</span>
        <div class="kpi-label">Agentes registrados</div>
        <div class="kpi-value">{{ $agentsCount }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $agentsCountByLine }}</span> asignados a líneas
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">agentes</span>
        <div class="kpi-label">Encargados de línea</div>
        <div class="kpi-value c-orange">{{ $agentsCountEncargado }}</div>
        <div class="kpi-desc">
            Agentes con rol de encargado
        </div>
    </div>

    <div class="kpi {{ $activeBonosCount > 0 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">bonos</span>
        <div class="kpi-label">Bonos activos</div>
        <div class="kpi-value {{ $activeBonosCount > 0 ? 'c-green' : 'c-muted' }}">{{ $activeBonosCount }}</div>
        <div class="kpi-desc">
            Bonos activos actualmente en el sistema
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">sorteos</span>
        <div class="kpi-label">Sorteos de líneas</div>
        <div class="kpi-value c-orange">{{ $rafflesByLineCount }}</div>
        <div class="kpi-desc">
            Total de sorteos registrados por líneas
        </div>
    </div>
</div>

{{-- ── LÍNEA CON MEJOR VENTA ── --}}
@if($bestSellingLine)
<div class="mod-section">
    <span class="mod-section-label">LÍNEA CON MEJOR VENTA DEL MES</span>
    <div class="mod-section-line"></div>
</div>
<div class="kpi-grid-3">
    <div class="kpi kpi-good">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">{{ $bestSellingLine['icon'] }} {{ $bestSellingLine['name'] }}</div>
        <div class="kpi-value c-green">${{ number_format($bestSellingLine['best_sales'], 2) }}</div>
        <div class="kpi-desc">
            Línea con mayor venta del mes · Moneda local
        </div>
    </div>
</div>
@endif

{{-- ── TABLAS ── --}}
<div class="mod-section">
    <span class="mod-section-label">ACTIVIDAD RECIENTE</span>
    <div class="mod-section-line"></div>
</div>
<div class="tables-row">
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">ÚLTIMOS 10 REGISTROS DE USUARIOS</span>
            <span class="ov-card-mod">users</span>
            <a href="{{ route('users.index') }}" wire:navigate class="ov-card-link">Ver todos →</a>
        </div>
        @forelse($last10Users as $user)
        <div class="row-item row-users">
            <div class="r-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
            <div>
                <div class="r-name">{{ $user->name }}</div>
                <div class="r-meta">{{ $user->email }}</div>
            </div>
            <div style="text-align:right;">
                <div class="r-time">{{ $user->created_at->diffForHumans(null, true) }}</div>
                @if($user->status === 'active')
                    <span class="badge badge-active">Activo</span>
                @elseif($user->status === 'blocked')
                    <span class="badge badge-blocked">Bloqueado</span>
                @else
                    <span class="badge badge-pending">Pendiente</span>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">Sin registros de usuarios aún</div>
        @endforelse
    </div>

    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">TICKETS ABIERTOS SIN RESPUESTA</span>
            <span class="ov-card-mod">tickets</span>
            <a href="{{ route('tickets') }}" wire:navigate class="ov-card-link">Ver todos →</a>
        </div>
        @forelse($urgentTickets as $ticket)
        @php $ageHours = $ticket->created_at->diffInHours(now()); @endphp
        <div class="row-item row-ticket">
            <div>
                <div class="t-subject">{{ $ticket->subject }}</div>
                <div class="t-user">de {{ $ticket->user->name ?? 'Usuario desconocido' }}</div>
            </div>
            <span class="{{ $ageHours >= 2 ? 'badge badge-stale' : 'badge badge-open' }}">
                {{ $ageHours >= 2 ? '⚠ +2h' : 'Abierto' }}
            </span>
            <div style="font-size:10px;color:var(--muted-2);text-align:right;">
                hace {{ $ticket->created_at->diffForHumans(null, true) }}
            </div>
        </div>
        @empty
        <div class="empty-state">✅ Sin tickets abiertos pendientes</div>
        @endforelse
    </div>
</div>

</div>
