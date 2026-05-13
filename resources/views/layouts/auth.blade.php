<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Ingresar — RED PICANTES</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --black: #0a0606; --black-2: #120909; --black-3: #1a0d0d;
            --line: rgba(255,255,255,0.08); --line-2: rgba(255,255,255,0.14);
            --orange: #ff6a1a; --amber: #ffb347; --white: #ffffff;
            --muted: rgba(255,255,255,0.62); --muted-2: rgba(255,255,255,0.42);
            --good: #25c46b;
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Manrope', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body { background:var(--black); color:var(--white); font-family:var(--font-body); min-height:100vh; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 7px;
            height: 38px; padding: 0 18px;
            border-radius: 9px; border: 1px solid rgba(255,106,26,0.55);
            background: linear-gradient(135deg, rgba(255,106,26,0.18), rgba(255,106,26,0.06));
            color: var(--orange); font-size: 13px; font-weight: 700;
            cursor: pointer; transition: all 0.2s; font-family: var(--font-body);
            white-space: nowrap; flex-shrink: 0; text-decoration: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(255,106,26,0.28), rgba(255,106,26,0.14));
            border-color: var(--orange);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(255,106,26,0.22);
        }
        .btn-ghost {
            background: rgba(255,255,255,0.04); color: #fff;
            border: 1px solid var(--line-2); border-radius: 999px;
            padding: 10px 20px; cursor: pointer; font-size: 12px; transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #fff !important;
            -webkit-box-shadow: 0 0 0px 1000px #1c0d0a inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        @media (max-width: 480px) {
            .btn-primary { height: 34px; font-size: 12px; padding: 0 14px; }
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
