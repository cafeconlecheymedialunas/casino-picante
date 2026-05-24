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

    $logoUrl = $assetUrl($vendor->logo);
    $heroImageUrl = $assetUrl($vendor->hero_image);
    $portraitImageUrl = $assetUrl($vendor->portrait_image) ?: $logoUrl;
    $cajeroDisplayName = trim(($cajero?->name ?? '').' '.($cajero?->apellido ?? ''));
    $displayName = $cajeroDisplayName ?: $vendor->name;
    $phone = $cajero?->phone;
    $rating = (float) $averageRating;
    $vendorFeatures = collect($vendor->features ?? [])
        ->filter(fn ($feature) => filled($feature['title'] ?? null))
        ->map(fn ($feature) => [
            'icon' => trim((string) ($feature['icon'] ?? 'fa-solid fa-star')) ?: 'fa-solid fa-star',
            'title' => trim((string) ($feature['title'] ?? '')),
            'description' => trim((string) ($feature['description'] ?? '')),
        ])
        ->values();

    $profileData = collect([
        ['label' => 'Nombre publico', 'value' => $vendor->name],
        ['label' => 'Usuario', 'value' => $cajero?->username],
        ['label' => 'Telefono', 'value' => $phone],
    ])->filter(fn ($item) => filled($item['value'] ?? null));

    $benefits = $vendorFeatures;

    $channelIcons = [
        'wsp' => 'fa-brands fa-whatsapp',
        'wsap' => 'fa-brands fa-whatsapp',
        'wa' => 'fa-brands fa-whatsapp',
        'whatsapp' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram',
        'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram',
        'ig' => 'fa-brands fa-instagram',
        'facebook' => 'fa-brands fa-facebook',
        'fb' => 'fa-brands fa-facebook',
        'phone' => 'fa-solid fa-phone',
        'telefono' => 'fa-solid fa-phone',
        'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope',
        'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe',
        'tiktok' => 'fa-brands fa-tiktok',
        'twitter' => 'fa-brands fa-x-twitter',
        'x' => 'fa-brands fa-x-twitter',
        'youtube' => 'fa-brands fa-youtube',
    ];

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
@endphp

