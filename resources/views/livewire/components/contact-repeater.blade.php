<div class="contact-repeater">
    @foreach($contacts as $index => $contact)
    <div class="contact-row" wire:key="cr-{{ $index }}">
        <div class="contact-main">
            <select wire:model="{{ $fieldName }}.{{ $index }}.type" class="contact-type">
                @foreach($types as $type => $label)
                <option value="{{ $type }}">{{ $label }}</option>
                @endforeach
            </select>
            @php
                $type = $contact['type'] ?? '';
                $placeholder = match ($type) {
                    'email' => 'Correo electrónico',
                    'instagram', 'facebook' => 'Usuario',
                    'whatsapp' => 'Número o chat',
                    'telegram' => 'Usuario o enlace',
                    default => 'Número, usuario o URL...',
                };
            @endphp
            <input 
                type="text" 
                wire:model="{{ $fieldName }}.{{ $index }}.value" 
                placeholder="{{ $placeholder }}" 
                class="contact-value"
            >
            <button type="button" wire:click="removeContact({{ $index }})" class="contact-remove" title="Eliminar">✕</button>
        </div>
        
        @if(in_array($contact['type'] ?? '', $messageTypes))
        <div class="contact-message-wrapper">
            <div class="contact-message-label">Mensaje</div>
            <textarea 
                wire:model="{{ $fieldName }}.{{ $index }}.message" 
                placeholder="Escribe el mensaje..." 
                rows="2"
                class="contact-msg-textarea"
            ></textarea>
        </div>
        @endif
    </div>
    @endforeach
    
    <button type="button" wire:click="addContact" class="contact-add">
        + Agregar contacto
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
    
    .contact-value:focus {
        outline: none;
        border-color: var(--orange);
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
    
    .contact-message-wrapper {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--line);
    }
    
    .contact-message-label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        color: var(--muted);
    }
    
    .contact-msg-textarea {
        width: 100%;
        padding: 10px 14px;
        background: linear-gradient(180deg, #1c0d0a, #120909);
        border: 1px solid var(--line-warm);
        border-radius: 8px;
        color: var(--white);
        font-size: 13px;
        resize: vertical;
    }
    
    .contact-msg-textarea:focus {
        outline: none;
        border-color: var(--orange);
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