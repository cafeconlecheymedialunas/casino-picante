@props([
    'kicker' => null,
    'title' => '',
    'highlight' => null,
    'subtitle' => null,
    'action' => null,
])

<div class="fe-section-head">
    <div>
        @if($kicker)
            <div class="fe-kicker">{{ $kicker }}</div>
        @endif
        <h2 class="fe-title">
            {{ $title }}
            @if($highlight)
                <span>{{ $highlight }}</span>
            @endif
        </h2>
        @if($subtitle)
            <p class="fe-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if($action)
        <div>{!! $action !!}</div>
    @endif
</div>
