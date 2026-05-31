@php
    $raffleIdsStr = $sections['sorteo']['raffle_ids'] ?? '';
    $initialRaffleIds = is_string($raffleIdsStr) && !empty($raffleIdsStr) 
        ? array_filter(array_map('trim', explode(',', $raffleIdsStr))) 
        : [];
    
    $bonusIdsStr = $sections['bonos']['bonus_ids'] ?? '';
    $initialBonusIds = is_string($bonusIdsStr) && !empty($bonusIdsStr) 
        ? array_filter(array_map('trim', explode(',', $bonusIdsStr))) 
        : [];
    
    $postIdsStr = $sections['blog']['post_ids'] ?? '';
    $initialPostIds = is_string($postIdsStr) && !empty($postIdsStr)
        ? array_filter(array_map('trim', explode(',', $postIdsStr)))
        : [];

    $lineIdsStr = $sections['lineas']['line_ids'] ?? '';
    $initialLineIds = is_string($lineIdsStr) && !empty($lineIdsStr)
        ? array_filter(array_map('trim', explode(',', $lineIdsStr)))
        : [];
@endphp

<div class="page-container" x-data="editorHome()">
    <script>
        function editorHome() {
            return {
                raffleIds: @json($initialRaffleIds),
                bonusIds: @json($initialBonusIds),
                postIds: @json($initialPostIds),
                lineIds: @json($initialLineIds),
                searchRaffle: '',
                searchBonus: '',
                searchPost: '',
                searchLine: '',
                filterRaffle: 'all',
                filterBonus: 'all',
                filterPost: 'all',
                filterLine: 'all',
                toastMsg: '',
                toastVisible: false,
                toastType: 'success',
                showToast(msg, type = 'success') {
                    this.toastMsg = msg;
                    this.toastType = type;
                    this.toastVisible = true;
                    setTimeout(() => { this.toastVisible = false; }, 3000);
                },
                matchesSearch(text, vendor, query) {
                    if (!query) return true;
                    const q = query.toLowerCase();
                    return (text || '').toLowerCase().includes(q) || (vendor || '').toLowerCase().includes(q);
                },
                matchesFilter(isOficial, filter) {
                    if (filter === 'all') return true;
                    if (filter === 'oficial') return isOficial;
                    if (filter === 'other') return !isOficial;
                    return true;
                },
                toggleRaffle(id) {
                    id = String(id);
                    const idx = this.raffleIds.indexOf(id);
                    if (idx > -1) this.raffleIds.splice(idx, 1);
                    else this.raffleIds.push(id);
                },
                toggleBonus(id) {
                    id = String(id);
                    const idx = this.bonusIds.indexOf(id);
                    if (idx > -1) this.bonusIds.splice(idx, 1);
                    else this.bonusIds.push(id);
                },
                togglePost(id) {
                    id = String(id);
                    const idx = this.postIds.indexOf(id);
                    if (idx > -1) this.postIds.splice(idx, 1);
                    else this.postIds.push(id);
                },
                isRaffleSelected(id) { return this.raffleIds.includes(String(id)); },
                isBonusSelected(id) { return this.bonusIds.includes(String(id)); },
                isPostSelected(id) { return this.postIds.includes(String(id)); },
                raffleOrder(id) { const i = this.raffleIds.indexOf(String(id)); return i > -1 ? i + 1 : null; },
                bonusOrder(id) { const i = this.bonusIds.indexOf(String(id)); return i > -1 ? i + 1 : null; },
                postOrder(id) { const i = this.postIds.indexOf(String(id)); return i > -1 ? i + 1 : null; },
                moveUp(arr, id) {
                    id = String(id);
                    const idx = arr.indexOf(id);
                    if (idx > 0) { [arr[idx-1], arr[idx]] = [arr[idx], arr[idx-1]]; }
                },
                moveDown(arr, id) {
                    id = String(id);
                    const idx = arr.indexOf(id);
                    if (idx > -1 && idx < arr.length - 1) { [arr[idx], arr[idx+1]] = [arr[idx+1], arr[idx]]; }
                },
                moveRaffleUp(id) { this.moveUp(this.raffleIds, id); },
                moveRaffleDown(id) { this.moveDown(this.raffleIds, id); },
                moveBonusUp(id) { this.moveUp(this.bonusIds, id); },
                moveBonusDown(id) { this.moveDown(this.bonusIds, id); },
                movePostUp(id) { this.moveUp(this.postIds, id); },
                movePostDown(id) { this.moveDown(this.postIds, id); },
                moveLineUp(id) { this.moveUp(this.lineIds, id); },
                moveLineDown(id) { this.moveDown(this.lineIds, id); },
                toggleLine(id) {
                    id = String(id);
                    const idx = this.lineIds.indexOf(id);
                    if (idx > -1) this.lineIds.splice(idx, 1);
                    else this.lineIds.push(id);
                },
                isLineSelected(id) { return this.lineIds.includes(String(id)); },
                lineOrder(id) { const i = this.lineIds.indexOf(String(id)); return i > -1 ? i + 1 : null; },
                saveSection(key) {
                    const ids = key === 'sorteo' ? this.raffleIds : (key === 'bonos' ? this.bonusIds : (key === 'lineas' ? this.lineIds : this.postIds));
                    const fieldKey = key === 'sorteo' ? 'raffle_ids' : (key === 'bonos' ? 'bonus_ids' : (key === 'lineas' ? 'line_ids' : 'post_ids'));
                    const sectionData = this.$wire.sections[key];
                    sectionData[fieldKey] = ids.join(',');
                    this.$wire.saveSection(key).then(() => {
                        this.showToast('Sección guardada correctamente', 'success');
                    }).catch(() => {
                        this.showToast('Error al guardar', 'error');
                    });
                }
            }
        }
    </script>
    <style>
        .eh-page { display:flex; flex-direction:column; gap:28px; min-width:0; max-width:100%; overflow-x:clip; }
        .eh-section { border:1px solid var(--line); border-radius:14px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; }
        .eh-section-head { padding:14px 20px; border-bottom:1px solid var(--line); }
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
        .eh-carousel-item { display:flex; flex-direction:column; gap:8px; }
        .eh-carousel-item.is-dragging { opacity:.45; }
        .eh-carousel-item.is-drag-over > .eh-repeater-item:first-child { border-color:var(--orange); background:rgba(255,106,26,.08); }
        .eh-repeater-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:10px; background:rgba(255,255,255,.025); border:1px solid var(--line); transition:border-color .15s, background .15s; }
        .eh-repeater-item:hover { border-color:var(--line-2); background:rgba(255,255,255,.04); }
        .eh-repeater-item.new-row { background:rgba(255,106,26,.04); border-color:rgba(255,106,26,.25); }
        .eh-repeater-item .drag-handle { width:20px; flex-shrink:0; display:flex; flex-direction:column; gap:2px; cursor:grab; opacity:.4; }
        .eh-repeater-item .drag-handle:active { cursor:grabbing; }
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
        .eh-save-btn { padding:6px 14px; border-radius:6px; border:1px solid var(--orange); background:rgba(255,106,26,.15); color:var(--orange); font-size:11px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all .15s; }
        .eh-save-btn:hover { background:var(--orange); color:#190702; }
        .eh-toast { position:fixed; top:20px; right:20px; z-index:99999; border-radius:8px; padding:12px 20px; font-size:13px; font-weight:700; box-shadow:0 4px 20px rgba(0,0,0,.4); animation:fadeIn .2s; }
        .eh-toast.success { background:var(--good,#25c46b); color:#fff; }
        .eh-toast.error { background:#ff4757; color:#fff; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
        .eh-search-bar { display:flex; align-items:center; gap:8px; padding:10px 20px 12px; border-bottom:1px solid rgba(255,255,255,.04); }
        .eh-search-input { flex:1; background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:8px; padding:7px 12px 7px 34px; color:var(--white); font-size:12px; outline:none; }
        .eh-search-input:focus { border-color:var(--orange); box-shadow:0 0 0 2px rgba(255,106,26,.1); }
        .eh-search-wrap { position:relative; flex:1; }
        .eh-search-wrap i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--muted-2); font-size:11px; pointer-events:none; }
        .eh-vendor-badge { font-size:9px; font-weight:800; padding:2px 7px; border-radius:999px; white-space:nowrap; }
        .eh-vendor-badge.oficial { background:rgba(255,106,26,.2); color:var(--orange); border:1px solid rgba(255,106,26,.3); }
        .eh-vendor-badge.other { background:rgba(255,255,255,.06); color:var(--muted-2); border:1px solid rgba(255,255,255,.08); }
        .eh-selected-list { padding:0 20px 12px; display:flex; flex-direction:column; gap:6px; }
        .eh-selected-header { font-size:10px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; padding:0 20px 6px; }
        .eh-selected-row { display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; background:rgba(255,106,26,.07); border:1px solid rgba(255,106,26,.2); }
        .eh-selected-row-title { flex:1; font-size:12px; font-weight:700; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .eh-selected-row-pos { font-size:10px; color:var(--orange); font-weight:800; min-width:20px; }
        .eh-filter-pills { display:flex; gap:6px; flex-wrap:wrap; align-items:center; }
        .eh-pill { font-size:10px; font-weight:700; padding:3px 10px; border-radius:999px; border:1px solid var(--line-2); background:transparent; color:var(--muted-2); cursor:pointer; transition:all .15s; }
        .eh-pill:hover { border-color:var(--orange); color:var(--orange); }
        .eh-pill.active { background:rgba(255,106,26,.15); border-color:var(--orange); color:var(--orange); }
        [data-dashboard-theme="light"] .eh-search-input { background:#fff !important; color:var(--white) !important; }
        [data-dashboard-theme="light"] .eh-selected-row { background:rgba(255,106,26,.08) !important; }

        [data-dashboard-theme="light"] .eh-section,
        [data-dashboard-theme="light"] .eh-card,
        [data-dashboard-theme="light"] .eh-repeater-item {
            background: #fffdf8 !important;
            background-image: none !important;
            border-color: var(--line) !important;
            color: var(--white);
            box-shadow: 0 12px 28px rgba(42,20,20,.06);
        }
        [data-dashboard-theme="light"] .eh-section-head {
            background: rgba(255,250,243,.94) !important;
            border-color: var(--line) !important;
        }
        [data-dashboard-theme="light"] .eh-card:hover,
        [data-dashboard-theme="light"] .eh-card.selected,
        [data-dashboard-theme="light"] .eh-repeater-item:hover,
        [data-dashboard-theme="light"] .eh-repeater-item.new-row {
            background: rgba(255,106,26,.08) !important;
            border-color: var(--orange) !important;
        }
        [data-dashboard-theme="light"] .eh-card-img,
        [data-dashboard-theme="light"] .eh-repeater-thumb {
            background: rgba(244,234,220,.78) !important;
        }
        [data-dashboard-theme="light"] .eh-repeater-field input {
            background: #fff !important;
            color: var(--white) !important;
            border-color: var(--line-2) !important;
        }
        [data-dashboard-theme="light"] .eh-repeater-actions button,
        [data-dashboard-theme="light"] .eh-repeater-addbtn,
        [data-dashboard-theme="light"] button[style*="background:transparent"] {
            background: rgba(244,234,220,.78) !important;
            color: var(--muted) !important;
            border-color: var(--line) !important;
        }
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
        .eh-picker-grid { display:grid; grid-template-columns:1fr 280px; gap:12px; align-items:start; }
        @media (max-width: 680px) {
            .eh-picker-grid { grid-template-columns:1fr; }
        }
    </style>

    <template x-if="toastVisible">
        <div class="eh-toast" :class="toastType" x-text="toastMsg"></div>
    </template>

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
                        @endif
                        
                        @if($key === 'carousel')
                            <div class="eh-counter">
                                Seleccionados: <span class="current">{{ count($selectedCarousel) }}</span>
                            </div>
                        @elseif(in_array($key, ['sorteo', 'bonos', 'blog', 'lineas']))
                            <div class="eh-counter">
                                Seleccionados: <span class="current" x-text="'{{ $key }}' === 'sorteo' ? raffleIds.length : ('{{ $key }}' === 'bonos' ? bonusIds.length : ('{{ $key }}' === 'lineas' ? lineIds.length : postIds.length))"></span>
                            </div>
                        @endif
                </div>
            </div>

                <div style="padding: 16px 20px;">
                    
                    @if($key !== 'carousel')
                        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px;">
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

                            @if(!in_array($key, ['carousel', 'como-empezar', 'nosotros']))
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <div class="eh-repeater-field">
                                    <label>Texto del botón de acción</label>
                                    <input type="text" wire:model="sections.{{ $key }}.action_text" placeholder="Ej: Ver todos">
                                </div>
                                <div class="eh-repeater-field">
                                    <label>URL del botón de acción</label>
                                    <input type="text" wire:model="sections.{{ $key }}.action_url" placeholder="Ej: /bonos">
                                </div>
                            </div>
                            @endif

                            @if($key === 'sorteo')
                            <div class="eh-repeater-field">
                                <label>Tipo de Sorteo</label>
                                <select wire:model="sections.{{ $key }}.raffle_type" style="background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:6px;padding:7px 10px;color:var(--white);font-size:12px;outline:none;">
                                    <option value="">Todos</option>
                                    <option value="active">Activos</option>
                                </select>
                            </div>
                            @endif

                            @if($key === 'bonos')
                            <div class="eh-repeater-field">
                                <label>Tipo de Bono (Filtro)</label>
                                <select wire:model="sections.{{ $key }}.bonus_type" style="background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:6px;padding:7px 10px;color:var(--white);font-size:12px;outline:none;">
                                    <option value="">Todos</option>
                                    <option value="active">Activos</option>
                                </select>
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
                            @endif
                        </div>
                    @endif

                    @if($key === 'carousel')
                        <div class="eh-repeater" x-data="{
                            open: false,
                            editing: null,
                            dragging: null,
                            startDrag(id, event) {
                                this.dragging = id;
                                event.dataTransfer.effectAllowed = 'move';
                                event.dataTransfer.setData('text/plain', id);
                                event.currentTarget.closest('.eh-carousel-item')?.classList.add('is-dragging');
                            },
                            endDrag(event) {
                                event.currentTarget.closest('.eh-carousel-item')?.classList.remove('is-dragging');
                                this.dragging = null;
                            },
                            ids() {
                                return Array.from(this.$refs.carouselList.querySelectorAll('[data-carousel-id]')).map((el) => Number(el.dataset.carouselId));
                            },
                            dropOn(id, event) {
                                const draggedId = this.dragging || Number(event.dataTransfer.getData('text/plain'));
                                if (!draggedId || draggedId === id) {
                                    return;
                                }

                                const dragged = this.$refs.carouselList.querySelector(`[data-carousel-id='${draggedId}']`);
                                const target = this.$refs.carouselList.querySelector(`[data-carousel-id='${id}']`);
                                if (!dragged || !target) {
                                    return;
                                }

                                const rect = target.getBoundingClientRect();
                                const before = event.clientY < rect.top + (rect.height / 2);
                                target.parentNode.insertBefore(dragged, before ? target : target.nextSibling);
                                this.$wire.reorderCarousel(this.ids());
                            }
                        }" style="padding: 0;">
                            <div x-ref="carouselList" class="eh-repeater" style="padding:0;">
                            @forelse($carouselItems as $i => $item)
                            <div class="eh-carousel-item" data-carousel-id="{{ $item['id'] }}" wire:key="carousel-item-{{ $item['id'] }}"
                                @dragover.prevent="$event.currentTarget.classList.add('is-drag-over')"
                                @dragleave="$event.currentTarget.classList.remove('is-drag-over')"
                                @drop.prevent="$event.currentTarget.classList.remove('is-drag-over'); dropOn({{ $item['id'] }}, $event)">
                                <div class="eh-repeater-item">
                                    <div class="drag-handle" draggable="true" title="Arrastrar para ordenar"
                                        @dragstart="startDrag({{ $item['id'] }}, $event)"
                                        @dragend="endDrag($event)">
                                        <span></span><span></span><span></span>
                                    </div>
                                    <img src="{{ $item['image'] }}" class="eh-repeater-thumb" alt="">
                                    <div class="eh-repeater-body">
                                        <div class="eh-repeater-title">{{ $item['title'] ?: 'Sin titulo' }}</div>
                                        <div class="eh-repeater-sub">{{ $item['description'] ?: ($item['link'] ?: 'Sin enlace') }}</div>
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
                                        <button type="button" @click="editing = editing === {{ $item['id'] }} ? null : {{ $item['id'] }}" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                        <button wire:click="removeCarouselItem({{ $item['id'] }})" wire:confirm="Eliminar esta imagen?" class="btn-del" title="Eliminar"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </div>
                                <div x-show="editing === {{ $item['id'] }}" x-cloak class="eh-repeater-item new-row" style="flex-wrap:wrap; align-items:flex-end;">
                                    <div class="eh-repeater-field" style="flex:1;min-width:150px;">
                                        <label>Titulo</label>
                                        <input type="text" wire:model.defer="carouselItems.{{ $i }}.title" placeholder="Opcional">
                                        @error("carouselItems.$i.title") <div style="color:#ff4757;font-size:10px;margin-top:2px;">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="eh-repeater-field" style="flex:2;min-width:220px;">
                                        <label>Descripcion</label>
                                        <input type="text" wire:model.defer="carouselItems.{{ $i }}.description" placeholder="Opcional">
                                        @error("carouselItems.$i.description") <div style="color:#ff4757;font-size:10px;margin-top:2px;">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="eh-repeater-field" style="flex:1;min-width:130px;">
                                        <label>CTA</label>
                                        <input type="text" wire:model.defer="carouselItems.{{ $i }}.cta_text" placeholder="Ej: Ver lineas">
                                        @error("carouselItems.$i.cta_text") <div style="color:#ff4757;font-size:10px;margin-top:2px;">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="eh-repeater-field" style="flex:1;min-width:160px;">
                                        <label>Link</label>
                                        <input type="text" wire:model.defer="carouselItems.{{ $i }}.link" placeholder="Opcional">
                                        @error("carouselItems.$i.link") <div style="color:#ff4757;font-size:10px;margin-top:2px;">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="eh-repeater-field" style="flex:1;min-width:170px;">
                                        <label>Reemplazar imagen</label>
                                        <x-upload-image label="" model="editCarouselImages.{{ $item['id'] }}" :value="$item['image'] ?? ''" aspect="851/315" hint="Opcional">
                                            @error("editCarouselImages.{$item['id']}") <div style="color:#ff4757;font-size:10px;margin-top:2px;">{{ $message }}</div> @enderror
                                        </x-upload-image>
                                    </div>
                                    <button type="button" @click="$wire.saveCarouselItem({{ $item['id'] }}, {{ $i }}).then(() => editing = null)" wire:loading.attr="disabled" class="eh-save-btn">
                                        <i class="fa-solid fa-save"></i> Guardar
                                    </button>
                                    <button type="button" @click="editing = null; $wire.loadCarouselItems()" class="eh-repeater-addbtn" style="border-style:solid;">
                                        <i class="fa-solid fa-xmark"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div style="text-align:center;padding:24px 16px;color:var(--muted-2);font-size:12px;">
                                <i class="fa-solid fa-image" style="font-size:24px;display:block;margin-bottom:8px;opacity:.3"></i>
                                No hay imagenes en el carrusel
                            </div>
                            @endforelse
                            </div>

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
                                    <div class="eh-repeater-field" style="flex:2;min-width:180px;">
                                        <label>Descripcion</label>
                                        <input type="text" wire:model="newCarouselDescription" placeholder="Opcional">
                                    </div>
                                    <div class="eh-repeater-field" style="flex:1;min-width:120px;">
                                        <label>CTA</label>
                                        <input type="text" wire:model="newCarouselCtaText" placeholder="Ej: Ver lineas">
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
                            @php $repeaterData = $sections[$key]['repeater_data'] ?? []; @endphp
                            @if(is_array($repeaterData) && count($repeaterData))
                                @foreach($repeaterData as $index => $item)
                                <div style="display: flex; flex-direction: column; gap: 8px; background: rgba(255,255,255,0.02); padding: 12px; border-radius: 8px; border: 1px solid var(--line);">
                                    <div class="eh-repeater-field">
                                        <label>Título</label>
                                        <input type="text" wire:model="sections.{{ $key }}.repeater_data.{{ $index }}.title" placeholder="Ej: Pedí tu usuario">
                                    </div>
                                    <div class="eh-repeater-field">
                                        <label>Descripción</label>
                                        <input type="text" wire:model="sections.{{ $key }}.repeater_data.{{ $index }}.subtitle" placeholder="Ej: Elegí una línea de atención...">
                                    </div>
                                    <div style="display: flex; justify-content: flex-end;">
                                        <button type="button" wire:click="removeRepeaterItem('{{ $key }}', {{ $index }})" style="color: #ff4757; background: transparent; border: none; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                            <button type="button" wire:click="addRepeaterItem('{{ $key }}')" class="eh-repeater-addbtn" style="width: auto;">
                                <i class="fa-solid fa-plus"></i> Agregar {{ $key === 'como-empezar' ? 'Paso' : 'Característica' }}
                            </button>
                        </div>
                    @endif

                    @if($key === 'sorteo')
                        @include('livewire.partials.eh-picker', [
                            'items'         => $raffleItems,
                            'searchModel'   => 'searchRaffle',
                            'filterModel'   => 'filterRaffle',
                            'selectedModel' => 'raffleIds',
                            'toggleFn'      => 'toggleRaffle',
                            'moveUpFn'      => 'moveRaffleUp',
                            'moveDownFn'    => 'moveRaffleDown',
                            'orderFn'       => 'raffleOrder',
                            'emptyMsg'      => 'No hay sorteos disponibles.',
                            'itemFields'    => ['title_field' => 'title', 'meta_field' => 'end_date', 'meta_prefix' => 'Vence: ', 'meta_format' => 'date'],
                        ])
                        <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                            <button type="button" class="eh-save-btn" @click="saveSection('sorteo')"><i class="fa-solid fa-save"></i> Guardar selección</button>
                        </div>
                    @endif

                    @if($key === 'bonos')
                        @include('livewire.partials.eh-picker', [
                            'items'         => $bonusItems,
                            'searchModel'   => 'searchBonus',
                            'filterModel'   => 'filterBonus',
                            'selectedModel' => 'bonusIds',
                            'toggleFn'      => 'toggleBonus',
                            'moveUpFn'      => 'moveBonusUp',
                            'moveDownFn'    => 'moveBonusDown',
                            'orderFn'       => 'bonusOrder',
                            'emptyMsg'      => 'No hay bonos activos disponibles.',
                            'itemFields'    => ['title_field' => 'title', 'meta_field' => 'code', 'meta_prefix' => '', 'meta_format' => 'text'],
                        ])
                        <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                            <button type="button" class="eh-save-btn" @click="saveSection('bonos')"><i class="fa-solid fa-save"></i> Guardar selección</button>
                        </div>
                    @endif

                    @if($key === 'blog')
                        @include('livewire.partials.eh-picker', [
                            'items'         => $blogPosts,
                            'searchModel'   => 'searchPost',
                            'filterModel'   => 'filterPost',
                            'selectedModel' => 'postIds',
                            'toggleFn'      => 'togglePost',
                            'moveUpFn'      => 'movePostUp',
                            'moveDownFn'    => 'movePostDown',
                            'orderFn'       => 'postOrder',
                            'emptyMsg'      => 'No hay entradas de blog publicadas.',
                            'itemFields'    => ['title_field' => 'title', 'meta_field' => 'published_at', 'meta_prefix' => '', 'meta_format' => 'date', 'image_field' => 'image'],
                        ])
                        <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                            <button type="button" class="eh-save-btn" @click="saveSection('blog')"><i class="fa-solid fa-save"></i> Guardar selección</button>
                        </div>
                    @endif

                    @if($key === 'lineas')
                        @include('livewire.partials.eh-picker', [
                            'items'         => $lineItems,
                            'searchModel'   => 'searchLine',
                            'filterModel'   => 'filterLine',
                            'selectedModel' => 'lineIds',
                            'toggleFn'      => 'toggleLine',
                            'moveUpFn'      => 'moveLineUp',
                            'moveDownFn'    => 'moveLineDown',
                            'orderFn'       => 'lineOrder',
                            'emptyMsg'      => 'No hay líneas activas disponibles.',
                            'itemFields'    => ['title_field' => 'name', 'meta_field' => 'type', 'meta_prefix' => '', 'meta_format' => 'text'],
                        ])
                        <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                            <button type="button" class="eh-save-btn" @click="saveSection('lineas')"><i class="fa-solid fa-save"></i> Guardar selección</button>
                        </div>
                    @endif

                    @if($key !== 'carousel' && !in_array($key, ['sorteo', 'bonos', 'blog', 'lineas']))
                        <div style="margin-top:14px;display:flex;justify-content:flex-end;">
                            <button type="button" wire:click="saveSection('{{ $key }}')" class="eh-save-btn">
                                <i class="fa-solid fa-save"></i> Guardar
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        @endforeach

    </div>
</div>
