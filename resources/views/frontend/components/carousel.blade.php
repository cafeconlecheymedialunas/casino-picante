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

<div
    x-data="{
        current: 0,
        total: {{ $items->count() ?: 1 }},
        timer: null,
        init() {
            this.startAuto();
        },
        startAuto() {
            this.timer = setInterval(() => this.next(), 5000);
        },
        stopAuto() {
            clearInterval(this.timer);
        },
        next() {
            this.current = (this.current + 1) % this.total;
            this.scrollTo(this.current);
        },
        scrollTo(index) {
            const track = this.$refs.track;
            if (!track) return;
            const width = track.offsetWidth;
            track.scrollTo({ left: index * width, behavior: 'smooth' });
        }
    }"
    @mouseenter="stopAuto()"
    @mouseleave="startAuto()"
    class="home-hero-carousel-wrapper"
>
    <div class="home-hero-carousel" x-ref="track">
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

</div>
