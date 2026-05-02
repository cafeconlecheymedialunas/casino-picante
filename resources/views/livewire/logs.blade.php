<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">LOGS</h1>
            <p class="page-subtitle">Registro de actividad del sistema</p>
        </div>
    </div>

    <style>
        .logs-card { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 20px; padding: 22px; box-shadow: 0 1px 0 rgba(255,255,255,0.04) inset, 0 12px 40px rgba(0,0,0,0.5); }
        .btn-ghost-lg { background: rgba(255,255,255,0.04); color: #fff; border: 1px solid var(--line-2); border-radius: 999px; cursor: pointer; }
        .log-thead { display: grid; grid-template-columns: 65px 140px 150px 1fr 90px; gap: 14px; font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; padding: 8px 0; border-bottom: 1px solid var(--line); }
        .log-row { display: grid; grid-template-columns: 65px 140px 150px 1fr 90px; gap: 14px; font-size: 12px; padding: 12px 0; border-bottom: 1px solid var(--line); align-items: center; }
        .log-row:last-child { border-bottom: none; }
        .log-time { font-family: var(--font-mono); color: var(--muted); font-size: 11px; }
        .severity-low { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); white-space: nowrap; }
        .severity-medium { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,179,71,0.15); color: var(--warn); white-space: nowrap; }
        .severity-high { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,106,26,0.18); color: var(--orange); white-space: nowrap; }
    </style>

    @if (session()->has('message'))
        <div style="background: rgba(37,196,107,0.12); border: 1px solid var(--good); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: var(--good); font-size: 13px; font-weight: 700;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div>
            <div style="font-size: 11px; color: var(--muted); letter-spacing: 0.12em; font-weight: 700;">REPORTES</div>
            <div style="font-family: var(--font-display); font-size: 32px; margin-top: 2px; letter-spacing: 0.02em;">Logs de Actividad</div>
        </div>
    </div>

    <div class="logs-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <h3 style="font-family:var(--font-display);font-size:20px;margin:0;letter-spacing:0.02em;">REGISTRO DE EVENTOS</h3>
            <div style="display:flex;gap:8px;">
                <button class="btn-ghost-lg" style="height:32px;padding:0 12px;font-size:11px;font-weight:700;">Últimas 24h ▾</button>
                <button class="btn-ghost-lg" style="height:32px;padding:0 12px;font-size:11px;font-weight:700;">Todos los agentes ▾</button>
                <button class="btn-ghost-lg" style="height:32px;padding:0 12px;font-size:11px;font-weight:700;">↓ Exportar</button>
            </div>
        </div>

        <div class="log-thead">
            <div>HORA</div><div>USUARIO</div><div>ACCIÓN</div><div>DETALLE</div><div>SEVERIDAD</div>
        </div>

        @php
        $logs = [
            ['time' => '14:32', 'user' => '@lucia.f',   'action' => 'editó promo',       'detail' => 'PICANTE200 (cambió rollover 25× → 20×)',      'sev' => 'medium'],
            ['time' => '14:28', 'user' => '@admin',      'action' => 'creó agente',       'detail' => 'tomas.c con rol Padre · línea L4',            'sev' => 'low'],
            ['time' => '14:21', 'user' => '@system',     'action' => 'aprobó retiro',     'detail' => 'usr_8473 · $8.500',                           'sev' => 'low'],
            ['time' => '14:15', 'user' => '@martin.r',  'action' => 'bloqueó usuario',   'detail' => '@el_capo · motivo: AML',                      'sev' => 'high'],
            ['time' => '13:54', 'user' => '@lucia.f',   'action' => 'publicó novedad',   'detail' => 'Llegó la Mega Slot Inferno',                  'sev' => 'low'],
            ['time' => '13:42', 'user' => '@system',    'action' => 'backup completado', 'detail' => 'rp-prod-db · 4.2 GB',                        'sev' => 'low'],
            ['time' => '13:18', 'user' => '@carlos.m',  'action' => 'cambió permisos',   'detail' => 'tomas.c · agregó canPublishPromos',           'sev' => 'medium'],
            ['time' => '12:55', 'user' => '@system',    'action' => 'rate-limit',        'detail' => 'IP 190.121.x.x · 142 req/min',               'sev' => 'medium'],
            ['time' => '12:33', 'user' => '@ana.t',     'action' => 'cerró ticket',      'detail' => '#1842 · resuelto en 28min',                   'sev' => 'low'],
            ['time' => '12:20', 'user' => '@admin',     'action' => 'actualizó ajuste',  'detail' => 'Marca · color primario #FF6A1A',              'sev' => 'low'],
            ['time' => '12:08', 'user' => '@system',    'action' => 'login fallido x5',  'detail' => 'admin · IP 200.x · bloqueada 10min',         'sev' => 'high'],
            ['time' => '11:54', 'user' => '@tomas.c',   'action' => 'editó línea',       'detail' => 'L4 · cambió Telegram a @rp_l4_oficial',       'sev' => 'low'],
        ];
        @endphp

        @foreach($logs as $log)
        <div class="log-row">
            <div class="log-time">{{ $log['time'] }}</div>
            <div style="font-weight:700;">{{ $log['user'] }}</div>
            <div style="color:var(--muted);">{{ $log['action'] }}</div>
            <div style="color:var(--muted);">{{ $log['detail'] }}</div>
            <div>
                @if($log['sev'] === 'high')
                    <span class="severity-high">● ALTO</span>
                @elseif($log['sev'] === 'medium')
                    <span class="severity-medium">● MEDIO</span>
                @else
                    <span class="severity-low">● BAJO</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
