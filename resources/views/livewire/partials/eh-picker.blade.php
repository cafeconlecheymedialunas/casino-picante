@php
$titleField  = $itemFields['title_field'] ?? 'title';
$metaField   = $itemFields['meta_field'] ?? null;
$metaPrefix  = $itemFields['meta_prefix'] ?? '';
$metaFormat  = $itemFields['meta_format'] ?? 'text';
$imageField  = $itemFields['image_field'] ?? null;
$totalCount  = count($items);
@endphp

<style>
.ehp-row { display:flex; align-items:center; gap:8px; padding:7px 10px; border-radius:7px; cursor:pointer; border:1px solid var(--line); background:rgba(255,255,255,.02); transition:background .12s,border-color .12s; }
.ehp-row:hover { background:rgba(255,255,255,.05); border-color:var(--line-2); }
.ehp-row.is-selected { background:rgba(255,106,26,.1); border-color:rgba(255,106,26,.35); }
.ehp-dot { width:16px; height:16px; border-radius:999px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:8px; border:1px solid var(--line-2); background:transparent; color:transparent; transition:all .12s; }
.ehp-row.is-selected .ehp-dot { background:var(--orange); border-color:var(--orange); color:#190702; }
.ehp-sel-row { display:flex; align-items:center; gap:6px; padding:6px 8px; border-radius:7px; background:rgba(255,106,26,.07); border:1px solid rgba(255,106,26,.18); }
.ehp-btn { width:22px; height:22px; border-radius:5px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:9px; transition:all .12s; flex-shrink:0; }
.ehp-btn:hover { border-color:var(--orange); color:var(--orange); }
.ehp-btn:disabled { opacity:.2; pointer-events:none; }
.ehp-btn.del { border-color:rgba(255,71,87,.3); background:rgba(255,71,87,.06); color:#ff4757; }
.ehp-search { width:100%; box-sizing:border-box; background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:7px 28px 7px 28px; color:var(--white); font-size:12px; outline:none; transition:border-color .15s; }
.ehp-search:focus { border-color:var(--orange); box-shadow:0 0 0 2px rgba(255,106,26,.1); }
.ehp-pill { font-size:10px; font-weight:700; padding:4px 9px; border-radius:999px; cursor:pointer; white-space:nowrap; transition:all .15s; border:1px solid var(--line-2); background:transparent; color:var(--muted-2); }
.ehp-pill.active { background:rgba(255,106,26,.2); border-color:var(--orange); color:var(--orange); }
[data-dashboard-theme="light"] .ehp-row { background:#fff; border-color:var(--line); }
[data-dashboard-theme="light"] .ehp-row.is-selected { background:rgba(255,106,26,.08); }
[data-dashboard-theme="light"] .ehp-search { background:#fff; color:var(--white); }
</style>

@if($totalCount === 0)
    <div class="eh-empty">{{ $emptyMsg }}</div>
@else
<div class="eh-picker-grid">

    {{-- COLUMNA IZQ: Buscador + Lista disponibles --}}
    <div>
        <div style="display:flex;gap:8px;margin-bottom:8px;flex-wrap:wrap;align-items:center;">
            <div style="position:relative;flex:1;min-width:160px;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--muted-2);font-size:10px;pointer-events:none;"></i>
                <input type="text" x-model="{{ $searchModel }}" placeholder="Buscar..." class="ehp-search">
                <template x-if="{{ $searchModel }}.length > 0">
                    <button @click="{{ $searchModel }} = ''" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted-2);cursor:pointer;font-size:11px;padding:0;line-height:1;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </template>
            </div>
            <div style="display:flex;gap:4px;flex-shrink:0;">
                <button class="ehp-pill" :class="{{ $filterModel }} === 'all' ? 'active' : ''" @click="{{ $filterModel }} = 'all'">Todos</button>
                <button class="ehp-pill" :class="{{ $filterModel }} === 'oficial' ? 'active' : ''" @click="{{ $filterModel }} = 'oficial'">★ Oficial</button>
                <button class="ehp-pill" :class="{{ $filterModel }} === 'other' ? 'active' : ''" @click="{{ $filterModel }} = 'other'">Otros</button>
            </div>
        </div>

        <div style="max-height:320px;overflow-y:auto;display:flex;flex-direction:column;gap:3px;padding-right:2px;">
            @foreach($items as $item)
            @php
                $id         = (string) $item['id'];
                $title      = $item[$titleField] ?? '—';
                $vendor     = $item['vendor'] ?? null;
                $vendorName = is_array($vendor) ? ($vendor['name'] ?? '') : '';
                $isOficial  = is_array($vendor) && !empty($vendor['is_direct']);
                $metaVal    = '';
                if ($metaField && isset($item[$metaField]) && $item[$metaField]) {
                    $raw = $item[$metaField];
                    $metaVal = $metaFormat === 'date'
                        ? $metaPrefix . \Carbon\Carbon::parse($raw)->format('d/m/Y')
                        : $metaPrefix . $raw;
                }
            @endphp
            <div
                x-show="matchesSearch('{{ addslashes($title) }}', '{{ addslashes($vendorName) }}', {{ $searchModel }}) && matchesFilter({{ $isOficial ? 'true' : 'false' }}, {{ $filterModel }})"
                class="ehp-row"
                :class="{{ $selectedModel }}.includes('{{ $id }}') ? 'is-selected' : ''"
                @click="{{ $toggleFn }}('{{ $id }}')"
            >
                <div class="ehp-dot"><i class="fa-solid fa-check"></i></div>

                @if($imageField && !empty($item[$imageField]))
                <img src="{{ asset('storage/' . $item[$imageField]) }}" style="width:36px;height:24px;border-radius:4px;object-fit:cover;flex-shrink:0;">
                @endif

                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $title }}</div>
                    <div style="display:flex;align-items:center;gap:5px;margin-top:1px;flex-wrap:wrap;">
                        @if($metaVal)
                        <span style="font-size:10px;color:var(--muted-2);">{{ $metaVal }}</span>
                        @endif
                        @if($isOficial)
                        <span style="font-size:9px;font-weight:800;padding:0 5px;border-radius:999px;background:rgba(255,106,26,.15);color:var(--orange);border:1px solid rgba(255,106,26,.2);">OFICIAL</span>
                        @else
                        <span style="font-size:10px;color:var(--muted-2);">{{ $vendorName }}</span>
                        @endif
                    </div>
                </div>

                <template x-if="{{ $selectedModel }}.includes('{{ $id }}')">
                    <span style="font-size:10px;font-weight:800;color:var(--orange);flex-shrink:0;" x-text="'#' + {{ $orderFn }}('{{ $id }}')"></span>
                </template>
            </div>
            @endforeach
        </div>
    </div>

    {{-- COLUMNA DER: Seleccionados --}}
    <div>
        <div style="font-size:10px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">
            <span>Seleccionados</span>
            <span style="color:var(--orange)" x-text="{{ $selectedModel }}.length + '/{{ $totalCount }}'"></span>
        </div>

        <template x-if="{{ $selectedModel }}.length === 0">
            <div style="border:1px dashed var(--line-2);border-radius:8px;padding:24px 12px;text-align:center;color:var(--muted-2);font-size:11px;line-height:1.5;">
                <i class="fa-solid fa-hand-pointer" style="display:block;font-size:20px;margin-bottom:6px;opacity:.25;"></i>
                Hacé click en items<br>de la izquierda
            </div>
        </template>

        <template x-if="{{ $selectedModel }}.length > 0">
            <div style="display:flex;flex-direction:column;gap:4px;max-height:320px;overflow-y:auto;">
                @foreach($items as $item)
                @php
                    $id         = (string) $item['id'];
                    $title      = $item[$titleField] ?? '—';
                    $vendor     = $item['vendor'] ?? null;
                    $vendorName = is_array($vendor) ? ($vendor['name'] ?? '') : '';
                    $isOficial  = is_array($vendor) && !empty($vendor['is_direct']);
                @endphp
                <template x-if="{{ $selectedModel }}.includes('{{ $id }}')">
                    <div class="ehp-sel-row">
                        <span style="font-size:10px;font-weight:900;color:var(--orange);min-width:16px;text-align:center;flex-shrink:0;" x-text="{{ $orderFn }}('{{ $id }}') + '.'"></span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:11px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $title }}</div>
                            @if($isOficial)
                            <span style="font-size:9px;color:var(--orange);font-weight:700;">OFICIAL</span>
                            @elseif($vendorName)
                            <span style="font-size:9px;color:var(--muted-2);">{{ $vendorName }}</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:2px;flex-shrink:0;">
                            <button class="ehp-btn" @click.stop="{{ $moveUpFn }}('{{ $id }}')" :disabled="{{ $selectedModel }}.indexOf('{{ $id }}') === 0"><i class="fa-solid fa-arrow-up"></i></button>
                            <button class="ehp-btn" @click.stop="{{ $moveDownFn }}('{{ $id }}')" :disabled="{{ $selectedModel }}.indexOf('{{ $id }}') === {{ $selectedModel }}.length - 1"><i class="fa-solid fa-arrow-down"></i></button>
                            <button class="ehp-btn del" @click.stop="{{ $toggleFn }}('{{ $id }}')"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                </template>
                @endforeach
            </div>
        </template>
    </div>

</div>
@endif
