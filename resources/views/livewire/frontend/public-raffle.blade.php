@push('styles')
<style>
    .raffles-page { padding:42px 0 60px; }
    .raffles-header { text-align:center; margin-bottom:42px; }
    .raffles-header h1 { font-family:var(--font-display); font-size:56px; line-height:.9; letter-spacing:.03em; margin:0 0 12px; }
    .raffles-header h1 span { color:var(--orange); }
    .raffles-header p { color:var(--muted); font-size:15px; max-width:600px; margin:0 auto; }
    .raffles-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:20px; }
    .raffle-card { display:flex; flex-direction:column; border:1px solid var(--line); border-radius:var(--r-xl); background:linear-gradient(180deg,#180b08,#0d0707); overflow:hidden; text-decoration:none; transition:transform .25s ease, border-color .25s ease, box-shadow .25s ease; }
    .raffle-card:hover { transform:translateY(-6px); border-color:var(--orange); box-shadow:0 20px 50px rgba(255,106,26,.15); }
    .raffle-card-image { position:relative; height:170px; display:flex; align-items:center; justify-content:center; background:radial-gradient(70% 72% at 50% 30%, rgba(255,106,26,.24), transparent 70%), linear-gradient(135deg, rgba(255,106,26,.08), rgba(255,255,255,.025)); overflow:hidden; }
    .raffle-card-image::after { content:""; position:absolute; inset:auto 0 0; height:72px; background:linear-gradient(180deg, transparent, rgba(13,7,7,.88)); pointer-events:none; }
    .raffle-card-image img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
    .raffle-card-placeholder { position:relative; z-index:1; width:88px; height:88px; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,106,26,.34); border-radius:24px; background:rgba(255,106,26,.10); color:var(--orange); font-size:38px; box-shadow:0 20px 46px rgba(0,0,0,.28), 0 0 36px rgba(255,106,26,.16); }
    .raffle-card-badge { position:absolute; top:12px; right:12px; padding:6px 12px; border-radius:999px; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; }
    .raffle-card-badge.active { background:var(--orange); color:#190702; }
    .raffle-card-badge.finished { background:rgba(255,71,87,.9); color:#fff; }
    .raffle-card-badge.inactive { background:rgba(255,179,71,.9); color:#190702; }
    .raffle-card-body { padding:20px; flex:1; display:flex; flex-direction:column; }
    .raffle-card-title { font-family:var(--font-display); font-size:24px; line-height:1.1; margin:0 0 8px; letter-spacing:.01em; }
    .raffle-card-desc { color:var(--muted); font-size:13px; line-height:1.5; margin:0 0 16px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .raffle-card-meta { display:grid; grid-template-columns:minmax(0, 1fr); gap:14px; margin-top:auto; padding-top:16px; border-top:1px solid var(--line); }
    .raffle-card-row { display:flex; justify-content:space-between; align-items:center; gap:12px; }
    .raffle-card-info { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:12px; }
    .raffle-card-info i { color:var(--orange); }
    .raffle-card-clock-label { display:inline-flex; align-items:center; gap:7px; color:var(--orange); font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
    .raffle-card-clock-label i { font-size:10px; }
    .raffle-card-countdown { position:relative; display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:7px; width:100%; border:1px solid rgba(255,106,26,.2); border-radius:10px; background:linear-gradient(180deg, rgba(255,106,26,.08), rgba(255,255,255,.025)); padding:10px; overflow:hidden; box-shadow:inset 0 1px 0 rgba(255,255,255,.04); }
    .raffle-card-countdown::before { content:""; position:absolute; inset:0; background:radial-gradient(80% 120% at 100% 0%, rgba(255,106,26,.16), transparent 62%); pointer-events:none; }
    .raffle-card-countdown .timer-unit { position:relative; z-index:1; min-width:0; min-height:52px; display:flex; flex-direction:column; align-items:center; justify-content:center; border:1px solid rgba(255,106,26,.24); border-radius:8px; background:rgba(8,3,2,.58); }
    .raffle-card-countdown .timer-val { color:#fff; font-family:var(--font-display); font-size:25px; line-height:.86; text-shadow:0 0 18px rgba(255,106,26,.22); }
    .raffle-card-countdown .timer-label { margin-top:5px; color:rgba(255,106,26,.95); font-size:7px; font-weight:900; letter-spacing:.12em; }
    .raffle-card-countdown.is-finished .timer-unit { opacity:.42; }
    .raffle-countdown-finished { position:absolute; z-index:3; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(11,4,3,.86); color:#fff; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; }
    .raffle-countdown-progress { position:relative; height:5px; border-radius:999px; background:rgba(255,255,255,.08); overflow:hidden; }
    .raffle-countdown-progress span { position:absolute; inset:0 auto 0 0; width:0; border-radius:inherit; background:linear-gradient(90deg, #ff6a1a, #ffb347); box-shadow:0 0 18px rgba(255,106,26,.42); transition:width .45s ease; }
    .raffle-card-prizes { display:flex; flex-wrap:wrap; gap:6px; margin-top:12px; padding-top:12px;padding-bottom:12px; border-top:1px solid var(--line); }
    .raffle-prize-tag { display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:rgba(255,106,26,.1); border:1px solid rgba(255,106,26,.25); border-radius:6px; font-size:10px; font-weight:800; color:var(--orange); }
    .raffle-prize-tag i { font-size:8px; }
    .raffle-prize-tag.more { background:rgba(255,255,255,.04); border-color:var(--line); color:var(--muted); }
    .raffle-card-btn { display:block; text-align:center; padding:10px 16px; margin-top:16px; border:1px solid var(--line); border-radius:var(--r-md); color:var(--muted-2); font-size:12px; font-weight:800; transition:all .2s; }
    .raffle-card:hover .raffle-card-btn { border-color:var(--orange); color:var(--orange); }
    .raffles-empty { border:1px dashed var(--line); border-radius:var(--r-xl); padding:60px 20px; color:var(--muted); text-align:center; }
    .raffles-empty i { font-size:48px; margin-bottom:16px; display:block; opacity:.5; }
    .lines-tabs { display:flex; gap:8px; border-bottom:1px solid rgba(255,255,255,.09); padding-bottom:0; }
    .lines-tab-btn { position:relative; display:inline-flex; align-items:center; gap:8px; height:42px; padding:0 20px; border:none; background:none; color:rgba(255,255,255,.5); font-size:13px; font-weight:800; cursor:pointer; font-family:var(--font-body); text-transform:uppercase; letter-spacing:.06em; border-radius:8px 8px 0 0; transition:color .18s; }
    .lines-tab-btn:hover { color:rgba(255,255,255,.8); }
    .lines-tab-btn.active { color:#fff; background:rgba(255,255,255,.04); }
    .lines-tab-btn.active::after { content:""; position:absolute; bottom:-1px; left:0; right:0; height:2px; background:var(--orange); border-radius:2px 2px 0 0; }
    .lines-tab-btn .tab-count { display:inline-flex; align-items:center; justify-content:center; min-width:20px; height:20px; padding:0 5px; border-radius:999px; font-size:10px; font-weight:900; background:rgba(255,255,255,.08); color:rgba(255,255,255,.5); }
    .lines-tab-btn.active .tab-count { background:rgba(255,106,26,.18); color:var(--orange); }
    @media (max-width: 740px) {
        .raffles-page { padding:28px 0 40px; }
        .raffles-header h1 { font-size:40px; }
        .raffles-grid { grid-template-columns:1fr; gap:16px; }
        .raffle-card-countdown { gap:5px; padding:8px; }
        .raffle-card-countdown .timer-unit { min-height:46px; }
        .raffle-card-countdown .timer-val { font-size:22px; }
    }
</style>
@endpush

<section class="raffles-page">
    <div class="fe-shell">
        @include('frontend.components.breadcrumbs', [
            'items' => isset($publicVendor)
                ? [
                    ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
                    ['label' => $publicVendor->name, 'url' => route('frontend.cajero.inicio', $publicVendor)],
                    ['label' => 'Sorteos'],
                ]
                : [
                    ['label' => 'Inicio', 'url' => route('frontend.home')],
                    ['label' => 'Sorteos'],
                ],
        ])
        <div class="raffles-header">
            <h1>SORTEOS <span>ACTIVOS</span></h1>
            <p>Participa en los sorteos de las lineas activas y acumula chances de ganar premios exclusivos.</p>
        </div>

        @if($showTabs)
            <div class="lines-tabs" style="margin-bottom:24px">
                <button type="button" class="lines-tab-btn {{ $tab === 'general' ? 'active' : '' }}" wire:click="$set('tab','general')">
                    <i class="fa-solid fa-crown" style="font-size:11px"></i>
                    Admin General
                    <span class="tab-count">{{ $generalRaffles->count() }}</span>
                </button>
                <button type="button" class="lines-tab-btn {{ $tab === 'cajeros' ? 'active' : '' }}" wire:click="$set('tab','cajeros')">
                    <i class="fa-solid fa-user-tie" style="font-size:11px"></i>
                    Cajeros
                    <span class="tab-count">{{ $cajeroRaffles->count() }}</span>
                </button>
            </div>
        @endif

        @if($raffles->isEmpty())
            <div class="raffles-empty">
                <i class="fa-solid fa-ticket-simple"></i>
                <p>No hay sorteos disponibles en este momento.</p>
            </div>
        @else
            <div class="raffles-grid">
                @foreach($raffles as $raffle)
                    @php
                        $prizes = collect($raffle->prizes ?? []);
                        $firstPrize = $prizes->first();
                        $prizeImg = $firstPrize['image'] ?? null;
                        $prizeImageUrl = $prizeImg ? (\Illuminate\Support\Str::startsWith($prizeImg, ['http://', 'https://', '/storage/']) ? $prizeImg : asset('storage/'.$prizeImg)) : null;
                        $statusLabel = $raffle->status === 'active' ? 'Activo' : ($raffle->status === 'finished' ? 'Finalizado' : 'Próximo');
                    @endphp
                    <a class="raffle-card" href="{{ isset($publicVendor) ? route('frontend.cajero.sorteos.detalle', [$publicVendor, $raffle]) : route('frontend.sorteos.detalle', $raffle) }}" wire:navigate>
                        <div class="raffle-card-image">
                            @if($prizeImageUrl)
                                <img src="{{ $prizeImageUrl }}" alt="{{ $firstPrize['name'] ?? 'Premio' }}">
                            @else
                                <span class="raffle-card-placeholder" aria-hidden="true">
                                    <i class="fa-solid fa-gift"></i>
                                </span>
                            @endif
                            <span class="raffle-card-badge {{ $raffle->status }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="raffle-card-body">
                            <h3 class="raffle-card-title">{{ $raffle->title }}</h3>
                            <p class="raffle-card-desc">{{ $raffle->description ?: 'Sorteo disponible para usuarios de Red Picantes.' }}</p>
                            @if($prizes->count())
                                <div class="raffle-card-prizes">
                                    @foreach($prizes->take(3) as $idx => $prize)
                                        <span class="raffle-prize-tag">
                                            <i class="fa-solid fa-trophy"></i>
                                            {{ $prize['position'] ?? $idx + 1 }}° {{ $prize['name'] ?? 'Premio' }}
                                        </span>
                                    @endforeach
                                    @if($prizes->count() > 3)
                                        <span class="raffle-prize-tag more">+{{ $prizes->count() - 3 }} más</span>
                                    @endif
                                </div>
                            @endif
                            <div class="raffle-card-meta">
                                <div class="raffle-card-row">
                                    <span class="raffle-card-info">
                                        <i class="fa-solid fa-ticket"></i>
                                        {{ $raffle->numbers_count }} participantes
                                    </span>
                                    @if($raffle->status === 'active' && ! $raffle->isFinished())
                                        <span class="raffle-card-clock-label">
                                            <i class="fa-regular fa-clock"></i>
                                            Termina en
                                        </span>
                                    @else
                                        <span class="raffle-card-clock-label" style="color: var(--muted);">
                                            {{ $raffle->end_date?->format('d/m') }}
                                        </span>
                                    @endif
                                </div>
                                @if($raffle->status === 'active' && ! $raffle->isFinished())
                                    <div
                                        class="raffle-card-countdown"
                                        data-raffle-countdown="{{ $raffle->end_date->toIso8601String() }}"
                                        data-countdown-start="{{ $raffle->start_date?->toIso8601String() }}"
                                    >
                                        <span class="raffle-countdown-finished" data-countdown-finished hidden>Sorteo finalizado</span>
                                        <span class="timer-unit"><span class="timer-val" data-unit="days">00</span><span class="timer-label">Dias</span></span>
                                        <span class="timer-unit"><span class="timer-val" data-unit="hours">00</span><span class="timer-label">Horas</span></span>
                                        <span class="timer-unit"><span class="timer-val" data-unit="minutes">00</span><span class="timer-label">Min</span></span>
                                        <span class="timer-unit"><span class="timer-val" data-unit="seconds">00</span><span class="timer-label">Seg</span></span>
                                    </div>
                                    <div class="raffle-countdown-progress" aria-hidden="true">
                                        <span data-countdown-progress></span>
                                    </div>
                                @endif
                            </div>
                            <span class="raffle-card-btn">Ver detalles</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
