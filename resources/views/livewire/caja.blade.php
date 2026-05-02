<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">CAJA / PAGOS</h1>
            <p class="page-subtitle">Gestión de depósitos, retiros y transacciones</p>
        </div>
    </div>

    <div class="stats-grid" style="padding: 0 28px;">
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-label">DEPÓSITOS HOY</div>
            <div class="stat-value">$1.2M</div>
            <div class="stat-change positive">▲ +15% vs ayer</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏧</div>
            <div class="stat-label">RETIROS HOY</div>
            <div class="stat-value">$840K</div>
            <div class="stat-change">▲ +8% vs ayer</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-label">TRANSACCIONES</div>
            <div class="stat-value">1,247</div>
            <div class="stat-change">Promedio diario</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">PENDIENTES</div>
            <div class="stat-value">12</div>
            <div class="stat-change neutral">Revisión manual</div>
        </div>
    </div>

    <div class="content" style="padding: 28px;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">TRANSACCIONES RECIENTES</h3>
                <button class="btn-ghost">Exportar</button>
            </div>
            <div class="table-container">
                <div class="table-header-row">
                    <div>ID</div>
                    <div>Usuario</div>
                    <div>Tipo</div>
                    <div>Monto</div>
                    <div>Método</div>
                    <div>Estado</div>
                    <div>Fecha</div>
                </div>
                @for($i = 1; $i <= 5; $i++)
                <div class="table-row">
                    <div class="row-mono">#{{ 1000 + $i }}</div>
                    <div class="row-user">
                        <div class="row-avatar">{{ chr(64 + $i) }}</div>
                        <span>Usuario {{ $i }}</span>
                    </div>
                    <div class="row-type {{ $i % 2 == 0 ? 'deposit' : 'withdrawal' }}">
                        {{ $i % 2 == 0 ? 'Depósito' : 'Retiro' }}
                    </div>
                    <div class="row-amount">${{ number_format(rand(10000, 100000), 0, ',', '.') }}</div>
                    <div class="row-method">Transferencia</div>
                    <div class="row-status completed">✓ Completado</div>
                    <div class="row-time">Hace {{ $i * 5 }}min</div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
        @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } }
        .stat-card { padding: 18px; background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: var(--r-lg); }
        .stat-icon { font-size: 24px; margin-bottom: 8px; }
        .stat-label { font-size: 11px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; text-transform: uppercase; }
        .stat-value { font-family: var(--font-display); font-size: 32px; margin-top: 8px; }
        .stat-change { font-size: 11px; color: var(--good); margin-top: 6px; }
        .stat-change.positive { color: var(--good); }
        .stat-change.neutral { color: var(--muted); }
        
        .card { padding: 22px; background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: var(--r-lg); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .card-title { font-family: var(--font-display); font-size: 20px; letter-spacing: 0.02em; margin: 0; }
        .table-container { overflow-x: auto; }
        .table-header-row { display: grid; grid-template-columns: 60px 1.5fr 100px 120px 1fr 100px 100px; gap: 12px; font-size: 11px; color: var(--muted); padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; }
        .table-row { display: grid; grid-template-columns: 60px 1.5fr 100px 120px 1fr 100px 100px; gap: 12px; font-size: 13px; padding: 12px 0; border-bottom: 1px solid var(--line); align-items: center; }
        .row-mono { font-family: var(--font-mono); color: var(--muted); }
        .row-user { display: flex; align-items: center; gap: 10px; }
        .row-avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; }
        .row-type { font-size: 11px; font-weight: 700; }
        .row-type.deposit { color: var(--good); }
        .row-type.withdrawal { color: var(--orange); }
        .row-amount { font-family: var(--font-mono); font-weight: 700; }
        .row-method { color: var(--muted); font-size: 12px; }
        .row-status { font-size: 11px; font-weight: 700; }
        .row-status.completed { color: var(--good); }
        .row-time { color: var(--muted); font-size: 12px; }
    </style>
</div>