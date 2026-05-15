@push('styles')
<style>
    .bonus-detail-page { padding:46px 0 0; }
    .bonus-detail-shell { display:grid; grid-template-columns:minmax(0, 1.1fr) minmax(320px, .65fr); gap:18px; align-items:start; }
    .bonus-detail-main, .bonus-detail-side { border:1px solid rgba(255,255,255,.1); border-radius:12px; background:linear-gradient(180deg,#170807,#080302); box-shadow:0 18px 48px rgba(0,0,0,.34); }
    .bonus-detail-main { position:relative; min-height:520px; padding:34px; overflow:hidden; }
    .bonus-detail-main::before { content:""; position:absolute; inset:16px; border:2px dashed rgba(255,106,26,.34); border-radius:10px; pointer-events:none; }
    .bonus-detail-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .bonus-detail-title { font-family:var(--font-display); font-size:72px; line-height:.86; letter-spacing:.02em; margin:12px 0 18px; max-width:760px; }
    .bonus-detail-title span { color:var(--orange); }
    .bonus-detail-code { display:inline-flex; max-width:100%; overflow-wrap:anywhere; border:1px solid rgba(255,106,26,.42); border-radius:8px; background:rgba(255,106,26,.1); color:var(--orange); padding:10px 14px; font-family:var(--font-mono); font-size:13px; font-weight:900; }
    .bonus-detail-description { color:var(--muted); font-size:15px; line-height:1.65; margin:24px 0 0; max-width:700px; font-weight:700; }
    .bonus-detail-metrics { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; margin-top:28px; }
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
    .bonus-detail-back { margin-top:14px; display:inline-flex; color:var(--orange); text-decoration:none; font-size:12px; font-weight:900; }
    @media (max-width: 860px) {
        .bonus-detail-shell { grid-template-columns:1fr; }
        .bonus-detail-title { font-size:48px; }
        .bonus-detail-metrics { grid-template-columns:1fr; }
    }
    @media (max-width: 560px) {
        .bonus-detail-page { padding-top:32px; }
        .bonus-detail-main, .bonus-detail-side { padding:20px; }
        .bonus-detail-main { min-height:0; }
        .bonus-detail-main::before { inset:10px; }
        .bonus-detail-title { font-size:40px; overflow-wrap:anywhere; }
        .bonus-detail-description { font-size:14px; }
        .bonus-detail-code { display:flex; width:100%; justify-content:center; text-align:center; }
    }
</style>
@endpush

@php
    $isExpired = $bonus->status === 'expired' || $bonus->end_date->isPast();
    $isUpcoming = $bonus->status === 'upcoming' || $bonus->start_date->isFuture();
    $isClaimed = $assignment && in_array($assignment->status, ['active', 'used'], true);
    $limitReached = $bonus->total_quantity !== null && $bonus->active_assignments_count >= $bonus->total_quantity;
    $isAvailable = ! $isExpired && ! $isUpcoming && ! $isClaimed && ! $limitReached && $bonus->line?->status === 'active';
    $claimHref = $bonus->line ? route('frontend.lines.show', $bonus->line) : route('frontend.lines');
@endphp

<section class="bonus-detail-page">
    <div class="fe-shell">
        <div class="bonus-detail-shell">
            <article class="bonus-detail-main">
                <div class="bonus-detail-kicker">Detalle del bono</div>
                <h1 class="bonus-detail-title">{{ $bonus->title }} <span>{{ $isAvailable ? 'activo' : '' }}</span></h1>
                <div class="bonus-detail-code">{{ $bonus->code ?: 'SIN CODIGO' }}</div>
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
                    <a href="{{ route('frontend.lines') }}" wire:navigate class="fe-btn ghost" style="width:100%;">Lineas de atencion</a>
                </div>

                <a href="{{ route('frontend.bonuses') }}" wire:navigate class="bonus-detail-back">Volver a bonos</a>
            </aside>
        </div>
    </div>
</section>
