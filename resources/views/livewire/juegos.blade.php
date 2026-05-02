<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">CATÁLOGO DE JUEGOS</h1>
            <p class="page-subtitle">Gestión de juegos, categorías y proveedores</p>
        </div>
        <button class="btn-primary"><span>+</span> Agregar juego</button>
    </div>

    <div class="content" style="padding: 0 28px 28px;">
        <div class="filters-row">
            <input type="text" class="search-input" placeholder="Buscar juegos...">
            <select class="filter-select"><option>Todas las categorías</option><option>Slots</option><option>Live Casino</option><option>Sports</option></select>
            <select class="filter-select"><option>Todos los proveedores</option></select>
        </div>

        <div class="games-grid">
            @for($i = 1; $i <= 12; $i++)
            <div class="game-card">
                <div class="game-thumb"></div>
                <div class="game-info">
                    <div class="game-name">Game Slot {{ $i }}</div>
                    <div class="game-provider">Provider {{ chr(64 + $i) }}</div>
                    <div class="game-status active">● Activo</div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .filters-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .search-input { padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-warm); color: var(--white); font-size: 13px; min-width: 200px; }
        .filter-select { padding: 10px 16px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-warm); color: var(--white); font-size: 13px; }
        .games-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        @media (max-width: 1200px) { .games-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .games-grid { grid-template-columns: repeat(2, 1fr); } }
        .game-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; overflow: hidden; }
        .game-thumb { height: 120px; background: linear-gradient(135deg, #2a1414, #1a0d0d); }
        .game-info { padding: 12px; }
        .game-name { font-weight: 700; font-size: 13px; }
        .game-provider { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .game-status { font-size: 10px; font-weight: 700; margin-top: 8px; color: var(--good); }
    </style>
</div>