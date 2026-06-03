@php
    $pageLabel = \App\Support\PublicPageContent::PAGES[$page]['label'];
    $hasTabs = !in_array($page, ['cajeros', 'novedades']);
    $itemFields = match ($page) {
        'lineas' => ['title_field' => 'name', 'meta_field' => 'type', 'meta_prefix' => '', 'meta_format' => 'text'],
        'bonos' => ['title_field' => 'title', 'meta_field' => 'code', 'meta_prefix' => '', 'meta_format' => 'text'],
        'sorteos' => ['title_field' => 'title', 'meta_field' => 'end_date', 'meta_prefix' => 'Vence: ', 'meta_format' => 'date'],
        'cajeros' => ['title_field' => 'name', 'meta_field' => 'active_lines_count', 'meta_prefix' => 'Líneas: ', 'meta_format' => 'text'],
        'novedades' => ['title_field' => 'title', 'meta_field' => 'published_at', 'meta_prefix' => '', 'meta_format' => 'date'],
        default => ['title_field' => 'title'],
    };
@endphp

<div class="page-container" x-data="editorPublicPage(@js($pageData))">
    <script>
        function editorPublicPage(initial) {
            return {
                page: initial,
                activeTab: 0,
                searchItems: '',
                filterItems: 'all',
                toastMsg: '',
                toastVisible: false,
                toastType: 'success',
                showToast(msg, type = 'success') {
                    this.toastMsg = msg;
                    this.toastType = type;
                    this.toastVisible = true;
                    setTimeout(() => { this.toastVisible = false; }, 3000);
                },
                activeTabData() {
                    if (!this.page.tabs.length) this.addTab();
                    return this.page.tabs[this.activeTab] || this.page.tabs[0];
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
                tabIds() {
                    const tab = this.activeTabData();
                    if (!Array.isArray(tab.item_ids)) tab.item_ids = [];
                    return tab.item_ids;
                },
                toggleItem(id) {
                    id = String(id);
                    const ids = this.tabIds();
                    const idx = ids.indexOf(id);
                    if (idx > -1) ids.splice(idx, 1);
                    else ids.push(id);
                },
                itemOrder(id) {
                    const idx = this.tabIds().indexOf(String(id));
                    return idx > -1 ? idx + 1 : null;
                },
                moveUp(arr, id) {
                    id = String(id);
                    const idx = arr.indexOf(id);
                    if (idx > 0) [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]];
                },
                moveDown(arr, id) {
                    id = String(id);
                    const idx = arr.indexOf(id);
                    if (idx > -1 && idx < arr.length - 1) [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]];
                },
                moveItemUp(id) { this.moveUp(this.tabIds(), id); },
                moveItemDown(id) { this.moveDown(this.tabIds(), id); },
                addTab() {
                    const next = this.page.tabs.length + 1;
                    this.page.tabs.push({ key: 'tab-' + next, title: 'Nueva tab', subtitle: '', enabled: true, item_ids: [] });
                    this.activeTab = this.page.tabs.length - 1;
                },
                removeTab(index) {
                    if (this.page.tabs.length <= 1) return;
                    this.page.tabs.splice(index, 1);
                    this.activeTab = Math.max(0, Math.min(this.activeTab, this.page.tabs.length - 1));
                },
                save() {
                    this.$wire.save(this.page).then(() => {
                        this.showToast('Pagina guardada correctamente', 'success');
                    }).catch(() => {
                        this.showToast('No se pudo guardar la pagina', 'error');
                    });
                }
            }
        }
    </script>

    <style>
        .ep-icon-picker-current { display:flex; align-items:center; justify-content:space-between; gap:12px; border:1px solid var(--line-2); border-radius:8px; background:rgba(255,255,255,.035); padding:10px 12px; margin-top:5px; }
        .ep-icon-preview { width:40px; height:40px; border-radius:8px; background:rgba(255,106,26,.10); border:1px solid rgba(255,106,26,.22); display:flex; align-items:center; justify-content:center; color:var(--orange); font-size:18px; flex-shrink:0; }
        .ep-icon-meta { min-width:0; flex:1; }
        .ep-icon-meta strong { display:block; color:var(--white); font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .ep-icon-meta span { display:block; color:var(--muted-2); font-size:11px; margin-top:2px; }
        .ep-icon-toggle { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); padding:0 11px; font-size:11px; font-weight:800; cursor:pointer; white-space:nowrap; flex-shrink:0; }
        .ep-icon-toggle:hover { border-color:var(--orange); background:rgba(255,106,26,.12); }
        .ep-icon-panel { border:1px solid var(--line); border-radius:8px; background:rgba(0,0,0,.18); padding:10px; margin-top:6px; }
        .ep-icon-search { width:100%; height:34px; border:1px solid var(--line-2); border-radius:7px; background:rgba(255,255,255,.04); color:#fff; outline:none; padding:0 11px; font-size:12px; margin-bottom:10px; }
        .ep-icon-search:focus { border-color:var(--orange); }
        .ep-icon-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(34px,1fr)); gap:5px; max-height:220px; overflow-y:auto; }
        .ep-icon-opt { cursor:pointer; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:14px; color:var(--muted); transition:border-color .12s, background .12s, color .12s; }
        .ep-icon-opt:hover { border-color:rgba(255,106,26,.4); color:#fff; background:rgba(255,106,26,.08); }
        .ep-icon-opt.is-selected { border-color:var(--orange); background:rgba(255,106,26,.14); color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.10); }
        .ep-page { display:flex; flex-direction:column; gap:18px; }
        .ep-head { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; border:1px solid var(--line); border-radius:14px; padding:18px; background:linear-gradient(180deg,#170b0b,#0f0707); }
        .ep-title { font-family:var(--font-display); font-size:30px; line-height:.95; letter-spacing:.03em; margin:0; }
        .ep-title span { color:var(--orange); }
        .ep-copy { color:var(--muted); font-size:13px; margin:7px 0 0; max-width:620px; line-height:1.45; }
        .ep-nav { display:flex; gap:8px; flex-wrap:wrap; }
        .ep-nav a { display:inline-flex; align-items:center; gap:7px; height:34px; padding:0 12px; border-radius:8px; border:1px solid var(--line); color:var(--muted); text-decoration:none; font-size:12px; font-weight:800; }
        .ep-nav a.active { border-color:rgba(255,106,26,.45); color:var(--orange); background:rgba(255,106,26,.09); }
        .ep-panel { border:1px solid var(--line); border-radius:14px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; }
        .ep-panel-head { padding:14px 18px; border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .ep-panel-title { font-size:12px; font-weight:900; color:var(--muted); text-transform:uppercase; letter-spacing:.12em; }
        .ep-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; padding:16px 18px; }
        .ep-field { display:flex; flex-direction:column; gap:5px; min-width:0; }
        .ep-field.full { grid-column:1 / -1; }
        .ep-field label { font-size:10px; font-weight:900; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }
        .ep-field input, .ep-field textarea { width:100%; border:1px solid var(--line-2); border-radius:8px; background:rgba(255,255,255,.04); color:var(--white); outline:none; padding:9px 11px; font-size:13px; }
        .ep-field textarea { min-height:78px; resize:vertical; }
        .ep-tabs-workspace { padding:0; }
        .ep-tabs-strip { display:flex; align-items:flex-end; gap:4px; padding:0 18px; border-bottom:1px solid rgba(255,255,255,.09); background:rgba(255,255,255,.018); overflow-x:auto; }
        .ep-tab-row { position:relative; display:inline-flex; align-items:center; gap:9px; min-width:132px; max-width:230px; height:50px; padding:0 12px; border:0; border-radius:8px 8px 0 0; background:transparent; color:rgba(255,255,255,.48); cursor:pointer; transition:color .16s, background .16s; flex:0 0 auto; }
        .ep-tab-row:hover { color:rgba(255,255,255,.82); background:rgba(255,255,255,.035); }
        .ep-tab-row.active { color:#fff; background:rgba(255,255,255,.055); }
        .ep-tab-row.active::after { content:""; position:absolute; left:0; right:0; bottom:-1px; height:2px; background:var(--orange); border-radius:2px 2px 0 0; }
        .ep-tab-row.is-off { opacity:.58; }
        .ep-tab-icon { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:999px; border:1px solid rgba(255,255,255,.1); background:rgba(255,255,255,.045); font-size:10px; color:var(--muted); flex-shrink:0; }
        .ep-tab-row.active .ep-tab-icon { border-color:rgba(255,106,26,.42); background:rgba(255,106,26,.14); color:var(--orange); }
        .ep-tab-text { min-width:0; flex:1; }
        .ep-tab-name { font-size:12px; font-weight:900; color:currentColor; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-transform:uppercase; letter-spacing:.04em; }
        .ep-tab-meta { display:inline-flex; align-items:center; gap:4px; margin-top:3px; font-size:10px; color:rgba(255,255,255,.38); }
        .ep-tab-row.active .ep-tab-meta { color:rgba(255,106,26,.9); }
        .ep-tabs-add { margin-left:auto; height:34px; align-self:center; border:1px dashed var(--line-2); border-radius:8px; background:transparent; color:var(--muted-2); padding:0 12px; font-size:12px; font-weight:900; cursor:pointer; white-space:nowrap; }
        .ep-tabs-add:hover { border-color:var(--orange); color:var(--orange); background:rgba(255,106,26,.05); }
        .ep-tab-editor { padding:16px 18px 18px; }
        .ep-tab-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px; padding:10px 12px; border:1px solid var(--line); border-radius:10px; background:rgba(255,255,255,.025); }
        .ep-tab-current { min-width:0; }
        .ep-tab-current-title { font-size:14px; font-weight:900; color:var(--white); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .ep-tab-current-sub { margin-top:2px; font-size:11px; color:var(--muted-2); }
        .ep-tab-actions { display:flex; gap:4px; flex-shrink:0; }
        .ep-icon-btn { width:28px; height:28px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; }
        .ep-icon-btn:hover { color:var(--orange); border-color:var(--orange); }
        .ep-icon-btn:disabled { opacity:.25; pointer-events:none; }
        .ep-icon-btn.danger { color:#ff4757; }
        .ep-save { display:inline-flex; align-items:center; justify-content:center; gap:8px; height:36px; border:0; border-radius:8px; padding:0 16px; background:linear-gradient(135deg,var(--orange),var(--amber)); color:#190702; font-size:12px; font-weight:900; cursor:pointer; }
        .ep-actions-bottom { display:flex; justify-content:flex-end; padding:14px 18px 18px; border-top:1px solid var(--line); background:rgba(255,255,255,.018); }
        .ep-toast { position:fixed; right:22px; bottom:22px; z-index:80; padding:12px 15px; border-radius:10px; background:#120807; border:1px solid rgba(37,196,107,.38); color:var(--good); font-size:13px; font-weight:800; box-shadow:0 18px 50px rgba(0,0,0,.38); }
        .ep-toast.error { border-color:rgba(255,71,87,.42); color:#ff4757; }
@media (max-width:900px) { .ep-head { flex-direction:column; } .ep-grid { grid-template-columns:1fr; } .ep-tabs-strip { padding:0 12px; } .ep-tab-row { min-width:124px; } .ep-tab-toolbar { flex-direction:column; align-items:stretch; } .ep-tab-actions { justify-content:flex-end; } }

    /* ── Light theme ── */
    [data-dashboard-theme="light"] .ep-head,
    [data-dashboard-theme="light"] .ep-panel { background:#fffdf8 !important; background-image:none !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-panel-head,
    [data-dashboard-theme="light"] .ep-actions-bottom { background:rgba(255,250,243,.94) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-tabs-strip { background:rgba(244,234,220,.5) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-tab-row { color:var(--muted) !important; }
    [data-dashboard-theme="light"] .ep-tab-row:hover { background:rgba(255,106,26,.06) !important; }
    [data-dashboard-theme="light"] .ep-tab-row.active { background:rgba(255,106,26,.08) !important; color:var(--white) !important; }
    [data-dashboard-theme="light"] .ep-tab-icon { background:rgba(244,234,220,.9) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-tab-toolbar { background:rgba(244,234,220,.5) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-field input,
    [data-dashboard-theme="light"] .ep-field textarea,
    [data-dashboard-theme="light"] .ep-icon-search { background:#fff !important; color:var(--white) !important; border-color:var(--line-2) !important; }
    [data-dashboard-theme="light"] .ep-icon-panel { background:rgba(244,234,220,.6) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-icon-picker-current { background:rgba(244,234,220,.6) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-icon-toggle,
    [data-dashboard-theme="light"] .ep-icon-btn,
    [data-dashboard-theme="light"] .ep-tabs-add { background:rgba(244,234,220,.78) !important; color:var(--muted) !important; border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .ep-nav a { background:rgba(244,234,220,.78) !important; border-color:var(--line) !important; color:var(--muted) !important; }
    [data-dashboard-theme="light"] .ep-nav a.active { background:rgba(255,106,26,.1) !important; border-color:rgba(255,106,26,.4) !important; color:var(--orange) !important; }
    </style>

@section('header')
    <x-livewire.components.page-header
        title="PÁGINA · {{ strtoupper($pageLabel) }}"
        subtitle="Editor de contenido, tabs e items visibles en la página pública" />
@endsection

    <div class="ep-page">

        <div class="ep-panel">
            <div class="ep-panel-head">
                <div class="ep-panel-title">Contenido de pagina</div>
                <label style="display:flex;align-items:center;gap:8px;color:var(--muted);font-size:12px;font-weight:800;">
                    <input type="checkbox" x-model="page.enabled"> Activa
                </label>
            </div>
            <div class="ep-grid">
                <div class="ep-field"><label>Kicker</label><input type="text" x-model="page.kicker"></div>
                <div class="ep-field"><label>Titulo</label><input type="text" x-model="page.title"></div>
                <div class="ep-field"><label>Highlight</label><input type="text" x-model="page.highlight"></div>
                <div class="ep-field"><label>Subtitulo</label><input type="text" x-model="page.subtitle"></div>
                <div class="ep-field"><label>Texto CTA</label><input type="text" x-model="page.action_text"></div>
                <div class="ep-field"><label>URL CTA</label><input type="text" x-model="page.action_url"></div>
                <div class="ep-field full"><label>Contenido</label><textarea x-model="page.content"></textarea></div>
                <div class="ep-field full">
                    <x-upload-image
                        label="Imagen hero"
                        model="heroImage"
                        :value="$currentImage ?? ''"
                        :remove-action="$currentImage ? 'removeHeroImage' : null"
                        aspect="16/5"
                        icon="fa-solid fa-panorama"
                        hint="Recomendado: 1600×500px"
                    />
                    @error('heroImage') <div style="color:#ff4757;font-size:11px;margin-top:4px">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="ep-panel">
            <div class="ep-panel-head">
                <div class="ep-panel-title">{{ $hasTabs ? 'Tabs e items' : 'Items seleccionados' }}</div>
            </div>

            @if($hasTabs)
            <div class="ep-tabs-workspace">
                <div class="ep-tabs-strip">
                    <template x-for="(tab, index) in page.tabs" :key="tab.key + '-' + index">
                        <button type="button" class="ep-tab-row" :class="{ 'active': activeTab === index, 'is-off': !tab.enabled }" @click="activeTab = index">
                            <span class="ep-tab-icon"><i :class="tab.icon ? tab.icon : (tab.enabled ? 'fa-solid fa-layer-group' : 'fa-solid fa-eye-slash')"></i></span>
                            <span class="ep-tab-text">
                                <div class="ep-tab-name" x-text="tab.title || 'Tab sin titulo'"></div>
                            </span>
                        </button>
                    </template>
                    <button type="button" class="ep-tabs-add" @click="addTab"><i class="fa-solid fa-plus"></i> Agregar tab</button>
                </div>

                <div class="ep-tab-editor">
                    <div class="ep-tab-toolbar">
                        <div class="ep-tab-current">
                            <div class="ep-tab-current-title" x-text="activeTabData().title || 'Tab sin titulo'"></div>
                            <div class="ep-tab-current-sub">
                                <span x-text="activeTabData().enabled ? 'Visible en frontend' : 'Oculta en frontend'"></span>
                            </div>
                        </div>
                        <div class="ep-tab-actions">
                            <button type="button" class="ep-icon-btn" title="Alternar visibilidad" @click.stop="activeTabData().enabled = !activeTabData().enabled"><i class="fa-solid" :class="activeTabData().enabled ? 'fa-eye' : 'fa-eye-slash'"></i></button>
                            <button type="button" class="ep-icon-btn danger" title="Eliminar tab" @click.stop="removeTab(activeTab)" :disabled="page.tabs.length <= 1"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>

                    <div style="min-width:0;">
                    <div class="ep-grid" style="padding:0 0 14px;">
                        <div class="ep-field"><label>Key</label><input type="text" x-model="activeTabData().key"></div>
                        <div class="ep-field"><label>Titulo de tab</label><input type="text" x-model="activeTabData().title"></div>
                        <div class="ep-field full"><label>Descripcion de tab</label><input type="text" x-model="activeTabData().subtitle"></div>
                        <div class="ep-field full" x-data="{
                            iconOpen: false,
                            iconSearch: '',
                            icons: @js(\App\Support\FontAwesomeIcons::options()),
                            get filtered() {
                                if (!this.iconSearch) return this.icons;
                                const q = this.iconSearch.toLowerCase();
                                return this.icons.filter(i => i.label.toLowerCase().includes(q) || i.name.toLowerCase().includes(q));
                            }
                        }">
                            <label>Icono</label>
                            <div class="ep-icon-picker-current">
                                <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1">
                                    <span class="ep-icon-preview" :style="page.tabs[activeTab]?.icon ? '' : 'opacity:.35'">
                                        <i :class="page.tabs[activeTab]?.icon || 'fa-solid fa-question'"></i>
                                    </span>
                                    <span class="ep-icon-meta">
                                        <strong x-text="page.tabs[activeTab]?.icon || 'Sin icono'"></strong>
                                        <span x-text="page.tabs[activeTab]?.icon ? 'Icono seleccionado' : 'Ninguno elegido'"></span>
                                    </span>
                                </div>
                                <button type="button" class="ep-icon-toggle" @click="iconOpen = !iconOpen" x-text="iconOpen ? 'Cerrar' : 'Elegir icono'"></button>
                            </div>
                            <div x-show="iconOpen" x-cloak @click.outside="iconOpen = false" class="ep-icon-panel">
                                <input type="text" class="ep-icon-search" x-model.debounce.200ms="iconSearch" placeholder="Buscar icono...">
                                <div class="ep-icon-grid">
                                    <template x-for="icon in filtered" :key="icon.class">
                                        <button
                                            type="button"
                                            class="ep-icon-opt"
                                            :class="{ 'is-selected': page.tabs[activeTab]?.icon === icon.class }"
                                            :title="icon.label"
                                            @click.stop="page.tabs[activeTab].icon = icon.class; iconOpen = false; iconSearch = ''"
                                        ><i :class="icon.class"></i></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <label style="display:flex;align-items:center;gap:8px;color:var(--muted);font-size:12px;font-weight:800;">
                            <input type="checkbox" x-model="activeTabData().enabled"> Tab activa
                        </label>
                    </div>

                    @include('livewire.partials.eh-picker', [
                        'items' => $items,
                        'searchModel' => 'searchItems',
                        'filterModel' => 'filterItems',
                        'selectedModel' => 'page.tabs[activeTab].item_ids',
                        'toggleFn' => 'toggleItem',
                        'moveUpFn' => 'moveItemUp',
                        'moveDownFn' => 'moveItemDown',
                        'orderFn' => 'itemOrder',
                        'emptyMsg' => 'No hay items disponibles.',
                        'itemFields' => $itemFields,
                    ])
                    </div>
                </div>
            </div>
            @else
            <div style="padding:16px 18px 18px;">
                @include('livewire.partials.eh-picker', [
                    'items' => $items,
                    'searchModel' => 'searchItems',
                    'filterModel' => 'filterItems',
                    'selectedModel' => 'page.tabs[0].item_ids',
                    'toggleFn' => 'toggleItem',
                    'moveUpFn' => 'moveItemUp',
                    'moveDownFn' => 'moveItemDown',
                    'orderFn' => 'itemOrder',
                    'emptyMsg' => 'No hay items disponibles.',
                    'itemFields' => $itemFields,
                ])
            </div>
            @endif

            <div class="ep-actions-bottom">
                <button type="button" class="ep-save" @click="save"><i class="fa-solid fa-save"></i> Guardar pagina</button>
            </div>
        </div>
    </div>

    <div x-show="toastVisible" x-transition class="ep-toast" :class="toastType === 'error' ? 'error' : ''" x-text="toastMsg" x-cloak></div>
</div>
