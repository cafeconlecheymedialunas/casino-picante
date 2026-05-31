@push('styles')
<style>
    .bonus-detail-page { padding:46px 0 0; }
    .bonus-detail-shell { display:grid; grid-template-columns:minmax(0, 1fr) minmax(320px, .52fr); gap:18px; align-items:start; }
    .bonus-detail-main, .bonus-detail-side { border:1px solid rgba(255,255,255,.1); border-radius:12px; background:linear-gradient(180deg,#170807,#080302); box-shadow:0 18px 48px rgba(0,0,0,.34); }
    .bonus-detail-main { position:relative; padding:30px; overflow:hidden; }
    .bonus-detail-main::before { content:""; position:absolute; inset:12px; border:1px dashed rgba(255,106,26,.32); border-radius:10px; pointer-events:none; }
    .bonus-detail-main > * { position:relative; z-index:1; }
    .bonus-detail-head { display:flex; justify-content:space-between; align-items:flex-start; gap:18px; }
    .bonus-detail-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .bonus-detail-title { font-family:var(--font-display); font-size:clamp(44px, 6vw, 76px); line-height:.86; letter-spacing:.02em; margin:12px 0 0; max-width:760px; text-transform:uppercase; overflow-wrap:anywhere; }
    .bonus-detail-status { display:inline-flex; align-items:center; gap:7px; border:1px solid rgba(255,106,26,.36); border-radius:999px; background:rgba(255,106,26,.10); color:var(--orange); padding:8px 12px; font-size:10px; font-weight:900; text-transform:uppercase; white-space:nowrap; }
    .bonus-detail-codebox { display:grid; gap:6px; margin-top:22px; width:min(100%, 420px); border:1px solid rgba(255,106,26,.36); border-radius:10px; background:rgba(255,106,26,.08); padding:14px; }
    .bonus-detail-codebox span { color:rgba(255,255,255,.52); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; }
    .bonus-detail-code { color:var(--orange); font-family:var(--font-mono); font-size:16px; font-weight:900; overflow-wrap:anywhere; }
    .bonus-detail-description { color:var(--muted); font-size:15px; line-height:1.65; margin:22px 0 0; max-width:760px; font-weight:700; }
    .bonus-detail-metrics { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; margin-top:22px; }
    .bonus-detail-metric { border:1px solid rgba(255,255,255,.09); border-radius:10px; background:rgba(255,255,255,.035); padding:14px; }
    .bonus-detail-metric span { display:block; color:var(--muted-2); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:5px; }
    .bonus-detail-metric strong { display:block; color:#fff; font-size:14px; line-height:1.25; }
    .bonus-detail-side { padding:22px; }
    .bonus-detail-side h2 { font-family:var(--font-display); font-size:30px; line-height:1; letter-spacing:.02em; margin:0 0 16px; }
    .bonus-detail-line { border:1px solid rgba(255,106,26,.22); border-radius:10px; background:rgba(255,106,26,.07); padding:16px; margin-bottom:14px; }
    .bonus-detail-line strong { display:block; font-size:15px; margin-bottom:5px; }
    .bonus-detail-line p { margin:0; color:var(--muted); font-size:12px; line-height:1.45; }
    .bonus-detail-actions { display:grid; gap:10px; }
    .bonus-detail-state { display:flex; align-items:center; justify-content:center; min-height:44px; border-radius:999px; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.06); color:var(--muted); font-size:12px; font-weight:900; text-transform:uppercase; }
    @media (max-width: 860px) {
        .bonus-detail-shell { grid-template-columns:1fr; }
        .bonus-detail-metrics { grid-template-columns:1fr; }
    }
    @media (max-width: 560px) {
        .bonus-detail-page { padding-top:32px; }
        .bonus-detail-main, .bonus-detail-side { padding:20px; }
        .bonus-detail-main::before { inset:10px; }
        .bonus-detail-head { display:grid; gap:12px; }
        .bonus-detail-status { width:max-content; max-width:100%; }
        .bonus-detail-description { font-size:14px; }
    }
</style>
@endpush

@php
    $isExpired = $bonus->status === 'expired' || $bonus->end_date->isPast();
    $isUpcoming = $bonus->status === 'upcoming' || $bonus->start_date->isFuture();
    $isClaimed = $assignment && in_array($assignment->status, ['active', 'used'], true);
    $limitReached = $bonus->total_quantity !== null && $bonus->active_assignments_count >= $bonus->total_quantity;
    $isAvailable = ! $isExpired && ! $isUpcoming && ! $isClaimed && ! $limitReached && $bonus->line?->status === 'active';
    $claimHref = $bonus->line
        ? route('frontend.cajero.lineas.detalle', [$bonus->line->vendor, $bonus->line])
        : route('frontend.cajero.lineas', $bonus->vendor);
@endphp

<section class="bonus-detail-page">
    <div class="fe-shell">
        @include('frontend.components.breadcrumbs', [
            'items' => isset($publicVendor)
                ? [
                    ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
                    ['label' => $publicVendor->name, 'url' => route('frontend.cajero.inicio', $publicVendor)],
                    ['label' => 'Bonos', 'url' => route('frontend.cajero.bonos', $publicVendor)],
                    ['label' => $bonus->title],
                ]
                : [
                    ['label' => 'Inicio', 'url' => route('frontend.home')],
                    ['label' => 'Bonos', 'url' => route('frontend.bonos')],
                    ['label' => $bonus->title],
                ],
        ])
        <div class="bonus-detail-shell">
            <article class="bonus-detail-main">
                <div class="bonus-detail-head">
                    <div>
                        <div class="bonus-detail-kicker">Detalle del bono</div>
                        <h1 class="bonus-detail-title">{{ $bonus->title }}</h1>
                    </div>
                    <span class="bonus-detail-status">
                        <i class="fa-solid fa-ticket"></i>
                        @if($isClaimed)
                            Reclamado
                        @elseif($isExpired)
                            Vencido
                        @elseif($limitReached)
                            Agotado
                        @elseif($isAvailable)
                            Activo
                        @else
                            Proximo
                        @endif
                    </span>
                </div>
                <div class="bonus-detail-codebox">
                    <span>Codigo del bono</span>
                    <strong class="bonus-detail-code">{{ $bonus->code ?: 'Sin codigo' }}</strong>
                </div>
                <p class="bonus-detail-description">{{ $bonus->description ?: 'Este bono no tiene descripcion cargada. Consulta las condiciones exactas con la linea disponible.' }}</p>

                <div class="bonus-detail-metrics">
                    <div class="bonus-detail-metric">
                        <span>Linea disponible</span>
                        <strong>{{ $bonus->line?->name ?? 'Sin linea' }}</strong>
                    </div>
                    <div class="bonus-detail-metric">
                        <span>Vigencia</span>
                        <strong>{{ $bonus->start_date->format('d/m H:i') }} - {{ $bonus->end_date->format('d/m H:i') }}</strong>
                    </div>
                    <div class="bonus-detail-metric">
                        <span>Codigo</span>
                        <strong>{{ $bonus->code ?: 'Sin codigo' }}</strong>
                    </div>
                </div>
            </article>

            <aside class="bonus-detail-side">
                <h2>Reclamar bono</h2>
                <div class="bonus-detail-line">
                    <strong>{{ $bonus->line?->name ?? 'Linea no disponible' }}</strong>
                    <p>{{ $bonus->line?->description ?: 'La linea de atencion te ayuda a activar o consultar este bono.' }}</p>
                </div>

                <div class="bonus-detail-actions">
                    @if($isAvailable)
                        <a href="{{ $claimHref }}" wire:navigate class="fe-btn primary" style="width:100%;">Reclamar</a>
                    @else
                        <span class="bonus-detail-state">
                            @if($isClaimed)
                                Reclamado
                            @elseif($isExpired)
                                Vencido
                            @elseif($limitReached)
                                Agotado
                            @else
                                Proximo
                            @endif
                        </span>
                    @endif
                    <a href="{{ route('frontend.cajero.lineas', $bonus->vendor) }}" wire:navigate class="fe-btn ghost" style="width:100%;">Lineas de atencion</a>
                </div>
            </aside>
        </div>
    </div>
</section>
