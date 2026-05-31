@props(['line', 'showPlatformButton' => true])

@php
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
    $platforms = $line->activePlatforms;
    $avgRating = (float) $line->average_rating;
    $platformsUrl = route('frontend.cajero.lineas.plataformas', [$line->vendor, $line]);

@endphp

@once
@push('styles')
<style>
    .public-line-card { overflow:hidden; border:1px solid var(--line-warm); border-radius:var(--r-lg); background:linear-gradient(180deg,#170b0b,#0b0505); box-shadow:0 20px 52px rgba(0,0,0,.34); min-width:0; position: relative; }
    .public-line-card::before { content:""; position:absolute; top:-34px; right:-34px; width:130px; height:130px; border-radius:999px; background:radial-gradient(circle, rgba(255,106,26,.38), transparent 70%); pointer-events:none; z-index: 1; }
    
    .public-line-cover { height:158px; position:relative; background:radial-gradient(90% 90% at 70% 0%, rgba(255,106,26,.36), transparent 70%), #130807; }
    .public-line-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    
    .public-line-avatar { position:absolute; left:18px; bottom:-30px; width:70px; height:70px; border-radius:18px; border:3px solid #100707; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; font-family:var(--font-display); font-size:30px; color:#160604; z-index: 2; }
    .public-line-avatar img { width:100%; height:100%; object-fit:cover; }
    .line-vendor-badge { position:absolute; top:10px; right:10px; z-index:3; display:inline-flex; align-items:center; gap:6px; padding:4px 10px 4px 4px; border-radius:999px; background:rgba(0,0,0,.60); border:1px solid rgba(255,106,26,.35); backdrop-filter:blur(6px); color:#fff; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; text-decoration:none; }
    .line-vendor-badge-logo { width:22px; height:22px; border-radius:999px; object-fit:cover; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; font-size:9px; font-weight:900; color:#160604; font-family:var(--font-display); }
    .line-vendor-badge-logo img { width:100%; height:100%; object-fit:cover; }
    
    .public-line-body { padding:42px 18px 18px; position: relative; z-index: 2; }
    
    .public-line-name { display:block; }
    .public-line-name h2 { margin:0; font-family:var(--font-display); font-size:34px; line-height:.95; letter-spacing:.02em; }
    
    .public-line-meta { margin-top:8px; color:var(--muted); font-size:13px; line-height:1.45; }
    
    .public-line-rating { margin-top: 12px; }
    
    .line-platforms-preview { margin-top:12px; color:var(--muted-2); font-size:11px; font-weight:800; }
    
    .line-channel-list { margin-top:14px; }
    
    .line-card-actions { display:flex; gap:8px; margin-top:16px; flex-wrap:wrap; }
    .line-card-actions .fe-btn { flex:1; min-width:max-content; }
    
    @media (max-width: 620px) {
        .public-line-cover { height:140px; }
        .public-rating { margin-top:8px; }
        .line-card-actions .fe-btn { width:100%; min-width:0; }
    }
</style>
@endpush
@endonce

<article class="public-line-card">
    <div class="public-line-cover">
        @if($line->portada_url)
            <img src="{{ $line->portada_url }}" alt="{{ $line->name }}" onerror="this.style.display='none'">
        @endif
        @if($line->vendor)
            @php
                $vendorLogo = $line->vendor->logo;
                if ($vendorLogo && !\Illuminate\Support\Str::startsWith($vendorLogo, ['http://', 'https://', '/storage/'])) {
                    $vendorLogo = asset('storage/'.$vendorLogo);
                }
            @endphp
            <a href="{{ route('frontend.cajero.inicio', $line->vendor) }}" wire:navigate class="line-vendor-badge">
                <span class="line-vendor-badge-logo">
                    @if($vendorLogo)
                        <img src="{{ $vendorLogo }}" alt="{{ $line->vendor->name }}" onerror="this.outerHTML='<i class=\'fa-solid fa-store\'></i>'">
                    @else
                        <i class="fa-solid fa-store"></i>
                    @endif
                </span>
                {{ $line->vendor->name }}
            </a>
        @endif
        <div class="public-line-avatar">
            @if($line->perfil_url)
                <img src="{{ $line->perfil_url }}" alt="" onerror="this.style.display='none';this.parentElement.insertAdjacentText('beforeend','{{ strtoupper(mb_substr($line->name, 0, 2)) }}')">
            @else
                {{ strtoupper(mb_substr($line->name, 0, 2)) }}
            @endif
        </div>
    </div>

    <div class="public-line-body">
        <div class="public-line-name">
            <h2>{{ $line->name }}</h2>
        </div>
        <div class="public-line-meta">{{ $line->description ?: 'Alta rapida, carga de saldo y atencion directa para jugar online.' }}</div>
        <div class="public-line-rating">
            @include('frontend.components.rating', [
                'rating' => $avgRating,
                'count' => $line->ratings_count,
                'showValue' => true,
                'size' => 'sm',
            ])
        </div>

        <div class="line-platforms-preview">
            {{ $platforms->count() }} plataformas disponibles
        </div>

        <div class="line-channel-list">
            @include('frontend.components.contact-icons', [
                'contacts' => $contacts,
                'limit' => 2,
                'class' => 'compact',
                'emptyText' => 'Sin canales directos',
            ])
        </div>

        @php
            $detailUrl = route('frontend.cajero.lineas.detalle', [$line->vendor, $line]);
        @endphp
        <div class="line-card-actions">
            <a href="{{ $detailUrl }}" wire:navigate class="fe-btn ghost">Ver detalle</a>
            @if($showPlatformButton && $platforms->count())
                <a href="{{ $platformsUrl }}" target="_blank" rel="noopener" class="fe-btn primary">
                    Plataformas
                </a>
            @endif
        </div>
    </div>
</article>
