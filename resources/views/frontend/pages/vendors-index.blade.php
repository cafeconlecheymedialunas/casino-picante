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

    $contactHref = function (array $contact): string {
        $value = trim((string) ($contact['value'] ?? ''));
        $type = strtolower(trim((string) ($contact['type'] ?? 'web')));

        if ($value === '') {
            return '#';
        }

        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return $value;
        }

        if (in_array($type, ['email', 'mail'], true)) {
            return 'mailto:'.$value;
        }

        if (in_array($type, ['phone', 'telefono', 'tel'], true)) {
            return 'tel:'.preg_replace('/\s+/', '', $value);
        }

        if (in_array($type, ['whatsapp', 'wsp', 'wsap', 'wa'], true)) {
            $digits = preg_replace('/\D+/', '', $value);
            return $digits ? 'https://wa.me/'.$digits : $value;
        }

        return $value;
    };

    $channelIcons = [
        'whatsapp' => 'fa-brands fa-whatsapp',
        'wsp' => 'fa-brands fa-whatsapp',
        'wsap' => 'fa-brands fa-whatsapp',
        'wa' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram',
        'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram',
        'ig' => 'fa-brands fa-instagram',
        'phone' => 'fa-solid fa-phone',
        'telefono' => 'fa-solid fa-phone',
        'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope',
        'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe',
    ];
@endphp

