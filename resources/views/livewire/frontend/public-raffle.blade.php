@push('styles')
<style>
    .raffles-page { padding:42px 0 0; }
    .raffles-hero { position:relative; overflow:hidden; border:1px solid rgba(255,106,26,.42); border-radius:10px; background:radial-gradient(65% 90% at 50% 0%, rgba(255,106,26,.18), transparent 62%), linear-gradient(180deg,#090302,#050202); box-shadow:0 26px 70px rgba(0,0,0,.48), inset 0 0 34px rgba(255,106,26,.08); }
    .raffles-gift { position:absolute; top:20px; width:154px; opacity:.78; filter:drop-shadow(0 18px 26px rgba(255,106,26,.28)); pointer-events:none; }
    .raffles-gift.left { left:24px; transform:rotate(-7deg); }
    .raffles-gift.right { right:24px; transform:scaleX(-1) rotate(-7deg); }
    .raffles-head { position:relative; z-index:1; text-align:center; padding:28px 190px 16px; }
    .raffles-kicker { color:var(--muted); font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .raffles-title { font-family:var(--font-display); font-size:50px; line-height:.9; letter-spacing:.03em; margin:0; }
    .raffles-title span { color:#ff3d12; }
    .raffles-copy { max-width:720px; margin:8px auto 0; color:rgba(255,255,255,.62); font-size:13px; line-height:1.45; }
    .raffles-actions { display:flex; align-items:center; justify-content:center; gap:10px; flex-wrap:wrap; margin-top:16px; }
    .raffles-status-note { max-width:680px; margin:12px auto 0; color:rgba(255,255,255,.66); font-size:13px; line-height:1.45; }
    .raffles-podium { position:relative; z-index:1; display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.28fr) minmax(0,1fr); gap:14px; align-items:end; padding:10px 24px 12px; }
    .raffles-prize { min-height:136px; display:grid; grid-template-columns:74px minmax(0,.8fr) minmax(120px,1fr); align-items:center; gap:12px; border:1px solid rgba(255,106,26,.74); border-radius:8px; background:#0b0605; box-shadow:inset 0 0 22px rgba(255,106,26,.08), 0 0 18px rgba(255,106,26,.1); padding:12px 14px; overflow:hidden; }
    .raffles-prize.main { min-height:166px; border-color:rgba(255,179,71,.8); background:#100705; transform:translateY(-10px); }
    .raffles-rank { font-family:var(--font-display); color:var(--orange); font-size:92px; line-height:.8; text-align:center; text-shadow:0 0 18px rgba(255,106,26,.42); }
    .raffles-prize.main .raffles-rank { font-size:118px; color:#ff8a1f; }
    .raffles-prize-data small { display:block; color:#fff; font-size:12px; font-weight:900; text-transform:uppercase; }
    .raffles-prize-data strong { display:block; margin-top:4px; color:#fff; font-size:14px; line-height:1.18; }
    .raffles-prize-data b { display:block; margin-top:5px; color:var(--orange); font-size:13px; }
    .raffles-prize-img { height:96px; border-radius:6px; background:#120807; overflow:hidden; display:flex; align-items:center; justify-content:center; }
    .raffles-prize.main .raffles-prize-img { height:122px; }
    .raffles-prize-img img { width:100%; height:100%; object-fit:cover; }
    .raffles-prize-img span { color:rgba(255,255,255,.14); font-family:var(--font-display); font-size:34px; }
    .raffles-stats { position:relative; z-index:1; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0; width:min(780px, calc(100% - 48px)); margin:0 auto 16px; border:1px solid rgba(255,106,26,.24); border-radius:8px; background:#080403; overflow:hidden; }
    .raffles-stat { display:flex; align-items:center; justify-content:center; gap:12px; padding:14px 18px; border-left:1px solid rgba(255,106,26,.16); }
    .raffles-stat:first-child { border-left:0; }
    .raffles-stat i { color:var(--orange); font-size:20px; }
    .raffles-stat span { color:rgba(255,255,255,.5); font-size:10px; font-weight:900; text-transform:uppercase; display:block; }
    .raffles-stat strong { color:var(--orange); font-family:var(--font-mono); font-size:15px; }
    .raffles-modal-backdrop { position:fixed; inset:0; z-index:80; display:flex; align-items:center; justify-content:center; padding:22px; background:rgba(0,0,0,.72); backdrop-filter:blur(10px); }
    .raffles-modal { width:min(720px,100%); max-height:min(760px,90vh); overflow:auto; border:1px solid rgba(255,106,26,.38); border-radius:12px; background:linear-gradient(180deg,#160807,#070302); box-shadow:0 30px 90px rgba(0,0,0,.68); padding:20px; }
    .raffles-modal-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; }
    .raffles-modal-head h2 { font-family:var(--font-display); font-size:38px; line-height:1; margin:0; letter-spacing:.02em; }
    .raffles-modal-close { width:36px; height:36px; border-radius:999px; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.04); color:#fff; cursor:pointer; font-weight:900; }
    .raffles-full-prizes { display:grid; gap:10px; }
    .raffles-full-prize { display:grid; grid-template-columns:48px minmax(0,1fr) auto; gap:12px; align-items:center; border:1px solid rgba(255,106,26,.16); border-radius:9px; background:rgba(255,255,255,.035); padding:12px; }
    .raffles-full-prize span { color:var(--orange); font-family:var(--font-display); font-size:34px; line-height:1; }
    .raffles-full-prize strong { color:#fff; font-size:14px; }
    .raffles-full-prize small { display:block; margin-top:3px; color:var(--muted); font-size:11px; }
    .raffles-full-prize b { color:var(--orange); font-size:13px; }
    .raffles-list { margin-top:38px; display:grid; gap:14px; }
    .raffles-row { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:18px; border:1px solid rgba(255,106,26,.2); border-radius:10px; background:linear-gradient(180deg,#130807,#080403); padding:18px; text-decoration:none; box-shadow:0 16px 44px rgba(0,0,0,.24); }
    .raffles-row:hover { border-color:rgba(255,106,26,.58); transform:translateY(-1px); }
    .raffles-row h3 { font-family:var(--font-display); font-size:34px; line-height:1; margin:0; letter-spacing:.02em; }
    .raffles-row p { color:var(--muted); font-size:13px; line-height:1.45; margin:6px 0 0; }
    .raffles-row-meta { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
    .raffles-chip { border:1px solid rgba(255,106,26,.24); border-radius:999px; color:rgba(255,255,255,.72); padding:6px 10px; font-size:11px; font-weight:900; text-transform:uppercase; }
    .raffles-chip.active { color:#190702; background:var(--orange); border-color:var(--orange); }
    .raffles-empty { margin-top:28px; border:1px dashed rgba(255,255,255,.18); border-radius:10px; padding:22px; color:var(--muted); text-align:center; }
    @media (max-width: 940px) {
        .raffles-head { padding:28px 26px 12px; }
        .raffles-gift { opacity:.18; }
        .raffles-podium { grid-template-columns:1fr; }
        .raffles-prize, .raffles-prize.main { transform:none; grid-template-columns:58px minmax(0,1fr) 120px; min-height:120px; }
        .raffles-rank, .raffles-prize.main .raffles-rank { font-size:76px; }
    }
    @media (max-width: 620px) {
        .raffles-page { padding-top:24px; }
        .raffles-title { font-size:38px; }
        .raffles-prize, .raffles-prize.main { grid-template-columns:54px minmax(0,1fr); }
        .raffles-prize-img { grid-column:1 / -1; width:100%; }
        .raffles-stats, .raffles-row { grid-template-columns:1fr; }
        .raffles-actions .fe-btn, .raffles-row .fe-btn { width:100%; }
        .raffles-stat { border-left:0; border-top:1px solid rgba(255,106,26,.16); }
        .raffles-stat:first-child { border-top:0; }
        .raffles-full-prize { grid-template-columns:42px minmax(0,1fr); }
        .raffles-full-prize b { grid-column:2; }
        .raffles-modal-backdrop { padding:12px; align-items:flex-start; }
        .raffles-modal { max-height:calc(100vh - 24px); padding:16px; }
        .raffles-modal-head { align-items:flex-start; }
        .raffles-modal-head h2 { font-size:31px; }
    }
</style>
@endpush

@php
    $heroRaffle = $activeRaffle ?? $upcomingRaffle ?? $endedRaffle ?? $raffles->first();
    $allPrizes = collect($heroRaffle?->prizes ?? [])->sortBy(fn ($prize) => (int) ($prize['position'] ?? 99))->values();
    $rankedPrizes = $allPrizes->take(3)->values();
    $podium = $rankedPrizes->count() === 3
        ? collect([2, 1, 3])->map(fn ($position) => $rankedPrizes->first(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1) === $position))->filter()->values()
        : $rankedPrizes;
    $numbersCount = $heroRaffle?->numbers()->count() ?? 0;
    $participantsCount = $heroRaffle?->numbers()->distinct('user_id')->count('user_id') ?? 0;
    $score = number_format($numbersCount * 125.10, 2, ',', '.');
@endphp

<section class="raffles-page" x-data="{ showPrizes: false }">
    <div class="fe-shell">
        @if($heroRaffle)
            <div class="raffles-hero">
                <img class="raffles-gift left" src="{{ asset('frontend/raffle-gift.png') }}" alt="">
                <img class="raffles-gift right" src="{{ asset('frontend/raffle-gift.png') }}" alt="">

                <div class="raffles-head">
                    <h1 class="raffles-title">{{ $heroRaffle->title }}</h1>
                    <div class="raffles-kicker">
                        {{ $heroRaffle->status === 'active' ? 'Sorteo activo' : ($heroRaffle->status === 'finished' ? 'Sorteo terminado' : 'Proximo sorteo') }}
                    </div>
                    <p class="raffles-copy">{{ $heroRaffle->description ?: 'Suma participaciones jugando en las lineas activas y segui los premios publicados.' }}</p>
                    @if($heroRaffle->status !== 'active' && ! $heroRaffle->isFinished())
                        <div class="raffles-status-note">Proximamente: registrate y enterate del proximo sorteo disponible.</div>
                    @endif
                    <div class="raffles-actions">
                        @if($allPrizes->count() > 3)
                            <button type="button" class="fe-btn ghost" @click="showPrizes = true">Ver premios</button>
                        @endif
                        @if($heroRaffle->status === 'active')
                            @auth
                                <a href="{{ route('frontend.lines') }}" wire:navigate class="fe-btn primary">Suma puntos</a>
                            @else
                                <a href="{{ route('login') }}" wire:navigate class="fe-btn primary">Registrarme o iniciar sesion</a>
                            @endauth
                        @elseif(! auth()->check())
                            <a href="{{ route('login') }}" wire:navigate class="fe-btn primary">Registrarme o iniciar sesion</a>
                        @endif
                        <a href="{{ route('frontend.raffles.show', $heroRaffle) }}" wire:navigate class="fe-btn ghost">Ver detalle</a>
                    </div>
                </div>

                @if($podium->count())
                    <div class="raffles-podium">
                        @foreach($podium as $index => $prize)
                            @php
                                $position = (int) ($prize['position'] ?? $index + 1);
                                $image = $prize['image'] ?? null;
                                $imageUrl = $image ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/storage/']) ? $image : asset('storage/'.$image)) : null;
                            @endphp
                            <article class="raffles-prize {{ $position === 1 ? 'main' : '' }}">
                                <div class="raffles-rank">{{ $position }}</div>
                                <div class="raffles-prize-data">
                                    <small>Premio {{ $position }}</small>
                                    <strong>{{ $prize['name'] ?? 'Premio del sorteo' }}</strong>
                                    @if(! empty($prize['amount']))
                                        <b>${{ number_format((float) $prize['amount'], 0, ',', '.') }}</b>
                                    @endif
                                </div>
                                <div class="raffles-prize-img">
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

                <div class="raffles-stats">
                    <div class="raffles-stat">
                        <i class="fa-solid fa-star"></i>
                        <div><span>Puntos generados</span><strong>{{ $score }}</strong></div>
                    </div>
                    <div class="raffles-stat">
                        <i class="fa-solid fa-users"></i>
                        <div><span>Participaciones</span><strong>{{ number_format($numbersCount, 0, ',', '.') }}</strong></div>
                    </div>
                </div>
            </div>
        @else
            <div class="raffles-empty">Todavia no hay sorteos publicados.</div>
        @endif

        <div class="raffles-list">
            @foreach($raffles as $raffle)
                <a class="raffles-row" href="{{ route('frontend.raffles.show', $raffle) }}" wire:navigate>
                    <div>
                        <h3>{{ $raffle->title }}</h3>
                        <p>{{ $raffle->description ?: 'Sorteo disponible para usuarios de Red Picantes.' }}</p>
                        <div class="raffles-row-meta">
                            <span class="raffles-chip {{ $raffle->status === 'active' ? 'active' : '' }}">{{ $raffle->status === 'active' ? 'Activo' : ($raffle->status === 'finished' ? 'Finalizado' : 'Proximo') }}</span>
                            <span class="raffles-chip">{{ $raffle->numbers_count }} participaciones</span>
                            <span class="raffles-chip">Termina {{ $raffle->end_date?->format('d/m H:i') }}</span>
                        </div>
                    </div>
                    <span class="fe-btn ghost">Ver detalle</span>
                </a>
            @endforeach
        </div>
    </div>

    @if($heroRaffle && $allPrizes->count() > 3)
        <div class="raffles-modal-backdrop" x-show="showPrizes" x-cloak x-transition @click.self="showPrizes = false">
            <div class="raffles-modal" role="dialog" aria-modal="true" aria-label="Listado de premios disponibles">
                <div class="raffles-modal-head">
                    <h2>Premios disponibles</h2>
                    <button type="button" class="raffles-modal-close" @click="showPrizes = false">X</button>
                </div>
                <div class="raffles-full-prizes">
                    @foreach($allPrizes as $prize)
                        <div class="raffles-full-prize">
                            <span>{{ $prize['position'] ?? $loop->iteration }}</span>
                            <div>
                                <strong>{{ $prize['name'] ?? 'Premio del sorteo' }}</strong>
                                <small>{{ $heroRaffle->status === 'active' ? 'Premio activo disponible' : ($heroRaffle->isFinished() ? 'Premio del sorteo finalizado' : 'Premio del proximo sorteo') }}</small>
                            </div>
                            @if(! empty($prize['amount']))
                                <b>${{ number_format((float) $prize['amount'], 0, ',', '.') }}</b>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>
