<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RED PICANTES' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --black: #0a0606; --black-2: #120909; --black-3: #1a0d0d;
            --line: rgba(255,255,255,0.08); --line-2: rgba(255,255,255,0.14);
            --line-warm: rgba(255,120,50,0.22);
            --orange: #ff6a1a; --orange-2: #ff8a3d; --orange-deep: #e6580f;
            --amber: #ffb347; --white: #ffffff;
            --muted: rgba(255,255,255,0.62); --muted-2: rgba(255,255,255,0.42);
            --good: #25c46b; --warn: #ffb347; --bad: #ff4757;
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Manrope', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --r-sm: 10px; --r-md: 14px; --r-lg: 20px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: var(--black); color: var(--white); font-family: var(--font-body); min-height: 100%; }

        .fe-navbar {
            background: linear-gradient(180deg, #0d0707, #0a0606);
            border-bottom: 1px solid var(--line);
            padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
            height: 56px; position: sticky; top: 0; z-index: 100;
        }
        .fe-logo { font-family: var(--font-display); font-size: 24px; color: var(--orange); text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .fe-nav-links { display: flex; align-items: center; gap: 6px; }
        .fe-nav-link {
            padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 600;
            color: var(--muted); text-decoration: none; transition: all 0.2s;
        }
        .fe-nav-link:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .fe-nav-link.active { color: var(--orange); background: rgba(255,106,26,0.1); }
        .fe-nav-user { display: flex; align-items: center; gap: 10px; }
        .fe-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; color: #190702; font-weight: 800; font-size: 13px; }
        .fe-user-name { font-size: 13px; font-weight: 700; }
        .fe-container { max-width: 960px; margin: 0 auto; padding: 32px 24px; }
        .fe-card { background: linear-gradient(180deg, #170b0b, #0f0707); border: 1px solid var(--line); border-radius: 20px; padding: 24px; }
        .fe-btn-primary { background: linear-gradient(180deg, var(--orange-2), var(--orange) 60%, var(--orange-deep)); color: #190702; border: none; border-radius: 999px; font-weight: 800; padding: 10px 22px; cursor: pointer; font-size: 13px; box-shadow: 0 8px 24px rgba(255,106,26,0.4); transition: all 0.2s; }
        .fe-btn-primary:hover { transform: translateY(-1px); }
        .fe-btn-ghost { background: rgba(255,255,255,0.04); color: #fff; border: 1px solid var(--line-2); border-radius: 999px; padding: 8px 18px; cursor: pointer; font-size: 12px; transition: all 0.2s; }
        .fe-btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--black-2); }
        ::-webkit-scrollbar-thumb { background: var(--orange); border-radius: 3px; }
        * { scrollbar-width: thin; scrollbar-color: var(--orange) var(--black-2); }

        /* ── Global select styles ── */
        select,
        select.select,
        select.filter-select,
        select.form-input,
        select.form-select,
        select.lns-select,
        select.sidebar-line-select,
        select.contact-type,
        select.repeater-type {
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
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ff6a1a' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px 12px;
            background-origin: content-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        select:hover,
        select.select:hover,
        select.filter-select:hover,
        select.form-input:hover,
        select.form-select:hover,
        select.lns-select:hover,
        select.sidebar-line-select:hover,
        select.contact-type:hover,
        select.repeater-type:hover { border-color: var(--orange); }
        select:focus,
        select.select:focus,
        select.filter-select:focus,
        select.form-input:focus,
        select.form-select:focus,
        select.lns-select:focus,
        select.sidebar-line-select:focus,
        select.contact-type:focus,
        select.repeater-type:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,0.15); }

        select option {
            background-color: #120909;
            color: var(--white);
            padding: 8px 12px;
        }
        select option:hover,
        select option:focus {
            background-color: rgba(255,106,26,0.12);
            color: var(--orange-2);
        }
        select option:checked,
        select option[selected] {
            background-color: rgba(255,106,26,0.25);
            color: var(--orange);
        }
        select option:disabled {
            color: var(--muted-2);
        }
        select option[value=""] {
            color: var(--muted-2);
        }
    </style>
</head>
<body>
    <nav class="fe-navbar">
        <a href="{{ url('/') }}" wire:navigate class="fe-logo">
            <svg width="20" height="20" viewBox="0 0 40 40">
                <circle cx="20" cy="22" r="16" fill="rgba(255,106,26,0.35)" opacity="0.6"/>
                <path d="M14 12 C 12 18, 12 26, 18 32 C 24 36, 32 32, 33 24 C 34 18, 28 14, 22 14 C 18 14, 16 13, 14 12 Z" fill="#ff6a1a"/>
            </svg>
            RED PICANTES
        </a>
        <div class="fe-nav-links">
            <a href="{{ route('sorteo.publico') }}" wire:navigate class="fe-nav-link {{ request()->routeIs('sorteo.publico') ? 'active' : '' }}">🎯 Sorteo</a>
            @auth
            <a href="{{ route('perfil') }}" wire:navigate class="fe-nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}">Mi Perfil</a>
            @endauth
        </div>
        <div class="fe-nav-user">
            @auth
            <div class="fe-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <span class="fe-user-name">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="fe-btn-ghost" style="padding:6px 12px;font-size:11px;">Salir</button>
            </form>
            @else
            <a href="{{ route('login') }}" wire:navigate class="fe-btn-ghost" style="padding:6px 14px;">Iniciar sesión</a>
            @endauth
        </div>
    </nav>

    {{ $slot }}

    @livewireScripts
</body>
</html>
