<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">REPORTES</h1>
            <p class="page-subtitle">Estadísticas y análisis del negocio</p>
        </div>
        <button class="btn-ghost">Exportar PDF</button>
    </div>

    <div class="stats-grid" style="padding: 0 28px;">
        <div class="stat-card"><div class="stat-label">USUARIOS MES</div><div class="stat-value">24,812</div><div class="stat-detail">+12% vs mes anterior</div></div>
        <div class="stat-card"><div class="stat-label">INGRESOS MES</div><div class="stat-value">$2.4M</div><div class="stat-detail">+8% vs mes anterior</div></div>
        <div class="stat-card"><div class="stat-label">TICKETS RESUELTOS</div><div class="stat-value">847</div><div class="stat-detail">95% satisfacción</div></div>
        <div class="stat-card"><div class="stat-label">NUEVAS LÍNEAS</div><div class="stat-value">2</div><div class="stat-detail">Este mes</div></div>
    </div>

    <div class="content" style="padding: 28px;">
        <div class="card" style="margin: 0 28px;">
            <div class="card-header"><h3 class="card-title">USUARIOS POR DÍA</h3></div>
            <div class="chart-placeholder">📊[ Gráfico de usuarios ]</div>
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
        .stat-card { padding: 18px; background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 14px; }
        .stat-label { font-size: 11px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; text-transform: uppercase; }
        .stat-value { font-family: var(--font-display); font-size: 32px; margin-top: 8px; }
        .stat-detail { font-size: 11px; color: var(--good); margin-top: 6px; }
        .card { padding: 22px; background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 14px; }
        .card-header { margin-bottom: 16px; }
        .card-title { font-family: var(--font-display); font-size: 20px; letter-spacing: 0.02em; margin: 0; }
        .chart-placeholder { height: 200px; display: flex; align-items: center; justify-content: center; color: var(--muted); }
    </style>
</div>