<div class="page-container">
@section('header')
    <x-livewire.components.page-header title="DASHBOARD" subtitle="Panel de control · {{ now()->format('d \d\e F Y') }}" />
@endsection

    <style>
    .alert-bar { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .alert-row {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
    }
    .alert-row.danger  { background: rgba(255,71,87,0.1);  border: 1px solid rgba(255,71,87,0.3);  color: #ff4757; }
    .alert-row.warning { background: rgba(255,179,71,0.08); border: 1px solid rgba(255,179,71,0.28); color: var(--warn); }
    .alert-row.info    { background: rgba(255,106,26,0.07); border: 1px solid rgba(255,106,26,0.2); color: var(--orange-2); }
    .alert-row a { color: inherit; font-weight: 800; text-decoration: underline; margin-left: auto; white-space: nowrap; }
    .mod-section {
        display: flex; align-items: center; gap: 10px;
        margin: 28px 0 10px;
    }
    .mod-section-label {
        font-size: 10px; font-weight: 800; letter-spacing: 0.18em;
        color: var(--orange); white-space: nowrap;
    }
    .mod-section-line { flex: 1; height: 1px; background: var(--line); }
    .mod-section-link {
        font-size: 11px; color: var(--muted-2); text-decoration: none; font-weight: 600;
        white-space: nowrap;
    }
    .mod-section-link:hover { color: var(--orange); }
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
        padding-right: 56px;
    }
    .kpi-value { font-family: var(--font-display); font-size: 38px; line-height: 1; color: var(--white); }
    .kpi-value.c-red    { color: #ff4757; }
    .kpi-value.c-orange { color: var(--orange); }
    .kpi-value.c-green  { color: var(--good); }
    .kpi-value.c-warn   { color: var(--warn); }
    .kpi-value.c-muted  { color: var(--muted-2); }
    .kpi-desc { font-size: 11px; color: var(--muted-2); line-height: 1.4; margin-top: 2px; }
    .kpi-desc .hi   { color: var(--white); font-weight: 700; }
    .kpi-desc .up   { color: var(--good); font-weight: 700; }
    .kpi-desc .down { color: #ff4757; font-weight: 700; }
    .kpi-desc .warn { color: var(--warn); font-weight: 700; }
    .tables-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 4px; }
    .ov-card { background: linear-gradient(180deg, #170b0b, #0f0707); border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }
    .ov-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-bottom: 1px solid var(--line);
    }
    .ov-card-title { font-size: 10px; font-weight: 800; letter-spacing: 0.14em; color: var(--muted); }
    .ov-card-mod { font-size: 9px; color: var(--muted-2); background: rgba(255,255,255,0.04); padding: 2px 7px; border-radius: 999px; border: 1px solid var(--line); }
    .ov-card-link { font-size: 11px; color: var(--orange); text-decoration: none; font-weight: 700; }
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
    .r-name { font-weight: 600; font-size: 12px; }
    .r-meta { font-size: 10px; color: var(--muted-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .r-time { font-size: 10px; color: var(--muted-2); text-align: right; }
    .badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; white-space: nowrap; }
    .badge-active  { background: rgba(37,196,107,0.12); color: var(--good); }
    .badge-blocked { background: rgba(255,71,87,0.12);  color: #ff4757; }
    .badge-pending { background: rgba(255,179,71,0.12); color: var(--warn); }
    .badge-open    { background: rgba(255,106,26,0.12); color: var(--orange); }
    .badge-stale   { background: rgba(255,71,87,0.12);  color: #ff4757; }
    .t-subject { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .t-user { font-size: 10px; color: var(--muted-2); }
    .empty-state { padding: 22px 16px; font-size: 12px; color: var(--muted-2); text-align: center; }
    .chart-container { position: relative; height: 140px; margin-top: 12px; }
    .growth-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; margin-left: 8px; }
    .growth-badge.up { background: rgba(37,196,107,0.15); color: var(--good); }
    .growth-badge.down { background: rgba(255,71,87,0.15); color: #ff4757; }
    .growth-badge.neutral { background: rgba(255,255,255,0.08); color: var(--muted-2); }
    .kpi-huge { font-family: var(--font-display); font-size: 42px; line-height: 1; }
    .priority-section { border-left: 3px solid var(--orange); padding-left: 12px; margin-bottom: 16px; }
    .priority-section.critical { border-left-color: #ff4757; }
    .priority-section.high { border-left-color: var(--orange); }
    .priority-section.medium { border-left-color: var(--warn); }

    @media (max-width: 1024px) {
        .kpi-grid-4 { grid-template-columns: repeat(2,1fr); }
        .kpi-grid-3 { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 640px) {
        .kpi-grid-4,
        .kpi-grid-3 { display:none; }
        .kpi-value { font-size: 28px; }
        .kpi-huge { font-size: 32px; }
        .tables-row { grid-template-columns: 1fr; }
        [style*="grid-template-columns: 2fr 1fr"],
        [style*="grid-template-columns: 3fr 1fr"],
        [style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
        .row-users { grid-template-columns: 28px 1fr 60px; gap: 6px; }
        .row-ticket { grid-template-columns: 1fr auto; row-gap: 4px; padding: 10px 12px; }
        .row-ticket .badge,
        .row-ticket > div:last-child { grid-column: 2; }
        .chart-container { height: 100px; }
        [id$="Chart"] { max-height: 120px; }
        .ov-card { border-radius: 10px; }
        .kpi { padding: 12px 14px; border-radius: 10px; }
        .kpi-label { font-size: 9px; }
        .alert-row { font-size: 12px; padding: 8px 12px; flex-wrap: wrap; }
        .alert-row a { margin-left: 0; }
        .mod-section { margin: 18px 0 6px; }
        .mod-section-label { font-size: 9px; }
    }
</style>

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

{{-- ── VENTAS ── --}}
<div class="mod-section">
    <span class="mod-section-label">💰 VENTAS DEL MES</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('ventas') }}" wire:navigate class="mod-section-link">Ver ventas →</a>
</div>
<div class="kpi-grid-4">
    <div class="kpi kpi-good">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">Total ventas</div>
        <div class="kpi-value c-green">${{ number_format((float)$totalSales, 2) }}</div>
        <div class="kpi-desc">
            @if($monthlyGrowth['direction'] === 'up')
                <span class="up">▲ {{ $monthlyGrowth['percent'] }}%</span> vs mes anterior
            @elseif($monthlyGrowth['direction'] === 'down')
                <span class="down">▼ {{ $monthlyGrowth['percent'] }}%</span> vs mes anterior
            @else
                <span class="hi">— 0%</span> sin cambio
            @endif
        </div>
    </div>

    @if($topPlatform)
    <div class="kpi">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">📱 Plataforma #1</div>
        <div class="kpi-value c-orange">{{ $topPlatform['name'] }}</div>
        <div class="kpi-desc">${{ number_format((float)$topPlatform['total'], 2) }}</div>
    </div>
    @else
    <div class="kpi">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">📱 Plataforma #1</div>
        <div class="kpi-value c-muted">—</div>
    </div>
    @endif

    @if($bestSellingLine)
    <div class="kpi kpi-good">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">🏆 Línea #1</div>
        <div class="kpi-value c-green">{{ $bestSellingLine['icon'] }} {{ $bestSellingLine['name'] }}</div>
        <div class="kpi-desc">${{ number_format((float)$bestSellingLine['best_sales'], 2) }}</div>
    </div>
    @else
    <div class="kpi">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">🏆 Línea #1</div>
        <div class="kpi-value c-muted">—</div>
    </div>
    @endif

    <div class="kpi">
        <span class="kpi-mod">ventas</span>
        <div class="kpi-label">💵 Promedio ticket</div>
        <div class="kpi-value c-orange">${{ number_format((float)($salesSummary['avg_ticket'] ?? 0), 2) }}</div>
        <div class="kpi-desc">{{ $salesSummary['transactions'] ?? 0 }} transacciones</div>
    </div>
</div>

{{-- Rankings Ventas + Gráficos --}}
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-top: 10px;">
    <div>
        <div class="kpi-grid-4">
            @if($topBuyer)
            <div class="kpi kpi-good">
                <span class="kpi-mod">cliente</span>
                <div class="kpi-label">👤 Cliente top</div>
                <div class="kpi-value c-green">{{ $topBuyer['username'] ?? $topBuyer['name'] }}</div>
                <div class="kpi-desc">${{ number_format((float)$topBuyer['total'], 2) }}</div>
            </div>
            @else
            <div class="kpi"><span class="kpi-mod">cliente</span><div class="kpi-label">👤 Cliente top</div><div class="kpi-value c-muted">—</div></div>
            @endif

            @if($topAgent)
            <div class="kpi kpi-good">
                <span class="kpi-mod">agente</span>
                <div class="kpi-label">👨‍💼 Agente top</div>
                <div class="kpi-value c-green">{{ $topAgent['username'] ?? $topAgent['name'] }}</div>
                <div class="kpi-desc">${{ number_format((float)$topAgent['total'], 2) }}</div>
            </div>
            @else
            <div class="kpi"><span class="kpi-mod">agente</span><div class="kpi-label">👨‍💼 Agente top</div><div class="kpi-value c-muted">—</div></div>
            @endif

            <div class="kpi">
                <span class="kpi-mod">clientes</span>
                <div class="kpi-label">👥 Clientes únicos</div>
                <div class="kpi-value">{{ $salesSummary['unique_clients'] ?? 0 }}</div>
                <div class="kpi-desc">compraron este mes</div>
            </div>

            <div class="kpi">
                <span class="kpi-mod">transacciones</span>
                <div class="kpi-label">📊 Transacciones</div>
                <div class="kpi-value c-orange">{{ $salesSummary['transactions'] ?? 0 }}</div>
                <div class="kpi-desc">ventas registradas</div>
            </div>
        </div>
    </div>
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">PLATAFORMAS</span>
            <span class="ov-card-mod">ranking</span>
        </div>
        <div style="padding: 12px; height: 120px;">
            <canvas id="platformChart"></canvas>
        </div>
    </div>
</div>

{{-- Gráfico Principal Ventas --}}
<div class="ov-card" style="margin-top: 12px;">
    <div class="ov-card-head">
        <span class="ov-card-title">📈 TENDENCIA VENTAS 30 DÍAS</span>
        <span class="ov-card-mod">diario</span>
    </div>
    <div style="padding: 16px; height: 180px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

{{-- ── CLIENTES ── --}}
<div class="mod-section">
    <span class="mod-section-label">CLIENTES</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('users.index') }}" wire:navigate class="mod-section-link">Ver clientes →</a>
</div>
<div class="kpi-grid-4">
    <div class="kpi {{ $users['todayNew'] > 0 ? 'kpi-good' : '' }}">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Registros hoy</div>
        <div class="kpi-value {{ $users['todayNew'] > 0 ? 'c-green' : 'c-muted' }}">{{ $users['todayNew'] }}</div>
        <div class="kpi-desc">
            @if($users['vsYesterday'] > 0)
                <span class="up">▲ {{ $users['vsYesterday'] }}%</span> más que ayer
                (<span class="hi">{{ $users['yesterdayNew'] }}</span> nuevos clientes ayer)
            @elseif($users['vsYesterday'] < 0)
                <span class="down">▼ {{ abs($users['vsYesterday']) }}%</span> menos que ayer
                (<span class="hi">{{ $users['yesterdayNew'] }}</span> nuevos clientes ayer)
            @else
                Igual que ayer — <span class="hi">{{ $users['yesterdayNew'] }}</span> nuevos clientes
            @endif
        </div>
    </div>

    <div class="kpi">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Nuevos esta semana</div>
        <div class="kpi-value c-orange">{{ $users['weekNew'] }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ $users['weekNew'] }}</span> nuevos clientes esta semana ·
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
        <div class="kpi-label">Clientes activos</div>
        <div class="kpi-value c-green">{{ number_format($users['active']) }}</div>
        <div class="kpi-desc">
            <span class="hi">{{ number_format($users['active']) }}</span> clientes activos
            de <span class="hi">{{ number_format($users['total']) }}</span> registrados en total
        </div>
    </div>

    <div class="kpi {{ $users['blocked'] > 0 ? 'kpi-urgent' : '' }}">
        <span class="kpi-mod">users</span>
        <div class="kpi-label">Clientes bloqueados</div>
        <div class="kpi-value {{ $users['blocked'] > 0 ? 'c-red' : 'c-muted' }}">{{ $users['blocked'] }}</div>
        <div class="kpi-desc">
            <span class="{{ $users['blocked'] > 0 ? 'down' : 'neu' }}">{{ $users['blocked'] }}</span> clientes bloqueados ·
            <span class="up">{{ number_format($users['active']) }}</span> activos de <span class="hi">{{ number_format($users['total']) }}</span> totales
        </div>
    </div>
</div>

{{-- Gráfico Clientes --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px;">
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">📊 REGISTROS 15 DÍAS</span>
            <span class="ov-card-mod">diario</span>
        </div>
        <div style="padding: 12px; height: 140px;">
            <canvas id="usersChart"></canvas>
        </div>
    </div>
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">👥 ESTADO CLIENTES</span>
            <span class="ov-card-mod">distribución</span>
        </div>
        <div style="padding: 12px; height: 140px;">
            <canvas id="clientsStatusChart"></canvas>
        </div>
    </div>
</div>

{{-- ── TICKETS ── --}}
<div class="mod-section">
    <span class="mod-section-label">TICKETS DE SOPORTE</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('tickets') }}" wire:navigate class="mod-section-link">Ver tickets →</a>
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

{{-- Gráfico Tickets --}}
<div class="ov-card" style="margin-top: 10px;">
    <div class="ov-card-head">
        <span class="ov-card-title">📊 ESTADO DE TICKETS</span>
        <span class="ov-card-mod">distribución</span>
    </div>
    <div style="padding: 16px; height: 150px;">
        <canvas id="ticketsChart"></canvas>
    </div>
</div>

{{-- ── BONOS ── --}}
<div class="mod-section">
    <span class="mod-section-label">🎁 BONOS</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('bonos') }}" wire:navigate class="mod-section-link">Ver bonos →</a>
</div>
<div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px;">
    <div class="kpi-grid-3">
        <div class="kpi {{ $bonuses['activeBonuses'] > 0 ? 'kpi-good' : '' }}">
            <span class="kpi-mod">bonuses</span>
            <div class="kpi-label">Bonos vigentes</div>
            <div class="kpi-value {{ $bonuses['activeBonuses'] > 0 ? 'c-green' : 'c-muted' }}">{{ $bonuses['activeBonuses'] }}</div>
            <div class="kpi-desc">
                <span class="hi">{{ $bonuses['totalBonuses'] }}</span> total · <span class="hi">{{ $bonuses['pausedBonuses'] }}</span> pausados
            </div>
        </div>

        <div class="kpi">
            <span class="kpi-mod">bonus_assignments</span>
            <div class="kpi-label">Asignaciones activas</div>
            <div class="kpi-value c-orange">{{ $bonuses['activeAssign'] }}</div>
            <div class="kpi-desc">
                <span class="up">{{ $bonuses['usedAssign'] }}</span> usados · <span class="down">{{ $bonuses['expiredAssign'] }}</span> expirados
            </div>
        </div>

        <div class="kpi {{ $bonuses['conversionRate'] >= 50 ? 'kpi-good' : 'kpi-warn' }}">
            <span class="kpi-mod">bonus_assignments</span>
            <div class="kpi-label">Conversión</div>
            <div class="kpi-value {{ $bonuses['conversionRate'] >= 50 ? 'c-green' : 'c-warn' }}">
                {{ $bonuses['conversionRate'] }}<span style="font-size:18px;color:var(--muted-2);">%</span>
            </div>
            <div class="kpi-desc">
                <span class="hi">{{ $bonuses['usedMonth'] }}</span> canjeados este mes
            </div>
        </div>
    </div>
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">🎯 ESTADO BONOS</span>
            <span class="ov-card-mod">distribución</span>
        </div>
        <div style="padding: 10px; height: 100px;">
            <canvas id="bonosChart"></canvas>
        </div>
    </div>
</div>

{{-- ── SORTEOS ── --}}
<div class="mod-section">
    <span class="mod-section-label">🎰 SORTEOS</span>
    <div class="mod-section-line"></div>
    <a href="{{ route('sorteos') }}" wire:navigate class="mod-section-link">Ver sorteos →</a>
</div>
<div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px;">
    <div class="kpi-grid-3">
        <div class="kpi {{ $raffles['active'] > 0 ? 'kpi-good' : '' }}">
            <span class="kpi-mod">raffles</span>
            <div class="kpi-label">Sorteos activos</div>
            <div class="kpi-value {{ $raffles['active'] > 0 ? 'c-green' : 'c-muted' }}">{{ $raffles['active'] }}</div>
            <div class="kpi-desc">
                {{ $raffles['numbersActive'] }} números activos
            </div>
        </div>

        <div class="kpi">
            <span class="kpi-mod">raffles</span>
            <div class="kpi-label">Próximos</div>
            <div class="kpi-value c-orange">{{ $raffles['upcoming'] }}</div>
            <div class="kpi-desc">
                {{ $raffles['uniqueParticip'] }} participantes únicos
            </div>
        </div>

        <div class="kpi">
            <span class="kpi-mod">raffles</span>
            <div class="kpi-label">Total sorteos</div>
            <div class="kpi-value">{{ $raffles['active'] + $raffles['upcoming'] }}</div>
            <div class="kpi-desc">
                activos + próximos
            </div>
        </div>
    </div>
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">🎰 ESTADO SORTEO</span>
            <span class="ov-card-mod">distribución</span>
        </div>
        <div style="padding: 10px; height: 100px;">
            <canvas id="sorteosChart"></canvas>
        </div>
    </div>
</div>

{{-- ── TABLAS ── --}}
<div class="mod-section">
    <span class="mod-section-label">ACTIVIDAD RECIENTE</span>
    <div class="mod-section-line"></div>
</div>
<div class="tables-row">
    <div class="ov-card">
        <div class="ov-card-head">
            <span class="ov-card-title">ÚLTIMOS 10 REGISTROS DE CLIENTES</span>
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
        <div class="empty-state">Sin registros de clientes aún</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 📈 Ventas - Línea principal
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($dailySales['labels']),
                datasets: [{
                    label: 'Ventas ($)',
                    data: @json($dailySales['data']),
                    borderColor: '#ff6a1a',
                    backgroundColor: 'rgba(255, 106, 26, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.42)', font: { size: 9 } } },
                    y: { grid: { color: 'rgba(255,255,255,0.08)' }, ticks: { color: 'rgba(255,255,255,0.42)', font: { size: 9 }, callback: function(value) { return '$' + (value/1000).toFixed(0) + 'k'; } } }
                }
            }
        });
    }

    // 📊 Plataformas - Barras
    const platformCtx = document.getElementById('platformChart');
    if (platformCtx) {
        new Chart(platformCtx, {
            type: 'bar',
            data: {
                labels: @json($platformComparison['labels']),
                datasets: [{
                    label: 'Ventas',
                    data: @json($platformComparison['data']),
                    backgroundColor: ['#ff6a1a', '#ff8a3d', '#ffb347', '#25c46b', '#6366f1'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.08)' }, ticks: { color: 'rgba(255,255,255,0.42)', font: { size: 9 }, callback: function(value) { return '$' + (value/1000).toFixed(0) + 'k'; } } },
                    y: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.6)', font: { size: 10 } } }
                }
            }
        });
    }

    // 👥 Clientes - Registros 15 días
    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: @json($dailyRegistrations['labels']),
                datasets: [{
                    label: 'Registros',
                    data: @json($dailyRegistrations['data']),
                    backgroundColor: 'rgba(37, 196, 107, 0.7)',
                    borderColor: '#25c46b',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.42)', font: { size: 8 } } },
                    y: { grid: { color: 'rgba(255,255,255,0.08)' }, ticks: { color: 'rgba(255,255,255,0.42)', font: { size: 9 }, stepSize: 1 }, beginAtZero: true }
                }
            }
        });
    }

    // 👥 Estado clientes - Doughnut
    const clientsStatusCtx = document.getElementById('clientsStatusChart');
    if (clientsStatusCtx) {
        new Chart(clientsStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Bloqueados', 'Pendientes'],
                datasets: [{
                    data: [{{ $users['active'] }}, {{ $users['blocked'] }}, {{ $users['total'] - $users['active'] - $users['blocked'] }}],
                    backgroundColor: ['#25c46b', '#ff4757', '#ffb347'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: 'rgba(255,255,255,0.7)', font: { size: 10 }, boxWidth: 12 } } }
            }
        });
    }

    // 🎫 Tickets - Doughnut
    const ticketsCtx = document.getElementById('ticketsChart');
    if (ticketsCtx) {
        new Chart(ticketsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Abiertos', 'En proceso', 'Cerrados'],
                datasets: [{
                    data: [{{ $tickets['open'] }}, {{ $tickets['progress'] }}, {{ $tickets['closed'] }}],
                    backgroundColor: ['#ff6a1a', '#ffb347', '#25c46b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: 'rgba(255,255,255,0.7)', font: { size: 10 }, boxWidth: 12 } } }
            }
        });
    }

    // 🎁 Bonos - Doughnut
    const bonosCtx = document.getElementById('bonosChart');
    if (bonosCtx) {
        new Chart(bonosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Usados', 'Expirados', 'Pausados'],
                datasets: [{
                    data: [
                        {{ $bonuses['activeAssign'] }},
                        {{ $bonuses['usedAssign'] }},
                        {{ $bonuses['expiredAssign'] }},
                        {{ $bonuses['pausedBonuses'] }}
                    ],
                    backgroundColor: ['#25c46b', '#ff6a1a', '#ff4757', '#ffb347'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: 'rgba(255,255,255,0.7)', font: { size: 10 }, boxWidth: 12 } } }
            }
        });
    }

    // 🎰 Sorteos - Doughnut
    const sorteosCtx = document.getElementById('sorteosChart');
    if (sorteosCtx) {
        new Chart(sorteosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Próximos', 'Cerrados'],
                datasets: [{
                    data: [
                        {{ $raffles['active'] }},
                        {{ $raffles['upcoming'] }},
                        {{ $rafflesByLineCount - $raffles['active'] - $raffles['upcoming'] }}
                    ],
                    backgroundColor: ['#25c46b', '#ff6a1a', '#6366f1'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: 'rgba(255,255,255,0.7)', font: { size: 10 }, boxWidth: 12 } } }
            }
        });
    }
});
</script>