<div class="vendors-public-page">
    <section class="vpi-hero">
        <div class="fe-shell">
            <x-page-header-cajeros
                :section="$pageSection"
                :vendor-count="$vendors->count()"
            />
        </div>
    </section>

    <section class="fe-shell vpi-content">
        @if($vendors->count())
            <div class="vpi-grid">
                @foreach($vendors as $vendor)
                    @php
                        $logo = $assetUrl($vendor->logo);
                        $portrait = $assetUrl($vendor->portrait_image);
                        $contacts = collect($vendor->contacts ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
                        $primaryContact = $contacts->first();
                        $displayName = trim(($vendor->user?->name ?? '').' '.($vendor->user?->apellido ?? '')) ?: $vendor->name;
                        $rank = $loop->iteration;
                        $rankLabel = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : "#$rank"));
                        $isTop = $rank <= 3;
                    @endphp
                    <article class="vpi-card {{ $isTop ? 'vpi-card--top' : '' }}"
                        {!! $portrait ? "style=\"--card-portrait: url('{$portrait}')\"" : '' !!}>
                        <div class="vpi-card-glow"></div>
                        <div class="vpi-card-top">
                            <div class="vpi-logo">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="{{ $vendor->name }}">
                                @else
                                    {{ strtoupper(mb_substr($vendor->name, 0, 2)) }}
                                @endif
                            </div>
                            <span class="vpi-rank {{ $isTop ? 'vpi-rank--top' : '' }}">{{ $rankLabel }}</span>
                        </div>

                        <div class="vpi-card-body">
                            <p class="vpi-slug">{{ $vendor->slug }}</p>
                            <h2>{{ $vendor->name }}</h2>
                            <p class="vpi-description">{{ $vendor->description ?: 'Cajero con atención personalizada, carga rápida y líneas disponibles para jugar online.' }}</p>

                            @if($vendor->features && count($vendor->features))
                                <div class="vpi-features-list">
                                    @foreach(collect($vendor->features)->take(2) as $feature)
                                        <div class="vpi-feature-tag">
                                            <i class="{{ $feature['icon'] ?? 'fa-solid fa-check' }}"></i>
                                            <span>{{ $feature['title'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="vpi-reputation">
                                <div class="vpi-rep-left">
                                    <div class="vpi-rep-stars">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="fa-{{ $s <= round($vendor->avg_rating) ? 'solid' : 'regular' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <span class="vpi-rep-value">{{ number_format($vendor->avg_rating, 1) }}</span>
                                    <span class="vpi-rep-label">Rating</span>
                                </div>
                                <div class="vpi-rep-divider"></div>
                                <div class="vpi-rep-right">
                                    <i class="fa-solid fa-users vpi-rep-icon"></i>
                                    <span class="vpi-rep-value">{{ number_format($vendor->total_clients) }}</span>
                                    <span class="vpi-rep-label">Clientes</span>
                                </div>
                            </div>

                            <div class="vpi-meta">
                                <div><strong>{{ $vendor->active_lines_count }}</strong><span>Líneas</span></div>
                                <div><strong>{{ $contacts->count() }}</strong><span>Contactos</span></div>
                                <div><strong>{{ $displayName }}</strong><span>Cajero</span></div>
                            </div>

                            @if($contacts->count())
                                <div class="vpi-contact-row">
                                    @foreach($contacts->take(3) as $contact)
                                        @php
                                            $type = strtolower(trim((string) ($contact['type'] ?? 'web')));
                                            $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                                        @endphp
                                        <a href="{{ $contactHref($contact) }}" target="_blank" rel="noopener" title="{{ $contact['name'] ?: ucfirst($type) }}">
                                            <i class="{{ $icon }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="vpi-card-actions">
                            <a class="vpi-main-btn" href="{{ route('frontend.cajero.inicio', $vendor) }}" wire:navigate>Ver detalle</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="vpi-empty">
                <h2>No hay cajeros publicados.</h2>
                <p>Cuando haya cajeros activos, van a aparecer en este listado.</p>
            </div>
        @endif
    </section>
</div>

@push('styles')
<style>
    .vendors-public-page {
        min-height: 70vh;
        background:
            radial-gradient(44rem 34rem at 82% -8%, rgba(255,106,26,.30), transparent 58%),
            radial-gradient(34rem 28rem at -8% 34%, rgba(255,179,71,.10), transparent 64%),
            #080404;
        padding-bottom: 28px;
    }
    .vpi-hero { padding: 76px 0 42px; --page-top-pad: 76px; }
    .vpi-hero-grid { display: grid; grid-template-columns: minmax(0, 1fr) 280px; gap: 28px; align-items: end; }
    .vpi-kicker { margin: 0 0 12px; color: var(--orange); font-size: 11px; font-weight: 900; letter-spacing: .18em; text-transform: uppercase; }
    .vpi-hero h1 { max-width: 780px; margin: 0; font-family: var(--font-display); font-size: clamp(48px, 8vw, 88px); line-height: .88; letter-spacing: .015em; text-transform: uppercase; }
    .vpi-hero h1 span { color: var(--orange); }
    .vpi-lead { max-width: 590px; margin: 18px 0 0; color: rgba(255,255,255,.68); font-size: 15px; line-height: 1.55; font-weight: 700; }
    .vpi-hero-card { min-height: 170px; border: 1px solid rgba(255,106,26,.28); border-radius: 8px; background: radial-gradient(110% 100% at 100% 0%, rgba(255,106,26,.24), transparent 60%), rgba(255,255,255,.05); padding: 22px; box-shadow: 0 20px 48px rgba(0,0,0,.24); }
    .vpi-hero-card strong { display: block; color: var(--orange); font-family: var(--font-display); font-size: 72px; line-height: .85; }
    .vpi-hero-card span { display: block; color: #fff; font-size: 13px; font-weight: 900; text-transform: uppercase; margin-top: 10px; }
    .vpi-hero-card em { display: block; color: rgba(255,255,255,.52); font-size: 12px; line-height: 1.4; margin-top: 8px; font-style: normal; }
    .vpi-content { margin-top: 24px; }
    .vpi-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .vpi-card { position: relative; overflow: hidden; display: flex; flex-direction: column; min-height: 390px; border: 1px solid rgba(255,255,255,.11); border-radius: 8px; background: linear-gradient(180deg, rgba(255,255,255,.075), rgba(255,255,255,.035)); box-shadow: 0 20px 50px rgba(0,0,0,.28); }
    .vpi-card::after { content: ""; position: absolute; inset: 0; left: auto; width: 55%; background: var(--card-portrait, none); background-size: cover; background-position: center top; opacity: .18; -webkit-mask-image: linear-gradient(to right, transparent 0%, black 55%); mask-image: linear-gradient(to right, transparent 0%, black 55%); pointer-events: none; z-index: 0; }
    .vpi-card-glow { position: absolute; width: 180px; height: 180px; right: -70px; top: -70px; border-radius: 999px; background: radial-gradient(circle, rgba(255,106,26,.34), transparent 68%); pointer-events: none; }
    .vpi-card-top { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: flex-start; padding: 18px; }
    .vpi-logo { width: 78px; height: 78px; border-radius: 18px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: linear-gradient(135deg, var(--orange), var(--amber)); color: #160604; font-family: var(--font-display); font-size: 34px; box-shadow: 0 16px 36px rgba(255,106,26,.24); }
    .vpi-logo img { width: 100%; height: 100%; object-fit: cover; }
    .vpi-status { display: inline-flex; align-items: center; gap: 6px; border: 1px solid rgba(37,196,107,.28); border-radius: 999px; background: rgba(37,196,107,.10); color: #61e897; padding: 6px 9px; font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .vpi-card-body { position: relative; z-index: 1; padding: 0 18px 18px; flex: 1; }
    .vpi-slug { margin: 0 0 8px; color: var(--orange); font-size: 10px; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; }
    .vpi-card h2 { margin: 0; font-family: var(--font-display); font-size: 38px; line-height: .9; letter-spacing: .02em; }
    .vpi-description { min-height: 62px; margin: 12px 0 0; color: rgba(255,255,255,.64); font-size: 13px; line-height: 1.5; font-weight: 700; }
    .vpi-meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-top: 18px; }
    .vpi-meta div { min-width: 0; border: 1px solid rgba(255,255,255,.08); border-radius: 8px; background: rgba(255,255,255,.04); padding: 9px; }
    .vpi-meta strong { display: block; color: #fff; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .vpi-meta span { display: block; margin-top: 3px; color: rgba(255,255,255,.44); font-size: 9px; font-weight: 900; text-transform: uppercase; }
    .vpi-contact-row { display: flex; gap: 8px; margin-top: 14px; }
    .vpi-contact-row a { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(255,106,26,.24); border-radius: 10px; background: rgba(255,106,26,.08); color: var(--orange); text-decoration: none; }
    .vpi-card-actions { position: relative; z-index: 1; display: flex; gap: 8px; padding: 14px 18px 18px; border-top: 1px solid rgba(255,255,255,.08); }
    .vpi-main-btn, .vpi-soft-btn { flex: 1; min-height: 40px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none; font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .vpi-main-btn { background: linear-gradient(180deg, var(--orange-2), var(--orange) 62%, var(--orange-deep)); color: #190702; box-shadow: 0 12px 28px rgba(255,106,26,.26); }
    .vpi-soft-btn { border: 1px solid rgba(255,106,26,.36); color: #fff; background: rgba(255,106,26,.06); }
    .vpi-empty { border: 1px dashed rgba(255,255,255,.14); border-radius: 8px; padding: 38px; text-align: center; background: rgba(255,255,255,.04); }
    .vpi-empty h2 { margin: 0; font-family: var(--font-display); font-size: 42px; }
    .vpi-empty p { margin: 8px 0 0; color: rgba(255,255,255,.62); }
    /* Rank badge */
    .vpi-rank { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:28px; padding:0 8px; border-radius:999px; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.06); color:rgba(255,255,255,.6); font-size:12px; font-weight:900; }
    .vpi-rank--top { border-color:rgba(255,106,26,.42); background:rgba(255,106,26,.12); color:var(--orange); font-size:14px; }
    .vpi-card--top { border-color:rgba(255,106,26,.28); box-shadow:0 20px 50px rgba(0,0,0,.28), 0 0 0 1px rgba(255,106,26,.10) inset; }
    /* Reputation block */
    .vpi-reputation { display:flex; align-items:center; gap:0; margin-top:16px; border:1px solid rgba(255,106,26,.18); border-radius:8px; background:rgba(255,106,26,.05); overflow:hidden; }
    .vpi-rep-left, .vpi-rep-right { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; flex:1; padding:10px 12px; }
    .vpi-rep-right { background:rgba(255,255,255,.03); }
    .vpi-rep-stars { display:flex; gap:3px; color:var(--orange); font-size:12px; margin-bottom:1px; }
    .vpi-rep-icon { color:rgba(255,255,255,.4); font-size:13px; margin-bottom:1px; }
    .vpi-rep-value { color:#fff; font-size:18px; font-weight:900; line-height:1; }
    .vpi-rep-label { color:rgba(255,255,255,.38); font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; }
    .vpi-rep-divider { width:1px; align-self:stretch; background:rgba(255,106,26,.18); flex-shrink:0; }
    @media (max-width: 1020px) {
        .vpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .vpi-hero-grid { grid-template-columns: 1fr; }
        .vpi-hero-card { max-width: 360px; }
    }
    @media (max-width: 660px) {
        .vpi-hero { padding: 48px 0 30px; }
        .vpi-grid { grid-template-columns: 1fr; }
        .vpi-card { min-height: 0; }
        .vpi-card-actions { flex-direction: column; }
        .vpi-meta { grid-template-columns: 1fr; }
        .vpi-description { min-height: 0; }
    }
</style>
@endpush
