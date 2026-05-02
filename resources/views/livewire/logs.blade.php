<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">LOGS DE ACTIVIDAD</h1>
            <p class="page-subtitle">Historial de acciones en el sistema</p>
        </div>
    </div>

    <div class="content" style="padding: 0 28px 28px;">
        <div class="card" style="padding: 0;">
            <div class="logs-list">
                @for($i = 1; $i <= 15; $i++)
                <div class="log-item">
                    <div class="log-time">{{ date('H:i:s', strtotime("-{$i} minutes")) }}</div>
                    <div class="log-action">{{ ['Creó usuario', 'Editó promoción', 'Cerró ticket', 'Actualizó línea', 'Modificó permisos'][$i % 5] }}</div>
                    <div class="log-user">Admin Sofía</div>
                    <div class="log-ip">192.168.1.{{ 100 + $i }}</div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .card { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 14px; }
        .logs-list { max-height: 600px; overflow-y: auto; }
        .log-item { display: grid; grid-template-columns: 80px 1.5fr 1fr 100px; gap: 16px; padding: 14px 22px; border-bottom: 1px solid var(--line); font-size: 13px; align-items: center; }
        .log-item:hover { background: rgba(255,255,255,0.02); }
        .log-time { font-family: var(--font-mono); color: var(--muted); font-size: 12px; }
        .log-action { color: var(--orange); font-weight: 600; }
        .log-user { color: var(--muted); }
        .log-ip { color: var(--muted-2); font-size: 12px; font-family: var(--font-mono); }
    </style>
</div>