<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">JUEGOS</h1>
            <p class="page-subtitle">Catálogo de juegos y proveedores</p>
        </div>
    </div>

    <style>
        .juegos-card { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 20px; padding: 22px; box-shadow: 0 12px 40px rgba(0,0,0,0.5); }
        .juegos-mini-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 18px; }
        .jms { padding: 14px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); }
        .jms-lbl { font-size: 10px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; }
        .jms-val { font-family: var(--font-display); font-size: 26px; margin-top: 4px; }
        .juegos-filters { display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
        .filter-pill { height: 30px; padding: 0 14px; border-radius: 999px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
        .filter-pill.active { background: var(--orange); color: #190702; border: none; }
        .filter-pill.inactive { background: rgba(255,255,255,0.04); color: #fff; border: 1px solid var(--line-2); }
        .filter-pill.inactive:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }
        .juegos-thead { display: grid; grid-template-columns: 50px 80px 2fr 1.5fr 80px 90px 90px 110px 90px; gap: 12px; font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; padding: 8px 0; border-bottom: 1px solid var(--line); }
        .juegos-row { display: grid; grid-template-columns: 50px 80px 2fr 1.5fr 80px 90px 90px 110px 90px; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--line); align-items: center; font-size: 12px; }
        .juegos-row:last-child { border-bottom: none; }
        .game-thumb { width: 56px; height: 40px; border-radius: 6px; border: 1px solid var(--line-2); }
        .badge-live-hot { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,106,26,0.15); color: var(--orange); white-space: nowrap; }
        .badge-live { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); white-space: nowrap; }
        .badge-paused-g { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); white-space: nowrap; }
    </style>

    @if (session()->has('message'))
        <div style="background: rgba(37,196,107,0.12); border: 1px solid var(--good); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: var(--good); font-size: 13px; font-weight: 700;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div>
            <div style="font-size: 11px; color: var(--muted); letter-spacing: 0.12em; font-weight: 700;">OPERACIÓN</div>
            <div style="font-family: var(--font-display); font-size: 32px; margin-top: 2px; letter-spacing: 0.02em;">Catálogo de Juegos</div>
        </div>
    </div>

    <div class="juegos-card">
        <div style="display: flex; justify-content: space-between; margin-bottom: 14px;">
            <p style="font-size: 11px; color: var(--muted); margin: 4px 0 0;">1.247 juegos · 12 proveedores · destacados editables</p>
            <div style="display: flex; gap: 8px;">
                <button class="btn-ghost" style="height:32px;padding:0 12px;font-size:11px;font-weight:700;">Importar de proveedor ▾</button>
                <button class="btn-primary" style="height:32px;padding:0 14px;font-size:11px;">+ Juego custom</button>
            </div>
        </div>

        <div class="juegos-mini-stats">
            <div class="jms">
                <div class="jms-lbl">TOTAL</div>
                <div class="jms-val">1.247</div>
            </div>
            <div class="jms">
                <div class="jms-lbl">ACTIVOS</div>
                <div class="jms-val" style="color: var(--good);">1.182</div>
            </div>
            <div class="jms">
                <div class="jms-lbl">PAUSADOS</div>
                <div class="jms-val">42</div>
            </div>
            <div class="jms">
                <div class="jms-lbl">EN JACKPOT</div>
                <div class="jms-val">23</div>
            </div>
            <div class="jms">
                <div class="jms-lbl">CUSTOM RP</div>
                <div class="jms-val" style="color: var(--orange);">8</div>
            </div>
        </div>

        <div class="juegos-filters">
            <button class="filter-pill active">Todos</button>
            <button class="filter-pill inactive">Slots</button>
            <button class="filter-pill inactive">Vivo</button>
            <button class="filter-pill inactive">Mesa</button>
            <button class="filter-pill inactive">Crash</button>
            <button class="filter-pill inactive">Jackpots</button>
            <button class="filter-pill inactive">Custom RP</button>
            <div style="flex:1;"></div>
            <div style="height:30px;padding:0 12px;display:flex;align-items:center;gap:8px;border-radius:999px;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);font-size:11px;color:var(--muted);">
                🔍 Buscar juego...
            </div>
        </div>

        <div class="juegos-thead">
            <div></div><div></div><div>Juego</div><div>Proveedor</div><div>RTP</div><div>Jugadas/d</div><div>GGR/mes</div><div>Estado</div><div></div>
        </div>
        @php
        $juegos = [
            ['rank' => 1, 'star' => true, 'name' => 'Inferno Slot', 'prov' => 'PicanteGames', 'rtp' => '96.4%', 'plays' => '12.4K', 'ggr' => '$420K', 'badge' => 'live-hot', 'hue' => 0],
            ['rank' => 2, 'star' => true, 'name' => 'Aviator', 'prov' => 'Spribe', 'rtp' => '97.0%', 'plays' => '10.2K', 'ggr' => '$310K', 'badge' => 'live', 'hue' => 40],
            ['rank' => 3, 'star' => false, 'name' => 'Ruleta Vivo', 'prov' => 'Evolution', 'rtp' => '97.3%', 'plays' => '8.4K', 'ggr' => '$285K', 'badge' => 'live', 'hue' => 80],
            ['rank' => 4, 'star' => false, 'name' => 'Crash X100', 'prov' => 'Spribe', 'rtp' => '96.8%', 'plays' => '7.1K', 'ggr' => '$240K', 'badge' => 'live', 'hue' => 120],
            ['rank' => 5, 'star' => false, 'name' => 'Sweet Spice', 'prov' => 'PragmaticPlay', 'rtp' => '96.5%', 'plays' => '5.8K', 'ggr' => '$185K', 'badge' => 'live', 'hue' => 160],
            ['rank' => 6, 'star' => false, 'name' => 'Mega Fortune', 'prov' => 'NetEnt', 'rtp' => '96.6%', 'plays' => '3.2K', 'ggr' => '$98K', 'badge' => 'live-hot', 'hue' => 200],
            ['rank' => 7, 'star' => false, 'name' => 'Wild West Gold', 'prov' => 'PragmaticPlay', 'rtp' => '96.5%', 'plays' => '2.4K', 'ggr' => '$74K', 'badge' => 'live', 'hue' => 240],
            ['rank' => 8, 'star' => false, 'name' => 'Beta Slot Test', 'prov' => 'PicanteGames', 'rtp' => '–', 'plays' => '0', 'ggr' => '–', 'badge' => 'paused', 'hue' => 280],
        ];
        @endphp
        @foreach ($juegos as $g)
        <div class="juegos-row">
            <div style="display:flex;align-items:center;gap:4px;">
                @if($g['star'])<span style="font-size:14px;">⭐</span>@endif
                <span style="font-family:var(--font-mono);color:var(--muted);font-size:10px;">#{{ $g['rank'] }}</span>
            </div>
            <div class="game-thumb" style="background: radial-gradient(circle at 30% 20%, hsl({{ $g['hue'] }}, 60%, 30%), #150508 80%);"></div>
            <strong style="font-size:13px;">{{ $g['name'] }}</strong>
            <div style="color:var(--muted);">{{ $g['prov'] }}</div>
            <div>{{ $g['rtp'] }}</div>
            <div style="color:var(--muted);">{{ $g['plays'] }}</div>
            <strong style="color:var(--orange);font-family:var(--font-display);font-size:14px;">{{ $g['ggr'] }}</strong>
            <div>
                @if($g['badge'] === 'live-hot')
                    <span class="badge-live-hot">● Live · Hot</span>
                @elseif($g['badge'] === 'live')
                    <span class="badge-live">● Live</span>
                @else
                    <span class="badge-paused-g">● Pausado</span>
                @endif
            </div>
            <button class="btn-ghost" style="height:26px;padding:0 10px;font-size:10px;font-weight:700;">Editar</button>
        </div>
        @endforeach
    </div>
</div>
