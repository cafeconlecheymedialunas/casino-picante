@push('styles')
<style>
    .raffle-detail { padding:42px 0 0; }
    .raffle-arena { position:relative; overflow:hidden; border:1px solid rgba(255,106,26,.44); border-radius:10px; background:radial-gradient(62% 88% at 50% 0%, rgba(255,106,26,.18), transparent 62%), linear-gradient(180deg,#090302,#050202 72%,#030101); box-shadow:0 28px 80px rgba(0,0,0,.52), inset 0 0 36px rgba(255,106,26,.08); }
    .raffle-detail-gift { position:absolute; top:22px; width:160px; opacity:.78; filter:drop-shadow(0 20px 28px rgba(255,106,26,.32)); pointer-events:none; z-index:0; }
    .raffle-detail-gift.left { left:28px; transform:rotate(-7deg); }
    .raffle-detail-gift.right { right:28px; transform:scaleX(-1) rotate(-7deg); }
    .raffle-detail-head { position:relative; z-index:1; text-align:center; padding:30px 190px 12px; }
    .raffle-detail-title { font-family:var(--font-display); font-size:52px; line-height:.9; letter-spacing:.03em; margin:0; text-transform:uppercase; }
    .raffle-detail-title span { color:#ff3d12; }
    .raffle-detail-subtitle { margin:7px auto 0; max-width:760px; color:rgba(255,255,255,.62); font-size:13px; line-height:1.45; }
    .raffle-detail-clock { display:inline-flex; align-items:center; gap:9px; margin-top:14px; border:1px solid rgba(255,106,26,.58); border-radius:999px; background:rgba(255,106,26,.1); color:#fff; padding:9px 18px; font-size:12px; font-weight:900; text-transform:uppercase; }
    .raffle-detail-clock strong { color:var(--orange); font-family:var(--font-mono); }
    .raffle-actions { display:flex; align-items:center; justify-content:center; gap:10px; flex-wrap:wrap; margin-top:16px; }
    .raffle-status-note { max-width:680px; margin:12px auto 0; color:rgba(255,255,255,.66); font-size:13px; line-height:1.45; }
    .raffle-prize-board { position:relative; z-index:1; display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.28fr) minmax(0,1fr); gap:14px; align-items:end; padding:50px 24px 18px; }
    .raffle-prize-cell { min-height:138px; display:grid; grid-template-columns:78px minmax(0,.85fr) minmax(128px,1fr); align-items:center; gap:12px; border:1px solid rgba(255,106,26,.76); border-radius:8px; background:#0b0605; box-shadow:inset 0 0 22px rgba(255,106,26,.08), 0 0 18px rgba(255,106,26,.11); padding:12px 14px; overflow:hidden; }
    .raffle-prize-cell.main { min-height:150px; border-color:rgba(255,179,71,.82); background:#100705; transform:translateY(-12px); }
    .raffle-prize-rank { font-family:var(--font-display); color:var(--orange); font-size:96px; line-height:.8; text-align:center; text-shadow:0 0 20px rgba(255,106,26,.42); }
    .raffle-prize-cell.main .raffle-prize-rank { font-size:80px; color:#ff8a1f; }
    .raffle-prize-copy small { display:block; color:#fff; font-size:12px; font-weight:900; text-transform:uppercase; }
    .raffle-prize-copy strong { display:block; margin-top:5px; color:#fff; font-size:14px; line-height:1.18; }
    .raffle-prize-copy b { display:block; margin-top:5px; color:var(--orange); font-size:13px; }
    .raffle-prize-art { height:98px; border-radius:6px; background:#120807; overflow:hidden; display:flex; align-items:center; justify-content:center; }
    .raffle-prize-cell.main .raffle-prize-art { height:126px; }
    .raffle-prize-art img { width:100%; height:100%; object-fit:cover; display:block; }
    .raffle-prize-art span { font-family:var(--font-display); color:rgba(255,255,255,.13); font-size:34px; }
    .raffle-metrics { position:relative; z-index:1; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); width:min(780px, calc(100% - 48px)); margin:0 auto 16px; border:1px solid rgba(255,106,26,.24); border-radius:8px; background:#080403; overflow:hidden; }
    .raffle-metric { display:flex; align-items:center; justify-content:center; gap:12px; padding:14px 18px; border-left:1px solid rgba(255,106,26,.16); }
    .raffle-metric:first-child { border-left:0; }
    .raffle-metric i { color:var(--orange); font-size:20px; }
    .raffle-metric span { display:block; color:rgba(255,255,255,.5); font-size:10px; font-weight:900; text-transform:uppercase; }
    .raffle-metric strong { color:var(--orange); font-family:var(--font-mono); font-size:15px; }
    .raffle-table-wrap { position:relative; z-index:1; width:min(960px, calc(100% - 48px)); margin:0 auto 22px; }
    .raffle-table-title { display:flex; align-items:center; justify-content:center; gap:14px; color:#fff; font-size:12px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; margin:4px 0 8px; }
    .raffle-table-title::before, .raffle-table-title::after { content:""; height:1px; width:80px; background:linear-gradient(90deg,transparent,var(--orange),transparent); }
    .raffle-table { width:100%; border-collapse:separate; border-spacing:0 5px; font-size:12px; }
    .raffle-table th { color:rgba(255,255,255,.38); font-size:10px; font-weight:900; text-transform:uppercase; text-align:left; padding:0 12px 2px; }
    .raffle-table td { background:rgba(255,255,255,.035); border-top:1px solid rgba(255,106,26,.13); border-bottom:1px solid rgba(255,106,26,.13); padding:8px 12px; color:rgba(255,255,255,.78); font-weight:800; }
    .raffle-table td:first-child { border-left:1px solid rgba(255,106,26,.13); border-radius:7px 0 0 7px; width:58px; }
    .raffle-table td:last-child { border-right:1px solid rgba(255,106,26,.13); border-radius:0 7px 7px 0; color:var(--orange); }
    .raffle-medal { width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center; border-radius:999px; background:linear-gradient(180deg,#ff9b32,#ff5a10); color:#170604; font-family:var(--font-mono); font-weight:900; box-shadow:0 0 16px rgba(255,106,26,.38); }
    .raffle-table .top td { border-color:rgba(255,106,26,.58); background:rgba(255,106,26,.08); box-shadow:0 0 16px rgba(255,106,26,.12); }
    .raffle-user { display:flex; align-items:center; gap:8px; }
    .raffle-user i { color:var(--orange); }
    .raffle-note { color:rgba(255,255,255,.42); font-size:11px; text-align:center; padding-bottom:16px; }
    .raffle-note i { color:var(--orange); margin-right:6px; }
    .raffle-secondary { margin-top:34px; display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:16px; }
    .raffle-info-card { border:1px solid rgba(255,255,255,.08); border-radius:10px; background:linear-gradient(180deg,#140807,#0b0403); padding:18px; }
    .raffle-info-card h2 { font-family:var(--font-display); font-size:32px; line-height:1; margin:0 0 12px; letter-spacing:.02em; }
    .raffle-tags { display:flex; gap:8px; flex-wrap:wrap; }
    .raffle-tag { border:1px solid rgba(255,106,26,.24); border-radius:999px; color:rgba(255,255,255,.72); padding:6px 10px; font-size:11px; font-weight:900; text-transform:uppercase; }
    .raffle-tag.active { color:#190702; background:var(--orange); border-color:var(--orange); }
    .raffle-number-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(72px,1fr)); gap:8px; }
    .raffle-number { border:1px solid rgba(255,106,26,.32); border-radius:8px; background:rgba(255,106,26,.08); padding:10px; text-align:center; font-family:var(--font-mono); color:var(--orange); font-weight:900; }
    .raffle-empty { color:var(--muted); font-size:13px; line-height:1.45; padding:16px; border:1px dashed rgba(255,255,255,.14); border-radius:8px; text-align:center; }
    .raffle-modal-backdrop { position:fixed; inset:0; z-index:80; display:flex; align-items:center; justify-content:center; padding:22px; background:rgba(0,0,0,.72); backdrop-filter:blur(10px); }
    .raffle-modal { width:min(720px, 100%); max-height:min(760px, 90vh); overflow:auto; border:1px solid rgba(255,106,26,.38); border-radius:12px; background:linear-gradient(180deg,#160807,#070302); box-shadow:0 30px 90px rgba(0,0,0,.68); padding:20px; }
    .raffle-modal-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; }
    .raffle-modal h2 { font-family:var(--font-display); font-size:38px; line-height:1; margin:0; letter-spacing:.02em; }
    .raffle-modal-close { width:36px; height:36px; border-radius:999px; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.04); color:#fff; cursor:pointer; font-weight:900; }
    .raffle-full-prizes { display:grid; gap:10px; }
    .raffle-full-prize { display:grid; grid-template-columns:48px minmax(0,1fr) auto; gap:12px; align-items:center; border:1px solid rgba(255,106,26,.16); border-radius:9px; background:rgba(255,255,255,.035); padding:12px; }
    .raffle-full-prize span { color:var(--orange); font-family:var(--font-display); font-size:34px; line-height:1; }
    .raffle-full-prize strong { color:#fff; font-size:14px; }
    .raffle-full-prize small { color:var(--muted); font-size:11px; }
    .raffle-full-prize b { color:var(--orange); font-size:13px; }
    @media (max-width: 980px) {
        .raffle-detail-head { padding:30px 28px 10px; }
        .raffle-detail-gift { opacity:.18; }
        .raffle-prize-board { grid-template-columns:1fr; }
        .raffle-prize-cell, .raffle-prize-cell.main { transform:none; grid-template-columns:60px minmax(0,1fr) 124px; min-height:124px; }
        .raffle-prize-rank, .raffle-prize-cell.main .raffle-prize-rank { font-size:78px; }
        .raffle-secondary { grid-template-columns:1fr; }
    }
    @media (max-width: 640px) {
        .raffle-detail { padding-top:24px; }
        .raffle-detail-title { font-size:38px; }
        .raffle-prize-cell, .raffle-prize-cell.main { grid-template-columns:54px minmax(0,1fr); }
        .raffle-prize-art { grid-column:1 / -1; width:100%; }
        .raffle-metrics { grid-template-columns:1fr; }
        .raffle-metric { border-left:0; border-top:1px solid rgba(255,106,26,.16); }
        .raffle-metric:first-child { border-top:0; }
        .raffle-table-wrap { overflow-x:auto; }
        .raffle-table { min-width:620px; }
        .raffle-actions .fe-btn { width:100%; }
        .raffle-modal-backdrop { padding:12px; align-items:flex-start; }
        .raffle-modal { max-height:calc(100vh - 24px); padding:16px; }
        .raffle-modal-head { align-items:flex-start; }
        .raffle-modal h2 { font-size:31px; }
        .raffle-full-prize { grid-template-columns:42px minmax(0, 1fr); }
        .raffle-full-prize b { grid-column:2; }
    }
</style>
@endpush

@php
    $allPrizes = collect($raffle->prizes ?? [])->sortBy(fn ($prize) => (int) ($prize['position'] ?? 99))->values();
    $rankedPrizes = $allPrizes->take(3)->values();
    $podium = $rankedPrizes->count() === 3
        ? collect([2, 1, 3])->map(fn ($position) => $rankedPrizes->first(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1) === $position))->filter()->values()
        : $rankedPrizes;
    $remaining = $raffle->end_date?->isFuture()
        ? $raffle->end_date->diffForHumans(now(), ['parts' => 2, 'short' => true])
        : 'finalizado';
    $score = number_format($numbersCount * 125.10, 2, ',', '.');
@endphp

<section class="raffle-detail" x-data="{ showPrizes: false }">
    <div class="fe-shell">
        <div class="raffle-arena">
            <img class="raffle-detail-gift left" src="{{ asset('frontend/gift-box.webp') }}" alt="">
            <img class="raffle-detail-gift right" src="{{ asset('frontend/gift-box.webp') }}" alt="">

            <div class="raffle-detail-head">
                <h1 class="raffle-detail-title">{{ $raffle->title }}</h1>
                <p class="raffle-detail-subtitle">{{ $raffle->description ?: 'Suma participaciones jugando en las lineas activas y segui los premios publicados.' }}</p>
                <div class="raffle-detail-clock">
                    <i class="fa-solid fa-clock"></i>
                    @if($raffle->status === 'active' && ! $raffle->isFinished())
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span>Termina en:</span>
                            <div class="raffle-timer" data-raffle-countdown="{{ $raffle->end_date->toIso8601String() }}" style="display: flex; gap: 4px; min-width: auto; background: none; box-shadow: none; border: none; padding: 0;">
                                <div class="timer-unit" style="min-height: auto; padding: 2px 4px; background: rgba(255,106,26,0.1); border-color: rgba(255,106,26,0.2);"><span class="timer-val" data-unit="days" style="font-size: 14px;">00</span><span class="timer-label" style="font-size: 7px; margin-top: 2px;">D</span></div>
                                <div class="timer-unit" style="min-height: auto; padding: 2px 4px; background: rgba(255,106,26,0.1); border-color: rgba(255,106,26,0.2);"><span class="timer-val" data-unit="hours" style="font-size: 14px;">00</span><span class="timer-label" style="font-size: 7px; margin-top: 2px;">H</span></div>
                                <div class="timer-unit" style="min-height: auto; padding: 2px 4px; background: rgba(255,106,26,0.1); border-color: rgba(255,106,26,0.2);"><span class="timer-val" data-unit="minutes" style="font-size: 14px;">00</span><span class="timer-label" style="font-size: 7px; margin-top: 2px;">M</span></div>
                                <div class="timer-unit" style="min-height: auto; padding: 2px 4px; background: rgba(255,106,26,0.1); border-color: rgba(255,106,26,0.2);"><span class="timer-val" data-unit="seconds" style="font-size: 14px;">00</span><span class="timer-label" style="font-size: 7px; margin-top: 2px;">S</span></div>
                            </div>
                        </div>
                    @else
                        {{ $raffle->end_date?->isFuture() ? 'Termina en' : 'Estado' }}
                        <strong>{{ $remaining }}</strong>
                    @endif
                </div>
                @if(! $raffle->isFinished() && $raffle->status !== 'active')
                    <div class="raffle-status-note">Proximamente: registrate y enterate del proximo sorteo disponible.</div>
                @endif
                <div class="raffle-actions">
                    @if($allPrizes->count() > 3)
                        <button type="button" class="fe-btn ghost" @click="showPrizes = true">Ver premios</button>
                    @endif
                    @if($raffle->status === 'active')
                        @auth
                            <a href="{{ route('frontend.lines') }}" wire:navigate class="fe-btn primary">Suma puntos</a>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="fe-btn primary">Registrarme o iniciar sesion</a>
                        @endauth
                    @elseif(! $raffle->isFinished())
                        <a href="{{ route('login') }}" wire:navigate class="fe-btn primary">Registrarme o iniciar sesion</a>
                    @elseif(! auth()->check())
                        <a href="{{ route('login') }}" wire:navigate class="fe-btn primary">Registrarme o iniciar sesion</a>
                    @endif
                </div>
            </div>

            @if($podium->count())
                <div class="raffle-prize-board">
                    @foreach($podium as $index => $prize)
                        @php
                            $position = (int) ($prize['position'] ?? $index + 1);
                            $image = $prize['image'] ?? null;
                            $imageUrl = $image ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/storage/']) ? $image : asset('storage/'.$image)) : null;
                        @endphp
                        <article class="raffle-prize-cell {{ $position === 1 ? 'main' : '' }}">
                            <div class="raffle-prize-rank">{{ $position }}</div>
                            <div class="raffle-prize-copy">
                                <small>Premio {{ $position }}</small>
                                <strong>{{ $prize['name'] ?? 'Premio del sorteo' }}</strong>
                                @if(! empty($prize['amount']))
                                    <b>${{ number_format((float) $prize['amount'], 0, ',', '.') }}</b>
                                @endif
                            </div>
                            <div class="raffle-prize-art">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $prize['name'] ?? 'Premio' }}">
                                @else
                                    <span>PREMIO</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="raffle-metrics">
                <div class="raffle-metric">
                    <i class="fa-solid fa-star"></i>
                    <div><span>Puntos generados</span><strong>{{ $score }}</strong></div>
                </div>
                <div class="raffle-metric">
                    <i class="fa-solid fa-users"></i>
                    <div><span>Participaciones</span><strong>{{ number_format($numbersCount, 0, ',', '.') }}</strong></div>
                </div>
            </div>

            @if($raffle->isFinished())
                @auth
                    <div class="raffle-table-wrap">
                        <div class="raffle-table-title">Listado de ganadores</div>
                        @if($prizeWinners->count())
                            <table class="raffle-table">
                                <thead>
                                    <tr>
                                        <th>Posicion</th>
                                        <th>Cant. participaciones</th>
                                        <th>Numero ganador</th>
                                        <th>Premio ganador</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prizeWinners as $winner)
                                        <tr class="{{ $loop->iteration <= 3 ? 'top' : '' }}">
                                            <td><span class="raffle-medal">{{ $winner['position'] ?? $loop->iteration }}</span></td>
                                            <td>{{ (int) ($winner['winner_participations_count'] ?? 1) }}</td>
                                            <td>{{ $winner['winner_number'] ?? '-' }}</td>
                                            <td>{{ $winner['name'] ?? 'Premio' }}</td>
                                            <td><span class="raffle-user"><i class="fa-solid fa-user"></i>{{ $winner['winner_username'] ?? $winner['winner_name'] ?? 'Ganador' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="raffle-empty">El sorteo termino, pero todavia no hay ganadores cargados.</div>
                        @endif
                        <div class="raffle-note"><i class="fa-solid fa-circle-info"></i>Los ganadores aparecen unicamente cuando el sorteo finaliza y el panel carga los resultados.</div>
                    </div>
                @endauth
            @else
                <div class="raffle-note"><i class="fa-solid fa-circle-info"></i>La lista de ganadores se habilita unicamente al finalizar el sorteo.</div>
            @endif
        </div>

        <div class="raffle-secondary">
            <section class="raffle-info-card">
                <h2>{{ $raffle->title }}</h2>
                <div class="raffle-tags">
                    <span class="raffle-tag {{ $raffle->status === 'active' ? 'active' : '' }}">{{ $raffle->status === 'active' ? 'Activo' : ($raffle->status === 'finished' ? 'Finalizado' : 'Proximo') }}</span>
                    @if($raffle->lines->count())
                        <span class="raffle-tag">{{ $raffle->lines->pluck('name')->join(', ') }}</span>
                    @endif
                    @if($raffle->platform)
                        <span class="raffle-tag">{{ $raffle->platform->name }}</span>
                    @endif
                </div>
            </section>

            <section class="raffle-info-card">
                <h2>Mis numeros</h2>
                @auth
                    @if($myNumbers->count())
                        <div class="raffle-number-grid">
                            @foreach($myNumbers as $number)
                                <div class="raffle-number">{{ $number->number }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="raffle-empty">Todavia no tenes numeros asignados para este sorteo.</div>
                    @endif
                @else
                    <div class="raffle-empty">Inicia sesion para ver tus numeros asignados.</div>
                @endauth
            </section>
        </div>
    </div>

    <div class="raffle-modal-backdrop" x-show="showPrizes" x-cloak x-transition @click.self="showPrizes = false">
        <div class="raffle-modal" role="dialog" aria-modal="true" aria-label="Listado de premios">
            <div class="raffle-modal-head">
                <h2>Premios disponibles</h2>
                <button type="button" class="raffle-modal-close" @click="showPrizes = false">X</button>
            </div>
            <div class="raffle-full-prizes">
                @foreach($allPrizes as $prize)
                    <div class="raffle-full-prize">
                        <span>{{ $prize['position'] ?? $loop->iteration }}</span>
                        <div>
                            <strong>{{ $prize['name'] ?? 'Premio del sorteo' }}</strong>
                            <small>Premio disponible en {{ $raffle->title }}</small>
                        </div>
                        @if(! empty($prize['amount']))
                            <b>${{ number_format((float) $prize['amount'], 0, ',', '.') }}</b>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
