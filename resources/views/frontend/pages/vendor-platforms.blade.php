@php
    $assetUrl = function (?string $path): ?string {
        if (! $path) return null;
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/storage/'])) return $path;
        return asset('storage/'.$path);
    };
    $logoUrl = $assetUrl($vendor->logo);
@endphp

<div class="vp-page">
    <div class="fe-shell">
        @include('frontend.components.breadcrumbs', [
            'items' => [
                ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
                ['label' => $vendor->name, 'url' => route('frontend.cajero.inicio', $vendor)],
                ['label' => 'Plataformas'],
            ],
        ])

        <header class="vp-header">
            <div class="vp-header-identity">
                <div class="vp-avatar">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $vendor->name }}">
                    @else
                        {{ strtoupper(mb_substr($vendor->name, 0, 2)) }}
                    @endif
                </div>
                <div>
                    <p>{{ $vendor->name }}</p>
                    <h1>Plataformas <span>disponibles</span></h1>
                </div>
            </div>
            <aside class="vp-count">
                <i class="fa-solid fa-layer-group"></i>
                <strong>{{ $platforms->count() }}</strong>
                <span>plataformas activas</span>
            </aside>
        </header>

        @if($platforms->count())
            <div class="public-platform-grid">
                @foreach($platforms as $platform)
                    @include('frontend.components.platform-card', ['platform' => $platform])
                @endforeach
            </div>
        @else
            <div class="vp-empty">Este cajero no tiene plataformas activas publicadas.</div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .vp-page { padding: 42px 0 48px; }
    .vp-header { display: flex; align-items: center; justify-content: space-between; gap: 20px; border: 1px solid rgba(255,106,26,.22); border-radius: 12px; background: linear-gradient(135deg, rgba(255,106,26,.08), rgba(255,255,255,.035)); padding: 22px 24px; margin-bottom: 22px; }
    .vp-header-identity { display: flex; align-items: center; gap: 16px; }
    .vp-avatar { width: 56px; height: 56px; flex-shrink: 0; border-radius: 12px; border: 1px solid rgba(255,255,255,.14); background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; overflow: hidden; color: #190702; font-family: var(--font-display); font-size: 22px; }
    .vp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .vp-header-identity p { margin: 0 0 4px; color: var(--orange); font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; }
    .vp-header-identity h1 { margin: 0; font-family: var(--font-display); font-size: clamp(32px, 5vw, 54px); line-height: .9; letter-spacing: .02em; text-transform: uppercase; }
    .vp-header-identity h1 span { color: var(--orange); }
    .vp-count { text-align: center; border: 1px solid rgba(255,255,255,.12); border-radius: 12px; background: rgba(255,255,255,.04); padding: 16px 24px; flex-shrink: 0; }
    .vp-count i { color: var(--orange); font-size: 22px; }
    .vp-count strong { display: block; font-family: var(--font-display); font-size: 48px; line-height: .85; margin-top: 8px; }
    .vp-count span { display: block; color: rgba(255,255,255,.52); font-size: 11px; font-weight: 900; text-transform: uppercase; margin-top: 6px; }
    .vp-empty { border: 1px dashed rgba(255,255,255,.14); border-radius: 10px; padding: 32px; color: rgba(255,255,255,.5); text-align: center; font-size: 14px; }
    @media (max-width: 640px) {
        .vp-header { flex-direction: column; align-items: flex-start; }
        .vp-count { width: 100%; }
    }
</style>
@endpush
