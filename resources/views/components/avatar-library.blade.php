@props([
    'label' => 'Libreria de avatars',
    'name' => 'avatar',
    'model' => null,
    'selected' => null,
])

@php
    $avatars = \App\Support\AvatarLibrary::options($selected);
    $selectedAvatar = $selected ?: \App\Support\AvatarLibrary::default();
    $current = collect($avatars)->firstWhere('value', $selectedAvatar) ?: $avatars[0];
@endphp

@once
    <style>
        .avatar-library { display:flex; flex-direction:column; gap:10px; }
        .avatar-library-current { display:flex; align-items:center; justify-content:space-between; gap:12px; border:1px solid var(--line-2); border-radius:8px; background:rgba(255,255,255,.035); padding:10px 12px; }
        .avatar-library-preview { display:flex; align-items:center; gap:10px; min-width:0; }
        .avatar-library-preview img, .avatar-library-preview .avatar-library-fallback { width:40px; height:40px; border-radius:8px; background:rgba(255,255,255,.06); flex-shrink:0; }
        .avatar-library-fallback { display:none; align-items:center; justify-content:center; color:var(--orange); font-family:var(--font-display); font-size:16px; }
        .avatar-library-meta { min-width:0; }
        .avatar-library-meta strong { display:block; color:var(--white); font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .avatar-library-meta span { display:block; color:var(--muted-2); font-size:11px; margin-top:2px; }
        .avatar-library-toggle { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); padding:0 11px; font-size:11px; font-weight:800; cursor:pointer; white-space:nowrap; }
        .avatar-library-toggle:hover { border-color:var(--orange); background:rgba(255,106,26,.12); }
        .avatar-library-panel { display:none; border:1px solid var(--line); border-radius:8px; background:rgba(0,0,0,.14); padding:10px; }
        .avatar-library.is-open .avatar-library-panel { display:block; }
        .avatar-library-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(42px, 1fr)); gap:6px; max-height:300px; overflow-y:auto; padding-right:4px; }
        .avatar-library-option { position:relative; cursor:pointer; border:1px solid var(--line); border-radius:7px; padding:3px; background:rgba(255,255,255,.03); transition:border-color .15s, background .15s, box-shadow .15s; min-width:0; }
        .avatar-library-option input { position:absolute; opacity:0; pointer-events:none; }
        .avatar-library-option img { width:100%; aspect-ratio:1; display:block; border-radius:6px; background:rgba(255,255,255,.05); object-fit:cover; }
        .avatar-library-option.is-selected,
        .avatar-library-option:has(input:checked) { border-color:var(--orange); background:rgba(255,106,26,.12); box-shadow:0 0 0 3px rgba(255,106,26,.1); }
        .avatar-library-option.is-broken { display:none; }
    </style>
@endonce

<div
    class="avatar-library"
    x-data="{
        open: false,
        avatars: @js($avatars),
        selected: @js($selectedAvatar),
        model: @js($model),
        label: @js($current['label']),
        url: @js($current['url']),
        currentBroken: false,
        initials: 'RP',
        selectAvatar(avatar) {
            this.selected = avatar.value;
            this.label = avatar.label;
            this.url = avatar.url;
            this.currentBroken = false;
            this.open = false;

            try {
                if (this.model && this.$wire) {
                    this.$wire.set(this.model, avatar.value);
                }
            } catch (error) {}
        },
    }"
    :class="{ 'is-open': open }"
>
    <label class="form-label">{{ $label }}</label>
    <input type="hidden" name="{{ $name }}" :value="selected">
    <div class="avatar-library-current">
        <div class="avatar-library-preview">
            <img x-show="!currentBroken" :src="url" alt="" x-on:error="currentBroken = true">
            <span x-show="currentBroken" class="avatar-library-fallback" x-text="initials"></span>
            <span class="avatar-library-meta">
                <strong>Actual</strong>
                <span>Avatar seleccionado</span>
            </span>
        </div>
        <button type="button" class="avatar-library-toggle" x-on:click="open = !open" x-text="open ? 'Cerrar libreria' : 'Elegir avatar'"></button>
    </div>
    <div class="avatar-library-panel">
        <div class="avatar-library-grid">
            <template x-for="avatar in avatars" :key="avatar.value">
                <label class="avatar-library-option" :class="{ 'is-selected': selected === avatar.value, 'is-broken': avatar.broken }">
                    <input
                        type="radio"
                        name="{{ $name }}_choice"
                        :value="avatar.value"
                        :checked="selected === avatar.value"
                        x-on:change="selectAvatar(avatar)"
                    >
                    <img loading="lazy" :src="avatar.url" :alt="avatar.label" x-on:error="avatar.broken = true">
                    <span class="avatar-library-fallback">RP</span>
                </label>
            </template>
        </div>
    </div>
    {{ $slot }}
</div>
