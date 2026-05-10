<div class="contact-repeater">
    @foreach($contacts as $index => $contact)
    <div class="contact-row" wire:key="cr-{{ $index }}">
        <div class="contact-row-header">Canal de contacto</div>
        <div class="contact-main">
            <select wire:model="{{ $fieldName }}.{{ $index }}.type" class="contact-type">
                @foreach($types as $type => $label)
                <option value="{{ $type }}">{{ $label }}</option>
                @endforeach
            </select>
            <input 
                type="url" 
                wire:model="{{ $fieldName }}.{{ $index }}.value" 
                placeholder="https://..." 
                class="contact-value"
            >
            <input 
                type="text" 
                wire:model="{{ $fieldName }}.{{ $index }}.name" 
                placeholder="Nombre (ej: línea principal)" 
                class="contact-name"
            >
            <button type="button" wire:click="removeContact({{ $index }})" class="contact-remove" title="Eliminar">✕</button>
        </div>
    </div>
    @endforeach
    
    <button type="button" wire:click="addContact" class="contact-add">
        + Agregar canal
    </button>
</div>

<style>
    .contact-repeater {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .contact-row {
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 12px;
    }
    
    .contact-row-header {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--muted);
        margin-bottom: 10px;
    }
    
    .contact-main {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .contact-type {
        width: 140px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #1c0d0a, #120909);
        border: 1px solid var(--line-warm);
        border-radius: 8px;
        color: var(--white);
        font-size: 13px;
        flex-shrink: 0;
    }
    
    .contact-value {
        flex: 1;
        padding: 10px 14px;
        background: linear-gradient(180deg, #1c0d0a, #120909);
        border: 1px solid var(--line-warm);
        border-radius: 8px;
        color: var(--white);
        font-size: 13px;
    }
    
    .contact-value:focus,
    .contact-name:focus {
        outline: none;
        border-color: var(--orange);
    }
    
    .contact-name {
        width: 180px;
        padding: 10px 14px;
        background: linear-gradient(180deg, #1c0d0a, #120909);
        border: 1px solid var(--line-warm);
        border-radius: 8px;
        color: var(--white);
        font-size: 13px;
        flex-shrink: 0;
    }
    
    .contact-remove {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        font-size: 12px;
        flex-shrink: 0;
    }
    
    .contact-remove:hover {
        border-color: #ff4757;
        color: #ff4757;
    }
    
    .contact-add {
        padding: 10px 16px;
        border-radius: 8px;
        border: 1px dashed var(--line);
        background: transparent;
        color: var(--orange);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .contact-add:hover {
        background: rgba(255,106,26,0.1);
        border-style: solid;
    }
</style>