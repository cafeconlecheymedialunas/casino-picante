@props([
    'rating' => 0,
    'count' => null,
    'showValue' => true,
    'showCount' => true,
    'label' => 'Valoracion general',
    'size' => 'md',
])

@php
    $ratingValue = max(0, min(5, (float) $rating));
    $fullStars = (int) floor($ratingValue);
    $hasHalfStar = ($ratingValue - $fullStars) >= 0.5 && $fullStars < 5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    $ratingCount = is_numeric($count) ? (int) $count : null;
@endphp

@once
@push('styles')
<style>
    .public-rating { display:inline-flex; align-items:center; gap:6px; color:var(--amber); white-space:nowrap; letter-spacing:.04em; }
    .public-rating-stars { display:inline-flex; align-items:center; gap:2px; }
    .public-rating-star { color:var(--amber); }
    .public-rating-star.empty { color:rgba(255,255,255,.15); }
    .public-rating-value { color:#fff; font-family:var(--font-body); font-weight:900; letter-spacing:0; }
    .public-rating-count { color:rgba(255,255,255,.58); font-family:var(--font-body); font-weight:700; letter-spacing:0; }
    .public-rating.sm { font-size:12px; }
    .public-rating.md { font-size:15px; }
    .public-rating.lg { font-size:18px; }
    .public-rating.xl { font-size:22px; }
</style>
@endpush
@endonce

<span class="public-rating {{ $size }}" aria-label="{{ $label }} {{ number_format($ratingValue, 1) }} de 5">
    <span class="public-rating-stars" aria-hidden="true">
        @for($i = 0; $i < $fullStars; $i++)
            <i class="fa-solid fa-star public-rating-star"></i>
        @endfor
        @if($hasHalfStar)
            <i class="fa-solid fa-star-half-stroke public-rating-star"></i>
        @endif
        @for($i = 0; $i < $emptyStars; $i++)
            <i class="fa-solid fa-star public-rating-star empty"></i>
        @endfor
    </span>
    @if($showValue)
        <strong class="public-rating-value">{{ number_format($ratingValue, 1) }}</strong>
    @endif
    @if($showCount && $ratingCount !== null)
        <span class="public-rating-count">({{ $ratingCount }})</span>
    @endif
</span>
