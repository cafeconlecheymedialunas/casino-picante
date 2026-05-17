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
                @foreach(($sections['como-empezar']['repeater_data'] ?? []) as $index => $step)
                <article class="step-card">
                    <div class="step-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3>{{ $step['title'] ?? '' }}</h3>
                    <p>{{ $step['subtitle'] ?? '' }}</p>
                </article>
                @endforeach
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

    @if(($sections['sorteo']['enabled'] ?? true) && $raffles->count())
    <section id="sorteo" class="fe-section">
        <div class="fe-shell">
            <div class="raffle-section-head">
                <h2 class="raffle-main-title">{{ $sections['sorteo']['title'] ?? 'SORTEOS' }} <span>{{ $sections['sorteo']['highlight'] ?? 'ACTIVOS' }}</span></h2>
            </div>

            @if($raffles->count() > 1)
            <div class="sorteo-slider-wrapper" x-data="rafflesSlider()" x-init="initSlider()">
                <button type="button" class="slider-btn slider-btn-prev" @click="prev()">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="raffles-slider-track" x-ref="track">
                    @foreach($raffles as $index => $raffle)
                    <div class="raffle-slide" x-ref="slide{{ $index }}">
                        <div class="raffle-slide-header">
                            <div class="raffle-meta">
                                <h4>{{ strtoupper($raffle->title) }}</h4>
                                @if($raffle->description)
                                    <p>{{ $raffle->description }}</p>
                                @endif
                            </div>
                            <div class="raffle-timer" data-raffle-countdown="{{ $raffle->end_date->toIso8601String() }}">
                                @php
                                    $remaining = now()->diff($raffle->end_date);
                                @endphp
                                <div class="timer-unit"><span class="timer-val" data-unit="days">{{ str_pad($remaining->d, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">DIAS</span></div>
                                <div class="timer-unit"><span class="timer-val" data-unit="hours">{{ str_pad($remaining->h, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">HRS</span></div>
                                <div class="timer-unit"><span class="timer-val" data-unit="minutes">{{ str_pad($remaining->i, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">MIN</span></div>
                                <div class="timer-unit"><span class="timer-val" data-unit="seconds">{{ str_pad($remaining->s, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">SEG</span></div>
                            </div>
                        </div>
                        <div class="raffle-prizes-carousel">
                            @foreach(collect($raffle->prizes)->sortBy(fn ($prize, $idx) => (int) ($prize['position'] ?? $idx + 1))->values() as $pIndex => $prize)
                            <article class="raffle-prize-item">
                                @php
                                    $position = (int) ($prize['position'] ?? $pIndex + 1);
                                    $image = $prize['image'] ?? null;
                                    if ($image && !Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
                                        $image = asset('storage/'.$image);
                                    }
                                    $displayImage = $image ?: 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1000&auto=format&fit=crop';
                                @endphp
                                <img src="{{ $displayImage }}" alt="{{ $prize['name'] ?? 'Premio '.$position }}">
                                <div class="raffle-prize-overlay">
                                    <span class="prize-tag">{{ $position }}° PUESTO</span>
                                    <h3 class="prize-name">{{ $prize['name'] ?? 'Premio sorpresa' }}</h3>
                                    <div class="prize-value">Valor estimado: ${{ number_format((float) ($prize['amount'] ?? 1000000), 0, ',', '.') }}</div>
                                </div>
                            </article>
                            @endforeach
                        </div>
                        <div class="raffle-slide-footer">
                            <a href="{{ route('frontend.raffles.show', $raffle->id) }}" wire:navigate class="fe-btn primary">Ver sorteo</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="slider-btn slider-btn-next" @click="next()">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            @else
            @php
                $raffle = $raffles->first();
                $remaining = now()->diff($raffle->end_date);
            @endphp
            <div class="raffle-single">
                <div class="raffle-slide-header">
                    <div class="raffle-meta">
                        <h4>{{ strtoupper($raffle->title) }}</h4>
                        @if($raffle->description)
                            <p>{{ $raffle->description }}</p>
                        @endif
                    </div>
                    <div class="raffle-timer" data-raffle-countdown="{{ $raffle->end_date->toIso8601String() }}">
                        <div class="timer-unit"><span class="timer-val" data-unit="days">{{ str_pad($remaining->d, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">DIAS</span></div>
                        <div class="timer-unit"><span class="timer-val" data-unit="hours">{{ str_pad($remaining->h, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">HRS</span></div>
                        <div class="timer-unit"><span class="timer-val" data-unit="minutes">{{ str_pad($remaining->i, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">MIN</span></div>
                        <div class="timer-unit"><span class="timer-val" data-unit="seconds">{{ str_pad($remaining->s, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">SEG</span></div>
                    </div>
                </div>
                <div class="raffle-prizes-carousel">
                    @foreach(collect($raffle->prizes)->sortBy(fn ($prize, $idx) => (int) ($prize['position'] ?? $idx + 1))->values() as $pIndex => $prize)
                    <article class="raffle-prize-item">
                        @php
                            $position = (int) ($prize['position'] ?? $pIndex + 1);
                            $image = $prize['image'] ?? null;
                            if ($image && !Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
                                $image = asset('storage/'.$image);
                            }
                            $displayImage = $image ?: 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1000&auto=format&fit=crop';
                        @endphp
                        <img src="{{ $displayImage }}" alt="{{ $prize['name'] ?? 'Premio '.$position }}">
                        <div class="raffle-prize-overlay">
                            <span class="prize-tag">{{ $position }}° PUESTO</span>
                            <h3 class="prize-name">{{ $prize['name'] ?? 'Premio sorpresa' }}</h3>
                            <div class="prize-value">Valor estimado: ${{ number_format((float) ($prize['amount'] ?? 1000000), 0, ',', '.') }}</div>
                        </div>
                    </article>
                    @endforeach
                </div>
                <div class="raffle-slide-footer">
                    <a href="{{ route('frontend.raffles.show', $raffle->id) }}" wire:navigate class="fe-btn primary">Ver sorteo</a>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    @if(($sections['nosotros']['enabled'] ?? true))
    <section id="nosotros" class="fe-section">
        <div class="fe-shell">
            <div class="about-box">
                <div>
                    <div class="fe-kicker">{{ $sections['nosotros']['kicker'] ?? 'Sobre RED PICANTES' }}</div>
                    <h2 class="about-title">{{ $sections['nosotros']['title'] ?? 'Casino online con atencion' }} <span>{{ $sections['nosotros']['highlight'] ?? 'real' }}</span></h2>
                    <p class="about-copy">
                        {{ $sections['nosotros']['subtitle'] ?? 'Una experiencia pensada para jugar facil: acceso rapido, promos claras, sorteos activos y soporte humano para acompaniarte.' }}
                    </p>
                </div>
                <div class="about-features">
                    @foreach(($sections['nosotros']['repeater_data'] ?? []) as $feature)
                    <div class="about-feature">
                        <strong>{{ $feature['title'] ?? '' }}</strong>
                        <p>{{ $feature['subtitle'] ?? '' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    @if(($sections['bonos']['enabled'] ?? true))
    <section id="bonos" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['bonos']['kicker'] ?? 'Promos para jugar mas',
                'title' => $sections['bonos']['title'] ?? 'Bonos',
                'highlight' => $sections['bonos']['highlight'] ?? 'activos',
                'subtitle' => $sections['bonos']['subtitle'] ?? 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
                'action' => '<a class="fe-btn ghost" href="'.route('frontend.bonuses').'" wire:navigate>Ver todos</a>',
            ])

            @if($bonusItems->count())
                <div class="bonus-carousel">
                    <button type="button" class="slider-btn slider-btn-prev bonus-swiper-btn-prev">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    @foreach($bonusItems as $bonus)
                        @include('frontend.components.bonus-card', ['bonus' => $bonus])
                    @endforeach
                    <button type="button" class="slider-btn slider-btn-next bonus-swiper-btn-next">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            @else
                <div class="empty-panel">No hay bonos activos vigentes.</div>
            @endif
        </div>
    </section>
    @endif

    @if(($sections['blog']['enabled'] ?? true))
    <section id="blog" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['blog']['kicker'] ?? 'Noticias y jugadas',
                'title' => $sections['blog']['title'] ?? '',
                'highlight' => $sections['blog']['highlight'] ?? 'Novedades',
                'subtitle' => $sections['blog']['subtitle'] ?? 'Enterate de novedades, sorteos, recomendaciones y promos nuevas antes de que pasen.',
                'action' => '<a class="fe-btn ghost" href="'.route('frontend.blog').'" wire:navigate>Ver novedades</a>',
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
    @endif

</div>

@push('styles')
<style>
    .home-hero { padding:0; }
    .home-hero .fe-shell { width:100%; max-width:none; }
    .home-hero-carousel { display:grid; grid-auto-flow:column; grid-auto-columns:100%; gap:0; overflow-x:auto; scroll-snap-type:inline mandatory; border-radius:0; box-shadow:0 22px 70px rgba(0,0,0,.5); }
    .home-hero-carousel::-webkit-scrollbar { display:none; }
    .home-hero-slide { position:relative; width:100%; min-height:520px; overflow:hidden; border:0; border-radius:0; background:#120909; scroll-snap-align:start; text-decoration:none; display:block; }
    .home-hero-slide img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .home-hero-empty { position:absolute; inset:0; background:radial-gradient(60% 80% at 80% 20%, rgba(255,106,26,.65), transparent 60%), radial-gradient(40% 50% at 0% 80%, rgba(255,138,61,.35), transparent 60%), linear-gradient(135deg,#1a0606,#3a1308); }
    .home-hero-content { position:absolute; bottom:0; left:0; right:0; padding:24px; background:linear-gradient(transparent, rgba(0,0,0,0.8)); }
    .home-hero-title { font-family:var(--font-display); font-size:42px; line-height:1; color:#fff; margin:0 0 8px; }
    .home-hero-subtitle { font-size:14px; color:rgba(255,255,255,0.8); margin:0; }
    .home-hero-btn { display:inline-flex; align-items:center; gap:8px; padding:12px 20px; border-radius:999px; background:var(--orange); color:#190702; font-size:13px; font-weight:800; text-decoration:none; margin-top:16px; }
    @media (max-width: 768px) {
        .home-hero-slide { min-height:380px; }
        .home-hero-content { padding:20px; }
        .home-hero-title { font-size:32px; }
        .home-hero-subtitle { font-size:13px; }
    }
    @media (max-width: 480px) {
        .home-hero-slide { min-height:280px; }
        .home-hero-content { padding:16px; }
        .home-hero-title { font-size:26px; }
        .home-hero-subtitle { font-size:12px; }
        .home-hero-btn { padding:10px 16px; font-size:12px; }
    }
    @media (max-width: 360px) {
        .home-hero-slide { min-height:220px; }
        .home-hero-content { padding:12px; }
        .home-hero-title { font-size:22px; }
    }
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
    #sorteo { position:relative; overflow:hidden;}
    #sorteo::before { content:""; position:absolute; inset:0; background:radial-gradient(52% 70% at 92% 22%, rgba(255,106,26,.17), transparent 64%), radial-gradient(36% 72% at 0% 45%, rgba(255,106,26,.1), transparent 70%); pointer-events:none; }
    #sorteo .fe-shell { position:relative; z-index:1; }
    .raffle-section-head { display:flex; flex-direction:column; align-items:center; gap:16px; margin-bottom:24px; }
    .raffle-main-title { margin:0; font-family:var(--font-display); font-size:52px; line-height:.82; letter-spacing:.02em; text-transform:uppercase; text-align:center; }
    .raffle-main-title span { color:var(--orange); text-shadow:0 0 32px rgba(255,106,26,.28); }
    .raffle-subtitle { margin:12px 0 0; color:#fff; font-size:clamp(14px, 1.45vw, 18px); line-height:1.35; max-width:600px; text-align:center; }
    .raffle-section-head .fe-btn { white-space:nowrap; box-shadow:0 8px 24px rgba(255,106,26,.28); }
    .raffle-meta { text-align:center; }
    .raffle-meta h4 { margin:0 0 8px; font-size:32px; font-weight:600; text-transform:uppercase; color:rgba(255,255,255,.85); }
    .raffle-meta p { margin:0; color:#fff; font-size:14px; line-height:1.4; }
    .raffle-meta strong { color:#fff; font-weight:700; }
    .raffle-timer { display:flex; justify-content:center; gap:8px; flex-wrap:wrap; max-width:100%; }
    .timer-unit { min-width:70px; height:70px; display:flex; flex-direction:column; align-items:center; justify-content:center; border:1px solid rgba(255,106,26,.28); border-radius:10px; background:linear-gradient(180deg, rgba(255,106,26,.13), rgba(255,255,255,.035)); box-shadow:0 10px 24px rgba(0,0,0,.28); flex:1; max-width:80px; }
    .timer-val { font-family:var(--font-display); color:#fff; font-size:28px; line-height:1; }
    .timer-label { margin-top:4px; color:var(--orange); font-size:8px; font-weight:900; letter-spacing:.14em; }
    .raffle-prizes-carousel { display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; overflow:visible; padding:20px 0; }
    .raffle-prizes-carousel::-webkit-scrollbar { display:none; }
    .raffle-prize-item { position:relative; min-height:160px; border:1px solid rgba(255,106,26,.34); border-radius:10px; background:#0b0504; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,.4); }
    .raffle-prize-item img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block; filter:brightness(0.7); }
    .raffle-prize-item::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0.1) 20%, rgba(0,0,0,0.8) 100%); }
    .raffle-prize-overlay { position:absolute; z-index:1; left:0; right:0; bottom:0; padding:12px; background:linear-gradient(transparent, rgba(0,0,0,0.85)); display:flex; flex-direction:column; justify-content:flex-end; height:100%; }
    .prize-tag { display:inline-flex; margin-bottom:4px; border:1px solid rgba(255,106,26,.6); border-radius:6px; background:rgba(255,106,26,.25); color:var(--orange); padding:3px 8px; font-size:9px; font-weight:900; letter-spacing:.08em; align-self:flex-start; }
    .prize-name { margin:0; color:#fff; font-family:var(--font-display); font-size:18px; line-height:1.1; text-transform:uppercase; }
    .prize-value { margin-top:2px; color:rgba(255,255,255,.75); font-size:11px; font-weight:600; }
    /* Raffle slider styles */
    .sorteo-slider-full { width:100vw; margin-left:calc(50% - 50vw); margin-right:calc(50% - 50vw); }
    .sorteo-slider-wrapper { position:relative; width:100%; max-width:100vw; overflow:hidden; }
    .raffles-slider { position:relative; width:100%; overflow:hidden; }
    .raffles-slider-track { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; -webkit-overflow-scrolling:touch; scrollbar-width:none; -ms-overflow-style:none; }
    .raffles-slider-track::-webkit-scrollbar { display:none; }
    .raffle-slide { flex:0 0 100%; scroll-snap-align:start; padding:0; }
    .raffle-slide-header { display:flex; flex-direction:column; align-items:center; gap:16px; padding:0 16px; }
    .raffle-slide-meta { text-align:center; }
    .raffle-slide-meta h4 { margin:0; font-size:32px; font-weight:700; text-transform:uppercase; letter-spacing:.02em; }
    .raffle-slide-meta p { margin:8px 0 0; font-size:14px; line-height:1.4; }
    .raffle-slide-footer { display:flex; justify-content:center; padding:16px; }
    .raffle-slide-footer .fe-btn { padding:14px 32px; font-size:15px; }
    .slider-btn { position:absolute; top:35%; transform:translateY(-50%); z-index:20; width:44px; height:44px; border-radius:50%; border:1px solid rgba(255,106,26,.6); background:rgba(10,6,6,.85); backdrop-filter:blur(4px); color:var(--orange); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; box-shadow:0 4px 16px rgba(0,0,0,.4); }
    .slider-btn-prev { left:8px; }
    .slider-btn-next { right:8px; }
    .slider-btn:hover { background:var(--orange); color:#190702; transform:translateY(-50%) scale(1.05); }
    .slider-btn i { font-size:14px; }
    /* Raffle section responsive */
    @media (max-width: 768px) {
        #sorteo { padding-block:28px; }
        .raffle-main-title { font-size:32px; margin-bottom:16px; }
        .raffle-subtitle { font-size:14px; max-width:90%; }
        .raffle-section-head .fe-btn { width:100%; justify-content:center; padding:10px 20px; font-size:13px; }
        .raffle-meta { margin-bottom:16px; }
        .raffle-meta h4 { font-size:20px; }
        .raffle-meta p { font-size:12px; }
        .raffle-timer { gap:4px; padding:0 8px; }
        .timer-unit { min-width:55px; height:52px; max-width:65px; }
        .timer-val { font-size:18px; }
        .timer-label { font-size:6px; }
        .raffle-prizes-carousel { grid-template-columns:repeat(2, 1fr); gap:8px; padding:12px 0; }
        .raffle-prize-item { min-height:120px; border-radius:8px; }
        .raffle-prize-overlay { padding:8px; }
        .prize-tag { font-size:7px; padding:2px 6px; margin-bottom:3px; }
        .prize-name { font-size:14px; }
        .prize-value { font-size:9px; }
        .raffle-slide-footer { margin-top:12px; }
        .raffle-slide-footer .fe-btn { width:100%; justify-content:center; padding:10px 20px; font-size:13px; }
    }
    @media (max-width: 480px) {
        #sorteo { padding-block:20px; }
        .raffle-main-title { font-size:26px; margin-bottom:12px; }
        .raffle-subtitle { font-size:13px; }
        .raffle-meta h4 { font-size:18px; }
        .raffle-meta p { font-size:11px; }
        .raffle-timer { gap:3px; }
        .timer-unit { min-width:48px; height:46px; max-width:56px; border-radius:6px; }
        .timer-val { font-size:16px; }
        .timer-label { font-size:5px; letter-spacing:.08em; }
        .raffle-prizes-carousel { grid-template-columns:repeat(2, 1fr) !important; gap:6px; }
        .raffle-prize-item { min-height:100px; border-radius:6px; }
        .raffle-prize-overlay { padding:6px; }
        .prize-tag { font-size:6px; padding:2px 5px; margin-bottom:2px; }
        .prize-name { font-size:12px; }
        .prize-value { font-size:8px; }
    }
    @media (max-width: 360px) {
        .raffle-main-title { font-size:22px; margin-bottom:10px; }
        .raffle-timer { gap:2px; }
        .timer-unit { min-width:42px; height:42px; max-width:50px; }
        .timer-val { font-size:14px; }
        .timer-label { font-size:4px; }
        .raffle-prizes-carousel { gap:5px; }
        .raffle-prize-item { min-height:90px; }
        .prize-name { font-size:11px; }
    }
    /* Single raffle styles */
    .raffle-single { padding:20px; max-width:100%; }
    .raffle-single .raffle-slide-header { flex-direction:column; gap:16px; padding:0; }
    .raffle-single .raffle-meta { text-align:center; }
    .raffle-single .raffle-meta h4 { font-size:24px; }
    .raffle-single .raffle-meta p { font-size:13px; }
    .raffle-single .raffle-timer { width:100%; max-width:340px; margin:0 auto; }
    .raffle-single .raffle-prizes-carousel { grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:12px; }
    .raffle-single .raffle-prize-item { min-height:140px; }
    .raffle-single .raffle-slide-footer { padding-top:14px; }
    .raffle-single .raffle-slide-footer .fe-btn { width:100%; }
    @media (max-width: 768px) {
        .raffle-single { padding:12px; }
        .raffle-single .raffle-meta h4 { font-size:20px; }
        .raffle-single .raffle-meta p { font-size:12px; }
        .raffle-single .raffle-prizes-carousel { grid-template-columns:repeat(2, 1fr); gap:8px; }
        .raffle-single .raffle-prize-item { min-height:110px; }
    }
    @media (max-width: 480px) {
        .raffle-single { padding:10px; }
        .raffle-single .raffle-meta h4 { font-size:18px; }
        .raffle-single .raffle-meta p { font-size:11px; }
        .raffle-single .raffle-timer { max-width:300px; }
        .raffle-single .raffle-prizes-carousel { grid-template-columns:repeat(2, 1fr) !important; gap:6px; }
        .raffle-single .raffle-prize-item { min-height:90px; }
        .raffle-single .prize-name { font-size:11px; }
        .raffle-single .prize-tag { font-size:6px; padding:2px 4px; }
    }
    @media (max-width: 360px) {
        .raffle-single .raffle-meta h4 { font-size:16px; }
        .raffle-single .prize-name { font-size:10px; }
        .raffle-single .raffle-prize-item { min-height:80px; }
    }
    .raffle-banner { position: relative;overflow: hidden;border-radius: 10px;min-height: 330px;padding: 18px 22px 22px;}
    .raffle-full { width:100vw; margin-left:calc(50% - 50vw); border-radius:0; }
    .raffle-deco { position:absolute; z-index:0; pointer-events:none; opacity:.66; filter:drop-shadow(0 20px 24px rgba(255,106,26,.22)); }
    .raffle-deco img { width:100%; height:100%; object-fit:contain; display:block; }
    .raffle-deco.gift-left { left: 34px;bottom: 113px;width: 160px;height: 130px;transform: rotate(3deg);}
    .raffle-deco.gift-right {     right: 34px;bottom: 113px;width: 160px;height: 130px;transform: scaleX(-1) rotate(0deg); }
    .raffle-banner-head { position:relative; z-index:2; text-align:center; padding:0 150px 18px; }
    .raffle-banner-head h3 { font-family:var(--font-display); font-size:44px; line-height:.9; letter-spacing:.03em; margin:0; }
    .raffle-banner-head h3 span { color:#ff3d12; }
    .raffle-banner-head p { margin:6px auto 0; color:var(--muted); font-size:12px; line-height:1.45; max-width:720px; }
    .raffle-countdown { display:inline-flex; align-items:center; justify-content:center; gap:8px; margin-top:12px; border:1px solid rgba(255,106,26,.55); border-radius:999px; background:rgba(255,106,26,.12); color:#fff; padding:8px 16px; font-size:12px; font-weight:900; letter-spacing:.04em; text-transform:uppercase; box-shadow:0 0 22px rgba(255,106,26,.16); }
    .raffle-countdown strong { color:var(--orange); font-size:14px; }
    .raffle-prize-strip { position:relative; z-index:2; display:grid; grid-auto-flow:column; grid-auto-columns:minmax(310px, 400px); gap:14px; padding:10px 0 12px; align-items:end; justify-content:start; overflow-x:auto; overscroll-behavior-inline:contain; -webkit-overflow-scrolling:touch; scroll-snap-type:inline mandatory; scrollbar-width:thin; scrollbar-color:rgba(255,106,26,.72) rgba(255,255,255,.08); }
    .raffle-prize-strip::-webkit-scrollbar, .bonus-carousel::-webkit-scrollbar { height:8px; }
    .raffle-prize-strip::-webkit-scrollbar-track, .bonus-carousel::-webkit-scrollbar-track { background:rgba(255,255,255,.08); border-radius:999px; }
    .raffle-prize-strip::-webkit-scrollbar-thumb, .bonus-carousel::-webkit-scrollbar-thumb { background:rgba(255,106,26,.72); border-radius:999px; }
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
    .sorteo-slider-full { width:100vw; margin-left:calc(50% - 50vw); margin-right:calc(50% - 50vw); }
    .sorteo-slider-wrapper { position:relative; width:100%; }
    .raffles-slider { position: relative; width: 100%; overflow: hidden; }
    .raffles-slider-track { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none; }
    .raffles-slider-track::-webkit-scrollbar { display: none; }
    .raffle-slide { flex: 0 0 100%; scroll-snap-align: start; padding: 0 20px; }
    .raffle-slide-header { display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; }
    .raffle-slide-footer { display:flex; justify-content:center; padding-top:8px; }
    .slider-btn { position:absolute; top:50%; transform:translateY(-50%); z-index:20; width:44px; height:44px; border-radius:50%; border:1px solid rgba(255,106,26,.45); background:rgba(0,0,0,.6); backdrop-filter:blur(4px); color:var(--orange); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; }
    .sorteo-slider-wrapper .slider-btn { top:calc(50% + 40px); }
    .slider-btn-prev { left:10px; }
    .slider-btn-next { right:10px; }
    .slider-btn:hover { background:rgba(255,106,26,.25); transform:translateY(-50%) scale(1.05); }
    @media (max-width: 768px) {
        .slider-btn { display: flex !important; width: 36px; height: 36px; top: 50%; }
    }
    .bonus-carousel { position:relative; width:100%; display:grid; grid-auto-flow:column; grid-auto-columns:minmax(280px, 360px); gap:16px; overflow-x:hidden; overscroll-behavior-inline:contain; -webkit-overflow-scrolling:touch; padding:4px 0 16px; scroll-snap-type:inline mandatory; scrollbar-width:thin; scrollbar-color:rgba(255,106,26,.72) rgba(255,255,255,.08); }
    .bonus-card { min-height:250px; color:#fff; position:relative; border:3px dashed rgba(255,106,26,.9); border-radius:18px; background:
        radial-gradient(90% 100% at 0% 0%, rgba(255,106,26,.2), transparent 58%),
        linear-gradient(180deg,#180b08,#090505);box-shadow:0 18px 42px rgba(0,0,0,.42), 0 0 0 1px rgba(255,255,255,.04) inset; transform:rotate(-1deg); overflow:hidden; padding:30px; scroll-snap-align:start; }
    .bonus-card:nth-child(even) { transform:rotate(1deg); }
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
    @media (max-width: 768px) {
        .blog-grid { grid-template-columns:repeat(2, 1fr); gap:10px; }
    }
    @media (max-width: 480px) {
        .blog-grid { grid-template-columns:1fr; gap:12px; }
    }
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
    @media (max-width: 768px) {
        .steps-grid { grid-template-columns:1fr 1fr; gap:10px; }
        .step-card { padding:18px; min-height:auto; }
        .step-num { font-size:42px; }
        .step-card h3 { font-size:24px; }
    }
    @media (max-width: 480px) {
        .steps-grid { grid-template-columns:1fr; gap:10px; }
        .step-card { padding:16px; }
        .step-num { font-size:36px; }
        .step-card h3 { font-size:20px; }
        .step-card p { font-size:12px; }
    }
    .about-box { display:grid; grid-template-columns:1fr 1fr; gap:30px; align-items:center; padding:34px; border:1px solid var(--line-warm); border-radius:var(--r-xl); background:radial-gradient(70% 80% at 90% 0%, rgba(255,106,26,.22), transparent 64%), linear-gradient(120deg,#1a0606,#2a0e0e); overflow:hidden; }
    .about-title { font-family:var(--font-display); font-size:48px; line-height:.98; margin:0 0 12px; letter-spacing:.02em; }
    .about-title span { color:var(--orange); }
    .about-copy { color:var(--muted); font-size:14px; line-height:1.65; margin:0; }
    .about-features { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .about-feature { min-height:118px; padding:16px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); }
    .about-feature strong { display:block; margin-bottom:6px; font-size:14px; }
    .about-feature p { color:var(--muted); font-size:12px; line-height:1.45; margin:0; }
    @media (max-width: 768px) {
        .about-box { grid-template-columns:1fr; gap:20px; padding:24px; }
        .about-title { font-size:36px; }
        .about-features { grid-template-columns:1fr 1fr; }
    }
    @media (max-width: 480px) {
        .about-box { padding:16px; gap:16px; }
        .about-title { font-size:28px; }
        .about-copy { font-size:13px; }
        .about-features { grid-template-columns:1fr; gap:10px; }
        .about-feature { min-height:auto; padding:12px; }
        .about-feature strong { font-size:13px; }
        .about-feature p { font-size:11px; }
    }
    .empty-panel { border:1px dashed var(--line-2); border-radius:var(--r-md); color:var(--muted); padding:24px; text-align:center; font-size:13px; }
    @media (max-width: 920px) {
        .lines-grid { grid-template-columns:repeat(2, 1fr); gap:12px; }
        .blog-grid { grid-template-columns:repeat(1, 1fr); }
        .steps-grid { grid-template-columns:repeat(1, 1fr); }
        .about-box { grid-template-columns:1fr; }
        .about-features { grid-template-columns:1fr 1fr; }
        .home-hero-slide { min-height:360px; }
        #sorteo { padding-block:48px; }
        .raffle-section-head, .raffle-info-bar { grid-template-columns:1fr; align-items:start; }
        .raffle-section-head .fe-btn { width:max-content; }
        .raffle-timer { min-width:0; width:100%; }
        .raffle-prizes-carousel { grid-auto-columns:minmax(300px, 78vw); }
        .raffle-prize-item { min-height:330px; }
        .raffle-deco { opacity:.14; }
        .raffle-banner-head { padding:18px 22px 8px; }
        .raffle-banner-head h3 { font-size:34px; }
        .raffle-prize-strip { grid-auto-columns:minmax(280px, 88vw); }
        .raffle-prize-strip.count-3 .raffle-prize-tile.primary { transform:none; }
        .raffle-prize-tile, .raffle-prize-tile.primary { grid-template-columns:54px minmax(0, 1fr) 104px; min-height:104px; }
        .raffle-rank, .raffle-prize-tile-primary .raffle-rank { font-size:72px; }
        .raffle-prize-image, .raffle-prize-tile.primary .raffle-prize-image { height:84px; }
        .bonus-carousel { grid-auto-columns:minmax(280px, 88vw); }
    }
    @media (max-width: 560px) {
        .home-hero-slide { min-height:280px; }
        #sorteo { padding-block:36px; }
        .raffle-section-head { gap:16px; margin-bottom:16px; }
        .raffle-main-title { font-size:42px; }
        .raffle-subtitle { font-size:14px; }
        .raffle-section-head .fe-btn { width:100%; }
        .raffle-info-bar { gap:14px; }
        .raffle-timer { grid-template-columns:repeat(2, minmax(0,1fr)); }
        .timer-unit { min-height:64px; }
        .timer-val { font-size:30px; }
        .raffle-prizes-carousel { grid-auto-columns:minmax(260px, 86vw); gap:12px; padding-top:16px; }
        .raffle-prize-item { min-height:150px; }
        .raffle-prize-overlay { padding:18px; }
        .prize-name { font-size: 24px; }
        .raffle-banner { min-height:0; padding:14px 12px 18px; }
        .raffle-deco { display:none; }
        .raffle-banner-head { padding:10px 4px 8px; }
        .raffle-banner-head h3 { font-size:30px; line-height:1; overflow-wrap:anywhere; }
        .raffle-countdown { width:100%; border-radius:10px; padding:9px 12px; }
        .raffle-prize-strip { grid-auto-columns:minmax(248px, 86vw); }
        .raffle-prize-tile, .raffle-prize-tile.primary { grid-template-columns:48px minmax(0, 1fr); gap:10px; padding:10px; }
        .raffle-rank, .raffle-prize-tile.primary .raffle-rank { font-size:62px; }
        .raffle-prize-image, .raffle-prize-tile.primary .raffle-prize-image { grid-column:1 / -1; width:100%; height:118px; }
        .bonus-carousel { grid-auto-columns:minmax(248px, 86vw); }
        .bonus-card { min-height:230px; padding:22px; transform:none !important; }
        .bonus-ticket-main { min-height:176px; }
        .bonus-card h3 { font-size:28px; max-width:100%; }
        .bonus-ticket-value { font-size:46px; }
        .step-card, .about-box { padding:22px; }
        .step-card { min-height:auto; }
        .step-card h3 { font-size:26px; }
        .about-title { font-size:36px; }
    }
    /* Mobile line card fixes */
    @media (max-width: 480px) {
        .lines-grid { grid-template-columns:1fr; gap:12px; }
        .line-card { border-radius:14px; }
        .line-cover { height:100px; }
        .line-avatar { left:12px; bottom:-20px; width:48px; height:48px; border-radius:12px; }
        .line-body { padding:28px 12px 12px; }
        .line-head { flex-direction:column; gap:8px; }
        .line-head h3 { font-size:22px; }
        .line-head p { font-size:11px; }
        .line-state { font-size:9px; padding:3px 6px; }
        .line-actions { flex-direction:column; gap:6px; margin-top:12px; }
        .line-contact { width:100%; padding:10px; font-size:11px; }
        .home-hero-slide { min-height:240px; }
        .step-num { font-size:42px; }
        .step-card h3 { font-size:22px; }
        .step-card p { font-size:12px; }
        .about-features { grid-template-columns:1fr; }
        .about-feature { min-height:auto; padding:12px; }
        .about-feature strong { font-size:13px; }
        .about-feature p { font-size:11px; }
    }
    @media (max-width: 360px) {
        .lines-grid { gap:10px; }
        .line-cover { height:90px; }
        .line-avatar { width:42px; height:42px; }
        .line-body { padding:24px 10px 10px; }
        .line-head h3 { font-size:20px; }
        .line-contact { padding:8px; font-size:10px; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rafflesSlider', () => ({
            currentIndex: 0,
            slideCount: 0,
            slides: [],
            
            initSlider() {
                this.slides = Array.from(this.$refs.track.children);
                this.slideCount = this.slides.length;
                this.updateSliderPosition();
            },
            
            updateSliderPosition() {
                const slideWidth = this.slides[0]?.offsetWidth || 0;
                this.$refs.track.scrollTo({
                    left: this.currentIndex * slideWidth,
                    behavior: 'smooth'
                });
            },
            
            next() {
                if (this.currentIndex < this.slideCount - 1) {
                    this.currentIndex++;
                    this.updateSliderPosition();
                }
            },
            
            prev() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.updateSliderPosition();
                }
            },
            
            goTo(index) {
                if (index >= 0 && index < this.slideCount) {
                    this.currentIndex = index;
                    this.updateSliderPosition();
                }
            }
        }));
    });
</script>
@endpush