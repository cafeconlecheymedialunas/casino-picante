@props([
    'section'    => null,
    'vendor'     => null,
    'searchModel' => 'search',
])

@php
    $hasImage = !empty($section?->image);
    $imageUrl = $hasImage
        ? (\Illuminate\Support\Str::startsWith($section->image, ['http://', 'https://', '/storage/'])
            ? $section->image
            : asset('storage/'.$section->image))
        : null;
    $breadcrumbs = $vendor
        ? [
            ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
            ['label' => $vendor->name, 'url' => route('frontend.cajero.inicio', $vendor)],
            ['label' => 'Novedades'],
          ]
        : [
            ['label' => 'Inicio', 'url' => route('frontend.home')],
            ['label' => 'Novedades'],
          ];
@endphp

<div class="phn-wrap {{ $hasImage ? 'phn-has-image' : '' }}"
    @if($hasImage) style="--phn-image: url('{{ $imageUrl }}')" @endif
>
    @if($hasImage)
        <div class="phn-bg"></div>
        <div class="phn-overlay"></div>
    @endif

    <div class="phn-inner">
        @include('frontend.components.breadcrumbs', ['items' => $breadcrumbs])

        <div class="phn-head">
            <div class="phn-text">
                @if(!$vendor && $section?->kicker)
                    <div class="ph-kicker {{ $hasImage ? 'ph-kicker--light' : '' }}">{{ $section->kicker }}</div>
                @endif
                @if(!$vendor && ($section?->title || $section?->highlight))
                    <h1 class="ph-title">
                        {{ $section?->title }}
                        @if($section?->highlight)<span>{{ $section->highlight }}</span>@endif
                    </h1>
                @endif
                @if(!$vendor && $section?->subtitle)
                    <p class="ph-subtitle {{ $hasImage ? 'ph-subtitle--light' : '' }}" style="max-width:560px">{{ $section->subtitle }}</p>
                @endif
            </div>

            <div class="phn-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    wire:model.live.debounce.300ms="{{ $searchModel }}"
                    class="phn-search"
                    type="search"
                    placeholder="Buscar novedades..."
                    aria-label="Buscar novedades"
                >
            </div>
        </div>
    </div>
</div>

@once
<style>
    .phn-wrap {
        position: relative;
        margin-bottom: 28px;
    }
    .phn-has-image {
        margin-top: calc(-1 * var(--page-top-pad, 52px));
        padding-top: calc(var(--page-top-pad, 52px) + 24px);
        padding-bottom: 44px;
        margin-bottom: 40px;
    }
    .phn-bg {
        position: absolute; top: 0; bottom: 0;
        left: 50%; width: 100vw;
        transform: translateX(-50%);
        background: var(--phn-image) center/cover no-repeat;
        z-index: 0; pointer-events: none;
    }
    .phn-overlay {
        position: absolute; top: 0; bottom: 0;
        left: 50%; width: 100vw;
        transform: translateX(-50%);
        background:
            linear-gradient(to right, rgba(4,1,1,.88) 0%, rgba(4,1,1,.52) 55%, rgba(4,1,1,.14) 100%),
            linear-gradient(to top, rgba(4,1,1,.92) 0%, transparent 58%);
        z-index: 0; pointer-events: none;
    }
    .phn-inner { position: relative; z-index: 1; }
    /* shared ph-* styles */
    .ph-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; margin-bottom:4px; }
    .ph-kicker--light { color:rgba(255,255,255,.75); }
    .ph-title { font-family:var(--font-display); font-size:clamp(44px,7vw,76px); line-height:.9; letter-spacing:.02em; margin:8px 0 0; color:#fff; }
    .ph-title span { color:var(--orange); }
    .ph-subtitle { color:var(--muted); font-size:15px; line-height:1.55; margin:10px 0 0; font-weight:700; }
    .ph-subtitle--light { color:rgba(255,255,255,.65); }
    .ph-cta { margin-top:18px; display:inline-flex; }
    .phn-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .phn-text { min-width: 0; flex: 1; }
    .phn-search-wrap {
        position: relative;
        flex-shrink: 0;
        width: 280px;
    }
    .phn-search-wrap i {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,.35); font-size: 12px;
        pointer-events: none;
    }
    .phn-search {
        height: 42px; width: 100%;
        border: 1px solid var(--line-2); border-radius: 999px;
        background: rgba(255,255,255,.06); color: #fff;
        outline: none; padding: 0 16px 0 38px;
        font: 700 13px var(--font-body);
        transition: border-color .15s, box-shadow .15s;
    }
    .phn-has-image .phn-search {
        background: rgba(0,0,0,.3);
        border-color: rgba(255,255,255,.2);
        backdrop-filter: blur(4px);
    }
    .phn-search:focus { border-color: var(--orange); box-shadow: 0 0 0 4px rgba(255,106,26,.12); }
    .phn-search::placeholder { color: rgba(255,255,255,.3); }
    @media (max-width: 680px) {
        .phn-head { flex-direction: column; align-items: flex-start; gap: 14px; }
        .phn-search-wrap { width: 100%; }
    }
</style>
@endonce
