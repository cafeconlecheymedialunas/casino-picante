<div>
    <section class="home-hero">
        <div class="fe-shell">
            @include('frontend.components.carousel', ['items' => $carouselItems])
        </div>
    </section>

    @if(($sections['como-empezar']['enabled'] ?? false) && count($sections['como-empezar']['repeater_data'] ?? []) > 0)
    <section id="como-empezar" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['como-empezar']['kicker'] ?? '',
                'title' => $sections['como-empezar']['title'] ?? '',
                'highlight' => $sections['como-empezar']['highlight'] ?? '',
                'subtitle' => $sections['como-empezar']['subtitle'] ?? '',
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

    @if(($sections['lineas']['enabled'] ?? false) && $lines->count())
    <section id="lineas" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['lineas']['kicker'] ?? '',
                'title' => $sections['lineas']['title'] ?? '',
                'highlight' => $sections['lineas']['highlight'] ?? '',
                'subtitle' => $sections['lineas']['subtitle'] ?? '',
                'action' => $sections['lineas']['action'] ?? null,
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

    @if(($sections['sorteo']['enabled'] ?? false) && $raffles->count() > 0)
    <section id="sorteo" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['sorteo']['kicker'] ?? '',
                'title' => $sections['sorteo']['title'] ?? '',
                'highlight' => $sections['sorteo']['highlight'] ?? '',
                'subtitle' => $sections['sorteo']['subtitle'] ?? '',
                'action' => $sections['sorteo']['action'] ?? null,
            ])

            <div class="raffle-container">
                @if($raffles->count() > 1)
                <div class="sorteo-slider-wrapper" x-data="rafflesSlider()" x-init="initSlider()">
                    <button type="button" class="slider-btn slider-btn-prev" @click="prev()">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div class="raffles-slider-track" x-ref="track">
                        @foreach($raffles as $index => $raffle)
                        <div class="raffle-slide" x-ref="slide{{ $index }}">
                            <div class="raffle-slide-content">
                                <div class="raffle-meta">
                                    <h3>{{ strtoupper($raffle->title) }}</h3>
                                    @if($raffle->description)
                                        <p>{{ $raffle->description }}</p>
                                    @endif
                                </div>
                                
                                <div class="raffle-timer" data-raffle-countdown="{{ $raffle->end_date->toIso8601String() }}">
                                    @php $remaining = now()->diff($raffle->end_date); @endphp
                                    <div class="timer-unit"><span class="timer-val" data-unit="days">{{ str_pad($remaining->d, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">DIAS</span></div>
                                    <div class="timer-unit"><span class="timer-val" data-unit="hours">{{ str_pad($remaining->h, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">HRS</span></div>
                                    <div class="timer-unit"><span class="timer-val" data-unit="minutes">{{ str_pad($remaining->i, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">MIN</span></div>
                                    <div class="timer-unit"><span class="timer-val" data-unit="seconds">{{ str_pad($remaining->s, 2, '0', STR_PAD_LEFT) }}</span><span class="timer-label">SEG</span></div>
                                </div>

                                <div class="raffle-prizes-grid">
                                    @foreach(collect($raffle->prizes)->sortBy(fn ($prize, $idx) => (int) ($prize['position'] ?? $idx + 1))->values()->take(3) as $pIndex => $prize)
                                    <article class="raffle-prize-card">
                                        @php
                                            $position = (int) ($prize['position'] ?? $pIndex + 1);
                                            $image = $prize['image'] ?? null;
                                            if ($image && !Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
                                                $image = asset('storage/'.$image);
                                            }
                                        @endphp
                                        @if($image)
                                            <img src="{{ $image }}" alt="{{ $prize['name'] ?? 'Premio '.$position }}">
                                        @endif
                                        <div class="raffle-prize-body">
                                            <span class="prize-rank">{{ $position }}°</span>
                                            <div class="prize-info">
                                                <h4>{{ $prize['name'] ?? '' }}</h4>
                                                <span>Estimado: ${{ number_format((float) ($prize['amount'] ?? 1000000), 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </article>
                                    @endforeach
                                </div>

                                <div class="raffle-slide-footer">
                                    <a href="{{ route('frontend.sorteos.detalle', $raffle) }}" wire:navigate class="fe-btn primary">Participar ahora</a>
                                </div>
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
                <div class="raffle-single-layout">
                    <div class="raffle-meta">
                        <h3>{{ strtoupper($raffle->title) }}</h3>
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

                    <div class="raffle-prizes-grid">
                        @foreach(collect($raffle->prizes)->sortBy(fn ($prize, $idx) => (int) ($prize['position'] ?? $idx + 1))->values()->take(3) as $pIndex => $prize)
                        <article class="raffle-prize-card">
                            @php
                                $position = (int) ($prize['position'] ?? $pIndex + 1);
                                $image = $prize['image'] ?? null;
                                if ($image && !Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
                                    $image = asset('storage/'.$image);
                                }
                            @endphp
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $prize['name'] ?? 'Premio '.$position }}">
                            @endif
                            <div class="raffle-prize-body">
                                <span class="prize-rank">{{ $position }}°</span>
                                <div class="prize-info">
                                    <h4>{{ $prize['name'] ?? '' }}</h4>
                                    <span>Estimado: ${{ number_format((float) ($prize['amount'] ?? 1000000), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    <div class="raffle-slide-footer">
                        <a href="{{ route('frontend.sorteos.detalle', $raffle) }}" wire:navigate class="fe-btn primary">Participar ahora</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    @if(($sections['nosotros']['enabled'] ?? false) && (($sections['nosotros']['subtitle'] ?? '') || count($sections['nosotros']['repeater_data'] ?? []) > 0))
    <section id="nosotros" class="fe-section">
        <div class="fe-shell">
            <div class="about-box">
                <div>
                    <div class="fe-kicker">{{ $sections['nosotros']['kicker'] ?? '' }}</div>
                    <h2 class="about-title">{{ $sections['nosotros']['title'] ?? '' }} <span>{{ $sections['nosotros']['highlight'] ?? '' }}</span></h2>
                    <p class="about-copy">
                        {{ $sections['nosotros']['subtitle'] ?? '' }}
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

    @if(($sections['bonos']['enabled'] ?? false) && $bonusItems->count())
    <section id="bonos" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['bonos']['kicker'] ?? '',
                'title' => $sections['bonos']['title'] ?? '',
                'highlight' => $sections['bonos']['highlight'] ?? '',
                'subtitle' => $sections['bonos']['subtitle'] ?? '',
                'action' => $sections['bonos']['action'] ?? null,
            ])

            @if($bonusItems->count())
                @if($bonusItems->count() > 4)
                    <div class="bonus-carousel-wrapper" x-data="bonusCarousel()">
                        <button type="button" class="slider-btn slider-btn-prev" @click="prev()">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="bonus-carousel" x-ref="track">
                            @foreach($bonusItems as $bonus)
                                @include('frontend.components.bonus-card', ['bonus' => $bonus])
                            @endforeach
                        </div>
                        <button type="button" class="slider-btn slider-btn-next" @click="next()">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                @else
                    <div class="bonus-grid">
                        @foreach($bonusItems as $bonus)
                            @include('frontend.components.bonus-card', ['bonus' => $bonus])
                        @endforeach
                    </div>
                @endif
            @else
                <div class="empty-panel">No hay bonos activos vigentes.</div>
            @endif
        </div>
    </section>
    @endif

    @if(($sections['blog']['enabled'] ?? false) && $blogPosts->count())
    <section id="blog" class="fe-section">
        <div class="fe-shell">
            @include('frontend.components.section-header', [
                'kicker' => $sections['blog']['kicker'] ?? '',
                'title' => $sections['blog']['title'] ?? '',
                'highlight' => $sections['blog']['highlight'] ?? '',
                'subtitle' => $sections['blog']['subtitle'] ?? '',
                'action' => $sections['blog']['action'] ?? null,
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
    .home-hero .fe-shell { width:100%; }
    .home-hero-carousel-wrapper { position:relative; background:#090404; }
    .home-hero-carousel { display:grid; grid-auto-flow:column; grid-auto-columns:100%; gap:0; overflow-x:auto; scroll-snap-type:inline mandatory; border-radius:0; box-shadow:0 22px 70px rgba(0,0,0,.5); scroll-behavior:smooth; }
    .home-hero-carousel::-webkit-scrollbar { display:none; }
    .home-hero-dots { position:absolute; bottom:16px; left:50%; transform:translateX(-50%); display:flex; gap:8px; z-index:10; }
    .home-hero-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.4); border:none; cursor:pointer; padding:0; transition:background .2s, transform .2s; }
    .home-hero-dot.active { background:#fff; transform:scale(1.3); }
    .home-hero-slide { position:relative; width:100%; min-height:560px; overflow:hidden; border:0; border-radius:0; background:#120909; scroll-snap-align:start; text-decoration:none; display:block; isolation:isolate; }
    .home-hero-slide::before { content:""; position:absolute; inset:0; z-index:1; background:linear-gradient(90deg, rgba(7,3,3,.96) 0%, rgba(16,6,5,.74) 34%, rgba(16,6,5,.18) 64%, rgba(7,3,3,.34) 100%); pointer-events:none; }
    .home-hero-slide::after { content:""; position:absolute; inset:auto 0 0; height:42%; z-index:2; background:linear-gradient(transparent, rgba(0,0,0,.68)); pointer-events:none; }
    .home-hero-slide img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1.012); transition:transform .8s ease, filter .8s ease; }
    .home-hero-slide:hover img { transform:scale(1.045); filter:saturate(1.08) contrast(1.04); }
    .home-hero-empty { position:absolute; inset:0; background:radial-gradient(60% 80% at 80% 20%, rgba(255,106,26,.65), transparent 60%), radial-gradient(40% 50% at 0% 80%, rgba(255,138,61,.35), transparent 60%), linear-gradient(135deg,#1a0606,#3a1308); }
    .home-hero-content { position:absolute; inset:0; z-index:3; display:flex; align-items:flex-end; padding:0 clamp(22px, 6vw, 84px) clamp(34px, 8vw, 82px); pointer-events:none; }
    .home-hero-copy { width:min(660px, 92vw); display:grid; gap:14px; }
    .home-hero-copy::before { content:"Destacado"; width:max-content; border:1px solid rgba(255,106,26,.48); border-radius:999px; padding:7px 12px; background:rgba(255,106,26,.12); color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; box-shadow:0 0 28px rgba(255,106,26,.14); }
    .home-hero-title { font-family:var(--font-display); font-size:clamp(42px, 8vw, 88px); line-height:.9; color:#fff; margin:0; letter-spacing:.02em; text-wrap:balance; text-shadow:0 18px 46px rgba(0,0,0,.55); }
    .home-hero-subtitle { max-width:560px; font-size:clamp(14px, 1.6vw, 18px); line-height:1.55; color:rgba(255,255,255,.78); margin:0; text-wrap:pretty; }
    .home-hero-cta { width:max-content; max-width:100%; display:inline-flex; align-items:center; gap:10px; min-height:44px; padding:0 18px; border-radius:999px; background:linear-gradient(180deg,var(--orange-2),var(--orange)); color:#190702; font-size:13px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; box-shadow:var(--glow-orange); }
    .home-hero-cta i { font-size:12px; transition:transform .18s ease; }
    .home-hero-slide:hover .home-hero-cta i { transform:translateX(3px); }
    @media (max-width: 768px) {
        .home-hero-slide { min-height:430px; }
        .home-hero-slide::before { background:linear-gradient(180deg, rgba(8,4,4,.22), rgba(8,4,4,.92)); }
        .home-hero-content { padding:0 20px 32px; align-items:flex-end; }
        .home-hero-copy { gap:12px; }
    }
    @media (max-width: 480px) {
        .home-hero-slide { min-height:430px; }
        .home-hero-content { padding:0 16px 24px; }
        .home-hero-copy::before { font-size:9px; padding:6px 10px; }
        .home-hero-subtitle { font-size:13px; line-height:1.45; }
        .home-hero-cta { min-height:38px; padding:0 14px; font-size:11px; }
    }
    .lines-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    @media (max-width: 860px) {
        .lines-grid { grid-template-columns:repeat(2, 1fr); }
    }
    @media (max-width: 560px) {
        .lines-grid { grid-template-columns:1fr; }
    }
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
    
    #sorteo { position:relative; overflow:hidden; }
    #sorteo::before { content:""; position:absolute; inset:0; background:radial-gradient(52% 70% at 92% 22%, rgba(255,106,26,.12), transparent 64%), radial-gradient(36% 72% at 0% 45%, rgba(255,106,26,.08), transparent 70%); pointer-events:none; }
    #sorteo .fe-shell { position:relative; z-index:1; }
    
    .raffle-container { background: rgba(255,255,255,.02); border: 1px solid var(--line-warm); border-radius: 24px; padding: 40px; margin-top: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); }
    .raffle-slide-content, .raffle-single-layout { display: flex; flex-direction: column; align-items: center; gap: 36px; }
    
    .raffle-meta { text-align:center; max-width: 800px; }
    .raffle-meta h3 { margin:0 0 12px; font-family: var(--font-display); font-size:42px; color:#fff; letter-spacing: 0.02em; text-shadow: 0 10px 20px rgba(0,0,0,0.5); }
    .raffle-meta p { margin:0; color:rgba(255,255,255,.6); font-size:16px; line-height:1.6; }
    
    .raffle-timer { display:flex; justify-content:center; gap:16px; }
    .timer-unit { width:90px; height:90px; display:flex; flex-direction:column; align-items:center; justify-content:center; border:1px solid rgba(255,106,26,.4); border-radius:16px; background:linear-gradient(180deg, rgba(255,106,26,.15), rgba(0,0,0,.3)); backdrop-filter: blur(12px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .timer-val { font-family:var(--font-display); color:#fff; font-size:36px; line-height:1; }
    .timer-label { margin-top:4px; color:var(--orange); font-size:10px; font-weight:900; letter-spacing:.15em; }
    
    .raffle-prizes-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; width: 100%; max-width: 1100px; }
    .raffle-prize-card { position:relative; height:200px; border-radius:20px; overflow:hidden; border:1px solid rgba(255,255,255,.1); background:#0a0505; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
    .raffle-prize-card img { width:100%; height:100%; object-fit:cover; opacity: 0.5; transition: all 0.7s ease; }
    .raffle-prize-card:hover img { transform: scale(1.12); opacity: 0.7; }
    .raffle-prize-body { position:absolute; inset:0; padding:24px; display:flex; flex-direction:column; justify-content:flex-end; background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, transparent 100%); }
    .prize-rank { position:absolute; top:20px; left:20px; width:40px; height:40px; display:flex; align-items:center; justify-content:center; background:var(--orange); color:#190702; border-radius:50%; font-weight:900; font-size:14px; box-shadow: 0 4px 15px rgba(255,106,26,0.5); }
    .prize-info h4 { margin:0; color:#fff; font-size:20px; font-weight:700; text-transform: uppercase; }
    .prize-info span { color:var(--orange); font-size:13px; font-weight:800; margin-top:4px; display: block; }
    
    .raffle-slide-footer { margin-top: 10px; }
    
    .sorteo-slider-wrapper { position:relative; width:100%; }
    .raffles-slider-track { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; -ms-overflow-style:none; }
    .raffles-slider-track::-webkit-scrollbar { display:none; }
    .raffle-slide { flex:0 0 100%; scroll-snap-align:start; }
    
    .slider-btn { position:absolute; top:50%; transform:translateY(-50%); z-index:10; width:52px; height:52px; border-radius:50%; border:1px solid rgba(255,255,255,0.15); background:rgba(0,0,0,0.6); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; backdrop-filter: blur(8px); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .slider-btn:hover { background:var(--orange); color:#000; border-color:var(--orange); transform: translateY(-50%) scale(1.1); box-shadow: 0 0 30px rgba(255,106,26,0.3); }
    .slider-btn-prev { left: -26px; }
    .slider-btn-next { right: -26px; }

    @media (max-width: 1200px) {
        .slider-btn-prev { left: 10px; }
        .slider-btn-next { right: 10px; }
        .raffle-container { padding: 30px; }
    }

    @media (max-width: 768px) {
        .raffle-container { padding: 24px 16px; border-radius: 16px; gap: 24px; }
        .raffle-slide-content, .raffle-single-layout { gap: 28px; }
        .raffle-meta h3 { font-size: 32px; }
        .raffle-meta p { font-size: 14px; }
        .raffle-timer { gap: 8px; }
        .timer-unit { width: 68px; height: 68px; border-radius: 12px; }
        .timer-val { font-size: 26px; }
        .timer-label { font-size: 8px; }
        .raffle-prizes-grid { grid-template-columns: 1fr; max-width: 440px; gap: 16px; }
        .raffle-prize-card { height: 130px; border-radius: 14px; }
        .prize-rank { width: 32px; height: 32px; top: 12px; left: 12px; font-size: 12px; }
        .prize-info h4 { font-size: 17px; }
        .slider-btn { width: 40px; height: 40px; }
    }

    .bonus-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px; padding:4px 0 16px; }
    .bonus-carousel-wrapper { position:relative; width:100%; }
    .bonus-carousel-wrapper .slider-btn-prev { left:0; }
    .bonus-carousel-wrapper .slider-btn-next { right:0; }
    .bonus-carousel { width:100%; display:grid; grid-auto-flow:column; grid-auto-columns:minmax(280px, 360px); gap:16px; overflow-x:auto; overscroll-behavior-inline:contain; -webkit-overflow-scrolling:touch; padding:4px 0 16px; scroll-snap-type:inline mandatory; scroll-behavior:smooth; scrollbar-width:none; -ms-overflow-style:none; }
    .bonus-carousel::-webkit-scrollbar { display:none; }
    .bonus-card { min-height:250px; color:#fff; position:relative; border:3px dashed rgba(255,106,26,.9); border-radius:18px; background: radial-gradient(90% 100% at 0% 0%, rgba(255,106,26,.2), transparent 58%), linear-gradient(180deg,#180b08,#090505);box-shadow:0 18px 42px rgba(0,0,0,.42), 0 0 0 1px rgba(255,255,255,.04) inset; transform:rotate(-1deg); overflow:hidden; padding:30px; scroll-snap-align:start; }
    .bonus-card:nth-child(even) { transform:rotate(1deg); }
    .bonus-card h3 { font-family:var(--font-display); font-size:34px; line-height:.92; margin:0; letter-spacing:.02em; color:#fff; text-transform:uppercase; max-width:270px; }
    .bonus-card p { color:var(--muted); font-size:13px; line-height:1.42; margin:12px 0 0; font-weight:700; max-width:270px; }
    @media (max-width: 560px) {
        .bonus-card, .bonus-card:nth-child(even) { transform:none; padding:22px; }
        .bonus-card h3 { font-size:28px; max-width:100%; }
        .bonus-card p { max-width:100%; }
        .bonus-grid { grid-template-columns:1fr; }
    }
    
    .blog-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    @media (max-width: 860px) {
        .blog-grid { grid-template-columns:repeat(2, 1fr); gap:10px; }
    }
    @media (max-width: 480px) {
        .blog-grid { grid-template-columns:1fr; gap:12px; }
    }
    .blog-thumb { height:180px; overflow:hidden; background:#120909; display:flex; align-items:center; justify-content:center; }
    .blog-thumb img { width:100%; height:100%; object-fit:cover; transition:transform .4s ease; }
    .blog-card:hover .blog-thumb img { transform:scale(1.05); }
    .blog-thumb span { font-family:var(--font-display); color:var(--orange); font-size:22px; letter-spacing:.06em; }
    .blog-body { padding:16px; }
    .blog-body time { color:var(--muted-2); font-size:11px; font-weight:700; }
    .blog-body h3 { font-size:18px; line-height:1.2; margin:7px 0; }
    .blog-body p { color:var(--muted); font-size:13px; line-height:1.45; margin:0; }
    @media (max-width: 560px) {
        .blog-thumb { height:150px; }
        .blog-body h3 { font-size:16px; }
    }
    
    .steps-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .step-card { padding:26px; border:1px solid var(--line-warm); border-radius:18px; background:radial-gradient(120% 80% at 0% 0%, rgba(255,106,26,.24), transparent 60%), linear-gradient(180deg,#1c0d0a,#120909); min-height:220px; }
    .step-num { font-family:var(--font-display); color:var(--orange); font-size:58px; line-height:.9; }
    .step-card h3 { font-family:var(--font-display); font-size:30px; line-height:.98; margin:12px 0 8px; letter-spacing:.02em; }
    @media (max-width: 860px) {
        .steps-grid { grid-template-columns:repeat(2, 1fr); }
        .step-card { min-height:auto; }
        .step-num { font-size:46px; }
        .step-card h3 { font-size:26px; }
    }
    @media (max-width: 560px) {
        .steps-grid { grid-template-columns:1fr; }
        .step-card { padding:20px; }
    }
    
    .about-box { display:grid; grid-template-columns:1fr 1fr; gap:30px; align-items:center; padding:34px; border:1px solid var(--line-warm); border-radius:var(--r-xl); background:radial-gradient(70% 80% at 90% 0%, rgba(255,106,26,.22), transparent 64%), linear-gradient(120deg,#1a0606,#2a0e0e); overflow:hidden; }
    .about-title { font-family:var(--font-display); font-size:48px; line-height:.98; margin:0 0 12px; letter-spacing:.02em; }
    .about-title span { color:var(--orange); }
    .about-features { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .about-feature { min-height:118px; padding:16px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); }
    @media (max-width: 860px) {
        .about-box { grid-template-columns:1fr; gap:20px; padding:24px; }
        .about-title { font-size:40px; }
    }
    @media (max-width: 560px) {
        .about-box { padding:20px; border-radius:var(--r-lg); }
        .about-title { font-size:34px; }
        .about-features { grid-template-columns:1fr; }
        .about-feature { min-height:auto; }
        .about-copy { font-size:13px; }
    }
    
    .empty-panel { border:1px dashed var(--line-2); border-radius:var(--r-md); color:var(--muted); padding:24px; text-align:center; font-size:13px; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bonusCarousel', () => ({
            get track() { return this.$refs.track; },
            cardWidth() {
                const card = this.track.querySelector('.bonus-card');
                return card ? card.offsetWidth + 16 : 296;
            },
            next() {
                this.track.scrollBy({ left: this.cardWidth(), behavior: 'smooth' });
            },
            prev() {
                this.track.scrollBy({ left: -this.cardWidth(), behavior: 'smooth' });
            },
        }));

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
            }
        }));
    });
</script>
@endpush
