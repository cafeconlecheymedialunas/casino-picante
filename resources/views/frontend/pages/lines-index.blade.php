@push('styles')
<style>
    .lines-page { padding:56px 0 0; }
    .lines-hero { display:grid; grid-template-columns:minmax(0, 1fr) minmax(260px, 360px); gap:24px; align-items:end; }
    .lines-title { font-family:var(--font-display); font-size:72px; line-height:.9; letter-spacing:.02em; margin:0; max-width:760px; }
    .lines-title span { color:var(--orange); }
    .public-lines-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:16px; margin-top:28px; }
    .public-line-card { overflow:hidden; border:1px solid var(--line-warm); border-radius:var(--r-lg); background:linear-gradient(180deg,#170b0b,#0b0505); box-shadow:0 20px 52px rgba(0,0,0,.34); min-width:0; }
    .public-line-cover { height:158px; position:relative; background:radial-gradient(90% 90% at 70% 0%, rgba(255,106,26,.36), transparent 70%), #130807; }
    .public-line-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    .public-line-avatar { position:absolute; left:18px; bottom:-30px; width:70px; height:70px; border-radius:18px; border:3px solid #100707; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; font-family:var(--font-display); font-size:30px; color:#160604; }
    .public-line-avatar img { width:100%; height:100%; object-fit:cover; }
    .public-line-body { padding:42px 18px 18px; }
    .public-line-name { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .public-line-name h2 { margin:0; font-family:var(--font-display); font-size:34px; line-height:.95; letter-spacing:.02em; }
    .rating-stars { color:var(--amber); font-size:13px; white-space:nowrap; letter-spacing:.04em; }
    .public-line-meta { margin-top:8px; color:var(--muted); font-size:13px; line-height:1.45; }
    .public-line-manager { margin-top:14px; padding:10px 12px; border:1px solid var(--line); border-radius:var(--r-sm); background:rgba(255,255,255,.035); color:#fff; font-size:12px; font-weight:800; }
    .public-line-manager span { color:var(--orange); font-family:var(--font-mono); }
    .line-channel-list { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; align-items:flex-start; }
    .line-channel { width:max-content; max-width:100%; display:inline-grid; grid-template-columns:34px auto; gap:10px; align-items:center; padding:9px 12px 9px 9px; border:1px solid var(--line); border-radius:999px; background:rgba(255,255,255,.035); text-decoration:none; }
    .line-channel i { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:rgba(154,154,154,.08); color:#9a9a9a; }
    .line-channel strong { font-size:12px; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .line-card-actions { display:flex; gap:8px; margin-top:16px; flex-wrap:wrap; }
    .line-card-actions .fe-btn { flex:1; min-width:max-content; }
    .line-platforms-preview { margin-top:12px; color:var(--muted-2); font-size:11px; font-weight:800; }
    .lines-empty { margin-top:28px; }
    @media (max-width: 980px) {
        .lines-hero, .public-lines-grid { grid-template-columns:1fr; }
        .lines-title { font-size:56px; }
    }
    @media (max-width: 620px) {
        .lines-page { padding-top:34px; }
        .lines-title { font-size:44px; }
        .public-line-cover { height:140px; }
        .public-line-name { display:block; }
        .rating-stars { margin-top:8px; display:block; }
        .line-channel { grid-template-columns:34px auto; }
    }
</style>
@endpush

@php
    $channelIcons = [
        'wsp' => 'fa-brands fa-whatsapp', 'wsap' => 'fa-brands fa-whatsapp', 'wa' => 'fa-brands fa-whatsapp', 'whatsapp' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram', 'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram', 'ig' => 'fa-brands fa-instagram',
        'facebook' => 'fa-brands fa-facebook', 'fb' => 'fa-brands fa-facebook',
        'phone' => 'fa-solid fa-phone', 'telefono' => 'fa-solid fa-phone', 'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope', 'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe', 'tiktok' => 'fa-brands fa-tiktok', 'twitter' => 'fa-brands fa-x-twitter', 'x' => 'fa-brands fa-x-twitter', 'youtube' => 'fa-brands fa-youtube',
    ];
    $channelColors = collect($channelIcons)->mapWithKeys(fn ($icon, $type) => [$type => '#9a9a9a'])->all();
    $normalizeChannelType = fn (?string $type): string => strtolower(trim((string) $type));
@endphp

<div>
    <section class="lines-page">
        <div class="fe-shell">
            <div class="lines-hero">
                <div>
                    <div class="fe-kicker">Lineas disponibles</div>
                    <h1 class="lines-title">Elegí una línea <span>activa</span></h1>
                    <p class="fe-subtitle">Pedí tu usuario, consultá plataformas disponibles y contactá al canal asignado para empezar a jugar.</p>
                </div>

            </div>

            @if($lines->count())
                <div class="public-lines-grid">
                    @foreach($lines as $line)
                        @php
                            $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
                            $manager = $line->lineAgents->first(fn ($lineAgent) => $lineAgent->role === 'encargado' && $lineAgent->is_active);
                            $platforms = $line->activePlatforms;
                        @endphp

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
                                    <div class="rating-stars" aria-label="Valoracion general 5 estrellas">★★★★★</div>
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
                                            $color = $channelColors[$type] ?? 'var(--orange)';
                                            $name = $contact['name'] ?: ucfirst($type);
                                        @endphp
                                        <a class="line-channel" href="{{ $contact['value'] }}" target="_blank" rel="noopener" style="border-color:rgba(154,154,154,.18);">
                                            <i class="{{ $icon }}"></i>
                                            <strong>{{ $name }}</strong>
                                        </a>
                                    @empty
                                        <div class="public-line-meta">Sin canales publicados.</div>
                                    @endforelse
                                </div>

                                <div class="line-card-actions">
                                    <a href="{{ route('frontend.lines.show', $line) }}" wire:navigate class="fe-btn ghost">Ver detalle</a>
                                    <a href="{{ route('frontend.lines.show', $line) }}#plataformas" wire:navigate class="fe-btn primary">Ver listado</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-panel lines-empty">No hay líneas activas publicadas por ahora.</div>
            @endif
        </div>
    </section>
</div>
