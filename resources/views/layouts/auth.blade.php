<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
