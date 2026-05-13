@props(['bonus'])

@php
    $value = $bonus->bonus_percent
        ? rtrim(rtrim(number_format((float) $bonus->bonus_percent, 2, ',', '.'), '0'), ',').'%'
        : ($bonus->bonus_amount ? '$'.number_format((float) $bonus->bonus_amount, 0, ',', '.') : 'BONO');
@endphp

<article class="bonus-card">
    <div class="bonus-value">{{ $value }}</div>
    <h3>{{ $bonus->title }}</h3>
    <p>{{ $bonus->description ?: 'Aprovechalo antes de que termine y sumale mas fichas a tu saldo.' }}</p>
    <div class="bonus-meta">
        <span>{{ $bonus->code ?: 'Sin codigo' }}</span>
        <span>Hasta {{ $bonus->end_date->format('d/m') }}</span>
    </div>
</article>
