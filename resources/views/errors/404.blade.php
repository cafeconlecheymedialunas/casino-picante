<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Página no encontrada - RED PICANTES</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --black: #0a0606;
            --orange: #ff6a1a;
            --amber: #ffb347;
            --white: #ffffff;
            --muted: rgba(255,255,255,0.62);
            --muted-2: rgba(255,255,255,0.42);
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Manrope', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: var(--black); color: var(--white); font-family: var(--font-body); height: 100%; }
        
        .btn-primary, .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 18px;
            border-radius: 9px;
            border: 1px solid rgba(255,106,26,0.55);
            background: linear-gradient(135deg, rgba(255,106,26,0.18), rgba(255,106,26,0.06));
            color: var(--orange);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: var(--font-body);
            white-space: nowrap;
            flex-shrink: 0;
            text-decoration: none;
        }
        .btn-primary:hover, .btn-add:hover {
            background: linear-gradient(135deg, rgba(255,106,26,0.28), rgba(255,106,26,0.14));
            border-color: var(--orange);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(255,106,26,0.22);
        }
        .btn-ghost {
            background: rgba(255,255,255,0.04);
            color: #fff;
            border: 1px solid var(--muted-2);
            border-radius: 999px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }
    </style>
</head>
<body>
    <div class="dash-shell">
        <!-- Sidebar (hidden for error pages to focus on content) -->
        <aside class="sidebar" style="display: none;">
            <!-- Sidebar content hidden -->
        </aside>
        
        <main class="main">
            <div class="main-content">
                <div class="wrap-content" style="padding: 0 2%; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
                    <div class="page-container" style="text-align: center; width: 100%; max-width: 500px;">
                        <div class="error-code" style="
                            font-family: var(--font-display);
                            font-size: 8rem;
                            font-weight: 800;
                            background: linear-gradient(135deg, var(--orange), var(--amber));
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            margin-bottom: 1.5rem;
                            line-height: 0.9;
                            display: inline-block;
                        ">404</div>
                        
                        <h1 class="page-title" style="
                            font-family: var(--font-display);
                            font-size: 2.5rem;
                            margin-bottom: 1rem;
                            color: var(--orange);
                        ">Página no encontrada</h1>
                        
                        <p class="page-subtitle" style="
                            color: var(--muted);
                            font-size: 1.125rem;
                            margin-bottom: 2rem;
                            max-width: 400px;
                            line-height: 1.5;
                        ">
                            La página que estás buscando no existe o ha sido movida.
                            Por favor, verifica la URL o regresa al panel principal.
                        </p>
                        
                        <div class="module-top-bar" style="justify-content: center; margin-top: 2rem;">
                            <a href="{{ route('dashboard') }}" class="btn-primary">
                                <i class="fa-solid fa-home"></i> Ir al Dashboard
                            </a>
                            <a href="javascript:history.back()" class="btn-ghost" style="margin-left: 1rem;">
                                <i class="fa-solid fa-arrow-left"></i> Volver atrás
                            </a>
                        </div>
                        
                        <div class="error-footer" style="
                            margin-top: 3rem;
                            color: var(--muted-2);
                            font-size: 0.875rem;
                        ">
                            © {{ now()->year }} RED PICANTES. Todos los derechos reservados.
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    @livewireScripts
</body>
</html>