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
    $inputId = 'img-upload-' . md5($model . uniqid());
    $initialPreview = $value;
    
    // Fallback to Livewire's temporaryUrl if Alpine preview is not yet set or during re-renders
    if (is_object($upload) && method_exists($upload, 'temporaryUrl')) {
        try {
            $initialPreview = $upload->temporaryUrl();
        } catch (\Throwable $exception) {
            // Ignore signature issues for now
        }
    }
@endphp

<div class="image-uploader image-uploader-{{ $variant }}" 
     x-data="{ 
        preview: '{{ $initialPreview }}',
        handleFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.preview = URL.createObjectURL(file);
            }
        }
     }"
     x-on:livewire:init="() => {
        // Optional: listen for property updates if needed
     }">
    <div class="image-uploader-head">
        <label class="image-uploader-label">{{ $label }}</label>
        @if($hint)
            <span class="image-uploader-hint">{{ $hint }}</span>
        @endif
    </div>

    <label class="image-uploader-drop" for="{{ $inputId }}">
        <img x-show="preview" :src="preview" alt="" style="display: none;" x-cloak>
        <span x-show="!preview" class="image-uploader-empty">Seleccionar imagen</span>
    </label>

    <input type="file" id="{{ $inputId }}" wire:model="{{ $model }}"
        accept="image/png,image/jpeg,image/webp,image/gif" style="display:none"
        @change="handleFile($event)">

    <div class="image-uploader-actions">
        <label class="image-uploader-button" for="{{ $inputId }}">Subir imagen</label>
        @if($removeAction)
            <button type="button" class="image-uploader-button danger" 
                    wire:click="{{ $removeAction }}"
                    @click="preview = ''">Borrar</button>
        @endif
    </div>

    <div class="image-uploader-loading" wire:loading wire:target="{{ $model }}">Subiendo imagen...</div>
    {{ $slot }}
</div>
