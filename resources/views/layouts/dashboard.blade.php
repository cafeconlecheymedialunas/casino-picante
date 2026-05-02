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
            --r-xs: 6px;
            --r-sm: 10px;
            --r-md: 14px;
            --r-lg: 20px;
            --r-xl: 28px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: var(--black); color: var(--white); font-family: var(--font-body); height: 100%; }
        
        .dash-shell { display: flex; height: 100vh; }
        .sidebar { 
            width: 220px; padding: 18px 14px; border-right: 1px solid var(--line); 
            background: linear-gradient(180deg, #0d0707 0%, #0a0606 100%); 
            display: flex; flex-direction: column; gap: 4; flex-shrink: 0; 
            overflow-y: auto;
        }
        .sidebar-logo { padding: 4px 8px 14px; border-bottom: 1px solid var(--line); margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .sidebar-logo-text { font-family: var(--font-display); font-size: 22px; color: var(--orange); }
        .sidebar-section { font-size: 10px; font-weight: 800; letter-spacing: 0.18em; color: var(--orange); margin: 16px 0 8px; padding-left: 8px; }
        .sidebar-item {
            display: flex; align-items: center; gap: 10;
            padding: 8px 10px; border-radius: 8px;
            font-size: 13px; cursor: pointer; transition: all 0.2s; color: var(--muted);
            text-decoration: none;
        }
        .sidebar-item:hover { background: rgba(255,255,255,0.04); color: #fff; }
        .sidebar-item.active { 
            background: rgba(255,106,26,0.12); color: var(--orange); font-weight: 700; 
            border: 1px solid rgba(255,106,26,0.3); 
        }
        .sidebar-item-icon { font-size: 14px; width: 16px; text-align: center; }
        .sidebar-spacer { flex: 1; }
        .sidebar-user { 
            padding: 10px; border-radius: 10px; 
            background: rgba(255,255,255,0.03); border: 1px solid var(--line); 
            display: flex; align-items: center; gap: 10; 
        }
        .sidebar-avatar { 
            width: 28px; height: 28px; border-radius: 50%; 
            background: linear-gradient(135deg, var(--orange), var(--amber)); 
            display: flex; align-items: center; justify-content: center; 
            color: #190702; font-weight: 800; font-size: 11px; 
        }
        .sidebar-user-name { font-size: 12px; font-weight: 700; }
        .sidebar-user-role { font-size: 10px; color: var(--muted); }
        
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        
        .btn-primary {
            background: linear-gradient(180deg, var(--orange-2) 0%, var(--orange) 60%, var(--orange-deep) 100%);
            color: #190702; border: none; border-radius: 999px;
            font-weight: 800; padding: 10px 20px; cursor: pointer;
            box-shadow: 0 12px 36px rgba(255,106,26,0.45), 0 0 0 1px rgba(255,170,80,0.22) inset;
            transition: all 0.2s;
        }
        .btn-primary:hover { transform: translateY(-2px); }
        
        .btn-ghost {
            background: rgba(255,255,255,0.04); color: #fff;
            border: 1px solid var(--line-2); border-radius: 999px;
            padding: 10px 20px; cursor: pointer; font-size: 12px; transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
        }
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
                <span class="sidebar-logo-text">RED PICANTES</span>
            </div>
            
            <div class="sidebar-section">DASHBOARD</div>
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sidebar-item-icon">◐</span> Overview
            </a>
            <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                <span class="sidebar-item-icon">◍</span> Usuarios
            </a>
            <a href="{{ route('agentes') }}" class="sidebar-item {{ request()->routeIs('agentes') ? 'active' : '' }}">
                <span class="sidebar-item-icon">⌘</span> Agentes
            </a>
            
            <div class="sidebar-section">CONTENIDO</div>
            <a href="{{ route('promociones') }}" class="sidebar-item {{ request()->routeIs('promociones') ? 'active' : '' }}">
                <span class="sidebar-item-icon">✦</span> Promociones
            </a>
            <a href="{{ route('novedades') }}" class="sidebar-item {{ request()->routeIs('novedades') ? 'active' : '' }}">
                <span class="sidebar-item-icon">✎</span> Novedades
            </a>
            <a href="{{ route('lineas') }}" class="sidebar-item {{ request()->routeIs('lineas') ? 'active' : '' }}">
                <span class="sidebar-item-icon">☎</span> Líneas & Redes
            </a>
            <a href="{{ route('tickets') }}" class="sidebar-item {{ request()->routeIs('tickets') ? 'active' : '' }}">
                <span class="sidebar-item-icon">✉</span> Tickets
            </a>
            <a href="{{ route('ajustes') }}" class="sidebar-item {{ request()->routeIs('ajustes') ? 'active' : '' }}">
                <span class="sidebar-item-icon">⚙</span> Ajustes
            </a>
            
            <div class="sidebar-section">OPERACIÓN</div>
            <a href="{{ route('caja') }}" class="sidebar-item {{ request()->routeIs('caja') ? 'active' : '' }}">
                <span class="sidebar-item-icon">💰</span> Caja / Pagos
            </a>
            <a href="{{ route('bonos') }}" class="sidebar-item {{ request()->routeIs('bonos') ? 'active' : '' }}">
                <span class="sidebar-item-icon">🎁</span> Bonos & VIP
            </a>
            <a href="{{ route('juegos') }}" class="sidebar-item {{ request()->routeIs('juegos') ? 'active' : '' }}">
                <span class="sidebar-item-icon">🎰</span> Catálogo de juegos
            </a>
            <a href="{{ route('banners') }}" class="sidebar-item {{ request()->routeIs('banners') ? 'active' : '' }}">
                <span class="sidebar-item-icon">📢</span> Banners & Notif.
            </a>
            
            <div class="sidebar-section">REPORTES</div>
            <a href="{{ route('reportes') }}" class="sidebar-item {{ request()->routeIs('reportes') ? 'active' : '' }}">
                <span class="sidebar-item-icon">📊</span> Reportes
            </a>
            <a href="{{ route('logs') }}" class="sidebar-item {{ request()->routeIs('logs') ? 'active' : '' }}">
                <span class="sidebar-item-icon">📋</span> Logs de actividad
            </a>
            
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
            {{ $slot }}
        </main>
    </div>
    
    @livewireScripts
</body>
</html>