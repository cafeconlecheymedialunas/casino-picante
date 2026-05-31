@props([
    'label'    => 'Icono',
    'name'     => 'icon',
    'selected' => '',
    'onChange' => null,
])

@php
    $icons = \App\Support\FontAwesomeIcons::options();
@endphp

@once
<style>
    .icon-library { display:flex; flex-direction:column; gap:10px; }
    .icon-library-current { display:flex; align-items:center; justify-content:space-between; gap:12px; border:1px solid var(--line-2); border-radius:8px; background:rgba(255,255,255,.035); padding:10px 12px; }
    .icon-library-preview { display:flex; align-items:center; gap:10px; min-width:0; }
    .icon-library-preview-icon { width:40px; height:40px; border-radius:8px; background:rgba(255,106,26,.10); border:1px solid rgba(255,106,26,.22); display:flex; align-items:center; justify-content:center; color:var(--orange); font-size:18px; flex-shrink:0; }
    .icon-library-meta { min-width:0; }
    .icon-library-meta strong { display:block; color:var(--white); font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .icon-library-meta span { display:block; color:var(--muted-2); font-size:11px; margin-top:2px; }
    .icon-library-toggle { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); padding:0 11px; font-size:11px; font-weight:800; cursor:pointer; white-space:nowrap; }
    .icon-library-toggle:hover { border-color:var(--orange); background:rgba(255,106,26,.12); }
    .icon-library-panel { display:none; border:1px solid var(--line); border-radius:8px; background:rgba(0,0,0,.14); padding:10px; }
    .icon-library.is-open .icon-library-panel { display:block; }
    .icon-library-search { width:100%; height:34px; border:1px solid var(--line-2); border-radius:7px; background:rgba(255,255,255,.04); color:#fff; outline:none; padding:0 11px; font-size:12px; margin-bottom:10px; }
    .icon-library-search:focus { border-color:var(--orange); }
    .icon-library-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(38px, 1fr)); gap:5px; max-height:260px; overflow-y:auto; padding-right:4px; }
    .icon-library-option { position:relative; cursor:pointer; border:1px solid var(--line); border-radius:7px; padding:0; background:rgba(255,255,255,.03); transition:border-color .12s, background .12s; aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:15px; color:var(--muted); }
    .icon-library-option:hover { border-color:rgba(255,106,26,.4); color:#fff; background:rgba(255,106,26,.08); }
    .icon-library-option.is-selected { border-color:var(--orange); background:rgba(255,106,26,.14); color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.10); }
    [data-dashboard-theme="light"] .icon-library-current,
    [data-dashboard-theme="light"] .icon-library-panel,
    [data-dashboard-theme="light"] .icon-library-option,
    [data-dashboard-theme="light"] .icon-library-toggle { background:#ffffff; border-color:var(--line); }
    [data-dashboard-theme="light"] .icon-library-option.is-selected { background:#fff7ed; border-color:var(--orange); }
</style>
@endonce

<div
    class="icon-library"
    x-data="{
        open: false,
        search: '',
        selected: @js($selected ?: ''),
        icons: @js($icons),
        onChange: {{ $onChange ? "'" . $onChange . "'" : 'null' }},
        get filtered() {
            if (!this.search) return this.icons;
            const q = this.search.toLowerCase();
            return this.icons.filter(i => i.label.toLowerCase().includes(q) || i.name.toLowerCase().includes(q));
        },
        select(icon) {
            this.selected = icon.class;
            this.open = false;
            this.search = '';
            if (this.onChange) {
                try { eval(this.onChange + ' = \'' + icon.class + '\''); } catch(e) {}
            }
            this.$dispatch('icon-selected', { value: icon.class, name: @js($name) });
        },
        get currentLabel() {
            const found = this.icons.find(i => i.class === this.selected);
            return found ? found.label : (this.selected ? this.selected : 'Sin icono');
        }
    }"
    :class="{ 'is-open': open }"
    @icon-select-{{ $name }}.window="selected = $event.detail.value"
>
    <input type="hidden" name="{{ $name }}" :value="selected">

    @if($label)
    <label class="ep-field" style="font-size:10px;font-weight:900;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;">{{ $label }}</label>
    @endif

    <div class="icon-library-current">
        <div class="icon-library-preview">
            <span class="icon-library-preview-icon" :style="selected ? '' : 'opacity:.35'">
                <i :class="selected || 'fa-solid fa-question'"></i>
            </span>
            <span class="icon-library-meta">
                <strong x-text="selected ? currentLabel : 'Sin icono'"></strong>
                <span x-text="selected ? 'Icono seleccionado' : 'Ninguno elegido'"></span>
            </span>
        </div>
        <button type="button" class="icon-library-toggle" @click="open = !open" x-text="open ? 'Cerrar' : 'Elegir icono'"></button>
    </div>

    <div class="icon-library-panel">
        <input
            type="text"
            class="icon-library-search"
            x-model.debounce.200ms="search"
            placeholder="Buscar icono..."
        >
        <div class="icon-library-grid">
            <template x-for="icon in filtered" :key="icon.class">
                <button
                    type="button"
                    class="icon-library-option"
                    :class="{ 'is-selected': selected === icon.class }"
                    :title="icon.label"
                    @click="select(icon)"
                >
                    <i :class="icon.class"></i>
                </button>
            </template>
        </div>
    </div>
</div>
