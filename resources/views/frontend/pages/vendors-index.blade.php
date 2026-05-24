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
            @include('frontend.components.breadcrumbs', [
                'items' => [
                    ['label' => 'Inicio', 'url' => route('frontend.home')],
                    ['label' => 'Cajeros'],
                ],
            ])
            <div class="vpi-hero-grid">
                <div>
                    <p class="vpi-kicker">Cajeros verificados</p>
                    <h1>Elegí tu <span>cajero</span> y empezá con una línea activa.</h1>
                    <p class="vpi-lead">Todos los cajeros publicados tienen perfil propio, canales de contacto, líneas disponibles, bonos y sorteos asociados.</p>
                </div>
                <div class="vpi-hero-card">
                    <strong>{{ $vendors->count() }}</strong>
                    <span>Cajeros activos</span>
                    <em>Perfiles públicos listos para compartir</em>
                </div>
            </div>
        </div>
    </section>

    <section class="fe-shell vpi-content">
        @if($vendors->count())
            <div class="vpi-grid">
                @foreach($vendors as $vendor)
                    @php
                        $logo = $assetUrl($vendor->logo);
                        $contacts = collect($vendor->contacts ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
                        $primaryContact = $contacts->first();
                        $displayName = trim(($vendor->user?->name ?? '').' '.($vendor->user?->apellido ?? '')) ?: $vendor->name;
                    @endphp
                    <article class="vpi-card">
                        <div class="vpi-card-glow"></div>
                        <div class="vpi-card-top">
                            <div class="vpi-logo">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="{{ $vendor->name }}">
                                @else
                                    {{ strtoupper(mb_substr($vendor->name, 0, 2)) }}
                                @endif
                            </div>
                            <span class="vpi-status"><i class="fa-solid fa-circle-check"></i> Activo</span>
                        </div>

                        <div class="vpi-card-body">
                            <p class="vpi-slug">{{ $vendor->slug }}</p>
                            <h2>{{ $vendor->name }}</h2>
                            <p class="vpi-description">{{ $vendor->description ?: 'Cajero con atención personalizada, carga rápida y líneas disponibles para jugar online.' }}</p>

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
                            @if($primaryContact)
                                <a class="vpi-soft-btn" href="{{ $contactHref($primaryContact) }}" target="_blank" rel="noopener">Contactar</a>
                            @endif
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
    .vpi-hero { padding: 76px 0 42px; border-bottom: 1px solid rgba(255,255,255,.08); background: linear-gradient(180deg, rgba(255,255,255,.025), transparent); }
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
