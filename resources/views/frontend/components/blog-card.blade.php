@props(['post'])

@php
    $image = $post->image
        ? (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://', '/storage/']) ? $post->image : asset('storage/'.$post->image))
        : null;
    $authorName = $post->authorAgent?->username ?: $post->authorAgent?->name ?: 'RED PICANTES BET';
@endphp

<a class="blog-card" href="{{ route('frontend.blog.show', $post->slug) }}" wire:navigate style="display:block;text-decoration:none;color:inherit">
    <div class="blog-thumb">
        @if($image)
            <img src="{{ $image }}" alt="{{ $post->title }}">
        @else
            <span>RED PICANTES</span>
        @endif
    </div>
    <div class="blog-body">
        <time>{{ $post->published_at?->format('d/m/Y') }} - Autor: {{ $authorName }} - <span style="color:var(--orange)">{{ strtoupper($post->category?->name ?? 'GENERAL') }}</span></time>
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
    </div>
</a>
