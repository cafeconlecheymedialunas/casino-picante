@push('styles')
<style>
    .lines-page { padding: 52px 0 64px; --page-top-pad: 52px; }
    .lines-tabs-wrap { margin-bottom: 28px; border-bottom: 1px solid rgba(255,255,255,.09); }
    .lines-tabs { display: flex; gap: 4px; flex-wrap:wrap; }
    .lines-tab-btn { position: relative; display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 22px; border: none; background: none; color: rgba(255,255,255,.46); font-size: 13px; font-weight: 800; cursor: pointer; font-family: var(--font-body); text-transform: uppercase; letter-spacing: .06em; border-radius: 8px 8px 0 0; transition: color .15s, background .15s; }
    .lines-tab-btn:hover { color: rgba(255,255,255,.78); background: rgba(255,255,255,.03); }
    .lines-tab-btn.active { color: #fff; background: rgba(255,255,255,.04); }
    .lines-tab-btn.active::after { content: ""; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: var(--orange); border-radius: 2px 2px 0 0; }
    .lines-tab-btn .tab-count { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px; padding: 0 6px; border-radius: 999px; font-size: 10px; font-weight: 900; background: rgba(255,255,255,.07); color: rgba(255,255,255,.44); }
    .lines-tab-btn.active .tab-count { background: rgba(255,106,26,.18); color: var(--orange); }
    .public-line-card { overflow:hidden; border:1px solid var(--line-warm); border-radius:var(--r-lg); background:linear-gradient(180deg,#170b0b,#0b0505); box-shadow:0 20px 52px rgba(0,0,0,.34); min-width:0; position:relative; }
    .public-line-card::before { content:""; position:absolute; top:-34px; right:-34px; width:130px; height:130px; border-radius:999px; background:radial-gradient(circle, rgba(255,106,26,.38), transparent 70%); pointer-events:none; z-index:1; }
    .public-line-cover { height:158px; position:relative; background:radial-gradient(90% 90% at 70% 0%, rgba(255,106,26,.36), transparent 70%), #130807; }
    .public-line-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    .public-line-avatar { position:absolute; left:18px; bottom:-30px; width:70px; height:70px; border-radius:18px; border:3px solid #100707; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; font-family:var(--font-display); font-size:30px; color:#160604; z-index:2; }
    .public-line-avatar img { width:100%; height:100%; object-fit:cover; }
    .line-vendor-badge { position:absolute; top:10px; right:10px; z-index:3; display:inline-flex; align-items:center; gap:6px; padding:4px 10px 4px 4px; border-radius:999px; background:rgba(0,0,0,.60); border:1px solid rgba(255,106,26,.35); backdrop-filter:blur(6px); color:#fff; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; text-decoration:none; }
    .line-vendor-badge-logo { width:22px; height:22px; border-radius:999px; object-fit:cover; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; font-size:9px; font-weight:900; color:#160604; font-family:var(--font-display); }
    .line-vendor-badge-logo img { width:100%; height:100%; object-fit:cover; }
    .public-line-body { padding:42px 18px 18px; position:relative; z-index:2; }
    .public-line-name h2 { margin:0; font-family:var(--font-display); font-size:34px; line-height:.95; letter-spacing:.02em; }
    .public-line-meta { margin-top:8px; color:var(--muted); font-size:13px; line-height:1.45; }
    .public-line-rating { margin-top:12px; }
    .line-platforms-preview { margin-top:12px; color:var(--muted-2); font-size:11px; font-weight:800; }
    .line-channel-list { margin-top:14px; }
    .line-card-actions { display:flex; gap:8px; margin-top:16px; flex-wrap:wrap; }
    .line-card-actions .fe-btn { flex:1; min-width:max-content; }
    .public-lines-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .lines-empty { margin-top: 28px; }
    @media (max-width: 980px) { .public-lines-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 620px) { .lines-page { padding-top: 34px; } .lines-title { font-size: 44px; } .public-lines-grid { grid-template-columns: 1fr; } .lines-tab-btn { padding: 0 14px; font-size: 11px; } }
</style>
@endpush

<div>
    <section class="lines-page">
        <div class="fe-shell">
            <x-page-header
                :section="$pageSection"
                :breadcrumbs="isset($publicVendor)
                    ? [['label'=>'Cajeros','url'=>route('frontend.cajeros')],['label'=>$publicVendor->name,'url'=>route('frontend.cajero.inicio',$publicVendor)],['label'=>'Lineas']]
                    : [['label'=>'Inicio','url'=>route('frontend.home')],['label'=>'Lineas']]"
            />

            @if($showTabs)
                <div class="lines-tabs-wrap">
                    <div class="lines-tabs">
                        @isset($tabs)
                            @foreach($tabs as $tabItem)
                                <button type="button" class="lines-tab-btn {{ $tab === $tabItem['key'] ? 'active' : '' }}" wire:click="$set('tab', '{{ $tabItem['key'] }}')">
                                    <i class="fa-solid fa-layer-group" style="font-size:11px"></i>
                                    {{ $tabItem['title'] }}
                                    <span class="tab-count">{{ count($tabItem['item_ids'] ?? []) }}</span>
                                </button>
                            @endforeach
                        @else
                            <button type="button" class="lines-tab-btn {{ $tab === 'directas' ? 'active' : '' }}" wire:click="$set('tab', 'directas')">
                                <i class="fa-solid fa-crown" style="font-size:11px"></i>
                                Admin General
                                <span class="tab-count">{{ $directLines->count() }}</span>
                            </button>
                            <button type="button" class="lines-tab-btn {{ $tab === 'cajeros' ? 'active' : '' }}" wire:click="$set('tab', 'cajeros')">
                                <i class="fa-solid fa-user-tie" style="font-size:11px"></i>
                                Cajeros
                                <span class="tab-count">{{ $cajeroLines->count() }}</span>
                            </button>
                        @endisset
                    </div>
                </div>
                @if(!empty($activeTab['subtitle']))
                    <p class="fe-subtitle" style="margin:-12px 0 22px;max-width:640px;">{{ $activeTab['subtitle'] }}</p>
                @endif
            @endif

            @if($lines->count())
                <div class="public-lines-grid">
                    @foreach($lines as $line)
                        @include('frontend.components.line-card', ['line' => $line])
                    @endforeach
                </div>
            @else
                <div class="empty-panel lines-empty">
                    @if(isset($activeTab))
                        No hay lineas seleccionadas para esta tab.
                    @elseif($showTabs && $tab === 'directas')
                        No hay lineas del admin general publicadas por ahora.
                    @elseif($showTabs && $tab === 'cajeros')
                        No hay lineas de cajeros activas publicadas por ahora.
                    @else
                        No hay lineas activas publicadas por ahora.
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
