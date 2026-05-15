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
    /* Raffle Section Redesign - Lovable Style */
    .raffle-section-head { margin-bottom: 32px; }
    .raffle-main-title { font-family: var(--font-display); font-size: 64px; line-height: 0.9; text-transform: uppercase; margin: 0; }
    .raffle-main-title span { color: var(--orange); }
    .raffle-subtitle { color: var(--muted); font-size: 16px; margin: 12px 0 0; max-width: 700px; }
    
    .raffle-info-bar { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 24px 32px; display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
    .raffle-meta h4 { font-family: var(--font-display); font-size: 28px; margin: 0; text-transform: uppercase; display: flex; align-items: center; gap: 12px; }
    .raffle-meta h4::before { content: ""; width: 10px; height: 10px; border-radius: 999px; background: var(--orange); box-shadow: 0 0 12px var(--orange); }
    .raffle-meta p { color: var(--muted); font-size: 14px; margin: 4px 0 0; }
    
    .raffle-timer { display: flex; gap: 12px; }
    .timer-unit { text-align: center; min-width: 60px; }
    .timer-val { display: block; font-family: var(--font-display); font-size: 32px; line-height: 1; color: #fff; }
    .timer-label { display: block; font-size: 10px; font-weight: 900; color: var(--muted-2); text-transform: uppercase; margin-top: 4px; }
    
    .raffle-prizes-carousel { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 12px; scroll-snap-type: x mandatory; }
    .raffle-prizes-carousel::-webkit-scrollbar { height: 6px; }
    .raffle-prizes-carousel::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    .raffle-prizes-carousel::-webkit-scrollbar-thumb { background: var(--orange); border-radius: 999px; }
    
    .raffle-prize-item { min-width: 320px; flex: 0 0 320px; aspect-ratio: 4/5; border-radius: 24px; overflow: hidden; position: relative; scroll-snap-align: start; border: 1px solid rgba(255,255,255,0.1); }
    .raffle-prize-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
    .raffle-prize-item:hover img { transform: scale(1.05); }
    .raffle-prize-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.9) 100%); padding: 24px; display: flex; flex-direction: column; justify-content: flex-end; }
    
    .prize-tag { display: inline-block; width: max-content; padding: 4px 12px; background: var(--orange); color: #000; font-weight: 900; font-size: 11px; text-transform: uppercase; border-radius: 999px; margin-bottom: 12px; }
    .prize-name { font-family: var(--font-display); font-size: 28px; line-height: 1; color: #fff; text-transform: uppercase; margin-bottom: 6px; }
    .prize-value { color: var(--muted); font-size: 13px; font-weight: 700; }

    @media (max-width: 768px) {
        .raffle-main-title { font-size: 44px; }
        .raffle-info-bar { flex-direction: column; align-items: flex-start; padding: 20px; }
        .raffle-prize-item { min-width: 260px; flex-basis: 260px; }
    }
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
            <div class="raffle-section-head">
                <h2 class="raffle-main-title">PREMIOS <span>EN JUEGO</span></h2>
                <p class="raffle-subtitle">{{ $activeRaffle->description }} · Cada $1.000 depositados = 1 ticket.</p>
            </div>

            <div class="raffle-info-bar">
                <div class="raffle-meta">
                    <h4>{{ strtoupper($activeRaffle->title) }}</h4>
                    <p>Pozo total: <strong>$2.500.000</strong> en premios físicos y cash</p>
                </div>
                @php
                    $remaining = now()->diff($activeRaffle->end_date);
                    $days = str_pad($remaining->d, 2, '0', STR_PAD_LEFT);
                    $hours = str_pad($remaining->h, 2, '0', STR_PAD_LEFT);
                    $mins = str_pad($remaining->i, 2, '0', STR_PAD_LEFT);
                    $secs = str_pad($remaining->s, 2, '0', STR_PAD_LEFT);

                    $prizeImage = function (?string $image): ?string {
                        if (! $image) return null;
                        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/storage/'])) return $image;
                        return asset('storage/'.$image);
                    };
                    $displayPrizes = collect($activeRaffle->prizes)->sortBy(fn ($prize, $index) => (int) ($prize['position'] ?? $index + 1))->values();
                @endphp
                <div class="raffle-timer">
                    <div class="timer-unit"><span class="timer-val">{{ $days }}</span><span class="timer-label">DÍAS</span></div>
                    <div class="timer-unit"><span class="timer-val">{{ $hours }}</span><span class="timer-label">HRS</span></div>
                    <div class="timer-unit"><span class="timer-val">{{ $mins }}</span><span class="timer-label">MIN</span></div>
                    <div class="timer-unit"><span class="timer-val">{{ $secs }}</span><span class="timer-label">SEG</span></div>
                </div>
            </div>

            <div class="raffle-prizes-carousel">
                @foreach($displayPrizes as $index => $prize)
                    @php
                        $position = (int) ($prize['position'] ?? $index + 1);
                        $image = $prizeImage($prize['image'] ?? null);
                        $placeholderImages = [
                            1 => 'https://images.unsplash.com/photo-1616348436168-de43ad0db179?q=80&w=1000&auto=format&fit=crop',
                            2 => 'https://images.unsplash.com/photo-1558981403-c5f91cbba527?q=80&w=1000&auto=format&fit=crop',
                            3 => 'https://images.unsplash.com/photo-1505156868547-9b49f4df4e04?q=80&w=1000&auto=format&fit=crop',
                            4 => 'https://images.unsplash.com/photo-1605462863863-10d9e47e15ee?q=80&w=1000&auto=format&fit=crop',
                        ];
                        $displayImage = $image ?: ($placeholderImages[$position] ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1000&auto=format&fit=crop');
                    @endphp
                    <article class="raffle-prize-item">
                        <img src="{{ $displayImage }}" alt="{{ $prize['name'] ?? 'Premio '.$position }}">
                        <div class="raffle-prize-overlay">
                            <span class="prize-tag">{{ $position }}° PUESTO</span>
                            <h3 class="prize-name">{{ $prize['name'] ?? 'Premio sorpresa' }}</h3>
                            <div class="prize-value">Valor estimado: ${{ number_format((float) ($prize['amount'] ?? 1000000), 0, ',', '.') }}</div>
                        </div>
                    </article>
                @endforeach
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
