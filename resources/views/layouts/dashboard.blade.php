<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RED PICANTES Dashboard' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --black: #0a0606;
            --black-2: #120909;
            --black-3: #1a0d0d;
            --ink: #2a1414;
            --line: rgba(255,255,255,0.08);
            --line-2: rgba(255,255,255,0.14);
            --line-warm: rgba(255,120,50,0.22);
            --orange: #ff6a1a;
            --orange-2: #ff8a3d;
            --orange-deep: #e6580f;
            --amber: #ffb347;
            --white: #ffffff;
            --muted: rgba(255,255,255,0.62);
            --muted-2: rgba(255,255,255,0.42);
            --good: #25c46b;
            --warn: #ffb347;
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Manrope', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: var(--black); color: var(--white); font-family: var(--font-body); height: 100%; }
        
        .dash-shell { display: flex; height: 100vh; }
        .sidebar { width: 220px; padding: 18px 14px; border-right: 1px solid var(--line); background: linear-gradient(180deg, #0d0707 0%, #0a0606 100%); display: flex; flex-direction: column; gap: 4; flex-shrink: 0; }
        .sidebar-logo { padding: 4px 8px 14px; border-bottom: 1px solid var(--line); margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .sidebar-section { font-size: 10px; font-weight: 800; letter-spacing: 0.18em; color: var(--orange); margin: 16px 0 8px; padding-left: 8px; }
        .sidebar-item { display: flex; align-items: center; gap: 10; padding: 8px 10px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: all 0.2s; color: var(--muted); }
        .sidebar-item:hover { background: rgba(255,255,255,0.04); color: #fff; }
        .sidebar-item.active { background: rgba(255,106,26,0.12); color: var(--orange); font-weight: 700; border: 1px solid rgba(255,106,26,0.3); }
        .sidebar-item-icon { font-size: 14px; width: 16px; text-align: center; }
        .sidebar-spacer { flex: 1; }
        .sidebar-user { padding: 10px; border-radius: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); display: flex; align-items: center; gap: 10px; }
        .sidebar-avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; color: #190702; font-weight: 800; font-size: 11px; }
        .sidebar-user-name { font-size: 12px; font-weight: 700; }
        .sidebar-user-role { font-size: 10px; color: var(--muted); }
        
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { padding: 18px 28px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; }
        .topbar-label { font-size: 11px; color: var(--muted); letter-spacing: 0.12em; font-weight: 700; }
        .topbar-title { font-family: var(--font-display); font-size: 32px; margin: 2px 0 0; letter-spacing: 0.02em; }
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        
        .content { flex: 1; padding: 28px; overflow: auto; }
        
        .btn-primary { background: linear-gradient(180deg, var(--orange-2) 0%, var(--orange) 60%, var(--orange-deep) 100%); color: #190702; border: none; border-radius: 999px; font-weight: 800; padding: 10px 20px; cursor: pointer; box-shadow: 0 12px 36px rgba(255,106,26,0.45), 0 0 0 1px rgba(255,170,80,0.22) inset; }
        .btn-ghost { background: rgba(255,255,255,0.04); color: #fff; border: 1px solid var(--line-2); border-radius: 999px; padding: 10px 20px; cursor: pointer; font-size: 12px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-card { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 14px; padding: 18px; }
        .stat-label { font-size: 11px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; text-transform: uppercase; }
        .stat-value { font-family: var(--font-display); font-size: 32px; margin-top: 8px; }
        .stat-detail { font-size: 11px; color: var(--good); margin-top: 6px; }
        
        .table-card { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 14px; padding: 22px; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 12px; }
        .table-title { font-family: var(--font-display); font-size: 22px; letter-spacing: 0.02em; }
        .table-header-row { display: grid; grid-template-columns: 40px 60px 2fr 1fr 1fr 1fr 80px; gap: 12px; font-size: 11px; color: var(--muted); padding: 8px 0; border-bottom: 1px solid var(--line); font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; }
        .table-row { display: grid; grid-template-columns: 40px 60px 2fr 1fr 1fr 1fr 80px; gap: 12px; font-size: 13px; padding: 12px 0; border-bottom: 1px solid var(--line); align-items: center; }
        .row-avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; }
        .row-email { color: var(--muted); }
        .row-line span { padding: 3px 8px; border-radius: 6px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 11px; font-weight: 700; }
        .row-status { font-size: 11px; font-weight: 700; }
        .row-status.active { color: var(--good); }
        .row-status.pending { color: var(--warn); }
        .row-status.blocked { color: var(--orange); }
        .row-time { color: var(--muted); font-size: 12px; }
        
        .search-input { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 10px 16px; color: var(--white); font-size: 12px; }
        .filter-select { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 10px 16px; color: var(--white); font-size: 12px; }
        
        .pagination { display: flex; gap: 8px; margin-top: 20px; }
        .pagination button { padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); color: var(--muted); cursor: pointer; }
        .pagination button.active { background: var(--orange); color: #190702; }
    </style>
</head>
<body>
    <div class="dash-shell">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <svg width="20" height="20" viewBox="0 0 40 40">
                    <circle cx="20" cy="22" r="16" fill="rgba(255,106,26,0.35)" opacity="0.6" />
                    <path d="M14 12 C 12 18, 12 26, 18 32 C 24 36, 32 32, 33 24 C 34 18, 28 14, 22 14 C 18 14, 16 13, 14 12 Z" fill="#ff6a1a" />
                </svg>
                <span style="font-family: var(--font-display); font-size: 16px;">RED <span style="color: var(--orange);">PICANTES</span></span>
            </div>
            
            <div class="sidebar-section">DASHBOARD</div>
            <div class="sidebar-item active"><span class="sidebar-item-icon">◐</span> Overview</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">◍</span> Usuarios</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">⌘</span> Agentes</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">◇</span> Permisos</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">✦</span> Promociones</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">✎</span> Novedades</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">☎</span> Líneas & Redes</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">✉</span> Tickets</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">⚙</span> Ajustes</div>
            
            <div class="sidebar-section">OPERACIÓN</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">💰</span> Caja / Pagos</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">🎁</span> Bonos & VIP</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">🎰</span> Catálogo de juegos</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">📢</span> Banners & Notif.</div>
            
            <div class="sidebar-section">REPORTES</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">📊</span> Reportes</div>
            <div class="sidebar-item"><span class="sidebar-item-icon">📋</span> Logs de actividad</div>
            
            <div class="sidebar-spacer"></div>
            <div class="sidebar-user">
                <div class="sidebar-avatar">SC</div>
                <div>
                    <div class="sidebar-user-name">Sofía C.</div>
                    <div class="sidebar-user-role">Admin general</div>
                </div>
            </div>
        </aside>
        
        <main class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-label">DASHBOARD</div>
                    <h1 class="topbar-title">{{ $title ?? 'Usuarios' }}</h1>
                </div>
                <div class="topbar-actions">
                    <div class="search-input" style="min-width: 220px;">🔍 Buscar usuarios, tickets, promos...</div>
                    <button class="btn-ghost">↻</button>
                </div>
            </div>
            
            <div class="content">
                {{ $slot }}
            </div>
        </main>
    </div>
    
    @livewireScripts
</body>
</html>