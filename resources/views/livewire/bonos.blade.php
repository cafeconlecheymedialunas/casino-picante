<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">BONOS & VIP</h1>
            <p class="page-subtitle">Gestión de bonos, programas VIP y niveles de usuario</p>
        </div>
        <button class="btn-primary"><span>+</span> Nuevo Bono</button>
    </div>

    <div class="stats-grid" style="padding: 0 28px;">
        <div class="stat-card">
            <div class="stat-label">BONOS ACTIVOS</div>
            <div class="stat-value">7</div>
            <div class="stat-detail">12 totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">USUARIOS VIP</div>
            <div class="stat-value" style="color: var(--amber);">463</div>
            <div class="stat-detail">1.87% del total</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">BONOS CANJEADOS</div>
            <div class="stat-value">1,247</div>
            <div class="stat-detail">Este mes</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">NIVELES VIP</div>
            <div class="stat-value">5</div>
            <div class="stat-detail">Bronce a Premium</div>
        </div>
    </div>

    <div class="content" style="padding: 28px;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">PROGRAMAS VIP</h3>
            </div>
            <div class="vip-grid">
                @php $levels = ['Bronce', 'Plata', 'Oro', 'Platino', 'Premium']; @endphp
                @foreach($levels as $index => $level)
                <div class="vip-card" style="border-left-color: {{ $index === 0 ? '#cd7f32' : ($index === 1 ? '#c0c0c0' : ($index === 2 ? '#ffd700' : ($index === 3 ? '#e5e4e2' : '#9f92ce'))) }}">
                    <div class="vip-icon" style="background: linear-gradient(135deg, {{ $index === 0 ? '#cd7f32' : ($index === 1 ? '#c0c0c0' : ($index === 2 ? '#ffd700' : ($index === 3 ? '#e5e4e2' : '#9f92ce'))) }}, #1a0d0d)">
                        {{ $index + 1 }}
                    </div>
                    <div class="vip-info">
                        <div class="vip-name">{{ $level }}</div>
                        <div class="vip-users">{{ rand(50, 200) }} usuarios</div>
                    </div>
                    <div class="vip-cashback">{{ $index * 2 + 1 }}% cashback</div>
                </div>
                @endforeach
            </div>
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
        .stat-detail { font-size: 11px; color: var(--muted); margin-top: 6px; }
        .card { padding: 22px; background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 14px; margin: 0 28px; }
        .card-header { margin-bottom: 16px; }
        .card-title { font-family: var(--font-display); font-size: 20px; letter-spacing: 0.02em; margin: 0; }
        .vip-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; }
        @media (max-width: 1024px) { .vip-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px) { .vip-grid { grid-template-columns: 1fr; } }
        .vip-card { display: flex; align-items: center; gap: 12px; padding: 14px; background: rgba(255,255,255,0.03); border-radius: 12px; border-left: 3px solid; }
        .vip-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; color: #190702; }
        .vip-info { flex: 1; }
        .vip-name { font-weight: 700; font-size: 14px; }
        .vip-users { font-size: 11px; color: var(--muted); }
        .vip-cashback { font-size: 12px; font-weight: 700; color: var(--orange); }
    </style>
</div>