@props(['items'])

@php
    $imageUrl = function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/storage/'])) {
            return $path;
        }

        return asset('storage/'.$path);
    };
@endphp

<div class="home-hero-carousel">
    @forelse($items as $item)
        @php $src = $imageUrl($item->image ?? null); @endphp
        <a class="home-hero-slide" href="{{ $item->link ?: '#lineas' }}" @if(! $item->link) aria-label="Ver lineas" @endif>
            @if($src)
                <img src="{{ $src }}" alt="{{ $item->title ?: 'Imagen principal' }}">
            @else
                <div class="home-hero-empty"></div>
            @endif
        </a>
    @empty
        <div class="home-hero-slide placeholder">
            <div class="home-hero-empty"></div>
        </div>
    @endforelse
</div>