<div class="vendor-detail-page">
    <section class="vd-hero"{{ $heroImageUrl ? " style=\"--vendor-hero-image: url('{$heroImageUrl}')\"" : '' }}>
        <div class="vd-shell">
            @include('frontend.components.breadcrumbs', [
                'items' => [
                    ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
                    ['label' => $vendor->name],
                ],
            ])
            <header class="vd-topbar">
                <a href="{{ route('frontend.home') }}" class="vd-brand" wire:navigate>
                    <span class="vd-brand-mark"></span>
                    <span>RED <strong>PICANTES</strong></span>
                </a>
                @if($vendor->is_active)
                    <span class="vd-badge"><i class="fa-solid fa-shield-halved"></i> Cajero verificado</span>
                @endif
            </header>

            <div class="vd-hero-grid">
                <div class="vd-hero-copy">
                    <p class="vd-kicker">{{ $vendor->slug }}</p>
                    <h1>{{ $vendor->name }} — tu <span>cajero.</span></h1>
                    @if($vendor->description)
                        <p class="vd-lead">{{ $vendor->description }}</p>
                    @endif

                    @if($benefits->isNotEmpty())
                    <div class="vd-benefits">
                        @foreach($benefits as $benefit)
                            <div>
                                <i class="{{ $benefit['icon'] }}"></i>
                                <strong>{{ $benefit['title'] }}</strong>
                                <span>{{ $benefit['description'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="vd-contact-widget">
                        <div class="vd-contact-widget-head">
                            <span>Canales directos</span>
                            <strong>{{ $contacts->count() }} disponibles</strong>
                        </div>
                        <div class="vd-contact-widget-list">
                            @forelse($contacts as $contact)
                                @php
                                    $type = strtolower(trim((string) ($contact['type'] ?? 'web')));
                                    $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                                    $contactLabel = $contact['name'] ?: ucfirst($type ?: 'Contacto');
                                @endphp
                                <a href="{{ $contactHref($contact) }}" target="_blank" rel="noopener" class="vd-contact-row">
                                    <i class="{{ $icon }}"></i>
                                    <span>
                                        <strong>{{ $contactLabel }}</strong>
                                    </span>
                                    <b class="fa-solid fa-chevron-right"></b>
                                </a>
                            @empty
                                <div class="vd-empty compact">Sin contactos publicados.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="vd-visual-stack">
                    <aside class="vd-profile-card">
                        <div class="vd-avatar">
                            @if($portraitImageUrl)
                                <img src="{{ $portraitImageUrl }}" alt="{{ $vendor->name }}">
                            @endif
                        </div>
                        <div>
                            <h2>{{ $displayName }}</h2>
                            @if(filled($cajero?->username))
                                <p>{{ $cajero->username }}</p>
                            @endif
                        </div>
                        @if($profileData->isNotEmpty())
                            <dl class="vd-profile-data">
                                @foreach($profileData as $item)
                                    <div><dt>{{ $item['label'] }}</dt><dd>{{ $item['value'] }}</dd></div>
                                @endforeach
                            </dl>
                        @endif
                        <div class="vd-rating">
                            <span>Calificacion</span>
                            @include('frontend.components.rating', [
                                'rating' => $rating,
                                'count' => $ratingsCount,
                                'showValue' => true,
                                'size' => 'sm',
                            ])
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section class="vd-shell vd-content-stack">

            {{-- Stats de confiabilidad --}}
            <div class="vd-trust-bar">
                <div class="vd-trust-stat">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <strong>Cajero verificado</strong>
                        <span>Miembro desde {{ $memberSince->format('M Y') }}</span>
                    </div>
                </div>
                <div class="vd-trust-stat">
                    <i class="fa-solid fa-users"></i>
                    <div>
                        <strong>{{ number_format($totalClients) }}+</strong>
                        <span>Clientes atendidos</span>
                    </div>
                </div>
                <div class="vd-trust-stat">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <div>
                        <strong>{{ number_format($totalOperations) }}+</strong>
                        <span>Operaciones realizadas</span>
                    </div>
                </div>
                <div class="vd-trust-stat">
                    <i class="fa-solid fa-gift"></i>
                    <div>
                        <strong>{{ $totalBonuses }} bonos activos</strong>
                        <span>Promociones vigentes</span>
                    </div>
                </div>
                <div class="vd-trust-stat">
                    <i class="fa-solid fa-layer-group"></i>
                    <div>
                        <strong>{{ $totalLines }} lineas · {{ $totalPlatforms }} plataformas</strong>
                        <span>Cobertura del cajero</span>
                    </div>
                </div>
            </div>

            {{-- Líneas --}}
            <section id="lineas" class="vd-panel">
                <div class="vd-panel-head">
                    <div>
                        <p>Lineas disponibles</p>
                        <h2>{{ $totalLines }} lineas para jugar</h2>
                    </div>
                    <a href="{{ route('frontend.cajero.lineas', $vendor) }}" class="vd-panel-btn" wire:navigate>Ver todos</a>
                </div>

                @if($lines->count())
                    <div class="vd-lines-grid">
                        @foreach($lines as $line)
                            @include('frontend.components.line-card', ['line' => $line, 'showPlatformButton' => false])
                        @endforeach
                    </div>
                @else
                    <div class="vd-empty">Este cajero todavia no tiene lineas activas publicadas.</div>
                @endif
            </section>

            {{-- Bonos y Sorteos --}}
            <div class="vd-full-row">
                <section class="vd-panel">
                    <div class="vd-panel-head">
                        <div>
                            <p>Bonos exclusivos</p>
                            <h2>Beneficios activos</h2>
                        </div>
                        <a href="{{ route('frontend.cajero.bonos', $vendor) }}" class="vd-panel-btn" wire:navigate>Ver todos</a>
                    </div>
                    <div class="vd-bonus-grid">
                        @forelse($bonuses as $bonus)
                            @include('frontend.components.bonus-public-card', ['bonus' => $bonus])
                        @empty
                            <div class="vd-empty compact">Sin bonos activos por ahora.</div>
                        @endforelse
                    </div>
                </section>

                <section class="vd-panel">
                    <div class="vd-panel-head">
                        <h2>Premios vigentes</h2>
                        <a href="{{ route('frontend.cajero.sorteos', $vendor) }}" class="vd-panel-btn" wire:navigate>Ver todos</a>
                    </div>
                    <div class="vd-mini-list">
                        @forelse($raffles as $raffle)
                            <article class="vd-raffle">
                                <i class="fa-solid fa-trophy"></i>
                                <div>
                                    <strong>{{ $raffle->title }}</strong>
                                    <span>Finaliza {{ $raffle->end_date->format('d/m H:i') }}</span>
                                    <a href="{{ route('frontend.cajero.sorteos.detalle', [$vendor, $raffle]) }}" wire:navigate>Participar ahora</a>
                                </div>
                            </article>
                        @empty
                            <div class="vd-empty compact">Sin sorteos activos por ahora.</div>
                        @endforelse
                    </div>
                </section>
            </div>

        <section class="vd-panel">
            <div class="vd-panel-head">
                <div>
                    <p>Agentes</p>
                    <h2>Equipo</h2>
                </div>
                <a href="{{ route('frontend.cajero.agentes', $vendor) }}" class="vd-panel-btn" wire:navigate>Ver todos</a>
            </div>
            @if($agents->count())
                <div class="vd-agents-grid">
                    @foreach($agents as $agent)
                        @include('frontend.components.agent-card', ['agent' => $agent])
                    @endforeach
                </div>
            @else
                <div class="vd-empty compact">Sin agentes del cajero.</div>
            @endif
        </section>
    </section>

</div>

@push('styles')
<style>
    .vendor-detail-page {
        --vd-panel: rgba(255,255,255,.052);
        --vd-panel-2: rgba(255,255,255,.075);
        --vd-stroke: rgba(255,255,255,.12);
        --vd-soft: rgba(255,255,255,.68);
        background:
            radial-gradient(46rem 36rem at 67% 2%, rgba(255,106,26,.22), transparent 60%),
            radial-gradient(32rem 28rem at 5% 30%, rgba(255,179,71,.10), transparent 62%),
            #060303;
        padding-bottom: 28px;
        overflow: hidden;
    }
    .vd-shell { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }
    .vd-hero { position: relative; min-height: 650px; padding: 22px 0 74px; background: linear-gradient(180deg, rgba(0,0,0,.2), rgba(0,0,0,.72)); }
    .vd-hero::before { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,.94) 0%, rgba(0,0,0,.62) 43%, rgba(0,0,0,.2) 100%), var(--vendor-hero-image); background-size: cover; background-position: center; opacity: .72; }
    .vd-hero::after { display: none; }
    .vd-hero > .vd-shell { position: relative; z-index: 1; }
    .vd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 78px; }
    .vd-brand { display: inline-flex; align-items: center; gap: 10px; color: #fff; text-decoration: none; font-weight: 900; letter-spacing: .05em; }
    .vd-brand strong { color: var(--orange); }
    .vd-brand-mark { width: 22px; height: 22px; border-radius: 7px; background: var(--orange); box-shadow: 0 0 24px rgba(255,106,26,.55); }
    .vd-badge { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,106,26,.42); border-radius: 999px; background: rgba(255,106,26,.10); color: var(--orange); padding: 8px 12px; font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .vd-hero-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(340px, 420px); gap: clamp(26px, 5vw, 64px); align-items: start; }
    .vd-hero-copy { position: relative; z-index: 3; padding-top: 46px; }
    .vd-kicker { margin: 0 0 16px; color: var(--orange); font-size: 11px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
    .vd-hero h1 { max-width: 560px; margin: 0; font-family: var(--font-display); font-size: clamp(58px, 8vw, 96px); line-height: .86; letter-spacing: .015em; text-transform: uppercase; }
    .vd-hero h1 span { color: var(--orange); }
    .vd-lead { max-width: 520px; margin: 20px 0 0; color: rgba(255,255,255,.76); font-size: 15px; line-height: 1.55; font-weight: 700; }
    .vd-benefits { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; max-width: 620px; margin-top: 28px; }
    .vd-benefits div { display: grid; grid-template-columns: 40px minmax(0,1fr); column-gap: 10px; row-gap: 2px; align-items: center; min-height: 70px; border: 1px solid var(--vd-stroke); border-radius: 8px; background: rgba(255,255,255,.045); padding: 10px; }
    .vd-benefits i { grid-row: span 2; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(255,106,26,.44); border-radius: 8px; color: var(--orange); font-size: 16px; }
    .vd-benefits strong { color: #fff; font-size: 11px; text-transform: uppercase; }
    .vd-benefits span { color: rgba(255,255,255,.58); font-size: 10px; line-height: 1.3; }
    .vd-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 26px; }
    .vd-contact-widget { width: min(100%, 430px); margin-top: 26px; border: 1px solid rgba(255,106,26,.26); border-radius: 10px; background: linear-gradient(180deg, rgba(255,255,255,.075), rgba(255,255,255,.035)); box-shadow: 0 18px 48px rgba(0,0,0,.28); backdrop-filter: blur(10px); padding: 14px; }
    .vd-contact-widget-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px; }
    .vd-contact-widget-head span { color: rgba(255,255,255,.62); font-size: 10px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
    .vd-contact-widget-head strong { color: var(--orange); font-size: 10px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
    .vd-contact-widget-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 8px; }
    .vd-contact-row { display: grid; grid-template-columns: 38px minmax(0, 1fr) 12px; gap: 10px; align-items: center; border: 1px solid rgba(255,255,255,.09); border-radius: 9px; background: rgba(255,255,255,.045); padding: 9px; color: #fff; text-decoration: none; transition: border-color .18s ease, background .18s ease, transform .18s ease; }
    .vd-contact-row:hover { transform: translateY(-1px); border-color: rgba(255,106,26,.36); background: rgba(255,106,26,.07); }
    .vd-contact-row > i { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: rgba(255,106,26,.1); color: var(--orange); font-size: 16px; }
    .vd-contact-row strong, .vd-contact-row em { display: block; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .vd-contact-row strong { color: #fff; font-size: 12px; }
    .vd-contact-row em { color: rgba(255,255,255,.56); font-size: 10px; font-style: normal; margin-top: 2px; }
    .vd-contact-row b { color: var(--orange); font-size: 10px; }
    .vd-btn { min-height: 48px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; border-radius: 8px; padding: 0 22px; text-decoration: none; font-size: 12px; font-weight: 900; text-transform: uppercase; border: 1px solid transparent; }
    .vd-btn-primary { background: linear-gradient(180deg, #ff8a3d, var(--orange) 62%, #e6580f); color: #190702; box-shadow: 0 14px 34px rgba(255,106,26,.34); }
    .vd-btn-ghost { border-color: rgba(255,106,26,.44); color: #fff; background: rgba(255,106,26,.05); }
    .vd-visual-stack { position: relative; z-index: 2; display: grid; justify-items: center; gap: 0; padding-top: 28px; }
    .vd-visual-stack::before { content: ""; position: absolute; width: min(420px, 82vw); aspect-ratio: 1; top: 72px; left: 50%; transform: translateX(-50%); border: 5px solid rgba(255,106,26,.72); border-radius: 999px; box-shadow: 0 0 48px rgba(255,106,26,.36), inset 0 0 38px rgba(255,106,26,.18); pointer-events: none; }
    .vd-profile-card { position: relative; z-index: 3; width: min(100%, 410px); margin-top: -34px; border: 1px solid var(--vd-stroke); border-radius: 8px; background: linear-gradient(180deg, rgba(22,18,17,.78), rgba(10,7,7,.58)); backdrop-filter: blur(16px); padding: 20px; box-shadow: 0 22px 54px rgba(0,0,0,.32); }
    .vd-portrait { position: relative; z-index: 2; width: min(370px, 100%); height: 420px; display: flex; align-items: flex-end; justify-content: center; overflow: hidden; pointer-events: none; filter: drop-shadow(0 26px 54px rgba(0,0,0,.62)); opacity: .98; }
    .vd-portrait img { width: 100%; height: 100%; display: block; object-fit: cover; object-position: center top; border-radius: 2px; -webkit-mask-image: linear-gradient(180deg, #000 78%, transparent 100%); mask-image: linear-gradient(180deg, #000 78%, transparent 100%); }
    .vd-avatar { width: 270px;height: 270px;margin: 0 auto 20px;object-fit: cover;border-radius: 18px;display: flex;align-items: center;justify-content: center;overflow: hidden;background: linear-gradient(135deg, var(--orange), var(--amber));color: #170704;font-family: var(--font-display);font-size: 34px; }
    .vd-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .vd-profile-card h2 { margin: 0; font-size: 26px; line-height: 1; }
    .vd-profile-card p { margin: 5px 0 18px; color: var(--orange); font-size: 12px; font-weight: 900; }
    .vd-profile-data { margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .vd-profile-data div { min-width: 0; border: 1px solid rgba(255,255,255,.08); border-radius: 8px; background: rgba(255,255,255,.045); padding: 9px 10px; }
    .vd-profile-data dt { color: rgba(255,255,255,.48); font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: .07em; }
    .vd-profile-data dd { margin: 3px 0 0; color: #fff; font-size: 12px; font-weight: 800; overflow-wrap: anywhere; }
    .vd-profile-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; color: rgba(255,255,255,.76); font-size: 12px; font-weight: 800; }
    .vd-profile-list li { display: flex; align-items: center; gap: 10px; }
    .vd-profile-list i { color: var(--orange); width: 16px; }
    .vd-rating { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 22px; padding-top: 16px; border-top: 1px solid var(--vd-stroke); font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,.56); }
    .vd-rating strong { color: var(--orange); letter-spacing: .08em; }
    .vd-rating em { color: rgba(255,255,255,.82); font-style: normal; text-transform: none; }
    .vd-hero-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: -70px; position: relative; z-index: 2; }
    .vd-content-stack { display: grid; gap: 16px; margin-top: -70px; position: relative; z-index: 2; background: linear-gradient(180deg, #050202 0%, #050202 100%); border-radius: 14px 14px 0 0; padding: 20px 0 0; }
    /* Agents */
    .vd-agents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; }
    /* Trust bar */
    .vd-trust-bar { display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 12px; }
    .vd-trust-stat { display: flex; align-items: center; gap: 14px; border: 1px solid rgba(255,255,255,.11); border-radius: 12px; background: linear-gradient(135deg, rgba(255,106,26,.08), rgba(255,255,255,.03)); padding: 18px; }
    .vd-trust-stat i { flex-shrink: 0; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: rgba(255,106,26,.14); color: var(--orange); font-size: 18px; }
    .vd-trust-stat strong { display: block; color: #fff; font-size: 15px; font-weight: 900; line-height: 1.15; }
    .vd-trust-stat span { display: block; color: rgba(255,255,255,.46); font-size: 11px; font-weight: 700; margin-top: 4px; }
    @media (max-width: 1100px) { .vd-trust-bar { grid-template-columns: repeat(3, minmax(0,1fr)); } }
    @media (max-width: 640px) { .vd-trust-bar { grid-template-columns: 1fr 1fr; } }
    .vd-panel { border: 1px solid var(--vd-stroke); border-radius: 8px; background: linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.035)); box-shadow: 0 18px 48px rgba(0,0,0,.26); padding: 18px; backdrop-filter: blur(8px); }
    .vd-panel-head { display: flex; justify-content: space-between; gap: 14px; align-items: start; margin-bottom: 16px; }
    .vd-panel-head p { margin: 0 0 5px; color: rgba(255,255,255,.58); font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .vd-panel-head h2 { margin: 0; font-family: var(--font-display); font-size: 28px; line-height: .95; letter-spacing: .025em; }
    .vd-panel-head a { color: var(--orange); text-decoration: none; font-size: 11px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
    .vd-panel-btn { display:inline-flex; align-items:center; justify-content:center; min-height:30px; padding:0 14px; border:1px solid rgba(255,106,26,.38); border-radius:6px; background:rgba(255,106,26,.07); color:var(--orange) !important; font-size:11px; font-weight:900; text-transform:uppercase; text-decoration:none; white-space:nowrap; transition:background .18s,border-color .18s; flex-shrink:0; }
    .vd-panel-btn:hover { background:rgba(255,106,26,.14); border-color:rgba(255,106,26,.6); }
    /* Lines — altura uniforme */
    .vd-lines-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; align-items: stretch; }
    .vd-lines-grid > * { display: flex; flex-direction: column; height: 100%; }
    .vd-line-card { min-width: 0; border: 1px solid rgba(255,255,255,.1); border-radius: 8px; background: rgba(255,255,255,.04); overflow: hidden; }
    .vd-line-media { height: 108px; position: relative; background: radial-gradient(80% 90% at 50% 0%, rgba(255,106,26,.32), transparent 64%), #100706; }
    .vd-line-media > img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .vd-line-avatar { position: absolute; left: 10px; bottom: -18px; width: 46px; height: 46px; border-radius: 12px; border: 2px solid #0b0504; background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; overflow: hidden; color: #190702; font-family: var(--font-display); font-size: 20px; }
    .vd-line-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .vd-line-body { padding: 26px 10px 10px; display: grid; gap: 10px; }
    .vd-line-body h3 { margin: 0; font-size: 15px; line-height: 1.1; }
    .vd-line-body p { margin: 4px 0 0; color: rgba(255,255,255,.58); font-size: 11px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .vd-line-meta { display: flex; gap: 6px; flex-wrap: wrap; color: var(--orange); font-size: 9px; font-weight: 900; text-transform: uppercase; }
    .vd-line-meta span { border: 1px solid rgba(255,106,26,.24); border-radius: 999px; padding: 4px 6px; background: rgba(255,106,26,.08); }
    .vd-line-actions { display: flex; gap: 6px; }
    .vd-line-actions a { flex: 1; min-height: 30px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--vd-stroke); border-radius: 6px; color: #fff; text-decoration: none; font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .vd-line-actions a:first-child { color: var(--orange); border-color: rgba(255,106,26,.36); }
    .vd-split { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: start; }
    .vd-full-row { display: grid; gap: 14px; }
    .vd-split > .vd-panel { align-self: start; }
    .vd-mini-list { display: grid; gap: 10px; }
    .vd-bonus-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
    /* Raffle cards */
    .vd-raffle { display: grid; grid-template-columns: 52px minmax(0, 1fr); gap: 14px; align-items: start; border: 1px solid rgba(255,255,255,.1); border-radius: 10px; background: linear-gradient(135deg, rgba(255,106,26,.06), rgba(255,255,255,.03)); padding: 16px; transition: border-color .18s, background .18s; }
    .vd-raffle:hover { border-color: rgba(255,106,26,.32); background: rgba(255,106,26,.08); }
    .vd-raffle i { width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: rgba(255,106,26,.14); color: var(--orange); font-size: 22px; flex-shrink: 0; }
    .vd-raffle strong { display: block; color: #fff; font-size: 14px; font-weight: 900; line-height: 1.2; }
    .vd-raffle span { display: block; color: rgba(255,255,255,.48); font-size: 11px; font-weight: 700; margin-top: 4px; }
    .vd-raffle a { display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; color: var(--orange); font-size: 11px; font-weight: 900; text-transform: uppercase; text-decoration: none; border: 1px solid rgba(255,106,26,.34); border-radius: 6px; padding: 5px 12px; background: rgba(255,106,26,.07); transition: background .15s; }
    .vd-raffle a:hover { background: rgba(255,106,26,.15); }
    .vd-contact-list { display: flex; flex-wrap: wrap; gap: 10px; }
    .vd-contact-list.icon-only { display: flex; flex-wrap: wrap; gap: 10px; }
    .vd-contact.icon-only { width: 46px; height: 46px; display: inline-flex; align-items: center; justify-content: center; grid-template-columns: none; gap: 0; padding: 0; border-radius: 12px; }
    .vd-contact.icon-only > i { width: 100%; height: 100%; border-radius: 12px; background: rgba(255,106,26,.10); font-size: 18px; }
    .vd-empty { border: 1px dashed rgba(255,255,255,.12); border-radius: 8px; padding: 18px; color: rgba(255,255,255,.6); font-size: 13px; text-align: center; }
    .vd-empty.compact { padding: 12px; font-size: 12px; }
    @media (max-width: 1060px) {
        .vd-hero-grid, .vd-hero-grid { grid-template-columns: 1fr; }
        .vd-hero-copy { padding-top: 0; }
        .vd-visual-stack { max-width: 460px; margin: 0 auto; }
        .vd-hero-grid { margin-top: -40px; }
        .vd-lines-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .vd-side-stack { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 760px) {
        .vd-shell { width: calc(100% - 24px); }
        .vd-hero { min-height: 0; padding-top: 16px; }
        .vd-topbar { margin-bottom: 46px; align-items: flex-start; }
        .vd-badge { max-width: 170px; justify-content: center; text-align: center; }
        .vd-visual-stack { padding-top: 8px; }
        .vd-visual-stack::before { width: min(310px, 82vw); top: 46px; }
        .vd-portrait { height: 340px; width: min(300px, 92%); }
        .vd-profile-card { margin-top: -24px; }
        .vd-profile-data { grid-template-columns: 1fr; }
        .vd-benefits, .vd-split, .vd-side-stack { grid-template-columns: 1fr; }
        .vd-lines-grid { grid-template-columns: 1fr; }
        .vd-actions .vd-btn { width: 100%; }
    }

    /* Visual polish pass: same structure, sharper hierarchy. */
    .vendor-detail-page {
        --vd-stroke: rgba(255,255,255,.14);
        --vd-stroke-warm: rgba(255,106,26,.30);
        background:
            radial-gradient(34rem 28rem at 84% -8%, rgba(255,106,26,.24), transparent 62%),
            radial-gradient(28rem 22rem at 6% 42%, rgba(255,179,71,.08), transparent 68%),
            linear-gradient(180deg, #050202 0%, #120807 46%, #050202 100%);
    }
    .vd-shell { width: min(1200px, calc(100% - 40px)); }
    .vd-hero { min-height: 680px; padding-bottom: 88px; }
    .vd-hero::before {
        background:
            linear-gradient(90deg, rgba(0,0,0,.96) 0%, rgba(0,0,0,.72) 42%, rgba(0,0,0,.28) 100%),
            linear-gradient(180deg, rgba(0,0,0,.12), #050202 96%),
            var(--vendor-hero-image);
        background-size: cover;
        background-position: center;
        opacity: .82;
        transform: scale(1.01);
    }
    .vd-hero::after { display:block; content:""; position:absolute; inset:auto 0 0; height:180px; background:linear-gradient(180deg, transparent, #050202); pointer-events:none; }
    .vd-hero .fe-breadcrumbs { margin-bottom:22px; }
    .vd-topbar { margin-bottom:64px; }
    .vd-hero-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(340px, 420px); gap: clamp(26px, 5vw, 64px); align-items: start; }
    .vd-hero-copy { padding-top:34px; }
    .vd-hero h1 { max-width:620px; font-size:clamp(62px, 8vw, 104px); line-height:.84; text-shadow:0 18px 52px rgba(0,0,0,.58); }
    .vd-lead { max-width:560px; margin-top:22px; color:rgba(255,255,255,.78); line-height:1.65; font-weight:750; }
    .vd-benefits div { min-height:72px; border-color:rgba(255,255,255,.11); background:linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.028)); box-shadow:0 14px 34px rgba(0,0,0,.18); }
    .vd-benefits i { background:rgba(255,106,26,.08); }
    .vd-btn { transition:transform .18s ease, border-color .18s ease, background .18s ease; }
    .vd-btn:hover { transform:translateY(-1px); }
    .vd-visual-stack::before { width:min(430px, 82vw); top:64px; border-width:4px; border-color:rgba(255,106,26,.66); box-shadow:0 0 54px rgba(255,106,26,.34), inset 0 0 38px rgba(255,106,26,.14); }
    .vd-profile-card { width:min(100%, 420px); margin-top:-26px; border-color:rgba(255,255,255,.16); border-radius:10px; background:linear-gradient(180deg, rgba(30,24,22,.82), rgba(10,7,7,.68)); backdrop-filter:blur(18px); box-shadow:0 26px 64px rgba(0,0,0,.38), 0 0 0 1px rgba(255,106,26,.06) inset; }
    .vd-avatar { width:230px; height:230px; border-radius:14px; box-shadow:0 20px 46px rgba(0,0,0,.34), 0 0 0 1px rgba(255,255,255,.10) inset; }
    .vd-profile-data div { border-color:rgba(255,255,255,.09); background:rgba(255,255,255,.052); }
    .vd-hero-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px; margin-top:-78px; }
    .vd-main-stack, .vd-side-stack { gap:16px; }
    .vd-panel { border-radius:10px; background:linear-gradient(180deg, rgba(255,255,255,.075), rgba(255,255,255,.035)); box-shadow:0 20px 54px rgba(0,0,0,.28); backdrop-filter:blur(10px); }
    .vd-panel-head h2 { font-size:30px; }
    .vd-lines-grid { grid-template-columns:repeat(4, minmax(0, 1fr)); gap:12px; }
    .vd-line-card { border-color:rgba(255,255,255,.11); border-radius:10px; background:rgba(255,255,255,.045); transition:transform .18s ease, border-color .18s ease, background .18s ease; }
    .vd-line-card:hover { transform:translateY(-2px); border-color:rgba(255,106,26,.38); background:rgba(255,255,255,.06); }
    .vd-line-media { height:116px; }
    .vd-line-body { padding:28px 12px 12px; gap:11px; }
    .vd-line-actions a { min-height:32px; }
    .vd-bonus, .vd-raffle, .vd-contact { border-color:rgba(255,255,255,.09); border-radius:10px; background:rgba(255,255,255,.045); }
    .vd-contact { transition:border-color .18s ease, background .18s ease; }
    .vd-contact:hover { border-color:rgba(255,106,26,.36); background:rgba(255,106,26,.07); }
    @media (max-width: 760px) {
        .vd-shell { width:calc(100% - 24px); }
        .vd-hero { padding-bottom:48px; }
        .vd-benefits, .vd-split { grid-template-columns:1fr; }
        .vd-content-stack { margin-top:-24px; }
    }
</style>
@endpush
