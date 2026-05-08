<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        [x-cloak] { display: none !important; }
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
        
        .page-action-strip { display: flex; justify-content: flex-end; gap: 10px; padding: 12px 28px 0; }
        .btn-ghost {
            background: rgba(255,255,255,0.04); color: #fff;
            border: 1px solid var(--line-2); border-radius: 999px;
            padding: 10px 20px; cursor: pointer; font-size: 12px; transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
        }

        .sidebar-line-selector {
            padding: 10px; margin: 4px 0 12px;
            background: rgba(255,106,26,0.06);
            border: 1px solid rgba(255,106,26,0.15);
            border-radius: 12px;
        }
        .sidebar-line-label {
            font-size: 9px; font-weight: 800; letter-spacing: 0.16em;
            color: var(--orange); opacity: 0.8; margin-bottom: 6px; padding-left: 2px;
        }
        .sidebar-line-select {
            width: 100%; background: rgba(255,255,255,0.05);
            border: 1px solid var(--line-2); border-radius: 8px;
            padding: 8px 30px 8px 10px; font-size: 13px; font-weight: 700;
            color: var(--white); cursor: pointer;
            background-position: right 8px center;
        }
        .sidebar-line-select:hover {
            border-color: var(--orange);
            background-color: rgba(255,255,255,0.08);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--black-2); border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--orange); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--orange-2); }
        ::-webkit-scrollbar-corner { background: var(--black-2); }
        
        /* Firefox */
        * { scrollbar-width: thin; scrollbar-color: var(--orange) var(--black-2); }
        
        /* Main content padding */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .main-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0;
        }
        .page-container {
            padding: 24px 28px;
            min-height: 100%;
        }
        
        /* Ensure all scrollable areas have proper styling */
        .scrollable {
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* â”€â”€ Global select styles â”€â”€ */
        select {
            background-color: var(--black-3);
            color: var(--white);
            border: 1px solid var(--line-2);
            border-radius: var(--r-sm);
            padding: 8px 32px 8px 12px;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ff6a1a' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        select:hover  { border-color: var(--orange); }
        select:focus  { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,0.15); }

        option {
            background-color: #1a0d0d;
            color: var(--white);
            padding: 8px 12px;
        }
        option:checked  { background-color: rgba(255,106,26,0.25); color: var(--orange-2); }
        option:disabled { color: var(--muted-2); }
        option[value=""] { color: var(--muted-2); }

        .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:22px 28px 14px; border-bottom:1px solid var(--line); background:var(--black); position:sticky; top:0; z-index:80; flex-wrap:wrap; }
        .page-header-left { flex:1; min-width:0; }
        .page-title { margin:0; font-family:var(--font-display); font-size:34px; letter-spacing:.03em; line-height:.95; }
        .page-subtitle { margin:5px 0 0; color:var(--muted-2); font-size:12px; }
        .page-header-btn { height:38px; padding:0 16px; border-radius:8px; border:1px solid var(--orange); background:var(--orange); color:#190702; font-weight:600; font-size:13px; cursor:pointer; transition:all .15s; }
        .page-header-btn:hover { background:#ff8a4a; border-color:#ff8a4a; }
        .page-header-right { display:flex; align-items:center; gap:10px; flex-shrink:0; }
        .header-icon-btn { position:relative; width:38px; height:38px; border-radius:8px; border:1px solid var(--line); background:rgba(255,255,255,.035); color:var(--white); cursor:pointer; font-weight:900; display:flex; align-items:center; justify-content:center; }
        .header-icon-btn b { position:absolute; top:-6px; right:-5px; min-width:18px; height:18px; padding:0 5px; border-radius:999px; background:var(--orange); color:#190702; font-size:10px; display:flex; align-items:center; justify-content:center; }
        .profile-menu, .notification-menu { position:relative; }
        .profile-trigger { height:38px; display:flex; align-items:center; gap:9px; border:1px solid var(--line); border-radius:8px; background:rgba(255,255,255,.035); color:var(--white); padding:4px 9px 4px 4px; cursor:pointer; }
        .header-icon-btn:hover, .profile-trigger:hover { border-color:var(--orange); background:rgba(255,106,26,.1); }
        .profile-trigger img { width:30px; height:30px; border-radius:7px; background:rgba(255,255,255,.08); }
        .profile-trigger strong { display:block; max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:12px; }
        .profile-trigger small { display:block; color:var(--muted-2); font-size:10px; margin-top:1px; text-align:left; }
        .header-dropdown { position:absolute; top:46px; right:0; min-width:230px; border:1px solid var(--line-2); border-radius:8px; background:#140909; box-shadow:0 18px 45px rgba(0,0,0,.45); z-index:120; overflow:hidden; }
        .profile-dropdown { min-width:200px; }
        .profile-dropdown a, .profile-dropdown button { display:block; width:100%; padding:12px 14px; color:var(--white); background:transparent; border:0; text-align:left; text-decoration:none; font:inherit; font-size:13px; cursor:pointer; }
        .profile-dropdown a:hover, .profile-dropdown button:hover { background:rgba(255,106,26,.12); }
        .notifications-dropdown { width:340px; max-width:calc(100vw - 32px); }
        .dropdown-head { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-bottom:1px solid var(--line); gap:8px; }
        .dropdown-head strong { font-size:12px; letter-spacing:.08em; text-transform:uppercase; }
        .settings-link { font-size:11px; color:var(--orange); text-decoration:none; font-weight:700; }
        .settings-link:hover { opacity:0.8; }
        .dropdown-head button { border:0; background:transparent; color:var(--orange); font-size:11px; cursor:pointer; font-weight:800; }
        .dropdown-head button:hover { opacity:0.8; }
        .dropdown-body { max-height:320px; overflow-y:auto; }
        .notification-item { display:grid; grid-template-columns:9px 1fr; gap:10px; padding:11px 14px; color:var(--white); border-bottom:1px solid var(--line); cursor:pointer; transition:background .12s; }
        .notification-item:hover { background:rgba(255,255,255,.03); }
        .notification-item.unread { background:rgba(255,106,26,.08); }
        .notification-item.unread:hover { background:rgba(255,106,26,.14); }
        .notification-dot { width:8px; height:8px; border-radius:999px; margin-top:5px; background:var(--orange); flex-shrink:0; }
        .notification-dot.type-danger { background:#ff4757; }
        .notification-dot.type-success { background:var(--good); }
        .notification-dot.type-warning { background:var(--warn); }
        .notification-content strong, .notification-content a, .notification-content small, .notification-content em { display:block; }
        .notification-content strong, .notification-content a { font-size:12px; color:var(--white); text-decoration:none; font-weight:800; }
        .notification-content a:hover { color:var(--orange); }
        .notification-content small { color:var(--muted); font-size:11px; line-height:1.35; margin-top:2px; }
        .notification-content em { color:var(--muted-2); font-size:10px; margin-top:5px; font-style:normal; }
        .dropdown-empty { padding:22px 14px; color:var(--muted-2); text-align:center; font-size:12px; }
        .page-action-strip { display:flex; justify-content:flex-end; gap:10px; padding:12px 28px 0; }

        .image-uploader { display: flex; flex-direction: column; gap: 8px; }
        .image-uploader-head { display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .image-uploader-label { color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .image-uploader-hint { color: var(--muted-2); font-size: 10px; font-weight: 700; }
        .image-uploader-drop { position:relative; display:flex; align-items:center; justify-content:center; min-height:136px; border:1px dashed var(--line-2); border-radius:8px; background:rgba(255,255,255,.03); overflow:hidden; cursor:pointer; }
        .image-uploader-wide .image-uploader-drop { aspect-ratio: 851 / 315; min-height: 120px; }
        .image-uploader-square .image-uploader-drop { aspect-ratio: 1; min-height: 140px; }
        .image-uploader-logo .image-uploader-drop { width:112px; height:112px; min-height:112px; }
        .image-uploader-drop:hover { border-color: var(--orange); background:rgba(255,106,26,.08); }
        .image-uploader-drop img { width:100%; height:100%; object-fit:cover; display:block; }
        .image-uploader-logo .image-uploader-drop img { object-fit:contain; padding:8px; }
        .image-uploader-empty { color:var(--muted-2); font-size:12px; font-weight:800; }
        .image-uploader-drop input, .image-uploader-button input { position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }
        .image-uploader-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .image-uploader-button { position:relative; height:32px; padding:0 12px; border:1px solid var(--line-2); border-radius:7px; background:rgba(255,255,255,.04); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; }
        .image-uploader-button:hover { border-color:var(--orange); background:rgba(255,106,26,.14); }
        .image-uploader-button.danger { color:#ff6b7a; }
        .image-uploader-loading { color:var(--orange); font-size:11px; font-weight:800; }
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
            
            {{-- Active line selector (only shown when an agent is in session) --}}
            @php
                $sessionAgentId = session('active_agent_id');
                $sessionLineId  = session('active_line_id');
                $sidebarLineAgent = $currentLineAgent ?? null;
                $sidebarCan = function (array $permissions) use ($sessionAgentId, $sidebarLineAgent): bool {
                    if (! $sessionAgentId) {
                        return true;
                    }

                    if (! $sidebarLineAgent) {
                        return false;
                    }

                    foreach ($permissions as $permission) {
                        if ($sidebarLineAgent->hasPermission($permission)) {
                            return true;
                        }
                    }

                    return false;
                };
                $sidebarCanEditHome = $sidebarCan([\App\Support\Permissions::HOME_EDIT]);
                $allLines = $sessionAgentId
                    ? \App\Models\LineAgent::with('line')
                        ->where('agent_id', $sessionAgentId)
                        ->where('is_active', true)
                        ->get()
                        ->pluck('line')
                    : \App\Models\Line::where('status', 'active')->get();
            @endphp
            @if($allLines->count() > 0)
            <div class="sidebar-line-selector">
                <div class="sidebar-line-label">LÃNEA ACTIVA</div>
                <form method="POST" id="line-selector-form">
                    @csrf
                    <select class="sidebar-line-select" onchange="switchLine(this.value)">
                        @foreach($allLines as $sl)
                        <option value="{{ $sl->id }}" {{ $sessionLineId == $sl->id ? 'selected' : '' }}>
                            {{ $sl->name }}
                        </option>
                        @endforeach
                        @if(!$sessionAgentId)
                        <option value="0" {{ !$sessionLineId ? 'selected' : '' }}>Todas las lÃ­neas</option>
                        @endif
                    </select>
                </form>
            </div>
            @endif

            <div class="sidebar-section">DASHBOARD</div>

            <a href="{{ route('dashboard') }}" wire:navigate class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sidebar-item-icon">â—</span> Overview
            </a>

            <div class="sidebar-section">GESTION</div>

            @if($sidebarCan([\App\Support\Permissions::USER_READ, \App\Support\Permissions::USER_UPDATE, \App\Support\Permissions::USER_BLOCK]))
            <a href="{{ route('clientes') }}" wire:navigate class="sidebar-item {{ request()->routeIs('clientes') ? 'active' : '' }}">
                <span class="sidebar-item-icon">●</span> Clientes
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::AGENT_CREATE, \App\Support\Permissions::AGENT_ASSIGN, \App\Support\Permissions::AGENT_UPDATE, \App\Support\Permissions::AGENT_PERMISSIONS]))
            <a href="{{ route('agentes') }}" wire:navigate class="sidebar-item {{ request()->routeIs('agentes') ? 'active' : '' }}">
                <span class="sidebar-item-icon">⌘</span> Agentes
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::LINE_READ, \App\Support\Permissions::LINE_VIEW, \App\Support\Permissions::LINE_CREATE, \App\Support\Permissions::LINE_EDIT_BASIC, \App\Support\Permissions::LINE_EDIT_CONTACTS, \App\Support\Permissions::LINE_EDIT_BRANDING]))
            <a href="{{ route('lineas') }}" wire:navigate class="sidebar-item {{ request()->routeIs('lineas') ? 'active' : '' }}">
                <span class="sidebar-item-icon">☎</span> Lineas
            </a>
            @endif

            @if(! $sessionAgentId)
            <a href="{{ route('platforms.master') }}" wire:navigate class="sidebar-item {{ request()->routeIs('platforms.master*') ? 'active' : '' }}">
                <span class="sidebar-item-icon">▣</span> Plataformas
            </a>
            @endif

            <div class="sidebar-section">CONTENIDO</div>

            @if($sidebarCanEditHome)
            <a href="{{ route('editor-home') }}" wire:navigate class="sidebar-item {{ request()->routeIs('editor-home') ? 'active' : '' }}">
                <span class="sidebar-item-icon">⌂</span> Editar Home
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::NEWS_READ, \App\Support\Permissions::NEWS_CREATE, \App\Support\Permissions::NEWS_UPDATE, \App\Support\Permissions::NEWS_DELETE]))
            <a href="{{ route('novedades') }}" wire:navigate class="sidebar-item {{ request()->routeIs('novedades') ? 'active' : '' }}">
                <span class="sidebar-item-icon">✎</span> Novedades
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::LINE_EDIT_BRANDING, \App\Support\Permissions::LINE_EDIT_BASIC]))
            <a href="{{ route('banners') }}" wire:navigate class="sidebar-item {{ request()->routeIs('banners') ? 'active' : '' }}">
                <span class="sidebar-item-icon">▰</span> Banners & Notif.
            </a>
            @endif

            <div class="sidebar-section">OPERACION</div>

            @if($sidebarCan([\App\Support\Permissions::BONO_READ, \App\Support\Permissions::BONO_CREATE, \App\Support\Permissions::BONO_UPDATE, \App\Support\Permissions::BONO_DELETE]))
            <a href="{{ route('bonos') }}" wire:navigate class="sidebar-item {{ request()->routeIs('bonos') ? 'active' : '' }}">
                <span class="sidebar-item-icon">◇</span> Bonos
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::LINE_EDIT_BASIC]))
            <a href="{{ route('ventas') }}" wire:navigate class="sidebar-item {{ request()->routeIs('ventas') ? 'active' : '' }}">
                <span class="sidebar-item-icon">$</span> Ventas
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::SORTEO_READ, \App\Support\Permissions::SORTEO_CREATE, \App\Support\Permissions::SORTEO_UPDATE, \App\Support\Permissions::SORTEO_DELETE]))
            <a href="{{ route('sorteos') }}" wire:navigate class="sidebar-item {{ request()->routeIs('sorteos') ? 'active' : '' }}">
                <span class="sidebar-item-icon">◎</span> Sorteos
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::TICKET_READ, \App\Support\Permissions::TICKET_UPDATE, \App\Support\Permissions::TICKET_CLOSE]))
            <a href="{{ route('tickets') }}" wire:navigate class="sidebar-item {{ request()->routeIs('tickets') ? 'active' : '' }}">
                <span class="sidebar-item-icon">✉</span> Tickets
            </a>
            @endif

            <div class="sidebar-section">SISTEMA</div>

            @if(! $sessionAgentId)
            <a href="{{ route('settings') }}" wire:navigate class="sidebar-item {{ request()->routeIs('settings*') ? 'active' : '' }}">
                <span class="sidebar-item-icon">⚙</span> Configuracion
            </a>
            @endif
            <div class="sidebar-spacer"></div>
        </aside>
        
        <main class="main">
            <div class="main-content">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </main>
    </div>
    
    @livewireScripts
    <script>
        function switchLine(lineId) {
            fetch('/session/line/' + lineId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                        ? document.querySelector('meta[name=csrf-token]').content
                        : '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            }).then(() => window.location.reload());
        }
    </script>
</body>
</html>
