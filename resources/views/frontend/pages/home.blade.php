@push('styles')
<style>
    .home-hero { padding:0; }
    .home-hero .fe-shell { width:100%; max-width:none; }
    .home-hero-carousel { display:grid; grid-auto-flow:column; grid-auto-columns:100%; gap:0; overflow-x:auto; scroll-snap-type:inline mandatory; border-radius:0; box-shadow:0 22px 70px rgba(0,0,0,.5); }
    .home-hero-carousel::-webkit-scrollbar { display:none; }
    .home-hero-slide { position:relative; width:100vw; min-height:520px; overflow:hidden; border:0; border-radius:0; background:#120909; scroll-snap-align:start; text-decoration:none; display:block; }
    .home-hero-slide img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .home-hero-empty { position:absolute; inset:0; background:radial-gradient(60% 80% at 80% 20%, rgba(255,106,26,.65), transparent 60%), radial-gradient(40% 50% at 0% 80%, rgba(255,138,61,.35), transparent 60%), linear-gradient(135deg,#1a0606,#3a1308); }
    .lines-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .line-card { overflow:hidden; border:1px solid rgba(255,106,26,.24); border-radius:18px; background:linear-gradient(180deg, rgba(255,106,26,.12) 0%, rgba(20,8,8,.9) 100%); position:relative; }
    .line-card::before { content:""; position:absolute; top:-34px; right:-34px; width:130px; height:130px; border-radius:999px; background:radial-gradient(circle, rgba(255,106,26,.38), transparent 70%); pointer-events:none; }
    .line-cover { height:140px; position:relative; background:radial-gradient(80% 100% at 80% 0%, rgba(255,106,26,.34), transparent 70%), #120909; }
    .line-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    .line-avatar { position:absolute; left:16px; bottom:-24px; width:58px; height:58px; border-radius:14px; border:2px solid #120909; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; color:#190702; font-weight:900; overflow:hidden; }
    .line-avatar img { width:100%; height:100%; object-fit:cover; }
    .line-body { padding:34px 16px 16px; }
    .line-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .line-head h3 { font-family:var(--font-display); font-size:28px; line-height:1; letter-spacing:.03em; margin:0 0 6px; }
    .line-head p { color:var(--muted); font-size:12px; line-height:1.45; margin:0; }
    .line-state { color:var(--good); background:rgba(37,196,107,.1); border:1px solid rgba(37,196,107,.22); border-radius:999px; padding:4px 8px; font-size:10px; font-weight:900; white-space:nowrap; }
    .line-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:15px; }
    .line-contact { min-height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; padding:0 12px; color:#fff; background:rgba(255,255,255,.06); border:1px solid var(--line-2); text-decoration:none; font-size:12px; font-weight:800; flex:1; }
    .line-contact:hover { border-color:var(--orange); color:var(--orange); }
    .line-contact.muted { color:var(--muted-2); }
    .prize-card, .bonus-card, .blog-card { border:1px solid rgba(255,255,255,.1); border-radius:18px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; box-shadow:0 16px 42px rgba(0,0,0,.32); }
    .prize-card { display:grid; grid-template-columns:120px 1fr; gap:14px; align-items:center; padding:12px; min-width:320px; }
    .prize-media { height:96px; border-radius:8px; background:rgba(255,106,26,.1); display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .prize-media img { width:100%; height:100%; object-fit:cover; }
    .prize-media span { font-family:var(--font-display); color:var(--orange); font-size:54px; line-height:1; }
    .prize-position { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:5px; }
    .prize-card h3 { margin:0; font-size:18px; line-height:1.2; }
    .raffle-banner { position:relative; overflow:hidden; border:1px solid rgba(255,106,26,.45); border-radius:10px; background:radial-gradient(80% 90% at 50% 0%, rgba(255,106,26,.16), transparent 58%), linear-gradient(180deg,#040404 0%,#0a0504 55%,#050505 100%); min-height:250px; box-shadow:0 20px 55px rgba(0,0,0,.55), 0 0 0 1px rgba(255,170,80,.08) inset; }
    .raffle-deco { position:absolute; z-index:0; pointer-events:none; opacity:.9; filter:drop-shadow(0 20px 24px rgba(255,106,26,.22)); }
    .raffle-deco.gift { left:26px; top:18px; width:112px; height:112px; border-radius:18px; background:linear-gradient(135deg,#ff8a1f,#ffb347); transform:rotate(-12deg); }
    .raffle-deco.gift::before { content:""; position:absolute; left:50%; top:0; bottom:0; width:18px; transform:translateX(-50%); background:#2b0b02; opacity:.28; }
    .raffle-deco.gift::after { content:""; position:absolute; left:14px; right:14px; top:34px; height:18px; background:#2b0b02; opacity:.28; border-radius:6px; }
    .raffle-deco.roulette { right:28px; top:20px; width:122px; height:122px; border-radius:999px; background:conic-gradient(#ff6a1a 0 18deg,#111 18deg 36deg,#ffb347 36deg 54deg,#111 54deg 72deg,#ff6a1a 72deg 90deg,#111 90deg 108deg,#ffb347 108deg 126deg,#111 126deg 144deg,#ff6a1a 144deg 162deg,#111 162deg 180deg,#ffb347 180deg 198deg,#111 198deg 216deg,#ff6a1a 216deg 234deg,#111 234deg 252deg,#ffb347 252deg 270deg,#111 270deg 288deg,#ff6a1a 288deg 306deg,#111 306deg 324deg,#ffb347 324deg 342deg,#111 342deg 360deg); border:8px solid #2a0d05; }
    .raffle-deco.roulette::after { content:""; position:absolute; inset:32px; border-radius:999px; background:radial-gradient(circle,#ffb347 0 14%,#140604 16% 100%); border:4px solid rgba(255,255,255,.08); }
    .raffle-banner-head { position:relative; z-index:1; text-align:center; padding:16px 180px 8px; }
    .raffle-banner-head h3 { font-family:var(--font-display); font-size:42px; line-height:.9; letter-spacing:.03em; margin:0; }
    .raffle-banner-head h3 span { color:#ff3d12; }
    .raffle-banner-head p { margin:6px auto 0; color:var(--muted); font-size:12px; line-height:1.45; max-width:720px; }
    .raffle-prize-strip { position:relative; z-index:1; display:grid; gap:10px; padding:8px 18px 18px; align-items:stretch; justify-content:center; }
    .raffle-prize-strip.count-1 { grid-template-columns:minmax(0, 400px); }
    .raffle-prize-strip.count-2 { grid-template-columns:repeat(2, minmax(0, 400px)); }
    .raffle-prize-strip.count-3 { grid-template-columns:repeat(3, minmax(0, 400px)); align-items:end; }
    .raffle-prize-tile { min-height:108px; display:grid; grid-template-columns:58px minmax(0, .78fr) minmax(110px, 1fr); align-items:center; gap:10px; border:1px solid rgba(255,106,26,.55); border-radius:8px; background:linear-gradient(180deg,rgba(255,106,26,.08),rgba(255,106,26,.02)); box-shadow:0 0 18px rgba(255,106,26,.09) inset; padding:10px; overflow:hidden; }
    .raffle-prize-tile.primary { min-height:126px; grid-template-columns:70px minmax(0, .7fr) minmax(140px, 1fr); border-color:rgba(255,179,71,.75); background:radial-gradient(90% 120% at 80% 45%, rgba(255,179,71,.16), transparent 60%), linear-gradient(180deg,rgba(255,106,26,.1),rgba(255,106,26,.02)); }
    .raffle-prize-strip.count-3 .raffle-prize-tile.primary { transform:translateY(-14px); }
    .raffle-rank { font-family:var(--font-display); font-size:82px; line-height:.8; color:var(--orange); text-align:center; text-shadow:0 0 20px rgba(255,106,26,.32); }
    .raffle-prize-tile.primary .raffle-rank { font-size:100px; color:#ff8a1f; }
    .raffle-prize-info strong { display:block; color:#fff; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
    .raffle-prize-info span { display:block; color:rgba(255,255,255,.78); font-size:12px; line-height:1.25; }
    .raffle-prize-info b { display:block; color:var(--orange); font-size:15px; margin-top:3px; }
    .raffle-prize-image { height:92px; border-radius:6px; background:radial-gradient(70% 70% at 50% 50%, rgba(255,106,26,.32), transparent 72%); display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .raffle-prize-tile.primary .raffle-prize-image { height:108px; }
    .raffle-prize-image img { width:100%; height:100%; object-fit:cover; }
    .raffle-prize-image span { font-family:var(--font-display); color:rgba(255,255,255,.12); font-size:44px; letter-spacing:.05em; }
    .bonus-card { min-height:250px; padding:26px; background:radial-gradient(110% 90% at 10% 0%, rgba(255,179,71,.42), transparent 42%), linear-gradient(135deg,#ff6a1a 0%,#ffb347 100%); color:#190702; position:relative; }
    .bonus-card::after { content:"BONO"; position:absolute; right:18px; bottom:10px; font-family:var(--font-display); font-size:70px; color:rgba(25,7,2,.1); line-height:1; }
    .bonus-value { font-family:var(--font-display); color:#190702; font-size:64px; line-height:.9; margin-bottom:12px; }
    .bonus-card h3 { font-family:var(--font-display); font-size:36px; line-height:.9; margin:0 0 8px; letter-spacing:.02em; max-width:280px; }
    .bonus-card p { color:rgba(25,7,2,.76); font-weight:700; }
    .bonus-meta { color:rgba(25,7,2,.68) !important; font-weight:900; position:relative; z-index:1; }
    .bonus-card p { color:var(--muted); font-size:13px; line-height:1.45; margin:0; }
    .bonus-meta { display:flex; justify-content:space-between; gap:10px; margin-top:20px; color:var(--muted-2); font-family:var(--font-mono); font-size:11px; }
    .blog-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .blog-thumb { height:150px; background:radial-gradient(80% 100% at 80% 10%, rgba(255,106,26,.35), transparent 70%), #140909; display:flex; align-items:end; justify-content:flex-end; padding:12px; overflow:hidden; }
    .blog-thumb img { width:100%; height:100%; object-fit:cover; margin:-12px; }
    .blog-thumb span { font-family:var(--font-display); font-size:24px; color:rgba(255,255,255,.82); }
    .blog-body { padding:16px; }
    .blog-body time { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.1em; }
    .blog-body h3 { font-size:18px; line-height:1.2; margin:7px 0; }
    .blog-body p { color:var(--muted); font-size:13px; line-height:1.45; margin:0; }
    .steps-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .step-card { padding:26px; border:1px solid var(--line-warm); border-radius:18px; background:radial-gradient(120% 80% at 0% 0%, rgba(255,106,26,.24), transparent 60%), linear-gradient(180deg,#1c0d0a,#120909); min-height:220px; }
    .step-num { font-family:var(--font-display); color:var(--orange); font-size:58px; line-height:.9; }
    .step-card h3 { font-family:var(--font-display); font-size:30px; line-height:.98; margin:12px 0 8px; letter-spacing:.02em; }
    .step-card p { color:var(--muted); font-size:13px; line-height:1.5; margin:0; }
    .about-box { display:grid; grid-template-columns:1fr 1fr; gap:30px; align-items:center; padding:34px; border:1px solid var(--line-warm); border-radius:var(--r-xl); background:radial-gradient(70% 80% at 90% 0%, rgba(255,106,26,.22), transparent 64%), linear-gradient(120deg,#1a0606,#2a0e0e); overflow:hidden; }
    .about-title { font-family:var(--font-display); font-size:48px; line-height:.98; margin:0 0 12px; letter-spacing:.02em; }
    .about-title span { color:var(--orange); }
    .about-copy { color:var(--muted); font-size:14px; line-height:1.65; margin:0; }
    .about-features { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .about-feature { min-height:118px; padding:16px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); }
    .about-feature strong { display:block; margin-bottom:6px; font-size:14px; }
    .about-feature p { color:var(--muted); font-size:12px; line-height:1.45; margin:0; }
    .empty-panel { border:1px dashed var(--line-2); border-radius:var(--r-md); color:var(--muted); padding:24px; text-align:center; font-size:13px; }
    @media (max-width: 920px) {
        .lines-grid, .blog-grid, .steps-grid, .about-box { grid-template-columns:1fr; }
        .about-features { grid-template-columns:1fr; }
        .home-hero-slide { min-height:360px; }
        .raffle-deco { opacity:.2; }
        .raffle-banner-head { padding:18px 22px 8px; }
        .raffle-banner-head h3 { font-size:34px; }
        .raffle-prize-strip,
        .raffle-prize-strip.count-1,
        .raffle-prize-strip.count-2,
        .raffle-prize-strip.count-3 { grid-template-columns:minmax(0, 1fr) !important; }
        .raffle-prize-strip.count-3 .raffle-prize-tile.primary { transform:none; }
        .raffle-prize-tile, .raffle-prize-tile.primary { grid-template-columns:54px minmax(0, 1fr) 104px; min-height:104px; }
        .raffle-rank, .raffle-prize-tile.primary .raffle-rank { font-size:72px; }
        .raffle-prize-image, .raffle-prize-tile.primary .raffle-prize-image { height:84px; }
    }
</style>
@endpush

<div>
    <section class="home-hero">
        <div class="fe-shell">
            @include('frontend.components.carousel', ['items' => $carouselItems])
        </div>
    </section>

    <section id="lineas" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => 'Empeza a jugar',
                'title' => 'Lineas de',
                'highlight' => 'atencion',
                'subtitle' => 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.',
            ])

            @if($lines->count())
                <div class="lines-grid">
                    @foreach($lines as $line)
                        @include('frontend.components.line-card', ['line' => $line])
                    @endforeach
                </div>
            @else
                <div class="empty-panel">No hay lineas activas cargadas todavia.</div>
            @endif
        </div>
    </section>

    <section id="sorteo" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => 'Mas chances para ganar',
                'title' => 'Premios del',
                'highlight' => 'sorteo',
                'subtitle' => $activeRaffle ? $activeRaffle->title : 'Jugá, participá y seguí los premios disponibles en cada sorteo activo.',
                'action' => $activeRaffle ? '<a class="fe-btn ghost" href="'.route('sorteo.publico').'" wire:navigate>Ver sorteo</a>' : null,
            ])

            @if($activeRaffle && ! empty($activeRaffle->prizes))
                @php
                    $prizeImage = function (?string $image): ?string {
                        if (! $image) {
                            return null;
                        }

                        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
                            return $image;
                        }

                        return asset('storage/'.$image);
                    };

                    $rankedPrizes = collect($activeRaffle->prizes)
                        ->sortBy(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1))
                        ->take(3)
                        ->values();

                    $displayPrizes = $rankedPrizes;
                    $prizeCount = max(1, $displayPrizes->count());
                    if ($displayPrizes->count() === 3) {
                        $displayPrizes = collect([2, 1, 3])
                            ->map(fn ($position) => $rankedPrizes->first(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1) === $position))
                            ->filter()
                            ->values();

                        if ($displayPrizes->count() < 3) {
                            $displayPrizes = $rankedPrizes;
                        }
                    }
                    $lineNames = $activeRaffle->lines->pluck('name')->filter()->join(', ');
                    $endNumber = $activeRaffle->end_number ?: (
                        $activeRaffle->numbers_limit
                            ? (int) $activeRaffle->start_number + (int) $activeRaffle->numbers_limit - 1
                            : null
                    );
                    $numbersInfo = $activeRaffle->numbers_limit
                        ? $activeRaffle->numbers_limit.' numeros disponibles'
                        : ($endNumber ? 'Numeros desde '.$activeRaffle->start_number.' hasta '.$endNumber : 'Numeros desde '.$activeRaffle->start_number);
                    $raffleInfo = collect([
                        $activeRaffle->title,
                        'Activo hasta '.$activeRaffle->end_date->format('d/m/Y H:i'),
                        $lineNames ? 'Lineas: '.$lineNames : null,
                        $activeRaffle->platform?->name ? 'Plataforma: '.$activeRaffle->platform->name : null,
                        $numbersInfo,
                    ])->filter()->join(' · ');
                @endphp

                <div class="raffle-banner">
                    <div class="raffle-deco gift" aria-hidden="true"></div>
                    <div class="raffle-deco roulette" aria-hidden="true"></div>
                    <div class="raffle-banner-head">
                        <h3>SORTEO DE <span>RED PICANTES BET</span></h3>
                        <p>{{ $raffleInfo }}</p>
                    </div>
                    <div class="raffle-prize-strip count-{{ $prizeCount }}">
                        @foreach($displayPrizes as $index => $prize)
                            @php
                                $position = (int) ($prize['position'] ?? $index + 1);
                                $image = $prizeImage($prize['image'] ?? null);
                            @endphp
                            <article class="raffle-prize-tile {{ $position === 1 ? 'primary' : '' }}">
                                <div class="raffle-rank">{{ $position }}</div>
                                <div class="raffle-prize-info">
                                    <strong>Premio {{ $position }}</strong>
                                    <span>{{ $prize['name'] ?? 'Premio sorpresa' }}</span>
                                    @if(!empty($prize['amount']))
                                        <b>${{ number_format((float) $prize['amount'], 0, ',', '.') }}</b>
                                    @endif
                                </div>
                                <div class="raffle-prize-image">
                                    @if($image)
                                        <img src="{{ $image }}" alt="{{ $prize['name'] ?? 'Premio '.$position }}">
                                    @else
                                        <span>BET</span>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="empty-panel">No hay premios publicados para un sorteo activo.</div>
            @endif
        </div>
    </section>

    <section id="bonos" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => 'Promos para jugar mas',
                'title' => 'Bonos',
                'highlight' => 'activos',
                'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
            ])

            @if($bonusItems->count())
                <div class="fe-h-scroll">
                    @foreach($bonusItems as $bonus)
                        @include('frontend.components.bonus-card', ['bonus' => $bonus])
                    @endforeach
                </div>
            @else
                <div class="empty-panel">No hay bonos activos vigentes.</div>
            @endif
        </div>
    </section>

    <section id="blog" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => 'Noticias y jugadas',
                'title' => 'Ultimas 3',
                'highlight' => 'entradas',
                'subtitle' => 'Enterate de novedades, sorteos, recomendaciones y promos nuevas antes de que pasen.',
            ])

            @if($blogPosts->count())
                <div class="blog-grid">
                    @foreach($blogPosts as $post)
                        @include('frontend.components.blog-card', ['post' => $post])
                    @endforeach
                </div>
            @else
                <div class="empty-panel">No hay entradas de blog publicadas.</div>
            @endif
        </div>
    </section>

    <section id="como-empezar" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => 'Como funciona',
                'title' => 'Empeza en',
                'highlight' => '3 pasos',
                'subtitle' => 'Sin vueltas: contacto, carga y juego. Si necesitás ayuda, una persona te responde.',
            ])

            <div class="steps-grid">
                <article class="step-card">
                    <div class="step-num">01</div>
                    <h3>Pedí tu usuario</h3>
                    <p>Elegí una línea de atención y solicitá el acceso para empezar a jugar.</p>
                </article>
                <article class="step-card">
                    <div class="step-num">02</div>
                    <h3>Cargá saldo</h3>
                    <p>Consultá medios de carga, promociones disponibles y bonos para tu cuenta.</p>
                </article>
                <article class="step-card">
                    <div class="step-num">03</div>
                    <h3>Entrá a jugar</h3>
                    <p>Disfrutá tus juegos favoritos, participá en sorteos y pedí asistencia cuando quieras.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="nosotros" class="fe-section">
        <div class="fe-shell">
            <div class="about-box">
                <div>
                    <div class="fe-kicker">Sobre RED PICANTES</div>
                    <h2 class="about-title">Casino online con atencion <span>real</span></h2>
                    <p class="about-copy">
                        Una experiencia pensada para jugar facil: acceso rapido, promos claras, sorteos activos y soporte humano para acompaniarte.
                    </p>
                </div>
                <div class="about-features">
                    <div class="about-feature">
                        <strong>Alta rapida</strong>
                        <p>Contactás una línea y pedís tu usuario sin formularios eternos.</p>
                    </div>
                    <div class="about-feature">
                        <strong>Bonos vigentes</strong>
                        <p>Promociones para recargar, arrancar con ventaja y jugar más.</p>
                    </div>
                    <div class="about-feature">
                        <strong>Sorteos activos</strong>
                        <p>Premios y chances extra para usuarios que participan.</p>
                    </div>
                    <div class="about-feature">
                        <strong>Soporte humano</strong>
                        <p>Atención directa para cargas, retiros, dudas y novedades.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
