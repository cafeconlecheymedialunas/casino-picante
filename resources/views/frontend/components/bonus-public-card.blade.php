@props(['bonus', 'assignment' => null])

@php
    $isExpired = $bonus->status === 'expired' || $bonus->end_date->isPast();
    $isUpcoming = $bonus->status === 'upcoming' || $bonus->start_date->isFuture();
    $isClaimed = $assignment && in_array($assignment->status, ['active', 'used'], true);
    $limitReached = $bonus->total_quantity !== null && $bonus->active_assignments_count >= $bonus->total_quantity;
    $isAvailable = ! $isExpired && ! $isUpcoming && ! $isClaimed && ! $limitReached && $bonus->line?->status === 'active';
    $claimHref = $bonus->line ? route('frontend.lines.show', $bonus->line) : route('frontend.lines');
    $detailHref = route('frontend.bonuses.show', $bonus->id);
@endphp

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
