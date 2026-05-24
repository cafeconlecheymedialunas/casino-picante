@props(['bonus', 'assignment' => null])

@php
    $isExpired = $bonus->status === 'expired' || $bonus->end_date->isPast();
    $isUpcoming = $bonus->status === 'upcoming' || $bonus->start_date->isFuture();
    $isClaimed = $assignment && in_array($assignment->status, ['active', 'used'], true);
    $limitReached = $bonus->total_quantity !== null && $bonus->active_assignments_count >= $bonus->total_quantity;
    $isAvailable = ! $isExpired && ! $isUpcoming && ! $isClaimed && ! $limitReached && $bonus->line?->status === 'active';
    $claimHref = $bonus->line
        ? (isset($publicVendor)
            ? route('frontend.cajero.lineas.detalle', [$publicVendor, $bonus->line])
            : route('frontend.lineas.detalle', $bonus->line))
        : (isset($publicVendor)
            ? route('frontend.cajero.lineas', $publicVendor)
            : route('frontend.lineas'));
    $detailHref = isset($publicVendor)
        ? route('frontend.cajero.bonos.detalle', [$publicVendor, $bonus])
        : route('frontend.bonos.detalle', $bonus);
@endphp

@once
@push('styles')
<style>
    .bonus-public-card { position:relative; min-height:270px; display:flex; flex-direction:column; justify-content:space-between; gap:16px; border:1px solid rgba(255,255,255,.1); border-radius:12px; background:radial-gradient(110% 90% at 0% 0%, rgba(255,106,26,.2), transparent 56%), linear-gradient(180deg,#180907,#090403); padding:22px; box-shadow:0 18px 48px rgba(0,0,0,.34); overflow:hidden; }
    .bonus-public-card::before { content:""; position:absolute; inset:10px; border:2px dashed rgba(255,106,26,.34); border-radius:9px; pointer-events:none; }
    .bonus-public-card.is-disabled { opacity:.72; filter:saturate(.72); }
    .bonus-public-head, .bonus-public-actions, .bonus-public-card p, .bonus-public-meta { position:relative; z-index:1; }
    .bonus-public-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
    .bonus-public-kicker { display:block; color:var(--orange); font-size:10px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; margin-bottom:8px; }
    .bonus-public-card h3 { font-family:var(--font-display); font-size:34px; line-height:.92; margin:0; letter-spacing:.02em; text-transform:uppercase; overflow-wrap:anywhere; }
    .bonus-public-code { max-width:136px; overflow-wrap:anywhere; border:1px solid rgba(255,106,26,.42); border-radius:8px; background:rgba(255,106,26,.1); color:var(--orange); padding:8px 10px; font-family:var(--font-mono); font-size:11px; font-weight:900; text-align:center; }
    .bonus-public-card p { color:var(--muted); font-size:13px; line-height:1.45; margin:0; font-weight:700; }
    .bonus-public-meta { display:flex; flex-wrap:wrap; gap:6px; }
    .bonus-public-meta span { border:1px solid rgba(255,255,255,.1); border-radius:999px; background:rgba(255,255,255,.04); color:rgba(255,255,255,.72); padding:5px 9px; font-size:10px; font-weight:900; }
    .bonus-public-actions { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .bonus-public-state { display:inline-flex; align-items:center; justify-content:center; height:40px; border-radius:999px; padding:0 18px; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.06); color:var(--muted); font-size:12px; font-weight:900; text-transform:uppercase; }
    .vd-mini-list .bonus-public-card { min-height:0; padding:18px; gap:14px; }
    .vd-mini-list .bonus-public-card h3 { font-size:26px; }
    .vd-mini-list .bonus-public-head { display:grid; gap:10px; }
    .vd-mini-list .bonus-public-code { max-width:100%; width:max-content; }
    .vd-mini-list .bonus-public-actions .fe-btn,
    .vd-mini-list .bonus-public-state { min-height:36px; height:auto; padding:9px 14px; }
    @media (max-width: 640px) {
        .bonus-public-card { min-height:0; padding:18px; }
        .bonus-public-head { display:block; }
        .bonus-public-card h3 { font-size:29px; }
        .bonus-public-code { display:inline-block; max-width:100%; margin-top:12px; }
        .bonus-public-actions .fe-btn, .bonus-public-state { width:100%; }
    }
</style>
@endpush
@endonce

<article class="bonus-public-card {{ $isAvailable ? 'is-available' : 'is-disabled' }}">
    <div class="bonus-public-head">
        <div>
            <span class="bonus-public-kicker">Bono activo</span>
            <h3>{{ $bonus->title }}</h3>
        </div>
        <span class="bonus-public-code">{{ $bonus->code ?: 'SIN CODIGO' }}</span>
    </div>

    <p>{{ $bonus->description ?: 'Consulta las condiciones con la linea disponible antes de reclamarlo.' }}</p>

    <div class="bonus-public-meta">
        <span>{{ $bonus->line?->name ?? 'Sin linea' }}</span>
        @if($bonus->platform)
            <span>{{ $bonus->platform->name }}</span>
        @endif
        <span>Hasta {{ $bonus->end_date->format('d/m H:i') }}</span>
    </div>

    <div class="bonus-public-actions">
        <a href="{{ $detailHref }}" wire:navigate class="fe-btn ghost">Ver detalle</a>
        @if($isAvailable)
            <a href="{{ $claimHref }}" wire:navigate class="fe-btn primary">Reclamar</a>
        @else
            <span class="bonus-public-state">
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
    </div>
</article>
