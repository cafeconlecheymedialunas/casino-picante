@props([
    'label',
    'model',
    'value' => null,
    'upload' => null,
    'removeAction' => null,
    'hint' => 'PNG, JPG o WEBP',
    'variant' => 'wide',
])

@php
    $preview = $value;
    if (is_object($upload) && method_exists($upload, 'temporaryUrl')) {
        try {
            $preview = $upload->temporaryUrl();
        } catch (\Throwable $exception) {
            $preview = $value;
        }
    }
@endphp

<div class="image-uploader image-uploader-{{ $variant }}">
    <div class="image-uploader-head">
        <label class="image-uploader-label">{{ $label }}</label>
        @if($hint)
            <span class="image-uploader-hint">{{ $hint }}</span>
        @endif
    </div>

    <label class="image-uploader-drop">
        @if($preview)
            <img src="{{ $preview }}" alt="">
        @else
            <span class="image-uploader-empty">Seleccionar imagen</span>
        @endif
        <input type="file" wire:model="{{ $model }}" accept="image/png,image/jpeg,image/webp,image/gif">
    </label>

    <div class="image-uploader-actions">
        <label class="image-uploader-button">
            Subir
            <input type="file" wire:model="{{ $model }}" accept="image/png,image/jpeg,image/webp,image/gif">
        </label>
        @if($preview && $removeAction)
            <button type="button" class="image-uploader-button danger" wire:click="{{ $removeAction }}">Borrar</button>
        @endif
    </div>

    <div class="image-uploader-loading" wire:loading wire:target="{{ $model }}">Subiendo imagen...</div>
    {{ $slot }}
</div>
