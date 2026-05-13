<main class="fe-container">
    <style>
        .public-hero { display:grid; grid-template-columns:minmax(0, 1.4fr) minmax(280px, .8fr); gap:24px; align-items:stretch; }
        .public-kicker { color:var(--orange); font-size:12px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
        .public-title { font-family:var(--font-display); font-size:58px; line-height:.95; letter-spacing:.02em; margin:8px 0 12px; }
        .public-copy { color:var(--muted); font-size:15px; line-height:1.55; max-width:620px; }
        .public-meta { display:flex; gap:10px; flex-wrap:wrap; margin-top:20px; }
        .public-chip { border:1px solid var(--line-warm); background:rgba(255,106,26,.08); color:var(--orange-2); border-radius:999px; padding:7px 12px; font-size:12px; font-weight:800; }
        .public-panel { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg,#170b0b,#0f0707); padding:22px; }
        .public-panel-title { font-family:var(--font-display); font-size:26px; letter-spacing:.03em; margin-bottom:12px; }
        .prize-list { display:grid; gap:10px; margin-top:16px; }
        .prize-item { display:flex; align-items:center; justify-content:space-between; gap:12px; border:1px solid var(--line); border-radius:8px; background:rgba(255,255,255,.03); padding:12px; }
        .prize-pos { color:var(--orange); font-weight:900; font-size:12px; letter-spacing:.08em; text-transform:uppercase; }
        .prize-name { font-weight:800; font-size:14px; }
        .number-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(78px,1fr)); gap:8px; margin-top:14px; }
        .number-card { border:1px solid var(--line-warm); border-radius:8px; background:rgba(255,106,26,.07); padding:12px; text-align:center; }
        .number-val { font-family:var(--font-display); color:var(--orange); font-size:26px; line-height:1; }
        .number-lbl { color:var(--muted-2); font-size:9px; font-weight:800; letter-spacing:.1em; margin-top:5px; }
        .public-section { margin-top:24px; }
        .empty-state { color:var(--muted); font-size:13px; line-height:1.45; padding:18px; border:1px dashed var(--line-2); border-radius:8px; text-align:center; }
        @media (max-width:840px) {
            .public-hero { grid-template-columns:1fr; }
            .public-title { font-size:42px; }
        }
    </style>

    <section class="public-hero">
        <div class="public-panel">
            <div class="public-kicker">RED PICANTES</div>
            <h1 class="public-title">{{ $activeRaffle?->title ?? 'Sorteos y novedades' }}</h1>
            <p class="public-copy">
                {{ $activeRaffle?->description ?? 'Muy pronto vas a poder ver aca los datos publicos del sistema, sorteos activos, premios y resultados.' }}
            </p>

            @if($activeRaffle)
                <div class="public-meta">
                    <span class="public-chip">Activo</span>
                    <span class="public-chip">Finaliza {{ $activeRaffle->end_date->format('d/m/Y H:i') }}</span>
                    @if($activeRaffle->lines->count())
                        <span class="public-chip">{{ $activeRaffle->lines->pluck('name')->join(', ') }}</span>
                    @endif
                </div>
            @else
                <div class="public-meta">
                    <span class="public-chip">Frontend publico</span>
                    <span class="public-chip">Panel en /admin</span>
                </div>
            @endif
        </div>

        <aside class="public-panel">
            <div class="public-panel-title">Premios</div>
            @if($activeRaffle && ! empty($activeRaffle->prizes))
                <div class="prize-list">
                    @foreach($activeRaffle->prizes as $prize)
                        <div class="prize-item">
                            <div>
                                <div class="prize-pos">{{ $prize['position'] ?? $loop->iteration }} puesto</div>
                                <div class="prize-name">{{ $prize['name'] ?? 'Premio' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">No hay premios publicados por ahora.</div>
            @endif
        </aside>
    </section>

    @auth
        @if($activeRaffle)
            <section class="public-section public-panel">
                <div class="public-panel-title">Mis numeros</div>
                @if($myNumbers->count())
                    <div class="number-grid">
                        @foreach($myNumbers as $number)
                            <div class="number-card">
                                <div class="number-val">{{ $number->number }}</div>
                                <div class="number-lbl">NUMERO</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Todavia no tenes numeros asignados para este sorteo.</div>
                @endif
            </section>
        @endif
    @endauth

    @if($endedRaffle)
        <section class="public-section public-panel">
            <div class="public-panel-title">Ultimo resultado</div>
            <div class="prize-item">
                <div>
                    <div class="prize-pos">{{ $endedRaffle->title }}</div>
                    <div class="prize-name">Numero ganador: {{ $endedRaffle->winner_number ?? 'sin cargar' }}</div>
                </div>
                @if($endedRaffle->winner)
                    <span class="public-chip">{{ $endedRaffle->winner->name }}</span>
                @endif
            </div>
        </section>
    @endif
</main>
