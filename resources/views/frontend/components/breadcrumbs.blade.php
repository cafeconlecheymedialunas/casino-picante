@props(['items' => []])

@if(count($items))
    <nav class="fe-breadcrumbs" aria-label="Migas de pan">
        @foreach($items as $item)
            @if(! $loop->first)
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            @endif

            @if(! empty($item['url']) && ! $loop->last)
                <a href="{{ $item['url'] }}" wire:navigate>{{ $item['label'] }}</a>
            @else
                <span>{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
