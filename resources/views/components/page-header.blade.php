@props([
    'section'      => null,
    'breadcrumbs'  => [],
    'cta'          => null,
])

@php
    $hasImage = !empty($section?->image);
    $imageUrl = $hasImage
        ? (\Illuminate\Support\Str::startsWith($section->image, ['http://', 'https://', '/storage/'])
            ? $section->image
            : asset('storage/'.$section->image))
        : null;
@endphp

<div class="ph-wrap {{ $hasImage ? 'ph-has-image' : '' }}"
    @if($hasImage) style="--ph-image: url('{{ $imageUrl }}')" @endif
>
    @if($hasImage)
        <div class="ph-bg"></div>
        <div class="ph-overlay"></div>
    @endif

    <div class="ph-inner">
        @if(count($breadcrumbs))
            @include('frontend.components.breadcrumbs', ['items' => $breadcrumbs])
        @endif

        @if($section?->kicker)
            <div class="ph-kicker">{{ $section->kicker }}</div>
        @endif

        @if($section?->title || $section?->highlight)
            <h1 class="ph-title">
                {{ $section?->title }}
                @if($section?->highlight)
                    <span>{{ $section->highlight }}</span>
                @endif
            </h1>
        @endif

        @if($section?->subtitle)
            <p class="ph-subtitle">{{ $section->subtitle }}</p>
        @endif

        @if(!empty($section?->action_text) && !empty($section?->action_url))
            <a href="{{ $section->action_url }}" wire:navigate class="fe-btn ghost ph-cta">{{ $section->action_text }}</a>
        @endif

        {{ $slot }}
    </div>
</div>

@once
<style>
    .ph-wrap {
        position: relative;
        margin-bottom: 28px;
    }
    .ph-wrap.ph-has-image {
        margin-top: calc(-1 * var(--page-top-pad, 52px));
        padding-top: calc(var(--page-top-pad, 52px) + 24px);
        padding-bottom: 44px;
        margin-bottom: 40px;
    }
    /* Full-bleed background via pseudo — no container break */
    .ph-bg {
        position: absolute;
        top: 0; bottom: 0;
        left: 50%; width: 100vw;
        transform: translateX(-50%);
        background: var(--ph-image) center/cover no-repeat;
        z-index: 0;
        pointer-events: none;
    }
    .ph-overlay {
        position: absolute;
        top: 0; bottom: 0;
        left: 50%; width: 100vw;
        transform: translateX(-50%);
        background:
            linear-gradient(to right, rgba(4,1,1,.88) 0%, rgba(4,1,1,.52) 55%, rgba(4,1,1,.14) 100%),
            linear-gradient(to top, rgba(4,1,1,.92) 0%, transparent 58%);
        z-index: 0;
        pointer-events: none;
    }
    .ph-inner {
        position: relative;
        z-index: 1;
        max-width: 680px;
    }
    .ph-kicker {
        color: var(--orange);
        font-size: 11px; font-weight: 900;
        letter-spacing: .16em; text-transform: uppercase;
        margin-bottom: 4px;
    }
    .ph-has-image .ph-kicker { color: rgba(255,255,255,.75); }
    .ph-title {
        font-family: var(--font-display);
        font-size: clamp(44px, 7vw, 76px);
        line-height: .9; letter-spacing: .02em;
        margin: 8px 0 0; color: #fff;
    }
    .ph-title span { color: var(--orange); }
    .ph-subtitle {
        color: var(--muted);
        font-size: 15px; line-height: 1.55;
        margin: 10px 0 0; font-weight: 700;
    }
    .ph-has-image .ph-subtitle { color: rgba(255,255,255,.65); }
    .ph-cta { margin-top: 18px; display: inline-flex; }
    @media (max-width: 680px) {
        .ph-wrap.ph-has-image { padding-top: calc(var(--page-top-pad, 40px) + 16px); padding-bottom: 28px; }
        .ph-title { font-size: 40px; }
    }
</style>
@endonce
