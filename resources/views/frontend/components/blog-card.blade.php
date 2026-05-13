@props(['post'])

@php
    $image = $post->image
        ? (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://', '/storage/']) ? $post->image : asset('storage/'.$post->image))
        : null;
@endphp

<article class="blog-card">
    <div class="blog-thumb">
        @if($image)
            <img src="{{ $image }}" alt="{{ $post->title }}">
        @else
            <span>RED PICANTES</span>
        @endif
    </div>
    <div class="blog-body">
        <time>{{ $post->published_at?->format('d/m/Y') }}</time>
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
    </div>
</article>
