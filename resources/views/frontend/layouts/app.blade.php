<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RED PICANTES' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --black:#0a0606; --black-2:#120909; --black-3:#1a0d0d;
            --line:rgba(255,255,255,.08); --line-2:rgba(255,255,255,.14); --line-warm:rgba(255,120,50,.22);
            --orange:#ff6a1a; --orange-2:#ff8a3d; --orange-deep:#e6580f; --amber:#ffb347;
            --white:#fff; --muted:rgba(255,255,255,.62); --muted-2:rgba(255,255,255,.42);
            --good:#25c46b; --bad:#ff4757;
            --font-display:'Bebas Neue', Impact, sans-serif;
            --font-body:'Manrope', system-ui, sans-serif;
            --font-mono:'JetBrains Mono', ui-monospace, monospace;
            --r-sm:8px; --r-md:12px; --r-lg:18px; --r-xl:26px;
            --glow-orange:0 12px 36px rgba(255,106,26,.36), 0 0 0 1px rgba(255,170,80,.18) inset;
        }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body {
            margin:0; min-height:100vh; background:
                radial-gradient(70% 45% at 80% -5%, rgba(255,106,26,.38), transparent 60%),
                radial-gradient(50% 35% at -10% 28%, rgba(255,138,61,.16), transparent 60%),
                var(--black);
            color:var(--white); font-family:var(--font-body); overflow-x:hidden;
        }
        a { color:inherit; }
        .fe-site-main { min-height:70vh; }
        .fe-shell { width:min(1220px, calc(100% - 40px)); margin:0 auto; }
        .fe-btn {
            display:inline-flex; align-items:center; justify-content:center; gap:8px;
            height:40px; padding:0 18px; border-radius:999px; font-size:13px; font-weight:800;
            text-decoration:none; border:1px solid transparent; cursor:pointer; white-space:nowrap;
        }
        .fe-btn.primary { background:linear-gradient(180deg,var(--orange-2),var(--orange) 62%,var(--orange-deep)); color:#190702; box-shadow:var(--glow-orange); }
        .fe-btn.ghost { background:rgba(255,255,255,.04); color:#fff; border-color:var(--line-2); }
        .fe-btn.ghost:hover { background:rgba(255,255,255,.08); border-color:var(--orange); }
        .fe-nav {
            position:sticky; top:0; z-index:50; border-bottom:1px solid rgba(255,255,255,.1);
            background:linear-gradient(180deg, rgba(10,6,6,.82), rgba(10,6,6,.58)); backdrop-filter:blur(16px);
        }
        .fe-nav-inner { height:68px; display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .fe-brand { display:flex; align-items:center; gap:10px; text-decoration:none; min-width:max-content; }
        .fe-brand-mark { width:32px; height:32px; border-radius:11px; background:linear-gradient(135deg,var(--orange),var(--amber)); box-shadow:0 10px 28px rgba(255,106,26,.34); position:relative; }
        .fe-brand-mark::after { content:""; position:absolute; inset:8px 7px 5px 10px; border-radius:60% 40% 55% 45%; background:#190702; opacity:.2; transform:rotate(-18deg); }
        .fe-brand-text { font-family:var(--font-display); font-size:30px; letter-spacing:.03em; line-height:1; }
        .fe-brand-text span { color:var(--orange); }
        .fe-nav-links { display:flex; align-items:center; justify-content:center; gap:26px; color:var(--muted); font-size:13px; font-weight:800; }
        .fe-nav-links a { text-decoration:none; }
        .fe-nav-links a:hover, .fe-nav-links a.active { color:var(--orange); }
        .fe-nav-actions { display:flex; align-items:center; gap:10px; }
        .fe-mobile-toggle { display:none; width:38px; height:38px; border-radius:8px; border:1px solid var(--line-2); background:rgba(255,255,255,.04); color:#fff; }
        .fe-mobile-menu { display:none; border-top:1px solid var(--line); padding:12px 0 16px; }
        .fe-mobile-menu a { display:block; padding:11px 0; color:var(--muted); text-decoration:none; font-weight:800; font-size:13px; }
        .fe-footer { margin-top:64px; border-top:1px solid var(--line); background:rgba(0,0,0,.22); }
        .fe-footer-grid { display:grid; grid-template-columns:1.5fr repeat(3, 1fr); gap:28px; padding:36px 0; }
        .fe-footer-title { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; margin-bottom:12px; }
        .fe-footer p, .fe-footer li { color:var(--muted); font-size:13px; line-height:1.55; }
        .fe-footer ul { list-style:none; padding:0; margin:0; display:grid; gap:8px; }
        .fe-footer-bottom { border-top:1px solid var(--line); padding:16px 0 22px; color:var(--muted-2); font-size:11px; display:flex; justify-content:space-between; gap:18px; flex-wrap:wrap; }
        .fe-section { padding:64px 0 0; }
        .fe-section-head { display:flex; align-items:end; justify-content:space-between; gap:18px; margin-bottom:20px; }
        .fe-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.18em; text-transform:uppercase; margin-bottom:7px; }
        .fe-title { font-family:var(--font-display); font-size:58px; line-height:.92; letter-spacing:.02em; margin:0; }
        .fe-title span { color:var(--orange); }
        .fe-subtitle { color:var(--muted); font-size:14px; line-height:1.55; margin:8px 0 0; max-width:520px; }
        .fe-card {
            border:1px solid var(--line); border-radius:var(--r-md);
            background:linear-gradient(180deg,#170b0b,#0f0707); box-shadow:0 16px 40px rgba(0,0,0,.32);
        }
        .fe-h-scroll { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(280px, 1fr); gap:14px; overflow-x:auto; overscroll-behavior-inline:contain; scroll-snap-type:inline mandatory; padding-bottom:8px; }
        .fe-h-scroll > * { scroll-snap-align:start; }
        .fe-h-scroll::-webkit-scrollbar { height:7px; }
        .fe-h-scroll::-webkit-scrollbar-track { background:var(--black-2); }
        .fe-h-scroll::-webkit-scrollbar-thumb { background:var(--orange); border-radius:999px; }
        @media (max-width: 860px) {
            .fe-shell { width:min(100% - 28px, 1180px); }
            .fe-nav-links, .fe-nav-actions { display:none; }
            .fe-mobile-toggle { display:inline-flex; align-items:center; justify-content:center; }
            .fe-mobile-menu.open { display:block; }
            .fe-footer-grid { grid-template-columns:1fr 1fr; }
            .fe-section-head { display:block; }
            .fe-title { font-size:40px; }
        }
        @media (max-width: 560px) {
            .fe-footer-grid { grid-template-columns:1fr; }
            .fe-h-scroll { grid-auto-columns:minmax(238px, 86vw); }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('frontend.partials.navbar')

    <main class="fe-site-main">
        {{ $slot }}
    </main>

    @include('frontend.partials.footer')

    @livewireScripts
    <script>
        window.toggleFrontendMenu = function () {
            document.querySelector('[data-fe-mobile-menu]')?.classList.toggle('open');
        };
    </script>
    @stack('scripts')
</body>
</html>
