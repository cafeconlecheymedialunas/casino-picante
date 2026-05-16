@push('styles')
<style>
    .home-hero { padding:0; }
    .home-hero .fe-shell { width:100%; max-width:none; }
    .home-hero-carousel { display:grid; grid-auto-flow:column; grid-auto-columns:100%; gap:0; overflow-x:auto; scroll-snap-type:inline mandatory; border-radius:0; box-shadow:0 22px 70px rgba(0,0,0,.5); }
    .home-hero-carousel::-webkit-scrollbar { display:none; }
    .home-hero-slide { position:relative; width:100%; min-height:520px; overflow:hidden; border:0; border-radius:0; background:#120909; scroll-snap-align:start; text-decoration:none; display:block; }
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
    #sorteo { position:relative; overflow:hidden; padding-block:76px; }
    #sorteo::before { content:""; position:absolute; inset:0; background:radial-gradient(52% 70% at 92% 22%, rgba(255,106,26,.17), transparent 64%), radial-gradient(36% 72% at 0% 45%, rgba(255,106,26,.1), transparent 70%); pointer-events:none; }
    #sorteo .fe-shell { position:relative; z-index:1; }
    .raffle-section-head { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:28px; align-items:end; }
    .raffle-main-title { margin:0 0 40px 0; font-family:var(--font-display); font-size:58px; line-height:.82; letter-spacing:.02em; text-transform:uppercase; text-align:center; }
    .raffle-main-title span { color:var(--orange); text-shadow:0 0 32px rgba(255,106,26,.28); }
    .raffle-subtitle { margin:14px 0 0; color:#fff; font-size:clamp(15px, 1.45vw, 22px); line-height:1.35; max-width:960px; text-wrap:balance; }
    .raffle-section-head .fe-btn { white-space:nowrap; box-shadow:0 18px 42px rgba(255,106,26,.28); }
    .raffle-info-bar { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:22px; align-items:end; margin:0 0 8px; }
    .raffle-meta h4 { margin:0; color:rgba(255,255,255,.72); font-size:40px; font-weight:600; text-transform:uppercase; }
    .raffle-meta p { margin:8px 0 0; color:#fff; font-size:clamp(15px, 1.35vw, 20px); line-height:1.35; }
    .raffle-meta strong { color:#fff; font-weight:950; }
    .raffle-timer { display:grid; grid-template-columns:repeat(4, minmax(62px,1fr)); gap:8px; min-width:330px; }
    .timer-unit { min-height:76px; display:flex; flex-direction:column; align-items:center; justify-content:center; border:1px solid rgba(255,106,26,.28); border-radius:8px; background:linear-gradient(180deg, rgba(255,106,26,.13), rgba(255,255,255,.035)); box-shadow:0 14px 36px rgba(0,0,0,.28), inset 0 0 20px rgba(255,106,26,.05); }
    .timer-val { font-family:var(--font-display); color:#fff; font-size:36px; line-height:.9; }
    .timer-label { margin-top:7px; color:var(--orange); font-size:10px; font-weight:900; letter-spacing:.16em; }
    .raffle-prizes-carousel { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(360px, 1fr); gap:18px; overflow-x:auto; overscroll-behavior-inline:contain; scroll-snap-type:inline mandatory; padding:22px 0 12px; scrollbar-width:thin; scrollbar-color:rgba(255,106,26,.74) rgba(255,255,255,.08); }
    .raffle-prizes-carousel::-webkit-scrollbar { height:8px; }
    .raffle-prizes-carousel::-webkit-scrollbar-track { background:rgba(255,255,255,.08); border-radius:999px; }
    .raffle-prizes-carousel::-webkit-scrollbar-thumb { background:rgba(255,106,26,.74); border-radius:999px; }
    .raffle-prize-item { position:relative; min-height:390px; border:1px solid rgba(255,106,26,.34); border-radius:10px; background:#0b0504; overflow:hidden; scroll-snap-align:start; box-shadow:0 26px 70px rgba(0,0,0,.46); }
    .raffle-prize-item img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block; transform:scale(1.01); transition:transform .35s ease; }
    .raffle-prize-item:hover img { transform:scale(1.045); }
    .raffle-prize-item::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,.04) 22%, rgba(0,0,0,.36) 58%, rgba(7,2,2,.94) 100%); }
    .raffle-prize-overlay { position:absolute; z-index:1; left:0; right:0; bottom:0; padding:24px; }
    .prize-tag { display:inline-flex; margin-bottom:9px; border:1px solid rgba(255,106,26,.45); border-radius:999px; background:rgba(255,106,26,.14); color:var(--orange); padding:6px 10px; font-size:10px; font-weight:950; letter-spacing:.16em; }
    .prize-name { margin:0; color:#fff; font-family:var(--font-display); font-size:clamp(32px, 3.4vw, 54px); line-height:.9; text-transform:uppercase; letter-spacing:.02em; }
    .prize-value { margin-top:9px; color:rgba(255,255,255,.82); font-size:13px; font-weight:800; }
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
    .raffles-slider { display:flex; overflow:hidden; position:relative; }
    .raffles-slider-track { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; overscroll-behavior:contain; scrollbar-width:none; -ms-overflow-style:none; }
    .raffles-slider-track::-webkit-scrollbar { display:none; }
    .raffle-slide { flex:0 0 100%; scroll-snap-align:start; display:flex; flex-direction:column; gap:20px; padding:0 10px; }
    .raffle-slide-header { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; }
    .raffle-slide-footer { display:flex; justify-content:center; padding-top:8px; }
    .slider-btn { position:absolute; top:50%; transform:translateY(-50%); z-index:10; width:44px; height:44px; border-radius:50%; border:1px solid rgba(255,106,26,.45); background:rgba(0,0,0,.6); backdrop-filter:blur(4px); color:var(--orange); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; }
    .sorteo-slider-wrapper .slider-btn { top:calc(50% + 40px); }
    .slider-btn-prev { left:10px; }
    .slider-btn-next { right:10px; }
    .slider-btn:hover { background:rgba(255,106,26,.25); transform:translateY(-50%) scale(1.05); }
    @media (max-width: 768px) {
        .slider-btn { display:none; }
    }
    .bonus-carousel { position:relative; width:100%; display:grid; grid-auto-flow:column; grid-auto-columns:minmax(280px, 360px); gap:16px; overflow-x:auto; overscroll-behavior-inline:contain; -webkit-overflow-scrolling:touch; padding:4px 0 16px; scroll-snap-type:inline mandatory; scrollbar-width:thin; scrollbar-color:rgba(255,106,26,.72) rgba(255,255,255,.08); }
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
        .raffle-rank, .raffle-prize-tile.primary .raffle-rank { font-size:72px; }
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
        .raffle-prize-item { min-height:280px; }
        .raffle-prize-overlay { padding:18px; }
        .prize-name { font-size:31px; }
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
            <div class="sorteo-slider-wrapper sorteo-slider-full">
                <button type="button" class="slider-btn slider-btn-prev raffle-swiper-btn-prev">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="raffleSwiper swiper">
                    <div class="swiper-wrapper">
                        @foreach($raffles as $index => $raffle)
                        <div class="swiper-slide raffle-slide">
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
                </div>
                <button type="button" class="slider-btn slider-btn-next raffle-swiper-btn-next">
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


