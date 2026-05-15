@push('styles')
<style>
    .home-hero { padding:0; }
    .home-hero .fe-shell { width:100%; max-width:none; }
    .home-hero-carousel { display:grid; grid-auto-flow:column; grid-auto-columns:100%; gap:0; overflow-x:auto; scroll-snap-type:inline mandatory; border-radius:0; box-shadow:0 22px 70px rgba(0,0,0,.5); }
    .home-hero-carousel::-webkit-scrollbar { display:none; }
    .home-hero-slide { position:relative; width:100%; min-height:520px; overflow:hidden; border:0; border-radius:0; background:#120909; scroll-snap-align:start; text-decoration:none; display:block; }
    .home-hero-slide img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .home-hero-empty { position:absolute; inset:0; background:radial-gradient(60% 80% at 80% 20%, rgba(255,106,26,.65), transparent 60%), radial-gradient(40% 50% at 0% 80%, rgba(255,138,61,.35), transparent 60%), linear-gradient(135deg,#1a0606,#3a1308); }
    .lines-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:16px; margin-top:28px; }
    .prize-card, .bonus-card, .blog-card { border:1px solid rgba(255,255,255,.1); border-radius:18px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; box-shadow:0 16px 42px rgba(0,0,0,.32); }
    .prize-card { display:grid; grid-template-columns:120px 1fr; gap:14px; align-items:center; padding:12px; min-width:320px; }
    .prize-media { height:96px; border-radius:8px; background:rgba(255,106,26,.1); display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .prize-media img { width:100%; height:100%; object-fit:cover; }
    .prize-media span { font-family:var(--font-display); color:var(--orange); font-size:54px; line-height:1; }
    .prize-position { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:5px; }
    .prize-card h3 { margin:0; font-size:18px; line-height:1.2; }
    .raffle-banner { 
        position: relative;
    overflow: hidden;
    /* border: 1px solid rgba(255, 106, 26, .45); */
    border-radius: 10px;
    min-height: 330px;
    padding: 18px 22px 22px;
    }
    .raffle-full { width:100vw; margin-left:calc(50% - 50vw); border-radius:0; }
    .raffle-deco { position:absolute; z-index:0; pointer-events:none; opacity:.66; filter:drop-shadow(0 20px 24px rgba(255,106,26,.22)); }
    .raffle-deco img { width:100%; height:100%; object-fit:contain; display:block; }
    .raffle-deco.gift-left { left: 34px;
    bottom: 113px;
    width: 160px;
    height: 130px;
    transform: rotate(3deg);}
    .raffle-deco.gift-right {     right: 34px;
    bottom: 113px;
    width: 160px;
    height: 130px;
    transform: scaleX(-1) rotate(0deg); }
    .raffle-banner-head { position:relative; z-index:2; text-align:center; padding:0 150px 18px; }
    .raffle-banner-head h3 { font-family:var(--font-display); font-size:44px; line-height:.9; letter-spacing:.03em; margin:0; }
    .raffle-banner-head h3 span { color:#ff3d12; }
    .raffle-banner-head p { margin:6px auto 0; color:var(--muted); font-size:12px; line-height:1.45; max-width:720px; }
    .raffle-countdown { display:inline-flex; align-items:center; justify-content:center; gap:8px; margin-top:12px; border:1px solid rgba(255,106,26,.55); border-radius:999px; background:rgba(255,106,26,.12); color:#fff; padding:8px 16px; font-size:12px; font-weight:900; letter-spacing:.04em; text-transform:uppercase; box-shadow:0 0 22px rgba(255,106,26,.16); }
    .raffle-countdown strong { color:var(--orange); font-size:14px; }
    .raffle-prize-strip { position:relative; z-index:2; display:grid; grid-auto-flow:column; grid-auto-columns:minmax(310px, 400px); gap:14px; padding:10px 0 12px; align-items:end; justify-content:start; overflow-x:auto; overscroll-behavior-inline:contain; -webkit-overflow-scrolling:touch; scroll-snap-type:inline mandatory; scrollbar-width:thin; scrollbar-color:rgba(255,106,26,.72) rgba(255,255,255,.08); }
    .raffle-prize-strip::-webkit-scrollbar { height:8px; }
    .raffle-prize-strip::-webkit-scrollbar-track { background:rgba(255,255,255,.08); border-radius:999px; }
    .raffle-prize-strip::-webkit-scrollbar-thumb { background:rgba(255,106,26,.72); border-radius:999px; }
    .raffle-prize-tile { min-height:116px; display:grid; grid-template-columns:58px minmax(0, .86fr) minmax(112px, 1fr); align-items:center; gap:12px; border:1px solid rgba(255,106,26,.55); border-radius:8px; background:#0d0706; box-shadow:0 0 18px rgba(255,106,26,.09) inset, 0 18px 38px rgba(0,0,0,.28); padding:12px; overflow:hidden; scroll-snap-align:start; }
    .raffle-prize-tile.primary { min-height:146px; grid-template-columns:72px minmax(0, .82fr) minmax(150px, 1fr); border-color:rgba(255,179,71,.75); background:#120807; }
    .raffle-prize-strip.count-3 .raffle-prize-tile.primary { transform:translateY(-18px); }
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
    .bonus-card { min-height:250px; color:#fff; position:relative; border:3px dashed rgba(255,106,26,.9); border-radius:18px; background:
        radial-gradient(90% 100% at 0% 0%, rgba(255,106,26,.2), transparent 58%),
        linear-gradient(180deg,#180b08,#090505);
        box-shadow:0 18px 42px rgba(0,0,0,.42), 0 0 0 1px rgba(255,255,255,.04) inset; overflow:hidden; padding:30px; }
    .bonus-card::before, .bonus-card::after { content:none; }
    .bonus-ticket-main { min-height:194px; display:flex; flex-direction:column; justify-content:center; align-items:flex-start; gap:8px; padding:0; position:relative; }
    .bonus-ticket-main::before { content:none; }
    .bonus-ticket-main::after { content:none; }
    .bonus-ticket-kicker { color:var(--orange); font-size:10px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
    .bonus-card h3 { font-family:var(--font-display); font-size:34px; line-height:.92; margin:0; letter-spacing:.02em; color:#fff; text-transform:uppercase; max-width:270px; }
    .bonus-card p { color:var(--muted); font-size:13px; line-height:1.42; margin:12px 0 0; font-weight:700; max-width:270px; }
    .bonus-ticket-value { font-family:var(--font-display); color:var(--orange); font-size:58px; line-height:.82; text-shadow:0 0 22px rgba(255,106,26,.22); }
    .bonus-card strong { display:block; font-family:var(--font-mono); font-size:12px; letter-spacing:.04em; overflow-wrap:anywhere; color:var(--orange); }
    .bonus-card em { display:block; font-style:normal; font-weight:900; font-size:10px; color:var(--muted-2); }
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
        .raffle-deco { opacity:.14; }
        .raffle-banner-head { padding:18px 22px 8px; }
        .raffle-banner-head h3 { font-size:34px; }
        .raffle-prize-strip { grid-auto-columns:minmax(280px, 88vw); }
        .raffle-prize-strip.count-3 .raffle-prize-tile.primary { transform:none; }
        .raffle-prize-tile, .raffle-prize-tile.primary { grid-template-columns:54px minmax(0, 1fr) 104px; min-height:104px; }
        .raffle-rank, .raffle-prize-tile.primary .raffle-rank { font-size:72px; }
        .raffle-prize-image, .raffle-prize-tile.primary .raffle-prize-image { height:84px; }
    }
    @media (max-width: 560px) {
        .home-hero-slide { min-height:280px; }
        .raffle-banner { min-height:0; padding:14px 12px 18px; }
        .raffle-deco { display:none; }
        .raffle-banner-head { padding:10px 4px 8px; }
        .raffle-banner-head h3 { font-size:30px; line-height:1; overflow-wrap:anywhere; }
        .raffle-countdown { width:100%; border-radius:10px; padding:9px 12px; }
        .raffle-prize-strip { grid-auto-columns:minmax(248px, 86vw); }
        .raffle-prize-tile, .raffle-prize-tile.primary { grid-template-columns:48px minmax(0, 1fr); gap:10px; padding:10px; }
        .raffle-rank, .raffle-prize-tile.primary .raffle-rank { font-size:62px; }
        .raffle-prize-image, .raffle-prize-tile.primary .raffle-prize-image { grid-column:1 / -1; width:100%; height:118px; }
        .bonus-card { min-height:230px; padding:22px; }
        .bonus-ticket-main { min-height:176px; }
        .bonus-card h3 { font-size:28px; max-width:100%; }
        .bonus-ticket-value { font-size:46px; }
        .step-card, .about-box { padding:22px; }
        .step-card { min-height:auto; }
        .step-card h3 { font-size:26px; }
        .about-title { font-size:36px; }
    }
</style>
@endpush

<div>
    <section class="home-hero">
        <div class="fe-shell">
            @include('frontend.components.carousel', ['items' => $carouselItems])
        </div>
    </section>

    @if(($sections['como-empezar']['enabled'] ?? true))
    <section id="como-empezar" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['como-empezar']['kicker'] ?? 'Como funciona',
                'title' => $sections['como-empezar']['title'] ?? 'Empeza en',
                'highlight' => $sections['como-empezar']['highlight'] ?? '3 pasos',
                'subtitle' => $sections['como-empezar']['subtitle'] ?? 'Sin vueltas: contacto, carga y juego. Si necesitás ayuda, una persona te responde.',
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
    @endif

    @if(($sections['lineas']['enabled'] ?? true))
    <section id="lineas" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['lineas']['kicker'] ?? 'Empeza a jugar',
                'title' => $sections['lineas']['title'] ?? 'Lineas de',
                'highlight' => $sections['lineas']['highlight'] ?? 'atencion',
                'subtitle' => $sections['lineas']['subtitle'] ?? 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.',
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
    @endif

    @if(($sections['sorteo']['enabled'] ?? true) && $activeRaffle && ! empty($activeRaffle->prizes))
    <section id="sorteo" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['sorteo']['kicker'] ?? 'Mas chances para ganar',
                'title' => $sections['sorteo']['title'] ?? 'Sorteos de',
                'highlight' => $sections['sorteo']['highlight'] ?? 'esta semana',
                'subtitle' => $activeRaffle->title,
                'action' => '<a class="fe-btn ghost" href="'.route('sorteo.publico').'" wire:navigate>Ver sorteo</a>',
            ])
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

                    $displayPrizes = collect($activeRaffle->prizes)
                        ->sortBy(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1))
                        ->values();
                    $prizeCount = max(1, $displayPrizes->count());
                    $lineNames = $activeRaffle->lines->pluck('name')->filter()->join(', ');
                    $remaining = now()->diff($activeRaffle->end_date);
                    $remainingText = $activeRaffle->end_date->isFuture()
                        ? trim(collect([
                            $remaining->d ? $remaining->d.'d' : null,
                            $remaining->h ? $remaining->h.'h' : null,
                            $remaining->i ? $remaining->i.'m' : null,
                        ])->filter()->take(2)->join(' '))
                        : 'finalizando';
                    $raffleInfo = collect([
                        $activeRaffle->description,
                        $lineNames ? 'Lineas: '.$lineNames : null,
                    ])->filter()->join(' · ');
                @endphp

                <div class="raffle-banner">
                    <div class="raffle-deco gift-left" aria-hidden="true">
                        <img src="{{ asset('frontend/raffle-gift.png') }}" alt="">
                    </div>
                    <div class="raffle-deco gift-right" aria-hidden="true">
                        <img src="{{ asset('frontend/raffle-gift.png') }}" alt="">
                    </div>
                    <div class="raffle-banner-head">
                        <h3>{{ $activeRaffle->title }}</h3>
                        @if($raffleInfo)
                            <p>{{ $raffleInfo }}</p>
                        @endif
                        <div class="raffle-countdown">
                            Termina en <strong>{{ $remainingText ?: 'menos de 1m' }}</strong>
                        </div>
                    </div>
                    <div class="raffle-prize-strip count-{{ min($prizeCount, 3) }}" aria-label="Carousel de premios del sorteo activo">
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
        </div>
    </section>
    @endif

    @if(($sections['nosotros']['enabled'] ?? true))
    <section id="nosotros" class="fe-section">
        <div class="fe-shell">
            <div class="about-box">
                <div>
                    <div class="fe-kicker">Sobre RED PICANTES</div>
                    <h2 class="about-title">{{ $sections['nosotros']['title'] ?? 'Casino online con atencion' }} <span>{{ $sections['nosotros']['highlight'] ?? 'real' }}</span></h2>
                    <p class="about-copy">
                        {{ $sections['nosotros']['content'] ?? 'Una experiencia pensada para jugar facil: acceso rapido, promos claras, sorteos activos y soporte humano para acompaniarte.' }}
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
    @endif

    @if(($sections['bonos']['enabled'] ?? true) && $bonusItems->count())
    <section id="bonos" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['bonos']['kicker'] ?? 'Promos para jugar mas',
                'title' => $sections['bonos']['title'] ?? 'Bonos',
                'highlight' => $sections['bonos']['highlight'] ?? 'activos',
                'subtitle' => $sections['bonos']['subtitle'] ?? 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jornada.',
                'action' => '<a class="fe-btn ghost" href="'.route('frontend.bonuses').'" wire:navigate>Ver todos</a>',
            ])

            <div class="bonus-carousel" aria-label="Carousel de bonos activos">
                @foreach($bonusItems as $bonus)
                    @include('frontend.components.bonus-card', ['bonus' => $bonus])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(($sections['blog']['enabled'] ?? true) && $blogPosts->count())
    <section id="blog" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['blog']['kicker'] ?? 'Noticias y jugadas',
                'title' => '',
                'highlight' => $sections['blog']['highlight'] ?? 'Novedades',
                'subtitle' => $sections['blog']['subtitle'] ?? 'Enterate de novedades, sorteos, recomendaciones y promos nuevas antes de que pasen.',
                'action' => '<a class="fe-btn ghost" href="'.route('frontend.blog').'" wire:navigate>Ver novedades</a>',
            ])

            <div class="blog-grid">
                @foreach($blogPosts as $post)
                    @include('frontend.components.blog-card', ['post' => $post])
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>
