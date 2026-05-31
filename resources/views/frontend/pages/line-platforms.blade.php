@push('styles')
<style>
    .line-platforms-page { padding:42px 0 0; }
    .lp-hero { position:relative; overflow:hidden; min-height:330px; border:1px solid var(--line-warm); border-radius:var(--r-xl); background:#090505; box-shadow:0 24px 70px rgba(0,0,0,.4); }
    .lp-hero-bg { position:absolute; inset:0; background:radial-gradient(70% 70% at 80% 0%, rgba(255,106,26,.28), transparent 70%), #120807; }
    .lp-hero-bg img { width:100%; height:100%; object-fit:cover; display:block; opacity:.48; }
    .lp-hero-bg::after { content:""; position:absolute; inset:0; background:linear-gradient(90deg, rgba(6,3,3,.96) 0%, rgba(6,3,3,.72) 46%, rgba(6,3,3,.45) 100%); }
    .lp-hero-content { position:relative; z-index:2; min-height:330px; padding:34px; display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:28px; align-items:end; }
    .lp-identity { display:flex; align-items:center; gap:16px; margin-bottom:80px; }
    .lp-avatar { width:76px; height:76px; border-radius:14px; border:1px solid rgba(255,255,255,.14); background:#070404; display:flex; align-items:center; justify-content:center; overflow:hidden; color:var(--orange); font-family:var(--font-display); font-size:34px; }
    .lp-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .lp-identity strong { display:block; font-family:var(--font-display); font-size:38px; line-height:.9; letter-spacing:.02em; }
    .lp-identity span { display:block; margin-top:5px; color:var(--muted); font-size:13px; max-width:520px; }
    .lp-title { margin:0; font-family:var(--font-display); font-size:76px; line-height:.88; letter-spacing:.02em; text-transform:uppercase; max-width:700px; }
    .lp-title span { color:var(--orange); }
    .lp-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:22px; }
    .lp-count-card { align-self:center; width:230px; border:1px solid rgba(255,255,255,.14); border-radius:14px; background:rgba(12,7,7,.72); backdrop-filter:blur(10px); padding:20px; }
    .lp-count-card i { color:var(--orange); font-size:24px; }
    .lp-count-card strong { display:block; margin-top:14px; font-family:var(--font-display); font-size:54px; line-height:.8; }
    .lp-count-card span { display:block; margin-top:7px; color:var(--muted); font-size:12px; font-weight:800; }
    .lp-vendor-strip { display:flex; align-items:center; justify-content:space-between; gap:16px; border:1px solid rgba(255,106,26,.22); border-radius:10px; background:linear-gradient(90deg, rgba(255,106,26,.07), rgba(255,255,255,.035)); padding:12px 16px; margin-bottom:14px; }
    .lp-vendor-identity { display:flex; align-items:center; gap:12px; text-decoration:none; color:#fff; transition:opacity .15s; }
    .lp-vendor-identity:hover { opacity:.82; }
    .lp-vendor-avatar { width:46px; height:46px; border-radius:10px; border:1px solid rgba(255,255,255,.14); background:linear-gradient(135deg, var(--orange), var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; color:#190702; font-family:var(--font-display); font-size:18px; flex-shrink:0; }
    .lp-vendor-avatar img { width:100%; height:100%; object-fit:cover; }
    .lp-vendor-identity span { display:block; color:rgba(255,255,255,.5); font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
    .lp-vendor-identity strong { display:block; font-size:15px; font-weight:900; margin-top:2px; }
    .lp-vendor-meta { display:flex; gap:8px; flex-wrap:wrap; }
    .lp-vendor-meta a { display:inline-flex; align-items:center; gap:6px; border:1px solid rgba(255,255,255,.12); border-radius:6px; padding:6px 12px; color:rgba(255,255,255,.72); text-decoration:none; font-size:11px; font-weight:900; text-transform:uppercase; transition:border-color .15s, color .15s; }
    .lp-vendor-meta a:hover { border-color:rgba(255,106,26,.42); color:var(--orange); }
    .lp-vendor-meta i { color:var(--orange); font-size:11px; }
    @media (max-width: 560px) { .lp-vendor-strip { flex-direction:column; align-items:flex-start; } }
    .lp-panel { margin-top:18px; border:1px solid var(--line); border-radius:var(--r-md); background:linear-gradient(180deg,#170b0b,#0f0707); padding:22px; }
    .lp-panel-head { display:flex; align-items:end; justify-content:space-between; gap:16px; margin-bottom:18px; }
    .lp-panel-head p { margin:0 0 5px; color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .lp-panel-head h2 { margin:0; font-family:var(--font-display); font-size:42px; line-height:.92; }
    @media (max-width: 860px) {
        .lp-hero-content { grid-template-columns:1fr; align-items:end; padding:24px; }
        .lp-identity { margin-bottom:40px; }
        .lp-title { font-size:56px; }
        .lp-count-card { width:100%; }
    }
    @media (max-width: 560px) {
        .line-platforms-page { padding-top:28px; }
        .lp-hero-content { padding:20px; }
        .lp-identity { display:grid; grid-template-columns:64px minmax(0,1fr); margin-bottom:32px; }
        .lp-avatar { width:64px; height:64px; }
        .lp-identity strong { font-size:30px; }
        .lp-title { font-size:42px; }
        .lp-panel { padding:16px; }
        .lp-panel-head { display:block; }
        .lp-panel-head h2 { font-size:34px; }
        .lp-actions .fe-btn { width:100%; }
    }
</style>
@endpush

@php
    $assetUrl = function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/storage/'])) {
            return $path;
        }

        return asset('storage/'.$path);
    };

    $cover = $assetUrl($line->portada_url);
    $avatar = $assetUrl($line->perfil_url);
    $lineUrl = route('frontend.cajero.lineas.detalle', [$publicVendor, $line]);
@endphp

<div class="line-platforms-page">
    <div class="fe-shell">
        @include('frontend.components.breadcrumbs', [
            'items' => [
                ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
                ['label' => $publicVendor->name, 'url' => route('frontend.cajero.inicio', $publicVendor)],
                ['label' => 'Lineas', 'url' => route('frontend.cajero.lineas', $publicVendor)],
                ['label' => $line->name, 'url' => route('frontend.cajero.lineas.detalle', [$publicVendor, $line])],
                ['label' => 'Plataformas'],
            ],
        ])
        @if(isset($publicVendor))
            @php
                $vendorLogoUrl = $assetUrl($publicVendor->logo);
                $vendorPortraitUrl = $assetUrl($publicVendor->portrait_image) ?: $vendorLogoUrl;
            @endphp
            <div class="lp-vendor-strip">
                <a href="{{ route('frontend.cajero.inicio', $publicVendor) }}" class="lp-vendor-identity" wire:navigate>
                    <div class="lp-vendor-avatar">
                        @if($vendorPortraitUrl)
                            <img src="{{ $vendorPortraitUrl }}" alt="{{ $publicVendor->name }}">
                        @else
                            {{ strtoupper(mb_substr($publicVendor->name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <span>Cajero</span>
                        <strong>{{ $publicVendor->name }}</strong>
                    </div>
                </a>
                <div class="lp-vendor-meta">
                    <a href="{{ route('frontend.cajero.lineas', $publicVendor) }}" wire:navigate>
                        <i class="fa-solid fa-layer-group"></i> Lineas
                    </a>
                    <a href="{{ route('frontend.cajero.bonos', $publicVendor) }}" wire:navigate>
                        <i class="fa-solid fa-gift"></i> Bonos
                    </a>
                    <a href="{{ route('frontend.cajero.sorteos', $publicVendor) }}" wire:navigate>
                        <i class="fa-solid fa-trophy"></i> Sorteos
                    </a>
                </div>
            </div>
        @endif

        <section class="lp-hero">
            <div class="lp-hero-bg">
                @if($cover)
                    <img src="{{ $cover }}" alt="{{ $line->name }}">
                @endif
            </div>
            <div class="lp-hero-content">
                <div>
                    <div class="lp-identity">
                        <div class="lp-avatar">
                            @if($avatar)
                                <img src="{{ $avatar }}" alt="{{ $line->name }}">
                            @else
                                {{ strtoupper(mb_substr($line->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <strong>Linea {{ $line->name }}</strong>
                            <span>{{ $line->description ?: 'Acceso rapido a plataformas exclusivas de esta linea.' }}</span>
                        </div>
                    </div>
                    <p class="fe-kicker">{{ $vendor?->name ?: 'RED PICANTES' }}</p>
                    <h1 class="lp-title">Plataformas <span>disponibles</span></h1>
                    <div class="lp-actions">
                        <a class="fe-btn ghost" href="{{ $lineUrl }}" wire:navigate>
                            <i class="fa-solid fa-arrow-left"></i>
                            Volver a la linea
                        </a>
                        @if($vendor)
                            <a class="fe-btn ghost" href="{{ route('frontend.cajero.inicio', $vendor) }}" wire:navigate>
                                <i class="fa-solid fa-user-tie"></i>
                                Ver cajero
                            </a>
                        @endif
                    </div>
                </div>
                <aside class="lp-count-card">
                    <i class="fa-solid fa-layer-group"></i>
                    <strong>{{ $platforms->count() }}</strong>
                    <span>plataformas asignadas</span>
                </aside>
            </div>
        </section>

        <section class="lp-panel">
            <div class="lp-panel-head">
                <div>
                    <p>Plataformas disponibles</p>
                    <h2>Accesos de {{ $line->name }}</h2>
                </div>
            </div>
            @if($platforms->count())
                <div class="public-platform-grid">
                    @foreach($platforms as $platform)
                        @include('frontend.components.platform-card', ['platform' => $platform, 'line' => $line])
                    @endforeach
                </div>
            @else
                <div class="empty-panel">No hay plataformas activas publicadas para esta linea.</div>
            @endif
        </section>
    </div>
</div>
