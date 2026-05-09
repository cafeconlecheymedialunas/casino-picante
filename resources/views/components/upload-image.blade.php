@props([
    'label',
    'model',
    'value'        => '',
    'removeAction' => null,
    'aspect'       => null,   // e.g. "16/9", "1" — null = auto height
    'icon'         => 'fa-solid fa-image',
    'hint'         => null,
])

@php $refName = 'input_' . md5($model); @endphp

<div x-data="{ preview: '{{ $value }}' }">
    <label class="form-label">
        {{ $label }}
        @if($hint) <span style="font-weight:400;color:var(--muted-2);text-transform:none;letter-spacing:0;font-size:10px">{{ $hint }}</span> @endif
    </label>

    <div style="position:relative;cursor:pointer;border:1px dashed var(--line-2);border-radius:8px;overflow:hidden;{{ $aspect ? 'aspect-ratio:'.$aspect.';' : 'min-height:90px;' }}background:rgba(255,255,255,.03);transition:border-color .15s"
         @mouseover="$el.style.borderColor='var(--orange)'"
         @mouseout="$el.style.borderColor=''"
         @click="$refs.{{ $refName }}.click()">

        <img x-show="preview" :src="preview" style="width:100%;height:100%;object-fit:cover;display:block">

        <div x-show="!preview" style="height:100%;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--muted-2);font-size:12px;padding:16px">
            <i class="{{ $icon }}" style="font-size:24px;opacity:.4"></i>
            Clic para seleccionar
        </div>

        <input type="file"
               x-ref="{{ $refName }}"
               wire:model="{{ $model }}"
               accept="image/png,image/jpeg,image/webp,image/gif"
               style="display:none"
               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
    </div>

    <div wire:loading wire:target="{{ $model }}" style="color:var(--orange);font-size:11px;margin-top:4px;font-weight:700">
        <i class="fa-solid fa-spinner fa-spin"></i> Subiendo...
    </div>

    @if($value && $removeAction)
        <button type="button" wire:click="{{ $removeAction }}"
                @click="preview = ''"
                style="margin-top:6px;font-size:11px;display:inline-flex;align-items:center;gap:5px;color:#ff4757;background:rgba(255,71,87,.1);border:1px solid rgba(255,71,87,.3);border-radius:6px;padding:4px 10px;cursor:pointer">
            <i class="fa-solid fa-trash"></i> Borrar
        </button>
    @endif

    {{ $slot }}
</div>
