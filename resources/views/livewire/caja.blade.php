<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">CAJA</h1>
            <p class="page-subtitle">Gestión de pagos, retiros y transacciones</p>
        </div>
    </div>

    <style>
        .stats-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .stat-card-dark { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 18px; }
        .stat-card-dark.alert { border-color: var(--orange); }
        .stat-lbl { font-size: 10px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; }
        .stat-lbl.alert { color: var(--orange); }
        .stat-val-lg { font-family: var(--font-display); font-size: 36px; margin-top: 6px; }
        .stat-sub-text { font-size: 11px; color: var(--good); margin-top: 2px; }
        .stat-sub-text.muted { color: var(--muted); }
        .dark-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 22px; }
        .dark-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .dark-card-title { font-family: var(--font-display); font-size: 20px; letter-spacing: 0.02em; }
        .tx-head { display: grid; grid-template-columns: 90px 1.5fr 1fr 1fr 55px 90px 1fr 120px; gap: 10px; font-size: 10px; color: var(--muted); padding: 8px 0; border-bottom: 1px solid var(--line); font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; }
        .tx-row { display: grid; grid-template-columns: 90px 1.5fr 1fr 1fr 55px 90px 1fr 120px; gap: 10px; font-size: 12px; padding: 12px 0; border-bottom: 1px solid var(--line); align-items: center; }
        .tx-row:last-child { border-bottom: none; }
        .tx-id { font-family: var(--font-mono); color: var(--muted); font-size: 11px; }
        .tx-amount { font-family: var(--font-mono); font-weight: 700; }
        .risk-low { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); white-space: nowrap; }
        .risk-medium { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,179,71,0.15); color: var(--warn); white-space: nowrap; }
        .risk-high { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,71,87,0.15); color: #ff4757; white-space: nowrap; }
        .tx-date { color: var(--muted); font-size: 11px; }
        .tx-actions { display: flex; gap: 5px; align-items: center; }
        .btn-approve { height: 28px; padding: 0 10px; font-size: 10px; font-weight: 700; background: var(--good); color: #000; border: none; border-radius: 6px; cursor: pointer; white-space: nowrap; }
        .btn-reject-tx { height: 28px; padding: 0 8px; font-size: 10px; font-weight: 700; background: transparent; color: #ff4757; border: 1px solid #ff4757; border-radius: 6px; cursor: pointer; }
        .btn-view-tx { height: 28px; padding: 0 8px; font-size: 10px; background: rgba(255,255,255,0.04); color: #fff; border: 1px solid var(--line-2); border-radius: 6px; cursor: pointer; }
        .kyc-ok { color: var(--good); font-weight: 700; }
        .kyc-no { color: var(--warn); font-weight: 700; }
    </style>

    @if (session()->has('message'))
        <div style="background: rgba(37,196,107,0.12); border: 1px solid var(--good); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: var(--good); font-size: 13px; font-weight: 700;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div>
            <div style="font-size: 11px; color: var(--muted); letter-spacing: 0.12em; font-weight: 700;">OPERACIÓN</div>
            <div style="font-family: var(--font-display); font-size: 32px; margin-top: 2px; letter-spacing: 0.02em;">Caja / Pagos</div>
        </div>
        <button class="btn-primary" style="height: 36px; padding: 0 16px; font-size: 12px;">↓ Exportar</button>
    </div>

    <div class="stats-grid-4">
        <div class="stat-card-dark alert">
            <div class="stat-lbl alert">PENDIENTES</div>
            <div class="stat-val-lg">23</div>
            <div class="stat-sub-text">Requieren aprobación</div>
        </div>
        <div class="stat-card-dark">
            <div class="stat-lbl">APROBADOS HOY</div>
            <div class="stat-val-lg">184</div>
            <div class="stat-sub-text">$1.2M procesado</div>
        </div>
        <div class="stat-card-dark">
            <div class="stat-lbl">VOLUMEN DEPÓSITOS</div>
            <div class="stat-val-lg">$3.4M</div>
            <div class="stat-sub-text muted">+12% vs ayer</div>
        </div>
        <div class="stat-card-dark">
            <div class="stat-lbl">VOLUMEN RETIROS</div>
            <div class="stat-val-lg">$1.8M</div>
            <div class="stat-sub-text muted">Promedio 42min</div>
        </div>
    </div>

    <div class="dark-card">
        <div class="dark-card-header">
            <div class="dark-card-title">RETIROS PENDIENTES DE APROBACIÓN</div>
            <div style="display: flex; gap: 8px;">
                <button class="btn-ghost" style="height: 32px; padding: 0 12px; font-size: 11px; font-weight: 700;">Filtrar ▾</button>
                <button class="btn-primary" style="height: 32px; padding: 0 14px; font-size: 11px;">Aprobar lote (12)</button>
            </div>
        </div>
        <div class="tx-head">
            <div>#TX-ID</div>
            <div>Usuario</div>
            <div>Método</div>
            <div>Monto</div>
            <div>KYC</div>
            <div>Riesgo</div>
            <div>Solicitado</div>
            <div></div>
        </div>
        <div class="tx-row">
            <div class="tx-id">#TX-4520</div>
            <div><strong>María L.</strong> <span style="color:var(--muted);font-size:11px;">@maria.l</span></div>
            <div style="color:var(--muted);">CBU BBVA</div>
            <div class="tx-amount">$8.500</div>
            <div class="kyc-ok">✓</div>
            <div><span class="risk-low">● Bajo</span></div>
            <div class="tx-date">hace 4min</div>
            <div class="tx-actions">
                <button class="btn-view-tx">Ver</button>
                <button class="btn-approve">Aprobar</button>
                <button class="btn-reject-tx">✗</button>
            </div>
        </div>
        <div class="tx-row">
            <div class="tx-id">#TX-4519</div>
            <div><strong>Diego R.</strong> <span style="color:var(--muted);font-size:11px;">@diego77</span></div>
            <div style="color:var(--muted);">USDT</div>
            <div class="tx-amount">$15.200</div>
            <div class="kyc-ok">✓</div>
            <div><span class="risk-low">● Bajo</span></div>
            <div class="tx-date">hace 12min</div>
            <div class="tx-actions">
                <button class="btn-view-tx">Ver</button>
                <button class="btn-approve">Aprobar</button>
                <button class="btn-reject-tx">✗</button>
            </div>
        </div>
        <div class="tx-row">
            <div class="tx-id">#TX-4518</div>
            <div><strong>Pao F.</strong> <span style="color:var(--muted);font-size:11px;">@pao.f</span></div>
            <div style="color:var(--muted);">MercadoPago</div>
            <div class="tx-amount">$3.400</div>
            <div class="kyc-ok">✓</div>
            <div><span class="risk-low">● Bajo</span></div>
            <div class="tx-date">hace 18min</div>
            <div class="tx-actions">
                <button class="btn-view-tx">Ver</button>
                <button class="btn-approve">Aprobar</button>
                <button class="btn-reject-tx">✗</button>
            </div>
        </div>
        <div class="tx-row">
            <div class="tx-id">#TX-4517</div>
            <div><strong>Ramón S.</strong> <span style="color:var(--muted);font-size:11px;">@ramon.s</span></div>
            <div style="color:var(--muted);">Transferencia</div>
            <div class="tx-amount">$42.000</div>
            <div class="kyc-no">!</div>
            <div><span class="risk-high">● Alto</span></div>
            <div class="tx-date">hace 34min</div>
            <div class="tx-actions">
                <button class="btn-view-tx">Ver</button>
                <button class="btn-approve">Aprobar</button>
                <button class="btn-reject-tx">✗</button>
            </div>
        </div>
        <div class="tx-row">
            <div class="tx-id">#TX-4516</div>
            <div><strong>Luisa M.</strong> <span style="color:var(--muted);font-size:11px;">@luisa.m</span></div>
            <div style="color:var(--muted);">Bitcoin</div>
            <div class="tx-amount">$28.500</div>
            <div class="kyc-ok">✓</div>
            <div><span class="risk-medium">● Medio</span></div>
            <div class="tx-date">hace 47min</div>
            <div class="tx-actions">
                <button class="btn-view-tx">Ver</button>
                <button class="btn-approve">Aprobar</button>
                <button class="btn-reject-tx">✗</button>
            </div>
        </div>
        <div class="tx-row">
            <div class="tx-id">#TX-4515</div>
            <div><strong>Carlos V.</strong> <span style="color:var(--muted);font-size:11px;">@carlos.v</span></div>
            <div style="color:var(--muted);">CBU Galicia</div>
            <div class="tx-amount">$6.800</div>
            <div class="kyc-ok">✓</div>
            <div><span class="risk-low">● Bajo</span></div>
            <div class="tx-date">hace 1h 2min</div>
            <div class="tx-actions">
                <button class="btn-view-tx">Ver</button>
                <button class="btn-approve">Aprobar</button>
                <button class="btn-reject-tx">✗</button>
            </div>
        </div>
    </div>
</div>
