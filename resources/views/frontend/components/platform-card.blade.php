@php
    $line = $line ?? null;
    $compact = $compact ?? false;

    $assetUrl = function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/storage/'])) {
            return $path;
        }

        return asset('storage/'.$path);
    };

    $logoUrl = $assetUrl($platform->logo_url);
    $message = $platform->pivot?->custom_message ?: $platform->description ?: 'Acceso exclusivo para esta linea.';
    $rawAccessUrl = trim((string) $platform->website_url);
    $accessUrl = $rawAccessUrl === ''
        ? null
        : (\Illuminate\Support\Str::startsWith($rawAccessUrl, ['http://', 'https://']) ? $rawAccessUrl : 'https://'.$rawAccessUrl);
@endphp

@once
@push('styles')
<style>
    .public-platform-grid { display:grid; gap:14px; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); }
    .public-platform-card { min-width:0; display:grid; grid-template-columns:72px minmax(0, 1fr); gap:14px; align-items:center; padding:14px; border:1px solid rgba(255,255,255,.1); border-radius:10px; background:linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.025)); box-shadow:0 18px 42px rgba(0,0,0,.26); overflow:hidden; }
    .public-platform-card.compact { grid-template-columns:64px minmax(0, 1fr); padding:12px; }
    .public-platform-logo { width:72px; aspect-ratio:1; border-radius:10px; border:1px solid rgba(255,255,255,.1); background:#080505; display:flex; align-items:center; justify-content:center; overflow:hidden; color:var(--orange); font-family:var(--font-display); font-size:30px; letter-spacing:.03em; }
    .public-platform-card.compact .public-platform-logo { width:64px; font-size:26px; }
    .public-platform-logo img { width:100%; height:100%; object-fit:cover; display:block; }
    .public-platform-body { min-width:0; display:grid; gap:8px; }
    .public-platform-name { display:block; color:#fff; font-family:var(--font-display); font-size:24px; line-height:.95; letter-spacing:.02em; overflow-wrap:anywhere; }
    .public-platform-copy { color:var(--muted); font-size:12px; line-height:1.35; margin:0; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .public-platform-btn { width:100%; min-height:34px; display:inline-flex; align-items:center; justify-content:center; gap:7px; border:1px solid transparent; border-radius:7px; background:linear-gradient(180deg,var(--orange-2),var(--orange) 62%,var(--orange-deep)); color:#190702; font-size:11px; font-weight:900; text-transform:uppercase; text-decoration:none; box-shadow:0 10px 24px rgba(255,106,26,.24); }
    .public-platform-btn.disabled { border-color:rgba(255,255,255,.1); background:rgba(255,255,255,.045); color:var(--muted-2); box-shadow:none; cursor:not-allowed; }
    @media (max-width: 560px) {
        .public-platform-grid { grid-template-columns:1fr; }
        .public-platform-card { grid-template-columns:64px minmax(0, 1fr); }
        .public-platform-logo { width:64px; }
    }
</style>
@endpush
@endonce

<article class="public-platform-card {{ $compact ? 'compact' : '' }}">
    <div class="public-platform-logo">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $platform->name }}">
        @else
            {{ strtoupper(mb_substr($platform->name, 0, 2)) }}
        @endif
    </div>
    <div class="public-platform-body">
        <div>
            <strong class="public-platform-name">{{ $platform->name }}</strong>
            <p class="public-platform-copy">{{ $message }}</p>
        </div>
        @if($accessUrl)
            <a class="public-platform-btn" href="{{ $accessUrl }}" target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Ingresar
            </a>
        @else
            <span class="public-platform-btn disabled">
                <i class="fa-solid fa-lock"></i>
                Sin acceso
            </span>
        @endif
    </div>
</article>
