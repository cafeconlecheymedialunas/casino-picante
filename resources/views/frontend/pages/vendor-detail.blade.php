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
    $heroImageUrl = $assetUrl($vendor->hero_image) ?: 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=1800&auto=format&fit=crop';
    $portraitImageUrl = $assetUrl($vendor->portrait_image) ?: $logoUrl;
    $primaryContact = $contacts->first();
    $primaryContactUrl = $primaryContact['value'] ?? null;
    $displayName = trim(($cajero?->name ?? '').' '.($cajero?->apellido ?? '')) ?: $vendor->name;
    $phone = $cajero?->phone ?: ($primaryContact['value'] ?? null);
    $rating = $averageRating > 0 ? $averageRating : 4.9;

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
    <section class="vd-hero" style="--vendor-hero-image: url('{{ $heroImageUrl }}');">
        <div class="vd-shell">
            <header class="vd-topbar">
                <a href="{{ route('frontend.home') }}" class="vd-brand" wire:navigate>
                    <span class="vd-brand-mark"></span>
                    <span>RED <strong>PICANTES</strong></span>
                </a>
                <span class="vd-badge"><i class="fa-solid fa-shield-halved"></i> Cajero verificado</span>
            </header>

            <div class="vd-hero-grid">
                <div class="vd-hero-copy">
                    <p class="vd-kicker">{{ $vendor->slug }}</p>
                    <h1>Tu mejor jugada, tu mejor <span>cajero.</span></h1>
                    <p class="vd-lead">{{ $vendor->description ?: 'Atencion personalizada, pagos rapidos y lineas listas para que empieces a jugar sin vueltas.' }}</p>

                    <div class="vd-benefits">
                        <div><i class="fa-solid fa-bolt"></i><strong>Pagos rapidos</strong><span>Cargas y retiros al instante.</span></div>
                        <div><i class="fa-solid fa-lock"></i><strong>100% confiable</strong><span>Operacion clara y segura.</span></div>
                        <div><i class="fa-solid fa-headset"></i><strong>Atencion 24/7</strong><span>Soporte cuando lo necesites.</span></div>
                    </div>

                    <div class="vd-actions">
                        @if($primaryContactUrl)
                            <a class="vd-btn vd-btn-primary" href="{{ $contactHref($primaryContact) }}" target="_blank" rel="noopener">
                                <i class="fa-brands fa-whatsapp"></i>
                                Escribir ahora
                            </a>
                        @endif
                        <a class="vd-btn vd-btn-ghost" href="#lineas">Ver lineas disponibles</a>
                    </div>
                </div>

                <div class="vd-visual-stack">
                    @if($portraitImageUrl)
                        <div class="vd-portrait" aria-hidden="true">
                            <img src="{{ $portraitImageUrl }}" alt="">
                        </div>
                    @endif

                   
                </div>
            </div>

            <aside class="vd-profile-card">
                <div class="vd-profile-identity">
                    <div class="vd-avatar">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $vendor->name }}">
                        @else
                            {{ strtoupper(mb_substr($vendor->name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <h2>{{ $displayName }}</h2>
                        <p>{{ $vendor->name }}</p>
                        <div class="vd-rating">
                            <span>Calificacion</span>
                            <strong>★★★★★</strong>
                            <em>{{ number_format($rating, 1) }} ({{ $ratingsCount ?: 125 }})</em>
                        </div>
                    </div>
                </div>

                <div class="vd-profile-details">
                    <div class="vd-char-container">
                        <p class="vd-char-label">Características</p>
                        <ul class="vd-char-list">
                            <li>
                                <i class="fa-solid fa-user-check"></i>
                                <div>
                                    <strong>Atencion personalizada</strong>
                                    <span>Soporte directo y trato humano.</span>
                                </div>
                            </li>
                            <li>
                                <i class="fa-solid fa-bolt"></i>
                                <div>
                                    <strong>Pagos rapidos</strong>
                                    <span>Cargas y retiros al instante.</span>
                                </div>
                            </li>
                            <li>
                                <i class="fa-solid fa-shield-halved"></i>
                                <div>
                                    <strong>100% confiable</strong>
                                    <span>Operacion clara y segura.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="vd-profile-data">
                        <dl>
                            <div><dt>Slug</dt><dd>{{ $vendor->slug }}</dd></div>
                            <div><dt>Usuario</dt><dd>{{ $cajero?->username ?: 'No asignado' }}</dd></div>
                            <div><dt>Email</dt><dd>{{ $cajero?->email ?: 'No publicado' }}</dd></div>
                            <div><dt>Telefono</dt><dd>{{ $phone ?: 'No publicado' }}</dd></div>
                            <div><dt>Estado</dt><dd>{{ $vendor->is_active ? 'Activo' : 'Inactivo' }}</dd></div>
                        </dl>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="vd-shell vd-content-grid">
        <div class="vd-column">
            <section id="lineas" class="vd-panel">
                <div class="vd-panel-head">
                    <div>
                        <p>Lineas disponibles</p>
                        <h2>{{ $lines->count() }} lineas para jugar</h2>
                    </div>
                    <a href="{{ route('frontend.lines') }}" wire:navigate>Ver todas</a>
                </div>

                @if($lines->count())
                    <div class="vd-lines-grid">
                        @foreach($lines as $line)
                            @php
                                $cover = $assetUrl($line->portada_url);
                                $avatar = $assetUrl($line->perfil_url);
                                $lineContact = collect($line->contact_links ?? [])->first(fn ($contact) => filled($contact['value'] ?? null));
                            @endphp
                            <article class="vd-line-card">
                                <div class="vd-line-media">
                                    @if($cover)
                                        <img src="{{ $cover }}" alt="{{ $line->name }}">
                                    @endif
                                    <span class="vd-line-avatar">
                                        @if($avatar)
                                            <img src="{{ $avatar }}" alt="">
                                        @else
                                            {{ strtoupper(mb_substr($line->name, 0, 2)) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="vd-line-body">
                                    <div>
                                        <h3>{{ $line->name }}</h3>
                                        <p>{{ $line->description ?: 'Alta rapida, cargas y soporte directo.' }}</p>
                                    </div>
                                    <div class="vd-line-meta">
                                        <span>{{ $line->activePlatforms->count() }} plataformas</span>
                                        <span>{{ number_format((float) ($line->average_rating ?: 4.8), 1) }} ★</span>
                                    </div>
                                    <div class="vd-line-actions">
                                        @if($lineContact)
                                            <a href="{{ $contactHref($lineContact) }}" target="_blank" rel="noopener">Jugar ahora</a>
                                        @endif
                                        <a href="{{ route('frontend.lines.show', $line) }}" wire:navigate>Detalle</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="vd-empty">Este cajero todavia no tiene lineas activas publicadas.</div>
                @endif
            </section>

            <section class="vd-panel">
                <div class="vd-panel-head">
                    <div>
                        <p>Conecta conmigo</p>
                        <h2>Canales</h2>
                    </div>
                </div>
                <div class="vd-contact-list">
                    @forelse($contacts as $contact)
                        @php
                            $type = strtolower(trim((string) ($contact['type'] ?? 'web')));
                            $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                        @endphp
                        <a href="{{ $contactHref($contact) }}" target="_blank" rel="noopener" class="vd-contact">
                            <i class="{{ $icon }}"></i>
                            <span>
                                <strong>{{ $contact['name'] ?: ucfirst($type ?: 'Contacto') }}</strong>
                                <em>{{ $contact['value'] }}</em>
                            </span>
                            <b class="fa-solid fa-chevron-right"></b>
                        </a>
                    @empty
                        <div class="vd-empty compact">Sin contactos publicados.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="vd-column">
            <section class="vd-panel">
                <div class="vd-panel-head">
                    <div>
                        <p>Bonos exclusivos</p>
                        <h2>Beneficios activos</h2>
                    </div>
                </div>
                <div class="vd-mini-list">
                    @forelse($bonuses as $bonus)
                        <article class="vd-bonus">
                            <i class="fa-solid fa-star"></i>
                            <div>
                                <strong>{{ $bonus->title }}</strong>
                                <span>{{ $bonus->bonus_percent ? number_format((float) $bonus->bonus_percent, 0).'% extra' : ($bonus->bonus_amount ? '$'.number_format((float) $bonus->bonus_amount, 0, ',', '.') : 'Bono disponible') }}</span>
                                <em>{{ $bonus->description ?: 'Aprovechalo antes de que termine.' }}</em>
                            </div>
                        </article>
                    @empty
                        <div class="vd-empty compact">Sin bonos activos por ahora.</div>
                    @endforelse
                </div>
            </section>

            <section class="vd-panel">
                <div class="vd-panel-head">
                    <div>
                        <p>Sorteos activos</p>
                        <h2>Premios vigentes</h2>
                    </div>
                </div>
                <div class="vd-mini-list">
                    @forelse($raffles as $raffle)
                        <article class="vd-raffle">
                            <i class="fa-solid fa-trophy"></i>
                            <div>
                                <strong>{{ $raffle->title }}</strong>
                                <span>Finaliza {{ $raffle->end_date->format('d/m H:i') }}</span>
                                <a href="{{ route('frontend.raffles.show', $raffle->id) }}" wire:navigate>Participar ahora</a>
                            </div>
                        </article>
                    @empty
                        <div class="vd-empty compact">Sin sorteos activos por ahora.</div>
                    @endforelse
                </div>
            </section>
        </div>
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
    .vd-btn { min-height: 48px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; border-radius: 8px; padding: 0 22px; text-decoration: none; font-size: 12px; font-weight: 900; text-transform: uppercase; border: 1px solid transparent; }
    .vd-btn-primary { background: linear-gradient(180deg, #ff8a3d, var(--orange) 62%, #e6580f); color: #190702; box-shadow: 0 14px 34px rgba(255,106,26,.34); }
    .vd-btn-ghost { border-color: rgba(255,106,26,.44); color: #fff; background: rgba(255,106,26,.05); }
    .vd-visual-stack { position: relative; z-index: 2; display: grid; justify-items: center; gap: 0; padding-top: 28px; }
    .vd-visual-stack::before { content: ""; position: absolute; width: min(420px, 82vw); aspect-ratio: 1; top: 72px; left: 50%; transform: translateX(-50%); border: 5px solid rgba(255,106,26,.72); border-radius: 999px; box-shadow: 0 0 48px rgba(255,106,26,.36), inset 0 0 38px rgba(255,106,26,.18); pointer-events: none; }
    .vd-profile-card { position: relative; z-index: 3; width: 100%; margin-top: -34px; border: 1px solid var(--vd-stroke); border-radius: 8px; background: linear-gradient(180deg, rgba(22,18,17,.78), rgba(10,7,7,.58)); backdrop-filter: blur(16px); padding: 24px; box-shadow: 0 22px 54px rgba(0,0,0,.32); display: grid; grid-template-columns: 340px 1fr; gap: 42px; align-items: start; }
    .vd-profile-identity { display: flex; gap: 20px; align-items: flex-start; }
    .vd-portrait { position: relative; z-index: 2; width: min(370px, 100%); height: 420px; display: flex; align-items: flex-end; justify-content: center; overflow: hidden; pointer-events: none; filter: drop-shadow(0 26px 54px rgba(0,0,0,.62)); opacity: .98;border-radius: 100%;margin-bottom: 76px;}
    .vd-portrait img { width: 100%; height: 100%; display: block; object-fit: cover; object-position: center top; border-radius: 2px; -webkit-mask-image: linear-gradient(180deg, #000 78%, transparent 100%); mask-image: linear-gradient(180deg, #000 78%, transparent 100%); }
    .vd-avatar { flex-shrink: 0; width: 84px; height: 84px; border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: linear-gradient(135deg, var(--orange), var(--amber)); color: #170704; font-family: var(--font-display); font-size: 38px; }
    .vd-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .vd-profile-card h2 { margin: 0; font-size: 28px; line-height: 1; }
    .vd-profile-card p { margin: 6px 0 0; color: var(--orange); font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; }
    .vd-profile-details { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; border-left: 1px solid var(--vd-stroke); padding-left: 42px; }
    .vd-char-container { display: grid; gap: 12px; }
    .vd-char-label { margin: 0; color: rgba(255,255,255,.48); font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .vd-char-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 14px; }
    .vd-char-list li { display: grid; grid-template-columns: 24px 1fr; gap: 12px; align-items: start; }
    .vd-char-list i { color: var(--orange); font-size: 16px; margin-top: 2px; text-align: center; }
    .vd-char-list strong { display: block; color: #fff; font-size: 13px; line-height: 1.2; }
    .vd-char-list span { display: block; margin-top: 3px; color: rgba(255,255,255,.58); font-size: 11px; line-height: 1.35; font-weight: 700; }
    .vd-rating { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,.08); font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,.56); }
    .vd-rating strong { color: var(--orange); letter-spacing: .08em; }
    .vd-rating em { color: rgba(255,255,255,.82); font-style: normal; text-transform: none; }
    .vd-content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: -70px; position: relative; z-index: 2; }
    .vd-column { display: grid; gap: 14px; align-content: start; }
    .vd-panel { border: 1px solid var(--vd-stroke); border-radius: 8px; background: linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.035)); box-shadow: 0 18px 48px rgba(0,0,0,.26); padding: 18px; backdrop-filter: blur(8px); }
    .vd-panel-head { display: flex; justify-content: space-between; gap: 14px; align-items: start; margin-bottom: 16px; }
    .vd-panel-head p { margin: 0 0 5px; color: rgba(255,255,255,.58); font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .vd-panel-head h2 { margin: 0; font-family: var(--font-display); font-size: 28px; line-height: .95; letter-spacing: .025em; }
    .vd-panel-head a { color: var(--orange); text-decoration: none; font-size: 11px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
    .vd-lines-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
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
    .vd-split { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .vd-mini-list { display: grid; gap: 10px; }
    .vd-bonus, .vd-raffle { display: grid; grid-template-columns: 48px minmax(0, 1fr); gap: 12px; align-items: center; border: 1px solid rgba(255,255,255,.08); border-radius: 8px; background: rgba(255,255,255,.04); padding: 12px; }
    .vd-bonus i, .vd-raffle i { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: rgba(255,106,26,.12); color: var(--orange); font-size: 22px; }
    .vd-bonus strong, .vd-raffle strong { display: block; color: #fff; font-size: 13px; }
    .vd-bonus span, .vd-raffle span { display: block; color: var(--orange); font-size: 11px; font-weight: 900; margin-top: 3px; }
    .vd-bonus em { display: block; color: rgba(255,255,255,.56); font-size: 11px; line-height: 1.35; font-style: normal; margin-top: 5px; }
    .vd-raffle a { display: inline-flex; color: var(--orange); margin-top: 8px; font-size: 10px; font-weight: 900; text-transform: uppercase; text-decoration: none; }
    .vd-contact-list { display: grid; gap: 8px; }
    .vd-contact { display: grid; grid-template-columns: 38px minmax(0,1fr) 12px; gap: 10px; align-items: center; border: 1px solid rgba(255,255,255,.08); border-radius: 8px; background: rgba(255,255,255,.04); padding: 10px; color: #fff; text-decoration: none; }
    .vd-contact > i { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: rgba(255,106,26,.1); color: var(--orange); font-size: 16px; }
    .vd-contact strong, .vd-contact em { display: block; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .vd-contact strong { font-size: 12px; }
    .vd-contact em { color: rgba(255,255,255,.52); font-size: 10px; font-style: normal; margin-top: 2px; }
    .vd-contact b { color: var(--orange); font-size: 10px; }
    .vd-profile-data { margin-top: 22px; padding-top: 16px; border-top: 1px solid var(--vd-stroke); }
    .vd-profile-data dl { margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 12px 14px; }
    .vd-profile-data dl div { border-bottom: 0; padding-bottom: 0; }
    .vd-profile-data dt { color: rgba(255,255,255,.48); font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .vd-profile-data dd { margin: 4px 0 0; color: #fff; font-size: 12px; font-weight: 800; overflow-wrap: anywhere; }
    .vd-empty { border: 1px dashed rgba(255,255,255,.12); border-radius: 8px; padding: 18px; color: rgba(255,255,255,.6); font-size: 13px; text-align: center; }
    .vd-empty.compact { padding: 12px; font-size: 12px; }
    @media (max-width: 1060px) {
        .vd-hero-grid, .vd-content-grid { grid-template-columns: 1fr; }
        .vd-hero-copy { padding-top: 0; }
        .vd-visual-stack { max-width: 460px; margin: 0 auto; }
        .vd-content-grid { margin-top: -40px; }
        .vd-lines-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 760px) {
        .vd-shell { width: calc(100% - 24px); }
        .vd-hero { min-height: 0; padding-top: 16px; }
        .vd-topbar { margin-bottom: 46px; align-items: flex-start; }
        .vd-badge { max-width: 170px; justify-content: center; text-align: center; }
        .vd-visual-stack { padding-top: 8px; }
        .vd-visual-stack::before { width: min(310px, 82vw); top: 46px; }
        .vd-portrait { height: 340px; width: min(300px, 92%); }
        .vd-benefits, .vd-split { grid-template-columns: 1fr; }
        .vd-lines-grid { grid-template-columns: 1fr; }
        .vd-actions .vd-btn { width: 100%; }
    }
</style>
@endpush
