<div class="page-container">
    <style>
        .eh-page { display:flex; flex-direction:column; gap:28px; min-width:0; max-width:100%; overflow-x:clip; }
        .eh-section { border:1px solid var(--line); border-radius:14px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; }
        .eh-section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-bottom:1px solid var(--line); }
        .eh-section-title { font-family:var(--font-display); font-size:20px; letter-spacing:.04em; display:flex; align-items:center; gap:10px; min-width:0; }
        .eh-section-title i { color:var(--orange); font-size:16px; }
        .eh-section-badge { font-size:10px; font-weight:800; color:var(--orange); background:rgba(255,106,26,.12); padding:3px 9px; border-radius:999px; }
        .eh-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr)); gap:10px; padding:16px 20px; min-width:0; }
        .eh-card { min-width:0; border:1px solid var(--line); border-radius:10px; background:rgba(255,255,255,.02); padding:12px; cursor:pointer; transition:all .18s; position:relative; }
        .eh-card:hover { border-color:var(--orange); background:rgba(255,106,26,.05); }
        .eh-card.selected { border-color:var(--orange); background:rgba(255,106,26,.1); }
        .eh-card-check { position:absolute; top:8px; right:10px; width:22px; height:22px; border-radius:999px; background:var(--orange); color:#190702; font-size:11px; font-weight:900; display:flex; align-items:center; justify-content:center; }
        .eh-card-img { width:100%; aspect-ratio:851/315; border-radius:6px; background:rgba(255,255,255,.04); object-fit:cover; display:block; margin-bottom:10px; }
        .eh-card-img.placeholder { display:flex; align-items:center; justify-content:center; color:var(--muted-2); font-size:28px; }
        .eh-card-title { font-weight:800; font-size:13px; margin-bottom:4px; overflow-wrap:anywhere; }
        .eh-card-meta { font-size:11px; color:var(--muted-2); display:flex; align-items:center; gap:8px; min-width:0; }
        .eh-bonus-value { font-family:var(--font-display); font-size:24px; color:var(--green,var(--good)); }
        .eh-bonus-label { font-size:10px; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; margin-top:4px; }
        .eh-empty { padding:40px 20px; text-align:center; color:var(--muted-2); font-size:13px; }
        .eh-counter { font-size:12px; color:var(--muted); }
        .eh-counter .current { color:var(--orange); font-weight:800; }
        .flash-error { border:1px solid rgba(255,71,87,.35); background:rgba(255,71,87,.12); color:#ff4757; border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; margin-bottom:16px; }
        .flash-success { border:1px solid rgba(37,196,107,.35); background:rgba(37,196,107,.12); color:var(--good); border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; margin-bottom:16px; }
        .eh-repeater { padding:12px 16px; display:flex; flex-direction:column; gap:8px; }
        .eh-repeater-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:10px; background:rgba(255,255,255,.025); border:1px solid var(--line); transition:border-color .15s, background .15s; }
        .eh-repeater-item:hover { border-color:var(--line-2); background:rgba(255,255,255,.04); }
        .eh-repeater-item.new-row { background:rgba(255,106,26,.04); border-color:rgba(255,106,26,.25); }
        .eh-repeater-item .drag-handle { width:20px; flex-shrink:0; display:flex; flex-direction:column; gap:2px; cursor:grab; opacity:.4; }
        .eh-repeater-item .drag-handle span { display:block; height:2px; border-radius:2px; background:var(--muted-2); }
        .eh-repeater-thumb { width:72px; height:36px; border-radius:6px; object-fit:cover; flex-shrink:0; background:rgba(255,255,255,.04); }
        .eh-repeater-body { flex:1; min-width:0; display:flex; flex-direction:column; }
        .eh-repeater-title { font-weight:700; font-size:12px; color:var(--white); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .eh-repeater-sub { font-size:10px; color:var(--muted-2); margin-top:1px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .eh-repeater-actions { display:flex; gap:3px; flex-shrink:0; }
        .eh-repeater-actions button { width:26px; height:26px; border-radius:6px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:10px; transition:all .15s; }
        .eh-repeater-actions button:hover { border-color:var(--orange); color:var(--orange); background:rgba(255,106,26,.08); }
        .eh-repeater-actions button:disabled { opacity:.25; cursor:default; pointer-events:none; }
        .eh-repeater-actions .btn-visible { color:var(--good); border-color:rgba(37,196,107,.3); background:rgba(37,196,107,.08); font-size:9px; width:auto; padding:0 10px; gap:4px; font-weight:700; }
        .eh-repeater-actions .btn-hidden { font-size:9px; width:auto; padding:0 10px; gap:4px; font-weight:700; }
        .eh-repeater-actions .btn-del { color:#ff4757; }
        .eh-repeater-actions .btn-del:hover { border-color:rgba(255,71,87,.4); background:rgba(255,71,87,.12); color:#ff4757; }
        .eh-repeater-addbtn { display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1px dashed var(--line-2); background:transparent; color:var(--muted-2); cursor:pointer; font-size:11px; font-weight:700; transition:all .15s; align-self:flex-start; }
        .eh-repeater-addbtn:hover { border-color:var(--orange); color:var(--orange); background:rgba(255,106,26,.04); }
        .eh-repeater-field { display:flex; flex-direction:column; gap:3px; min-width:0; }
        .eh-repeater-field label { font-size:9px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }
        .eh-repeater-field input { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:6px; padding:7px 10px; color:var(--white); font-size:12px; outline:none; }
        .eh-repeater-field input:focus { border-color:var(--orange); box-shadow:0 0 0 2px rgba(255,106,26,.1); }
        .eh-card-icon { color:var(--orange); font-size:24px; margin-bottom:8px; }
        @media (max-width: 768px) {
            .page-container:has(.eh-page) { overflow-x:hidden; }
            .eh-page { gap:16px; }
            .eh-section { border-radius:10px; }
            .eh-section-head { flex-direction:column; align-items:stretch; padding:14px; }
            .eh-section-title { flex-wrap:wrap; font-size:19px; line-height:1.1; }
            .eh-section-badge { width:max-content; max-width:100%; }
            .eh-counter { font-size:11px; }
            .eh-grid { grid-template-columns:1fr; padding:14px; }
            .eh-repeater { padding:12px; }
            .eh-repeater-item { align-items:flex-start; gap:10px; padding:12px; }
            .eh-repeater-thumb { width:64px; height:36px; }
            .eh-repeater-actions { width:100%; flex-wrap:wrap; justify-content:flex-end; }
            .eh-repeater-actions button { min-width:30px; height:30px; }
            .eh-repeater-actions .btn-visible,
            .eh-repeater-actions .btn-hidden { flex:1 1 110px; justify-content:center; }
            .eh-repeater-addbtn { width:100%; justify-content:center; }
            .eh-repeater-item.new-row { display:grid; grid-template-columns:1fr; }
            .eh-repeater-item.new-row .btn-primary { width:100%; justify-content:center; }
        }
        @media (max-width: 520px) {
            .eh-repeater-item:not(.new-row) { display:grid; grid-template-columns:20px 56px minmax(0,1fr); }
            .eh-repeater-actions { grid-column:1 / -1; }
            .eh-card-meta { flex-direction:column; align-items:flex-start; }
        }
    </style>

    @section('header')
    <x-livewire.components.page-header title="EDITAR HOME" subtitle="Configura las secciones visibles en la pagina principal" />
@endsection

    @if(session()->has('message_error'))
        <div class="flash-error">{{ session('message_error') }}</div>
    @endif

    @if(session()->has('message_success'))
        <div class="flash-success">{{ session('message_success') }}</div>
    @endif

    <div class="eh-page">

        @php
        $orderedSections = [
            'carousel' => ['label' => '1. HERO / CARRUSEL', 'icon' => 'fa-images'],
            'como-empezar' => ['label' => '2. ¿CÓMO EMPEZAR? (3 PASOS)', 'icon' => 'fa-play'],
            'lineas' => ['label' => '3. LÍNEAS DE ATENCIÓN', 'icon' => 'fa-headset'],
            'sorteo' => ['label' => '4. PRÓXIMOS SORTEOS', 'icon' => 'fa-calendar-days'],
            'nosotros' => ['label' => '5. SOBRE NOSOTROS', 'icon' => 'fa-users'],
            'bonos' => ['label' => '6. BONOS DISPONIBLES', 'icon' => 'fa-gift'],
            'blog' => ['label' => '7. BLOG / NOVEDADES', 'icon' => 'fa-newspaper'],
        ];
        @endphp

        @foreach($orderedSections as $key => $meta)
            <div class="eh-section">
                {{-- HEADER DE SECCIÓN --}}
                <div class="eh-section-head">
                    <div class="eh-section-title">
                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                        {{ $meta['label'] }}
                        @if($key !== 'carousel')
                            @if(isset($sections[$key]['enabled']) && !$sections[$key]['enabled'])
                                <span class="eh-section-badge" style="background:#ff4757; color:#fff;">OCULTO</span>
                            @endif
                        @endif
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        @if($key !== 'carousel')
                            <button type="button" wire:click="toggleSectionEnabled('{{ $key }}')" 
                                style="padding:4px 10px;border-radius:6px;border:1px solid var(--line);background:transparent;color:var(--muted-2);font-size:10px;cursor:pointer;">
                                {{ ($sections[$key]['enabled'] ?? true) ? 'Ocultar' : 'Mostrar' }}
                            </button>
                            <button type="button" wire:click="saveSection('{{ $key }}')" 
                                style="padding:4px 10px;border-radius:6px;border:1px solid var(--orange);background:rgba(255,106,26,.1);color:var(--orange);font-size:10px;font-weight:700;cursor:pointer;">
                                <i class="fa-solid fa-save"></i> Guardar
                            </button>
                        @endif
                        
                        @if($key === 'carousel')
                            <div class="eh-counter">
                                Visibles: <span class="current">{{ count($selectedCarousel) }}</span> / 5
                            </div>
                        @elseif($key === 'sorteo')
                            <div class="eh-counter">
                                Seleccionados: <span class="current">{{ count($selectedRafflesUpcoming) }}</span> / 5
                            </div>
                        @elseif($key === 'bonos')
                            <div class="eh-counter">
                                Seleccionados: <span class="current">{{ count($selectedBonuses) }}</span> / 5
                            </div>
                        @elseif($key === 'blog')
                            <div class="eh-counter">
                                Seleccionadas: <span class="current">{{ count($selectedBlogs) }}</span> / 3
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CONTENIDO DE SECCIÓN --}}
                <div style="padding: 16px 20px;">
                    
                    {{-- CAMPOS DE TEXTO COMUNES (Kicker, Title, Highlight, Subtitle) --}}
                    @if($key !== 'carousel')
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px;">
                            <div class="eh-repeater-field">
                                <label>Kicker (mini titulo)</label>
                                <input type="text" wire:model="sections.{{ $key }}.kicker" placeholder="Opcional">
                            </div>
                            <div class="eh-repeater-field">
                                <label>Titulo</label>
                                <input type="text" wire:model="sections.{{ $key }}.title" placeholder="Opcional">
                            </div>
                            <div class="eh-repeater-field">
                                <label>Highlight (palabra destacada)</label>
                                <input type="text" wire:model="sections.{{ $key }}.highlight" placeholder="Opcional">
                            </div>
                            <div class="eh-repeater-field">
                                <label>Subtitulo</label>
                                <input type="text" wire:model="sections.{{ $key }}.subtitle" placeholder="Opcional">
                            </div>

                            @if($key === 'nosotros')
                            <div class="eh-repeater-field" style="grid-column:1 / -1;">
                                <label>Contenido Principal (Sobre Nosotros)</label>
                                <textarea wire:model="sections.{{ $key }}.subtitle" rows="2" style="background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:6px;padding:7px 10px;color:var(--white);font-size:12px;outline:none;width:100%;resize:vertical;" placeholder="Texto descriptivo..."></textarea>
                            </div>
                            @endif

                            @if($key === 'sorteo')
                            <div class="eh-repeater-field">
                                <label>Tipo de Sorteo</label>
                                <select wire:model="sections.{{ $key }}.raffle_type" style="background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:6px;padding:7px 10px;color:var(--white);font-size:12px;outline:none;">
                                    <option value="">Todos</option>
                                    <option value="active">Activos</option>
                                    <option value="featured">Destacados</option>
                                </select>
                            </div>
                            <div class="eh-repeater-field">
                                <label>IDs de Sorteos (separados por coma)</label>
                                <input type="text" wire:model="sections.{{ $key }}.raffle_ids" placeholder="1,2,3">
                            </div>
                            @endif

                            @if($key === 'bonos')
                            <div class="eh-repeater-field">
                                <label>Tipo de Bono (Filtro)</label>
                                <select wire:model="sections.{{ $key }}.bonus_type" style="background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:6px;padding:7px 10px;color:var(--white);font-size:12px;outline:none;">
                                    <option value="">Todos</option>
                                    <option value="active">Activos</option>
                                    <option value="featured">Destacados</option>
                                </select>
                            </div>
                            <div class="eh-repeater-field">
                                <label>IDs de Bonos (Filtro manual)</label>
                                <input type="text" wire:model="sections.{{ $key }}.bonus_ids" placeholder="1,2,3">
                            </div>
                            @endif

                            @if($key === 'blog')
                            <div class="eh-repeater-field">
                                <label>Tipo de Post (Filtro)</label>
                                <select wire:model="sections.{{ $key }}.post_type" style="background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:6px;padding:7px 10px;color:var(--white);font-size:12px;outline:none;">
                                    <option value="">Todos</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="eh-repeater-field">
                                <label>IDs de Posts (Filtro manual)</label>
                                <input type="text" wire:model="sections.{{ $key }}.post_ids" placeholder="1,2,3">
                            </div>
                            @endif
                        </div>
                    @endif

                    {{-- CONTENIDO ESPECÍFICO DE CADA SECCIÓN --}}

                    @if($key === 'carousel')
                        <div class="eh-repeater" x-data="{ open: false }" style="padding: 0;">
                            @forelse($carouselItems as $i => $item)
                            <div class="eh-repeater-item">
                                <div class="drag-handle"><span></span><span></span><span></span></div>
                                <img src="{{ $item['image'] }}" class="eh-repeater-thumb" alt="">
                                <div class="eh-repeater-body">
                                    <div class="eh-repeater-title">{{ $item['title'] ?: 'Sin titulo' }}</div>
                                    <div class="eh-repeater-sub">{{ $item['link'] ?: 'Sin enlace' }}</div>
                                </div>
                                <div class="eh-repeater-actions">
                                    <button wire:click="moveCarouselUp({{ $item['id'] }})" title="Subir" {{ $i === 0 ? 'disabled' : '' }}><i class="fa-solid fa-arrow-up"></i></button>
                                    <button wire:click="moveCarouselDown({{ $item['id'] }})" title="Bajar" {{ $i === count($carouselItems) - 1 ? 'disabled' : '' }}><i class="fa-solid fa-arrow-down"></i></button>
                                    <button wire:click="toggleCarousel({{ $item['id'] }})"
                                        class="{{ in_array($item['id'], $selectedCarousel) ? 'btn-visible' : 'btn-hidden' }}"
                                        title="{{ in_array($item['id'], $selectedCarousel) ? 'Ocultar' : 'Mostrar' }}">
                                        <i class="fa-solid {{ in_array($item['id'], $selectedCarousel) ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                        {{ in_array($item['id'], $selectedCarousel) ? 'Visible' : 'Oculto' }}
                                    </button>
                                    <button wire:click="removeCarouselItem({{ $item['id'] }})" wire:confirm="Eliminar esta imagen?" class="btn-del" title="Eliminar"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                            @empty
                            <div style="text-align:center;padding:24px 16px;color:var(--muted-2);font-size:12px;">
                                <i class="fa-solid fa-image" style="font-size:24px;display:block;margin-bottom:8px;opacity:.3"></i>
                                No hay imagenes en el carrusel
                            </div>
                            @endforelse

                            <button type="button" @click="open = !open" class="eh-repeater-addbtn">
                                <i class="fa-solid" :class="open ? 'fa-xmark' : 'fa-plus'"></i>
                                <span x-text="open ? 'Cancelar' : 'Agregar imagen'"></span>
                            </button>

                            <template x-if="open">
                                <div class="eh-repeater-item new-row" style="flex-wrap:wrap;">
                                    <div style="flex:1;min-width:140px;">
                                        <x-upload-image label="" model="newCarouselImage" :value="''" aspect="851/315" hint="Max 5MB">
                                            @error('newCarouselImage') <div style="color:#ff4757;font-size:10px;margin-top:2px;">{{ $message }}</div> @enderror
                                        </x-upload-image>
                                    </div>
                                    <div class="eh-repeater-field" style="flex:1;min-width:100px;">
                                        <label>Titulo</label>
                                        <input type="text" wire:model="newCarouselTitle" placeholder="Opcional">
                                    </div>
                                    <div class="eh-repeater-field" style="flex:1;min-width:100px;">
                                        <label>Link</label>
                                        <input type="text" wire:model="newCarouselLink" placeholder="Opcional">
                                    </div>
                                    <button type="button" wire:click="addCarouselItem" wire:loading.attr="disabled" @click="open = false" class="btn-primary" style="height:30px;padding:0 14px;font-size:11px;white-space:nowrap;">
                                        <i class="fa-solid fa-check"></i> Agregar
                                    </button>
                                </div>
                            </template>
                        </div>
                    @endif

                    @if(in_array($key, ['como-empezar', 'nosotros']))
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="font-size:10px; font-weight:800; color:var(--muted); text-transform:uppercase; margin-bottom: 4px; display: block;">
                                {{ $key === 'como-empezar' ? 'PASOS DINÁMICOS' : 'CARACTERÍSTICAS / BENEFICIOS' }}
                            </label>
                            @if(isset($sections[$key]['repeater_data']))
                                @foreach($sections[$key]['repeater_data'] as $index => $item)
                                <div style="display: grid; grid-template-columns: 1fr 2fr auto; gap: 10px; align-items: start; background: rgba(255,255,255,0.02); padding: 10px; border-radius: 8px; border: 1px solid var(--line);">
                                    <div class="eh-repeater-field">
                                        <label>Título</label>
                                        <input type="text" wire:model="sections.{{ $key }}.repeater_data.{{ $index }}.title" placeholder="Ej: Pedí tu usuario">
                                    </div>
                                    <div class="eh-repeater-field">
                                        <label>Descripción</label>
                                        <input type="text" wire:model="sections.{{ $key }}.repeater_data.{{ $index }}.subtitle" placeholder="Ej: Elegí una línea de atención...">
                                    </div>
                                    <button type="button" wire:click="removeRepeaterItem('{{ $key }}', {{ $index }})" style="margin-top: 18px; color: #ff4757; background: transparent; border: none; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            @endif
                            <button type="button" wire:click="addRepeaterItem('{{ $key }}')" class="eh-repeater-addbtn" style="width: auto;">
                                <i class="fa-solid fa-plus"></i> Agregar {{ $key === 'como-empezar' ? 'Paso' : 'Característica' }}
                            </button>
                        </div>
                    @endif

                    @if($key === 'sorteo')
                        @if(count($raffleItems) > 0)
                        <div class="eh-grid" style="padding: 0;">
                            @foreach($raffleItems as $raffle)
                            <div class="eh-card {{ in_array($raffle['id'], $selectedRafflesUpcoming) ? 'selected' : '' }}"
                                 wire:click="toggleRaffle({{ $raffle['id'] }})">
                                @if(in_array($raffle['id'], $selectedRafflesUpcoming))
                                    <div class="eh-card-check"><i class="fa-solid fa-check"></i></div>
                                    @php $orderIdx = array_search($raffle['id'], $selectedRafflesUpcoming); @endphp
                                    <div style="position: absolute; bottom: 8px; right: 8px; display: flex; gap: 4px; z-index: 5;" onclick="event.stopPropagation()">
                                        <button wire:click.stop="moveItemUp('raffles_upcoming', {{ $raffle['id'] }})" style="background: rgba(0,0,0,0.5); border: 1px solid var(--line); color: white; border-radius: 4px; padding: 2px 6px; font-size: 10px;" {{ $orderIdx === 0 ? 'disabled' : '' }}>↑</button>
                                        <button wire:click.stop="moveItemDown('raffles_upcoming', {{ $raffle['id'] }})" style="background: rgba(0,0,0,0.5); border: 1px solid var(--line); color: white; border-radius: 4px; padding: 2px 6px; font-size: 10px;" {{ $orderIdx === count($selectedRafflesUpcoming)-1 ? 'disabled' : '' }}>↓</button>
                                    </div>
                                @endif
                                <div class="eh-card-title">{{ $raffle['title'] }}</div>
                                <div class="eh-card-meta">
                                    <span>Vence: {{ \Carbon\Carbon::parse($raffle['end_date'])->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="eh-empty">No hay sorteos disponibles. Crea uno en el modulo de <strong>Sorteos</strong>.</div>
                        @endif
                    @endif

                    @if($key === 'bonos')
                        @if(count($bonusItems) > 0)
                        <div class="eh-grid" style="padding: 0;">
                            @foreach($bonusItems as $bonus)
                            <div class="eh-card {{ in_array($bonus['id'], $selectedBonuses) ? 'selected' : '' }}"
                                 wire:click="toggleBonus({{ $bonus['id'] }})">
                                @if(in_array($bonus['id'], $selectedBonuses))
                                    <div class="eh-card-check"><i class="fa-solid fa-check"></i></div>
                                    @php $orderIdx = array_search($bonus['id'], $selectedBonuses); @endphp
                                    <div style="position: absolute; bottom: 8px; right: 8px; display: flex; gap: 4px; z-index: 5;" onclick="event.stopPropagation()">
                                        <button wire:click.stop="moveItemUp('bonuses', {{ $bonus['id'] }})" style="background: rgba(0,0,0,0.5); border: 1px solid var(--line); color: white; border-radius: 4px; padding: 2px 6px; font-size: 10px;" {{ $orderIdx === 0 ? 'disabled' : '' }}>↑</button>
                                        <button wire:click.stop="moveItemDown('bonuses', {{ $bonus['id'] }})" style="background: rgba(0,0,0,0.5); border: 1px solid var(--line); color: white; border-radius: 4px; padding: 2px 6px; font-size: 10px;" {{ $orderIdx === count($selectedBonuses)-1 ? 'disabled' : '' }}>↓</button>
                                    </div>
                                @endif
                                <div class="eh-bonus-value">
                                    @if($bonus['bonus_percent'])
                                        {{ $bonus['bonus_percent'] }}%
                                    @elseif($bonus['bonus_amount'])
                                        ${{ number_format($bonus['bonus_amount'], 2) }}
                                    @else
                                        <i class="fa-solid fa-gift eh-card-icon"></i>
                                    @endif
                                </div>
                                <div class="eh-card-title">{{ $bonus['title'] }}</div>
                                <div class="eh-card-meta">
                                    <span>{{ $bonus['code'] ?? 'Sin codigo' }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="eh-empty">No hay bonos activos disponibles. Crea uno en el modulo de <strong>Bonos</strong>.</div>
                        @endif
                    @endif

                    @if($key === 'blog')
                        @if(count($blogPosts) > 0)
                        <div class="eh-grid" style="padding: 0;">
                            @foreach($blogPosts as $post)
                            <div class="eh-card {{ in_array($post['id'], $selectedBlogs) ? 'selected' : '' }}"
                                 wire:click="toggleBlog({{ $post['id'] }})">
                                @if(in_array($post['id'], $selectedBlogs))
                                    <div class="eh-card-check"><i class="fa-solid fa-check"></i></div>
                                    @php $orderIdx = array_search($post['id'], $selectedBlogs); @endphp
                                    <div style="position: absolute; bottom: 8px; right: 8px; display: flex; gap: 4px; z-index: 5;" onclick="event.stopPropagation()">
                                        <button wire:click.stop="moveItemUp('blog', {{ $post['id'] }})" style="background: rgba(0,0,0,0.5); border: 1px solid var(--line); color: white; border-radius: 4px; padding: 2px 6px; font-size: 10px;" {{ $orderIdx === 0 ? 'disabled' : '' }}>↑</button>
                                        <button wire:click.stop="moveItemDown('blog', {{ $post['id'] }})" style="background: rgba(0,0,0,0.5); border: 1px solid var(--line); color: white; border-radius: 4px; padding: 2px 6px; font-size: 10px;" {{ $orderIdx === count($selectedBlogs)-1 ? 'disabled' : '' }}>↓</button>
                                    </div>
                                @endif
                                @if($post['image'])
                                <img src="{{ asset('storage/' . $post['image']) }}" class="eh-card-img" alt="{{ $post['title'] }}">
                                @else
                                <div class="eh-card-img placeholder"><i class="fa-solid fa-newspaper"></i></div>
                                @endif
                                <div class="eh-card-title">{{ $post['title'] }}</div>
                                <div class="eh-card-meta">
                                    <span>{{ \Carbon\Carbon::parse($post['published_at'])->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="eh-empty">No hay entradas de blog publicadas. Crea una en <strong>Novedades</strong> con tipo "Blog".</div>
                        @endif
                    @endif

                </div>
            </div>
        @endforeach

    </div>
</div>
