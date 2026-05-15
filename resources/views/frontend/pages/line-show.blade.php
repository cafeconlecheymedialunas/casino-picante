@push('styles')
<style>
    .line-detail-page { padding:42px 0 0; }
    .line-detail-hero { overflow:hidden; border:1px solid var(--line-warm); border-radius:var(--r-xl); background:linear-gradient(180deg,#180b08,#090505); box-shadow:0 24px 70px rgba(0,0,0,.38); }
    .line-detail-cover { height:310px; position:relative; background:radial-gradient(80% 80% at 80% 0%, rgba(255,106,26,.35), transparent 70%), #130807; }
    .line-detail-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    .line-detail-cover::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg, transparent 40%, rgba(9,5,5,.88)); }
    .line-detail-profile { position:relative; margin-top:-72px; padding:0 28px 28px; display:grid; grid-template-columns:118px minmax(0, 1fr) auto; gap:20px; align-items:end; }
    .line-detail-avatar { width:118px; height:118px; border-radius:24px; border:4px solid #100707; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; font-family:var(--font-display); font-size:44px; color:#160604; z-index:2; }
    .line-detail-avatar img { width:100%; height:100%; object-fit:cover; }
    .line-detail-title { z-index:2; }
    .line-detail-title h1 { margin:0; font-family:var(--font-display); font-size:64px; line-height:.9; letter-spacing:.02em; }
    .line-detail-title p { margin:10px 0 0; color:var(--muted); font-size:14px; line-height:1.55; max-width:720px; }
    .line-status-box { z-index:2; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(0,0,0,.28); padding:14px 16px; min-width:180px; }
    .line-status-box strong { display:block; color:var(--amber); letter-spacing:.08em; }
    .line-detail-grid { display:grid; grid-template-columns:minmax(0, .95fr) minmax(0, 1.35fr); gap:16px; margin-top:18px; }
    .line-panel { border:1px solid var(--line); border-radius:var(--r-md); background:linear-gradient(180deg,#170b0b,#0f0707); padding:20px; min-width:0; }
    .line-panel-title { font-family:var(--font-display); font-size:30px; line-height:1; margin:0 0 14px; letter-spacing:.02em; }
    .line-info-row { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid var(--line); color:var(--muted); font-size:13px; }
    .line-info-row:last-child { border-bottom:0; }
    .line-info-row strong { color:#fff; text-align:right; }
    .detail-channel-list { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-start; }
    .platform-grid { display:grid; gap:10px; grid-template-columns:repeat(2, minmax(0, 1fr)); }
    .detail-channel { width:max-content; max-width:100%; display:inline-grid; grid-template-columns:42px minmax(0, auto); gap:12px; align-items:center; padding:12px 14px 12px 12px; border:1px solid rgba(154,154,154,.18); border-radius:999px; background:rgba(255,255,255,.035); text-decoration:none; }
    .detail-channel i { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(154,154,154,.08); color:#9a9a9a; font-size:18px; }
    .detail-channel strong { display:block; font-size:14px; }
    .detail-channel small { display:block; color:var(--muted-2); font-size:11px; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:min(420px, 58vw); }
    .platform-card { display:grid; grid-template-columns:46px minmax(0, 1fr); gap:12px; align-items:center; padding:14px; border:1px solid var(--line); border-radius:var(--r-sm); background:rgba(255,255,255,.035); }
    .platform-logo { width:46px; height:46px; border-radius:12px; background:rgba(255,106,26,.14); display:flex; align-items:center; justify-content:center; overflow:hidden; color:var(--orange); font-weight:900; }
    .platform-logo img { width:100%; height:100%; object-fit:cover; }
    .platform-card strong { display:block; font-size:14px; }
    .platform-card p { color:var(--muted); font-size:12px; line-height:1.4; margin:4px 0 0; }
    .line-rating-wrap { margin-top:18px; }
    .rating-summary { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:16px; }
    .rating-big { font-family:var(--font-display); font-size:54px; line-height:.85; color:var(--orange); }
    .rating-stars { display:flex; gap:4px; color:var(--amber); font-size:18px; }
    .rating-count { color:var(--muted); font-size:12px; font-weight:800; }
    .rating-form { display:grid; gap:12px; margin-bottom:16px; }
    .rating-pick { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .rating-star-btn { width:38px; height:38px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.035); color:rgba(255,255,255,.28); cursor:pointer; font-size:18px; }
    .rating-star-btn.active { color:var(--amber); border-color:rgba(255,179,71,.5); background:rgba(255,179,71,.1); }
    .line-rating-input { width:100%; min-height:92px; resize:vertical; border:1px solid var(--line-2); border-radius:var(--r-md); background:rgba(255,255,255,.04); color:#fff; outline:none; padding:14px; font:600 13px var(--font-body); }
    .line-rating-input:focus { border-color:var(--orange); box-shadow:0 0 0 4px rgba(255,106,26,.12); }
    .rating-error { color:#ff8a8a; font-size:12px; font-weight:800; }
    .rating-list { display:grid; gap:10px; }
    .rating-item { display:grid; grid-template-columns:42px minmax(0, 1fr); gap:12px; padding:14px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); }
    .rating-avatar { width:42px; height:42px; border-radius:999px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:rgba(255,106,26,.16); color:var(--orange); font-weight:900; }
    .rating-avatar img { width:100%; height:100%; object-fit:cover; }
    .rating-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:5px; }
    .rating-name { font-size:13px; font-weight:900; color:#fff; }
    .rating-date { font-size:10px; color:var(--muted-2); font-weight:800; white-space:nowrap; }
    .rating-message { color:var(--muted); font-size:13px; line-height:1.5; margin:6px 0 0; }
    .line-login-box { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:14px; border:1px solid var(--line); border-radius:var(--r-md); background:rgba(255,255,255,.035); margin-bottom:16px; color:var(--muted); font-size:13px; }
    @media (max-width: 920px) {
        .line-detail-profile, .line-detail-grid { grid-template-columns:1fr; }
        .line-status-box { width:100%; }
        .line-detail-title h1 { font-size:50px; }
        .platform-grid { grid-template-columns:1fr; }
    }
    @media (max-width: 620px) {
        .line-detail-page { padding-top:28px; }
        .line-detail-cover { height:210px; }
        .line-detail-profile { padding:0 18px 20px; gap:12px; }
        .line-detail-avatar { width:96px; height:96px; border-radius:20px; }
        .line-detail-title h1 { font-size:42px; }
        .line-info-row { display:grid; grid-template-columns:1fr; gap:4px; }
        .line-info-row strong { text-align:left; overflow-wrap:anywhere; }
        .detail-channel { width:100%; grid-template-columns:42px minmax(0, 1fr); border-radius:12px; }
        .detail-channel small { max-width:none; white-space:normal; overflow-wrap:anywhere; }
        .line-login-box .fe-btn, .rating-form .fe-btn { width:100%; }
        .rating-item { grid-template-columns:1fr; }
        .rating-head { display:block; }
        .rating-date { display:block; margin-top:3px; }
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
    $normalizeChannelType = fn (?string $type): string => strtolower(trim((string) $type));
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null))->values();
    $manager = $line->lineAgents->first(fn ($lineAgent) => $lineAgent->role === 'encargado' && $lineAgent->is_active);
    $platforms = $line->activePlatforms;
@endphp

<div>
    <section class="line-detail-page">
        <div class="fe-shell">
            <article class="line-detail-hero">
                <div class="line-detail-cover">
                    @if($line->portada_url)
                        <img src="{{ $line->portada_url }}" alt="{{ $line->name }}">
                    @endif
                </div>
                <div class="line-detail-profile">
                    <div class="line-detail-avatar">
                        @if($line->perfil_url)
                            <img src="{{ $line->perfil_url }}" alt="">
                        @else
                            {{ strtoupper(mb_substr($line->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="line-detail-title">
                        <div class="fe-kicker">Linea activa</div>
                        <h1>{{ $line->name }}</h1>
                        <p>{{ $line->description ?: 'Alta rapida, carga de saldo, plataformas disponibles y atencion directa para jugar online.' }}</p>
                    </div>
                    <div class="line-status-box">
                        <small>Valoracion general</small>
                        <strong>★★★★★ {{ number_format($ratingAverage, 1) }}</strong>
                    </div>
                </div>
            </article>

            <div class="line-detail-grid">
                <div class="line-panel">
                    <h2 class="line-panel-title">Informacion</h2>
                    <div class="line-info-row"><span>Encargado</span><strong>{{ $manager?->agent?->username ?: $manager?->agent?->name ?: 'A confirmar' }}</strong></div>
                    <div class="line-info-row"><span>Estado</span><strong>Activa</strong></div>
                    <div class="line-info-row"><span>Plataformas disponibles</span><strong>{{ $platforms->count() }}</strong></div>
                    <div class="line-info-row"><span>Canales publicados</span><strong>{{ $contacts->count() }}</strong></div>
                    <div class="line-info-row"><span>Valoraciones</span><strong>{{ $ratingCount }}</strong></div>
                    @if($line->type)
                        <div class="line-info-row"><span>Tipo</span><strong>{{ ucfirst($line->type) }}</strong></div>
                    @endif
                </div>

                <div class="line-panel">
                    <h2 class="line-panel-title">Canales de contacto</h2>
                    <div class="detail-channel-list">
                        @forelse($contacts as $contact)
                            @php
                                $type = $normalizeChannelType($contact['type'] ?? 'web');
                                $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                                $name = $contact['name'] ?: ucfirst($type);
                            @endphp
                            <a class="detail-channel" href="{{ $contact['value'] }}" target="_blank" rel="noopener">
                                <i class="{{ $icon }}"></i>
                                <div>
                                    <strong>{{ $name }}</strong>
                                    <small>{{ $contact['value'] }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="empty-panel">Esta linea todavia no tiene canales publicados.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <section id="plataformas" class="fe-section">
                <div class="line-panel">
                    <h2 class="line-panel-title">Plataformas disponibles</h2>
                    @if($platforms->count())
                        <div class="platform-grid">
                            @foreach($platforms as $platform)
                                <article class="platform-card">
                                    <div class="platform-logo">
                                        @if($platform->logo_url)
                                            <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}">
                                        @else
                                            {{ strtoupper(mb_substr($platform->name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $platform->name }}</strong>
                                        <p>{{ $platform->pivot?->custom_message ?: $platform->description ?: 'Disponible para esta linea.' }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-panel">No hay plataformas activas publicadas para esta linea.</div>
                    @endif
                </div>
            </section>

            <section id="valoracion" class="fe-section">
                <div class="line-panel line-rating-wrap">
                    <h2 class="line-panel-title">Valoracion de usuarios</h2>

                    <div class="rating-summary">
                        <div>
                            <div class="rating-big">{{ number_format($ratingAverage, 1) }}</div>
                            <div class="rating-stars">★★★★★</div>
                        </div>
                        <div class="rating-count">{{ $ratingCount }} valoraciones publicadas</div>
                    </div>

                    @auth
                        <form wire:submit.prevent="saveRating" class="rating-form">
                            <div class="rating-pick" aria-label="Elegir valoracion">
                                @for($star = 1; $star <= 5; $star++)
                                    <button type="button" wire:click="setRating({{ $star }})" class="rating-star-btn {{ ($selectedRating ?? 0) >= $star ? 'active' : '' }}">★</button>
                                @endfor
                            </div>
                            @error('selectedRating') <div class="rating-error">{{ $message }}</div> @enderror
                            <textarea wire:model.defer="ratingMessage" class="line-rating-input" placeholder="Deja un mensaje sobre tu experiencia con esta linea" aria-label="Mensaje de valoracion"></textarea>
                            @error('ratingMessage') <div class="rating-error">{{ $message }}</div> @enderror
                            <div><button type="submit" class="fe-btn primary">Guardar valoracion</button></div>
                        </form>
                    @else
                        <div class="line-login-box">
                            <span>Inicia sesion para valorar esta linea.</span>
                            <a href="{{ route('login') }}" wire:navigate class="fe-btn ghost">Ingresar</a>
                        </div>
                    @endauth

                    @if($ratings->count())
                        <div class="rating-list">
                            @foreach($ratings as $rating)
                                <article class="rating-item">
                                    <div class="rating-avatar">
                                        @if($rating->user?->avatar)
                                            <img src="{{ $rating->user->avatar }}" alt="">
                                        @else
                                            {{ strtoupper(mb_substr($rating->user?->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="rating-head">
                                            <div>
                                                <div class="rating-name">{{ $rating->user?->name ?? 'Usuario' }}</div>
                                                <div class="rating-stars">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</div>
                                            </div>
                                            <time class="rating-date">{{ $rating->created_at->diffForHumans() }}</time>
                                        </div>
                                        @if($rating->message)
                                            <p class="rating-message">{{ $rating->message }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-panel">Todavia no hay valoraciones publicadas para esta linea.</div>
                    @endif
                </div>
            </section>
        </div>
    </section>
</div>
