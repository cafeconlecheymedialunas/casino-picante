<div>
    <x-livewire.components.page-header title="GESTIÓN GLOBAL DE PLATAFORMAS" subtitle="Catálogo maestro · los cambios se propagan a todas las líneas" buttonText="+ Nueva plataforma" buttonAction="openCreateModal" />

    @if(session()->has('message'))
    <div class="pm-flash">{{ session('message') }}</div>
    @endif

    <div class="pm-grid">
        @forelse($platforms as $platform)
        <div class="pm-card {{ !$platform->is_active ? 'inactive' : '' }}">
            <div class="pm-header">
                @if($platform->logo_url)
                <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" class="pm-logo">
                @else
                <div class="pm-logo-placeholder">🎮</div>
                @endif
                <div class="pm-info">
                    <div class="pm-name">{{ $platform->name }}</div>
                    <div class="pm-slug">{{ $platform->slug }}</div>
                </div>
                <div class="pm-actions">
                    <button class="btn-ghost pm-btn" wire:click="openEditModal({{ $platform->id }})">✎</button>
                    <button class="btn-ghost pm-btn danger" wire:click="deletePlatform({{ $platform->id }})"
                        wire:confirm="¿Eliminar '{{ $platform->name }}'? Se desasociará de todas las líneas.">🗑</button>
                </div>
            </div>

            @if($platform->description)
            <div class="pm-desc">{{ $platform->description }}</div>
            @endif

            {{-- Contacts preview --}}
            @if($platform->contacts && count($platform->contacts) > 0)
            <div class="pm-contacts">
                @foreach($platform->contacts as $contact)
                <span class="pm-contact-badge pm-contact-{{ $contact['type'] }}">
                    @if($contact['type'] === 'whatsapp')💬
                    @elseif($contact['type'] === 'telegram')✈️
                    @elseif($contact['type'] === 'instagram')📷
                    @elseif($contact['type'] === 'facebook')📘
                    @endif
                    {{ $contact['value'] }}
                </span>
                @endforeach
            </div>
            @endif

            <div class="pm-meta">
                @if($platform->website_url)
                <a href="{{ $platform->website_url }}" target="_blank" class="pm-link">🌐 Sitio web</a>
                @endif
                <span class="pm-status {{ $platform->is_active ? 'active' : 'inactive' }}">
                    {{ $platform->is_active ? '● Activa' : '● Inactiva' }}
                </span>
                <button class="pm-toggle" wire:click="toggleActive({{ $platform->id }})">
                    {{ $platform->is_active ? 'Desactivar' : 'Activar' }}
                </button>
                <span class="pm-lines-count">
                    {{ $platform->lines->count() }} {{ $platform->lines->count() === 1 ? 'línea' : 'líneas' }}
                </span>
            </div>
        </div>
        @empty
        <div class="pm-empty">
            <div style="font-size:36px;margin-bottom:10px;">🎮</div>
            <p>No hay plataformas creadas aún.</p>
            <p style="font-size:12px;margin-top:4px;">Creá la primera plataforma para que los agentes puedan asociarla a sus líneas.</p>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingPlatform ? 'EDITAR PLATAFORMA' : 'NUEVA PLATAFORMA' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="savePlatform">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" wire:model="name" placeholder="Ej: Ganamos.net">
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Slug * <span style="font-weight:400;color:var(--muted-2);">(único, sin espacios)</span></label>
                        <input type="text" wire:model="slug" placeholder="Ej: ganamos">
                        @error('slug') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <x-image-uploader label="Logo" model="logoUpload" :upload="$logoUpload" :value="$logo_url" remove-action="removeLogo" variant="logo">
                        @error('logoUpload') <span class="form-error">{{ $message }}</span> @enderror
                    </x-image-uploader>
                </div>
                <div class="form-group">
                    <label>Sitio Web</label>
                    <input type="url" wire:model="website_url" placeholder="https://...">
                    @error('website_url') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea wire:model="description" rows="2" placeholder="Breve descripción..."></textarea>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select wire:model="is_active">
                        <option value="1">Activa (disponible para líneas)</option>
                        <option value="0">Inactiva</option>
                    </select>
                </div>

                {{-- Contacts Repeater --}}
                <div class="form-group">
                    <label style="margin-bottom:10px;display:block;">
                        Contactos de la plataforma
                        <span style="font-weight:400;color:var(--muted-2);font-size:11px;"> · WhatsApp, Telegram, Instagram, Facebook</span>
                    </label>
                    <div class="repeater">
                        @foreach($contacts as $index => $contact)
                        <div class="repeater-item" wire:key="contact-{{ $index }}">
                            <select wire:model="contacts.{{ $index }}.type" class="repeater-type">
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="telegram">✈️ Telegram</option>
                                <option value="instagram">📷 Instagram</option>
                                <option value="facebook">📘 Facebook</option>
                            </select>
                            <input type="text"
                                wire:model="contacts.{{ $index }}.value"
                                placeholder="{{ in_array($contacts[$index]['type'] ?? '', ['whatsapp']) ? '+54 9 11 ...' : ($contacts[$index]['type'] === 'telegram' ? '@usuario' : 'URL o usuario') }}"
                                class="repeater-value">
                            <button type="button" wire:click="removeContact({{ $index }})" class="repeater-remove">✕</button>
                        </div>
                        @if(in_array($contact['type'] ?? '', ['whatsapp', 'telegram']))
                        <div class="repeater-message" wire:key="msg-{{ $index }}">
                            <textarea
                                wire:model="contacts.{{ $index }}.message"
                                placeholder="Mensaje automático (opcional)..."
                                rows="2"
                                class="repeater-msg-area"></textarea>
                        </div>
                        @endif
                        @endforeach
                        <button type="button" wire:click="addContact" class="repeater-add">+ Agregar contacto</button>
                    </div>
                </div>

                <div class="modal-actions">
                    @if($editingPlatform)
                    <button type="button" class="btn-ghost" style="color:#ff4757;border-color:#ff4757;"
                        wire:click="deletePlatform({{ $editingPlatform->id }})"
                        wire:confirm="¿Eliminar esta plataforma?">Eliminar</button>
                    @endif
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingPlatform ? 'Guardar cambios' : 'Crear plataforma' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        .pm-flash {
            position: fixed; top: 20px; right: 20px; background: var(--good); color: #000;
            padding: 12px 20px; border-radius: 8px; font-weight: 700; z-index: 2000; font-size: 13px;
        }

        .pm-grid {
            padding: 24px 28px 40px;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 14px;
        }

        .pm-card {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm); border-radius: 14px; padding: 18px;
            transition: all 0.2s;
        }
        .pm-card:hover { border-color: var(--orange); }
        .pm-card.inactive { opacity: 0.55; }

        .pm-header { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 10px; }
        .pm-logo { width: 44px; height: 44px; border-radius: 8px; object-fit: contain; background: rgba(255,255,255,0.05); padding: 4px; flex-shrink: 0; }
        .pm-logo-placeholder { width: 44px; height: 44px; border-radius: 8px; background: rgba(255,106,26,0.12); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .pm-info { flex: 1; min-width: 0; }
        .pm-name { font-weight: 700; font-size: 15px; color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pm-slug { font-size: 11px; color: var(--muted); font-family: var(--font-mono); }
        .pm-actions { display: flex; gap: 4px; flex-shrink: 0; }
        .pm-btn { width: 28px; height: 28px; padding: 0; font-size: 12px; }

        .pm-desc { font-size: 12px; color: var(--muted); margin-bottom: 10px; line-height: 1.4; }

        .pm-contacts { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; }
        .pm-contact-badge {
            font-size: 10px; padding: 3px 8px; border-radius: 6px; font-family: var(--font-mono);
            background: rgba(255,255,255,0.06); color: var(--muted-2); border: 1px solid var(--line);
        }
        .pm-contact-badge.pm-contact-whatsapp { background: rgba(37,196,107,0.1); color: #25c46b; border-color: rgba(37,196,107,0.25); }
        .pm-contact-badge.pm-contact-telegram { background: rgba(100,150,255,0.1); color: #6496ff; border-color: rgba(100,150,255,0.25); }
        .pm-contact-badge.pm-contact-instagram { background: rgba(255,106,180,0.1); color: #ff6ab4; border-color: rgba(255,106,180,0.25); }
        .pm-contact-badge.pm-contact-facebook { background: rgba(100,130,255,0.1); color: #6482ff; border-color: rgba(100,130,255,0.25); }

        .pm-meta { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .pm-link { font-size: 11px; color: var(--orange); text-decoration: none; }
        .pm-link:hover { text-decoration: underline; }
        .pm-status { font-size: 11px; padding: 2px 8px; border-radius: 6px; font-weight: 700; }
        .pm-status.active { background: rgba(37,196,107,0.12); color: var(--good); }
        .pm-status.inactive { background: rgba(255,255,255,0.05); color: var(--muted); }
        .pm-toggle {
            padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
            border: 1px solid var(--line); background: transparent; color: var(--muted);
            cursor: pointer; transition: all 0.2s;
        }
        .pm-toggle:hover { border-color: var(--orange); color: var(--orange); }
        .pm-lines-count { font-size: 10px; color: var(--muted-2); margin-left: auto; }

        .pm-empty {
            grid-column: 1 / -1; text-align: center; color: var(--muted);
            padding: 60px 40px; background: rgba(255,255,255,0.02);
            border: 1px dashed var(--line); border-radius: 14px;
        }

        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.8); display: flex; align-items: center;
            justify-content: center; z-index: 1000; padding: 20px;
        }
        .modal-content {
            background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%);
            border: 1px solid var(--line); border-radius: 20px;
            width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto;
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 24px; border-bottom: 1px solid var(--line);
            position: sticky; top: 0; background: #1a0d0d; z-index: 1;
        }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group textarea {
            width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm); border-radius: 10px;
            padding: 12px 16px; color: var(--white); font-size: 14px; font-family: var(--font-body);
        }
        .form-group textarea { resize: vertical; }
        .form-error { color: #ff4757; font-size: 11px; margin-top: 4px; display: block; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; flex-wrap: wrap; }

        .repeater { display: flex; flex-direction: column; gap: 0; }
        .repeater-item {
            display: flex; gap: 8px; align-items: center;
            padding: 8px; background: rgba(255,255,255,0.03);
            border: 1px solid var(--line); border-radius: 8px 8px 0 0;
            margin-top: 8px;
        }
        .repeater-message {
            padding: 0 8px 8px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--line); border-top: none;
            border-radius: 0 0 8px 8px;
            margin-bottom: 0;
        }
        .repeater-type {
            flex-shrink: 0; width: 140px;
            background: linear-gradient(180deg,#1c0d0a,#120909);
            border: 1px solid var(--line-warm); border-radius: 6px;
            padding: 7px 10px; color: var(--white); font-size: 12px;
        }
        .repeater-value {
            flex: 1; background: linear-gradient(180deg,#1c0d0a,#120909);
            border: 1px solid var(--line-warm); border-radius: 6px;
            padding: 7px 10px; color: var(--white); font-size: 12px;
        }
        .repeater-remove {
            flex-shrink: 0; width: 30px; height: 30px; padding: 0;
            background: rgba(255,71,87,0.15); border: 1px solid rgba(255,71,87,0.4);
            color: #ff4757; border-radius: 6px; cursor: pointer; font-size: 12px;
        }
        .repeater-msg-area {
            width: 100%; background: linear-gradient(180deg,#1c0d0a,#120909);
            border: 1px solid var(--line); border-radius: 6px;
            padding: 8px 10px; color: var(--muted); font-size: 12px;
            font-family: var(--font-body); resize: none; margin-top: 6px;
        }
        .repeater-add {
            margin-top: 10px; padding: 7px 14px;
            background: rgba(255,106,26,0.12); border: 1px solid rgba(255,106,26,0.35);
            color: var(--orange); border-radius: 6px; cursor: pointer;
            font-size: 12px; font-weight: 700; transition: all 0.2s; align-self: flex-start;
        }
        .repeater-add:hover { background: rgba(255,106,26,0.2); }
    </style>
</div>
