@props([
    'section'     => null,
    'vendorCount' => 0,
])

@php
    $hasImage = !empty($section?->image);
    $imageUrl = $hasImage
        ? (\Illuminate\Support\Str::startsWith($section->image, ['http://', 'https://', '/storage/'])
            ? $section->image
            : asset('storage/'.$section->image))
        : null;
@endphp

<div class="phc-wrap {{ $hasImage ? 'phc-has-image' : '' }}"
    @if($hasImage) style="--phc-image: url('{{ $imageUrl }}')" @endif
>
    @if($hasImage)
        <div class="phc-bg"></div>
        <div class="phc-overlay"></div>
    @endif

    <div class="phc-inner">
        @include('frontend.components.breadcrumbs', [
            'items' => [
                ['label' => 'Inicio', 'url' => route('frontend.home')],
                ['label' => 'Cajeros'],
            ],
        ])

        <div class="phc-grid">
            <div class="phc-text">
                @if($section?->kicker)
                    <div class="ph-kicker {{ $hasImage ? 'ph-kicker--light' : '' }}">{{ $section->kicker }}</div>
                @endif
                @if($section?->title || $section?->highlight)
                    <h1 class="ph-title">
                        {{ $section?->title }}
                        @if($section?->highlight)<span>{{ $section->highlight }}</span>@endif
                    </h1>
                @endif
                @if($section?->subtitle)
                    <p class="ph-subtitle {{ $hasImage ? 'ph-subtitle--light' : '' }}">{{ $section->subtitle }}</p>
                @endif
                @if(!empty($section?->action_text) && !empty($section?->action_url))
                    <a href="{{ $section->action_url }}" wire:navigate class="fe-btn ghost ph-cta">{{ $section->action_text }}</a>
                @endif
            </div>

            <div class="phc-stat">
                <strong>{{ $vendorCount }}</strong>
                <span>Cajeros activos</span>
                @if($section?->content)
                    <em>{{ $section->content }}</em>
                @endif
            </div>
        </div>
    </div>
</div>

@once
<style>
    .phc-wrap {
        position: relative;
        margin-bottom: 28px;
    }
    .phc-has-image {
        margin-top: calc(-1 * var(--page-top-pad, 76px));
        padding-top: calc(var(--page-top-pad, 76px) + 24px);
        padding-bottom: 44px;
        margin-bottom: 40px;
    }
    .phc-bg {
        position: absolute; top: 0; bottom: 0;
        left: 50%; width: 100vw;
        transform: translateX(-50%);
        background: var(--phc-image) center/cover no-repeat;
        z-index: 0; pointer-events: none;
    }
    .phc-overlay {
        position: absolute; top: 0; bottom: 0;
        left: 50%; width: 100vw;
        transform: translateX(-50%);
        background:
            linear-gradient(to right, rgba(4,1,1,.88) 0%, rgba(4,1,1,.52) 55%, rgba(4,1,1,.14) 100%),
            linear-gradient(to top, rgba(4,1,1,.92) 0%, transparent 58%);
        z-index: 0; pointer-events: none;
    }
    .phc-inner { position: relative; z-index: 1; }
    /* shared ph-* styles */
    .ph-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; margin-bottom:4px; }
    .ph-kicker--light { color:rgba(255,255,255,.75); }
    .ph-title { font-family:var(--font-display); font-size:clamp(44px,7vw,76px); line-height:.9; letter-spacing:.02em; margin:8px 0 0; color:#fff; }
    .ph-title span { color:var(--orange); }
    .ph-subtitle { color:var(--muted); font-size:15px; line-height:1.55; margin:10px 0 0; font-weight:700; }
    .ph-subtitle--light { color:rgba(255,255,255,.65); }
    .ph-cta { margin-top:18px; display:inline-flex; }
    .phc-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 260px;
        gap: 28px;
        align-items: end;
        margin-top: 8px;
    }
    .phc-text { min-width: 0; }
    .phc-stat {
        min-height: 160px;
        border: 1px solid rgba(255,106,26,.28);
        border-radius: 10px;
        background: radial-gradient(110% 100% at 100% 0%, rgba(255,106,26,.22), transparent 60%), rgba(255,255,255,.05);
        padding: 22px;
        backdrop-filter: blur(6px);
    }
    .phc-has-image .phc-stat {
        background: radial-gradient(110% 100% at 100% 0%, rgba(255,106,26,.28), transparent 60%), rgba(0,0,0,.32);
    }
    .phc-stat strong {
        display: block;
        color: var(--orange);
        font-family: var(--font-display);
        font-size: 68px; line-height: .85;
    }
    .phc-stat span {
        display: block;
        color: #fff;
        font-size: 13px; font-weight: 900;
        text-transform: uppercase; margin-top: 10px;
    }
    .phc-stat em {
        display: block;
        color: rgba(255,255,255,.5);
        font-size: 12px; line-height: 1.4;
        margin-top: 8px; font-style: normal;
    }
    .ph-kicker--light { color: rgba(255,255,255,.75); }
    .ph-subtitle--light { color: rgba(255,255,255,.65); }
    @media (max-width: 860px) {
        .phc-grid { grid-template-columns: 1fr; }
        .phc-stat { max-width: 280px; }
    }
</style>
@endonce
