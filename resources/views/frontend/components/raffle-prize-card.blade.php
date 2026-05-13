@props(['prize', 'index' => 0])

@php
    $image = $prize['image'] ?? null;
    if ($image && ! \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
        $image = asset('storage/'.$image);
    }
@endphp

<article class="prize-card">
    <div class="prize-media">
        @if($image)
            <img src="{{ $image }}" alt="{{ $prize['name'] ?? 'Premio' }}">
        @else
            <span>{{ $prize['position'] ?? $index + 1 }}</span>
        @endif
    </div>
    <div>
        <div class="prize-position">{{ $prize['position'] ?? $index + 1 }} puesto</div>
        <h3>{{ $prize['name'] ?? 'Premio del sorteo' }}</h3>
    </div>
</article>
