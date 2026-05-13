@props(['bonus'])

@php
    $value = $bonus->bonus_percent
        ? rtrim(rtrim(number_format((float) $bonus->bonus_percent, 2, ',', '.'), '0'), ',').'%'
        : ($bonus->bonus_amount ? '$'.number_format((float) $bonus->bonus_amount, 0, ',', '.') : 'BONO');
    $hasCode = filled($bonus->code);
@endphp

<article class="bonus-card">
    <div class="bonus-ticket-main">
        <div class="bonus-ticket-kicker">Bono</div>
        <h3>{{ $bonus->title }}</h3>
        <div class="bonus-ticket-value">{{ $value }}</div>
        <p>{{ $bonus->description ?: 'Aprovechalo antes de que termine y sumale mas fichas a tu saldo.' }}</p>
        @if($hasCode)
            <strong>{{ $bonus->code }}</strong>
        @endif
        <em>Hasta {{ $bonus->end_date->format('d/m H:i') }}</em>
    </div>
</article>
