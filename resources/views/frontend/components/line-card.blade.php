@props(['line'])

@php
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
    $manager = $line->lineAgents->first(fn ($lineAgent) => $lineAgent->role === 'encargado' && $lineAgent->is_active);
    $platforms = $line->activePlatforms;
    
    // Dynamic rating calculation
    $avgRating = $line->average_rating; // from Model attribute
    $fullStars = floor($avgRating);
    $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);

    $channelIcons = [
        'wsp' => 'fa-brands fa-whatsapp', 'wsap' => 'fa-brands fa-whatsapp', 'wa' => 'fa-brands fa-whatsapp', 'whatsapp' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram', 'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram', 'ig' => 'fa-brands fa-instagram',
        'facebook' => 'fa-brands fa-facebook', 'fb' => 'fa-brands fa-facebook',
        'phone' => 'fa-solid fa-phone', 'telefono' => 'fa-solid fa-phone', 'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope', 'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe', 'tiktok' => 'fa-brands fa-tiktok', 'twitter' => 'fa-brands fa-x-twitter', 'x' => 'fa-brands fa-x-twitter', 'youtube' => 'fa-brands fa-youtube',
    ];
    $normalizeChannelType = fn (?string $type): string => strtolower(trim((string) $type));
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
    
    .public-line-body { padding:42px 18px 18px; position: relative; z-index: 2; }
    
    .public-line-name { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .public-line-name h2 { margin:0; font-family:var(--font-display); font-size:34px; line-height:.95; letter-spacing:.02em; }
    
    .rating-stars { color:var(--amber); font-size:13px; white-space:nowrap; letter-spacing:.04em; }
    .rating-stars .empty-star { color: rgba(255,255,255,0.15); }
    
    .public-line-meta { margin-top:8px; color:var(--muted); font-size:13px; line-height:1.45; }
    
    .public-line-manager { margin-top:14px; padding:10px 12px; border:1px solid var(--line); border-radius:var(--r-sm); background:rgba(255,255,255,.035); color:#fff; font-size:12px; font-weight:800; }
    .public-line-manager span { color:var(--orange); font-family:var(--font-mono); }
    
    .line-platforms-preview { margin-top:12px; color:var(--muted-2); font-size:11px; font-weight:800; }
    
    .line-channel-list { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; align-items:flex-start; }
    .line-channel { width:max-content; max-width:100%; display:inline-grid; grid-template-columns:34px auto; gap:10px; align-items:center; padding:9px 12px 9px 9px; border:1px solid var(--line); border-radius:999px; background:rgba(255,255,255,.035); text-decoration:none; transition: all 0.2s ease; }
    .line-channel:hover { border-color: var(--orange); background: rgba(255, 106, 26, 0.05); }
    .line-channel i { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:rgba(154,154,154,.08); color:#9a9a9a; }
    .line-channel strong { font-size:12px; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; min-width:0; }
    
    .line-card-actions { display:flex; gap:8px; margin-top:16px; flex-wrap:wrap; }
    .line-card-actions .fe-btn { flex:1; min-width:max-content; }
    
    @media (max-width: 620px) {
        .public-line-cover { height:140px; }
        .public-line-name { display:block; }
        .rating-stars { margin-top:8px; display:block; }
        .line-channel { width:100%; grid-template-columns:34px minmax(0, 1fr); border-radius:12px; }
        .line-channel strong { white-space:normal; overflow-wrap:anywhere; }
        .line-card-actions .fe-btn { width:100%; min-width:0; }
    }
</style>
@endpush
@endonce

<article class="public-line-card">
    <div class="public-line-cover">
        @if($line->portada_url)
            <img src="{{ $line->portada_url }}" alt="{{ $line->name }}">
        @endif
        <div class="public-line-avatar">
            @if($line->perfil_url)
                <img src="{{ $line->perfil_url }}" alt="">
            @else
                {{ strtoupper(mb_substr($line->name, 0, 2)) }}
            @endif
        </div>
    </div>

    <div class="public-line-body">
        <div class="public-line-name">
            <h2>{{ $line->name }}</h2>
            <div class="rating-stars" aria-label="Valoracion general {{ $avgRating }} estrellas">
                @for($i = 0; $i < $fullStars; $i++)
                    ★
                @endfor
                @if($hasHalfStar)
                    <i class="fa-solid fa-star-half-stroke" style="font-size: 0.9em; vertical-align: middle; margin-top: -2px; display: inline-block;"></i>
                @endif
                @for($i = 0; $i < $emptyStars; $i++)
                    <span class="empty-star">★</span>
                @endfor
                <span style="font-size: 0.85em; opacity: 0.6; margin-left: 4px; font-family: var(--font-body); font-weight: 500;">({{ $line->ratings_count }})</span>
            </div>
        </div>
        <div class="public-line-meta">{{ $line->description ?: 'Alta rápida, carga de saldo y atención directa para jugar online.' }}</div>
        <div class="public-line-manager">
            Encargado:
            <span>{{ $manager?->agent?->username ?: $manager?->agent?->name ?: 'A confirmar' }}</span>
        </div>

        <div class="line-platforms-preview">
            {{ $platforms->count() }} plataformas disponibles
        </div>

        <div class="line-channel-list">
            @forelse($contacts->take(2) as $contact)
                @php
                    $type = $normalizeChannelType($contact['type'] ?? 'web');
                    $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                    $name = $contact['name'] ?: ucfirst($type);
                @endphp
                <a class="line-channel" href="{{ $contact['value'] }}" target="_blank" rel="noopener" style="border-color:rgba(154,154,154,.18);">
                    <i class="{{ $icon }}"></i>
                    <strong>{{ $name }}</strong>
                </a>
            @empty
                <div class="line-channel-empty">Sin canales directos</div>
            @endforelse
        </div>

        <div class="line-card-actions">
            <a href="{{ route('frontend.lines.show', $line) }}" wire:navigate class="fe-btn ghost" style="width:100%;">Ver detalle</a>
        </div>
    </div>
</article>
