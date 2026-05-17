@push('styles')
<style>
    .line-detail-page { padding:42px 0 0; }
    .line-detail-hero { overflow:hidden; border:1px solid var(--line-warm); border-radius:var(--r-xl); background:linear-gradient(180deg,#180b08,#090505); box-shadow:0 24px 70px rgba(0,0,0,.38); }
    .line-detail-cover { height:310px; position:relative; background:radial-gradient(80% 80% at 80% 0%, rgba(255,106,26,.35), transparent 70%), #130807; }
    .line-detail-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    .line-detail-cover::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg, transparent 40%, rgba(9,5,5,.88)); }
    .line-detail-profile { position:relative; margin-top:-72px; padding:0 28px 28px; display:grid; grid-template-columns:118px minmax(0, 1fr) auto; gap:20px; align-items:end; }
    .line-detail-avatar { width:118px; height:118px; border-radius:24px; border:4px solid #100707; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; font-family:var(--font-display); font-size:44px; color:#160604; z-index:2; }
    .line-detail-avatar img { width:100%; height:100%; object-fit:cover; }
    .line-detail-title { z-index:2; }
    .line-detail-title h1 { margin:0; font-family:var(--font-display); font-size:64px; line-height:.9; letter-spacing:.02em; }
    .line-detail-title p { margin:10px 0 0; color:var(--muted); font-size:14px; line-height:1.55; max-width:720px; }
    .line-status-box { z-index:2; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(0,0,0,.28); padding:14px 16px; min-width:180px; }
    .line-status-box strong { display:block; color:var(--amber); letter-spacing:.08em; }
    .line-detail-grid { display:grid; grid-template-columns:minmax(0, .95fr) minmax(0, 1.35fr); gap:16px; margin-top:18px; }
    .line-panel { border:1px solid var(--line); border-radius:var(--r-md); background:linear-gradient(180deg,#170b0b,#0f0707); padding:20px; min-width:0; }
    .line-panel-title { font-family:var(--font-display); font-size:30px; line-height:1; margin:0 0 14px; letter-spacing:.02em; }
    .line-info-row { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid var(--line); color:var(--muted); font-size:13px; }
    .line-info-row:last-child { border-bottom:0; }
    .line-info-row strong { color:#fff; text-align:right; }
    .detail-channel-list { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-start; }
    .detail-channel { width:max-content; max-width:100%; display:inline-grid; grid-template-columns:42px minmax(0, auto); gap:12px; align-items:center; padding:12px 14px 12px 12px; border:1px solid rgba(154,154,154,.18); border-radius:999px; background:rgba(255,255,255,.035); text-decoration:none; }
    .detail-channel i { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(154,154,154,.08); color:#9a9a9a; font-size:18px; }
    .detail-channel strong { display:block; font-size:14px; }
    .detail-channel small { display:block; color:var(--muted-2); font-size:11px; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:min(420px, 58vw); }
    .platform-grid { display:grid; gap:12px; grid-template-columns:repeat(4, minmax(0, 1fr)); }
    .platform-card { display:flex; flex-direction:column; gap:12px; padding:16px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); text-align:center; align-items:center; transition: transform 0.2s ease, border-color 0.2s ease; }
    .platform-card:hover { transform: translateY(-4px); border-color: var(--orange); }
    .platform-logo { width:100%; height:100px; border-radius:12px; background:rgba(255,106,26,.14); display:flex; align-items:center; justify-content:center; overflow:hidden; color:var(--orange); font-weight:900; font-size:32px; font-family:var(--font-display); }
    .platform-logo img { width:100%; height:100%; object-fit:cover; }
    .platform-card strong { display:block; font-size:18px; font-family:var(--font-display); letter-spacing:0.02em; margin-top:4px; }
    .platform-card p { color:var(--muted); font-size:12px; line-height:1.4; margin:2px 0 0; }
    .line-rating-wrap { margin-top:18px; }
    .rating-summary { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:16px; }
    .rating-big { font-family:var(--font-display); font-size:54px; line-height:.85; color:var(--orange); }
    .rating-stars { display:flex; gap:4px; color:var(--amber); font-size:18px; }
    .rating-count { color:var(--muted); font-size:12px; font-weight:800; }
    .rating-form { display:grid; gap:12px; margin-bottom:16px; }
    .rating-pick { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .rating-star-btn { width:38px; height:38px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.035); color:rgba(255,255,255,.28); cursor:pointer; font-size:18px; }
    .rating-star-btn.active { color:var(--amber); border-color:rgba(255,179,71,.5); background:rgba(255,179,71,.1); }
    .line-rating-input { width:100%; min-height:92px; resize:vertical; border:1px solid var(--line-2); border-radius:var(--r-md); background:rgba(255,255,255,.04); color:#fff; outline:none; padding:14px; font:600 13px var(--font-body); }
    .line-rating-input:focus { border-color:var(--orange); box-shadow:0 0 0 4px rgba(255,106,26,.12); }
    .rating-error { color:#ff8a8a; font-size:12px; font-weight:800; }
    .rating-list { display:grid; gap:10px; }
    .rating-item { display:grid; grid-template-columns:42px minmax(0, 1fr); gap:12px; padding:14px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); }
    .rating-avatar { width:42px; height:42px; border-radius:999px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:rgba(255,106,26,.16); color:var(--orange); font-weight:900; }
    .rating-avatar img { width:100%; height:100%; object-fit:cover; }
    .rating-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:5px; }
    .rating-name { font-size:13px; font-weight:900; color:#fff; }
    .rating-date { font-size:10px; color:var(--muted-2); font-weight:800; white-space:nowrap; }
    .rating-message { color:var(--muted); font-size:13px; line-height:1.5; margin:6px 0 0; }
    .line-login-box { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:14px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); margin-bottom:16px; color:var(--muted); font-size:13px; }

    /* Raffle & Bonus Styles */
    .raffle-banner { position: relative; overflow: hidden; border-radius: 10px; min-height: 330px; padding: 18px 22px 22px; }
    .raffle-banner-head h3 { font-family:var(--font-display); font-size:44px; line-height:.9; letter-spacing:.03em; margin:0; }
    .raffle-banner-head p { margin:6px auto 0; color:var(--muted); font-size:12px; line-height:1.45; }
    .raffle-countdown { display:inline-flex; align-items:center; justify-content:center; gap:8px; margin-top:12px; border:1px solid rgba(255,106,26,.55); border-radius:999px; background:rgba(255,106,26,.12); color:#fff; padding:8px 16px; font-size:12px; font-weight:900; letter-spacing:.04em; text-transform:uppercase; box-shadow:0 0 22px rgba(255,106,26,.16); }
    .raffle-countdown strong { color:var(--orange); font-size:14px; }
    .raffle-prize-strip { position:relative; z-index:2; display:grid; grid-auto-flow:column; grid-auto-columns:minmax(310px, 400px); gap:14px; padding:10px 0 12px; align-items:end; justify-content:start; overflow-x:auto; overscroll-behavior-inline:contain; -webkit-overflow-scrolling:touch; scroll-snap-type:inline mandatory; scrollbar-width:thin; scrollbar-color:rgba(255,106,26,.72) rgba(255,255,255,.08); }
    .raffle-prize-strip::-webkit-scrollbar { height:8px; }
    .raffle-prize-strip::-webkit-scrollbar-track { background:rgba(255,255,255,.08); border-radius:999px; }
    .raffle-prize-strip::-webkit-scrollbar-thumb { background:rgba(255,106,26,.72); border-radius:999px; }
    .raffle-prize-tile { min-height:116px; display:grid; grid-template-columns:58px minmax(0, .86fr) minmax(112px, 1fr); align-items:center; gap:12px; border:1px solid rgba(255,106,26,.55); border-radius:8px; background:#0d0706; box-shadow:0 0 18px rgba(255,106,26,.09) inset, 0 18px 38px rgba(0,0,0,.28); padding:12px; overflow:hidden; scroll-snap-align:start; }
    .raffle-prize-tile.primary { min-height:146px; grid-template-columns:72px minmax(0, .82fr) minmax(150px, 1fr); border-color:rgba(255,179,71,.75); background:#120807; }
    .raffle-rank { font-family:var(--font-display); font-size:82px; line-height:.8; color:var(--orange); text-align:center; text-shadow:0 0 20px rgba(255,106,26,.32); }
    .raffle-prize-tile.primary .raffle-rank { font-size:100px; color:#ff8a1f; }
    .raffle-prize-info strong { display:block; color:#fff; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
    .raffle-prize-info span { display:block; color:rgba(255,255,255,.78); font-size:12px; line-height:1.25; }
    .raffle-prize-info b { display:block; color:var(--orange); font-size:15px; margin-top:3px; }
    .raffle-prize-image { height:90px; border-radius:6px; background:radial-gradient(70% 70% at 50% 50%, rgba(255,106,26,.22), transparent 72%); display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .raffle-prize-tile.primary .raffle-prize-image { height:116px; }
    .raffle-prize-image img { width:100%; height:100%; object-fit:cover; }
    .raffle-prize-image span { font-family:var(--font-display); color:rgba(255,255,255,.12); font-size:44px; letter-spacing:.05em; }
    .bonus-carousel { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px; padding:4px 0 16px; }
    .bonus-card { min-height:250px; color:#fff; position:relative; border:3px dashed rgba(255,106,26,.9); border-radius:18px; background:radial-gradient(90% 100% at 0% 0%, rgba(255,106,26,.2), transparent 58%), linear-gradient(180deg,#180b08,#090505); box-shadow:0 18px 42px rgba(0,0,0,.42), 0 0 0 1px rgba(255,255,255,.04) inset; overflow:hidden; padding:30px; }
    .bonus-ticket-main { min-height:194px; display:flex; flex-direction:column; justify-content:center; align-items:flex-start; gap:8px; padding:0; position:relative; }
    .bonus-ticket-kicker { color:var(--orange); font-size:10px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
    .bonus-card h3 { font-family:var(--font-display); font-size:34px; line-height:.92; margin:0; letter-spacing:.02em; color:#fff; text-transform:uppercase; max-width:270px; }
    .bonus-card p { color:var(--muted); font-size:13px; line-height:1.42; margin:12px 0 0; font-weight:700; max-width:270px; }
    .bonus-ticket-value { font-family:var(--font-display); color:var(--orange); font-size:58px; line-height:.82; text-shadow:0 0 22px rgba(255,106,26,.22); }
    .bonus-card strong { display:block; font-family:var(--font-mono); font-size:12px; letter-spacing:.04em; overflow-wrap:anywhere; color:var(--orange); }
    .bonus-card em { display:block; font-style:normal; font-weight:900; font-size:10px; color:var(--muted-2); }

    /* Raffle Cards */
    .raffles-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:16px; margin-top:16px; }
    .raffle-card { border:1px solid var(--line); border-radius:var(--r-lg); background:linear-gradient(180deg,#180b08,#0d0707); overflow:hidden; transition:transform .2s, border-color .2s; }
    .raffle-card:hover { transform:translateY(-4px); border-color:var(--orange); }
    .raffle-card-image { position:relative; height:160px; background:radial-gradient(80% 50% at 50% 0%, rgba(255,106,26,.2), transparent 70%); }
    .raffle-card-image img { width:100%; height:100%; object-fit:cover; }
    .raffle-card-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(255,106,26,.1); color:var(--orange); font-size:48px; }
    .raffle-card-timer { position:absolute; bottom:12px; left:12px; display:inline-flex; align-items:center; gap:6px; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); padding:6px 12px; border-radius:999px; font-size:11px; font-weight:800; color:#fff; }
    .raffle-card-timer i { color:var(--orange); }
    .raffle-card-body { padding:16px; }
    .raffle-card-body h3 { font-family:var(--font-display); font-size:20px; margin:0 0 6px; letter-spacing:.02em; }
    .raffle-card-desc { color:var(--muted); font-size:12px; line-height:1.4; margin:0 0 12px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .raffle-card-prizes { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px; }
    .prize-badge { display:inline-block; padding:4px 8px; background:rgba(255,106,26,.12); border:1px solid rgba(255,106,26,.3); border-radius:6px; font-size:10px; font-weight:800; color:var(--orange); }
    .prize-badge.more { background:rgba(255,255,255,.06); border-color:var(--line); color:var(--muted); }
    .raffle-card-btn { display:block; text-align:center; padding:10px 16px; border:1px solid var(--orange); border-radius:var(--r-md); background:rgba(255,106,26,.08); color:var(--orange); font-size:12px; font-weight:800; text-decoration:none; transition:all .2s; }
    .raffle-card-btn:hover { background:var(--orange); color:#190702; }

    @media (max-width: 920px) {
        .line-detail-profile, .line-detail-grid { grid-template-columns:1fr; }
        .line-status-box { width:100%; }
        .line-detail-title h1 { font-size:50px; }
        .platform-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 620px) {
        .line-detail-page { padding-top:28px; }
        .line-detail-cover { height:210px; }
        .line-detail-profile { padding:0 18px 20px; gap:12px; }
        .line-detail-avatar { width:96px; height:96px; border-radius:20px; }
        .line-detail-title h1 { font-size:42px; }
        .line-info-row { display:grid; grid-template-columns:1fr; gap:4px; }
        .line-info-row strong { text-align:left; overflow-wrap:anywhere; }
        .detail-channel { width:100%; grid-template-columns:42px minmax(0, 1fr); border-radius:12px; }
        .detail-channel small { max-width:none; white-space:normal; overflow-wrap:anywhere; }
        .line-login-box .fe-btn, .rating-form .fe-btn { width:100%; }
        .rating-item { grid-template-columns:1fr; }
        .rating-head { display:block; }
        .rating-date { display:block; margin-top:3px; }
        .platform-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@php
    $channelIcons = [
        'wsp' => 'fa-brands fa-whatsapp', 'wsap' => 'fa-brands fa-whatsapp', 'wa' => 'fa-brands fa-whatsapp', 'whatsapp' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram', 'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram', 'ig' => 'fa-brands fa-instagram',
        'facebook' => 'fa-brands fa-facebook', 'fb' => 'fa-brands fa-facebook',
        'phone' => 'fa-solid fa-phone', 'telefono' => 'fa-solid fa-phone', 'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope', 'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe', 'tiktok' => 'fa-brands fa-tiktok', 'twitter' => 'fa-brands fa-x-twitter', 'x' => 'fa-brands fa-x-twitter', 'youtube' => 'fa-brands fa-youtube',
    ];
    $normalizeChannelType = fn (?string $type): string => strtolower(trim((string) $type));
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
    $manager = $line->lineAgents->first(fn ($lineAgent) => $lineAgent->role === 'encargado' && $lineAgent->is_active);
    $platforms = $line->activePlatforms;
@endphp

<div>
    <section class="line-detail-page">
        <div class="fe-shell">
            <article class="line-detail-hero">
                <div class="line-detail-cover">
                    @if($line->portada_url)
                        <img src="{{ $line->portada_url }}" alt="{{ $line->name }}">
                    @endif
                </div>
                <div class="line-detail-profile">
                    <div class="line-detail-avatar">
                        @if($line->perfil_url)
                            <img src="{{ $line->perfil_url }}" alt="">
                        @else
                            {{ strtoupper(mb_substr($line->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="line-detail-title">
                        <div class="fe-kicker">Linea activa</div>
                        <h1>{{ $line->name }}</h1>
                        <p>{{ $line->description ?: 'Alta rapida, carga de saldo, plataformas disponibles y atencion directa para jugar online.' }}</p>
                    </div>
                    <div class="line-status-box">
                        <small>Valoracion general</small>
                        <div class="rating-stars" aria-label="Valoracion general {{ number_format($ratingAverage, 1) }} estrellas">
                            @php
                                $fullStars = floor($ratingAverage);
                                $hasHalfStar = ($ratingAverage - $fullStars) >= 0.5;
                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            @endphp
                            @for($i = 0; $i < $fullStars; $i++)
                                ★
                            @endfor
                            @if($hasHalfStar)
                                <i class="fa-solid fa-star-half-stroke" style="font-size: 0.9em; vertical-align: middle; margin-top: -2px; display: inline-block;"></i>
                            @endif
                            @for($i = 0; $i < $emptyStars; $i++)
                                <span style="color: rgba(255,255,255,0.15);">★</span>
                            @endfor
                            <strong style="margin-left: 4px; color: #fff;">{{ number_format($ratingAverage, 1) }}</strong>
                        </div>
                    </div>
                </div>
            </article>

            <div class="line-detail-grid">
                <div class="line-panel">
                    <h2 class="line-panel-title">Informacion</h2>
                    <div class="line-info-row"><span>Encargado</span><strong>{{ $manager?->agent?->username ?: $manager?->agent?->name ?: 'A confirmar' }}</strong></div>
                    <div class="line-info-row"><span>Estado</span><strong>Activa</strong></div>
                    <div class="line-info-row"><span>Plataformas disponibles</span><strong>{{ $platforms->count() }}</strong></div>
                    <div class="line-info-row"><span>Canales publicados</span><strong>{{ $contacts->count() }}</strong></div>
                    <div class="line-info-row">
                        <span>Valoración</span>
                        <strong>
                            @if($ratingCount > 0)
                                ★ {{ number_format($ratingAverage, 1) }}
                            @else
                                Sin valoraciones
                            @endif
                        </strong>
                    </div>
                    @if($line->type)
                        <div class="line-info-row"><span>Tipo</span><strong>{{ ucfirst($line->type) }}</strong></div>
                    @endif
                </div>

                <div class="line-panel">
                    <h2 class="line-panel-title">Canales de contacto</h2>
                    <div class="detail-channel-list">
                        @forelse($contacts as $contact)
                            @php
                                $type = $normalizeChannelType($contact['type'] ?? 'web');
                                $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                                $name = $contact['name'] ?: ucfirst($type);
                            @endphp
                            <a class="detail-channel" href="{{ $contact['value'] }}" target="_blank" rel="noopener">
                                <i class="{{ $icon }}"></i>
                                <div>
                                    <strong>{{ $name }}</strong>
                                    <small>{{ $contact['value'] }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="empty-panel">Esta linea todavia no tiene canales publicados.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <section id="plataformas" class="fe-section">
                <div class="line-panel">
                    <h2 class="line-panel-title">Plataformas disponibles</h2>
                    @if($platforms->count())
                        <div class="platform-grid">
                            @foreach($platforms as $platform)
                                <article class="platform-card">
                                    <div class="platform-logo">
                                        @if($platform->logo_url)
                                            <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}">
                                        @else
                                            {{ strtoupper(mb_substr($platform->name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $platform->name }}</strong>
                                        <p>{{ $platform->pivot?->custom_message ?: $platform->description ?: 'Disponible para esta linea.' }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-panel">No hay plataformas activas publicadas para esta linea.</div>
                    @endif
                </div>
            </section>

            @if($activeBonuses->count())
                <section id="bonos" class="fe-section">
                    <div class="line-panel">
                        <h2 class="line-panel-title">Bonos activos</h2>
                        <div class="bonus-carousel" style="margin-top: 10px;">
                            @foreach($activeBonuses as $bonus)
                                @include('frontend.components.bonus-card', ['bonus' => $bonus])
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if($activeRaffles->count())
                <section id="sorteos" class="fe-section">
                    <div class="line-panel">
                        <h2 class="line-panel-title">Sorteos activos</h2>
                        <div class="raffles-grid">
                            @foreach($activeRaffles as $raffle)
                                @php
                                    $prizeImage = function (?string $image): ?string {
                                        if (! $image) return null;
                                        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/storage/'])) return $image;
                                        return asset('storage/'.$image);
                                    };
                                    $displayPrizes = collect($raffle->prizes)->sortBy(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1))->values();
                                    $firstPrize = $displayPrizes->first();
                                    $firstPrizeImage = $prizeImage($firstPrize['image'] ?? null);
                                    $remaining = now()->diff($raffle->end_date);
                                    $remainingText = $raffle->end_date->isFuture() 
                                        ? trim(collect([$remaining->d ? $remaining->d.' días' : null, $remaining->h ? $remaining->h.'h' : null])->filter()->take(2)->join(' ')) 
                                        : 'Finalizando';
                                @endphp
                                <article class="raffle-card">
                                    <div class="raffle-card-image">
                                        @if($firstPrizeImage)
                                            <img src="{{ $firstPrizeImage }}" alt="{{ $firstPrize['name'] ?? 'Premio' }}">
                                        @else
                                            <div class="raffle-card-placeholder">
                                                <i class="fa-solid fa-trophy"></i>
                                            </div>
                                        @endif
                                        <div class="raffle-card-timer">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $remainingText }}
                                        </div>
                                    </div>
                                    <div class="raffle-card-body">
                                        <h3>{{ $raffle->title }}</h3>
                                        <p class="raffle-card-desc">{{ $raffle->description }}</p>
                                        <div class="raffle-card-prizes">
                                            @foreach($displayPrizes->take(3) as $idx => $prize)
                                                <span class="prize-badge">
                                                    {{ $prize['position'] ?? $idx + 1 }}° {{ $prize['name'] ?? 'Premio' }}
                                                </span>
                                            @endforeach
                                            @if($displayPrizes->count() > 3)
                                                <span class="prize-badge more">+{{ $displayPrizes->count() - 3 }}</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('frontend.raffles.show', $raffle->id) }}" wire:navigate class="raffle-card-btn">
                                            Ver detalles
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <section id="valoracion" class="fe-section">
                <div class="line-panel line-rating-wrap">
                    <h2 class="line-panel-title">Valoracion de usuarios</h2>

                    <div class="rating-summary">
                        <div>
                            <div class="rating-big">{{ number_format($ratingAverage, 1) }}</div>
                            <div class="rating-stars">★★★★★</div>
                        </div>
                        <div class="rating-count">{{ $ratingCount }} valoraciones publicadas</div>
                    </div>

                    @auth
                        <form wire:submit.prevent="saveRating" class="rating-form">
                            <div class="rating-pick" aria-label="Elegir valoracion">
                                @for($star = 1; $star <= 5; $star++)
                                    <button type="button" wire:click="setRating({{ $star }})" class="rating-star-btn {{ ($selectedRating ?? 0) >= $star ? 'active' : '' }}">★</button>
                                @endfor
                            </div>
                            @error('selectedRating') <div class="rating-error">{{ $message }}</div> @enderror
                            <textarea wire:model.defer="ratingMessage" class="line-rating-input" placeholder="Deja un mensaje sobre tu experiencia con esta linea" aria-label="Mensaje de valoracion"></textarea>
                            @error('ratingMessage') <div class="rating-error">{{ $message }}</div> @enderror
                            <div><button type="submit" class="fe-btn primary">Guardar valoracion</button></div>
                        </form>
                    @else
                        <div class="line-login-box">
                            <span>Inicia sesion para valorar esta linea.</span>
                            <a href="{{ route('login') }}" wire:navigate class="fe-btn ghost">Ingresar</a>
                        </div>
                    @endauth

                    @if($ratings->count())
                        <div class="rating-list">
                            @foreach($ratings as $rating)
                                <article class="rating-item">
                                    <div class="rating-avatar">
                                        @if($rating->user?->avatar)
                                            <img src="{{ $rating->user->avatar }}" alt="">
                                        @else
                                            {{ strtoupper(mb_substr($rating->user?->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="rating-head">
                                            <div>
                                                <div class="rating-name">{{ $rating->user?->name ?? 'Usuario' }}</div>
                                                <div class="rating-stars">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</div>
                                            </div>
                                            <time class="rating-date">{{ $rating->created_at->diffForHumans() }}</time>
                                        </div>
                                        @if($rating->message)
                                            <p class="rating-message">{{ $rating->message }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-panel">Todavia no hay valoraciones publicadas para esta linea.</div>
                    @endif
                </div>
            </section>
        </div>
    </section>
</div>
