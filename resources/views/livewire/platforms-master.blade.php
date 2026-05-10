<div>
@section('header')
    <x-livewire.components.page-header title="GESTIÓN GLOBAL DE PLATAFORMAS" subtitle="Catálogo maestro · los cambios se propagan a todas las líneas" />
@endsection

<div class="module-top-bar">
    <button type="button" class="btn-primary" wire:click="openCreateModal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Nueva plataforma
    </button>
</div>

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
        <div class="modal-panel" style="max-width:600px" wire:click.stop>

            <div class="modal-head">
                <h3><i class="fa-solid {{ $editingPlatform ? 'fa-pen-to-square' : 'fa-gamepad' }}" style="color:var(--orange);margin-right:8px"></i>{{ $editingPlatform ? 'Editar plataforma' : 'Nueva plataforma' }}</h3>
                <button class="modal-close" wire:click="closeModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form wire:submit.prevent="savePlatform" class="modal-form">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nombre <span style="color:var(--orange)">*</span></label>
                        <input type="text" wire:model="name" class="form-input" placeholder="Ej: Ganamos.net">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug <span style="color:var(--orange)">*</span> <span style="font-weight:400;color:var(--muted-2);text-transform:none;letter-spacing:0">(único, sin espacios)</span></label>
                        <input type="text" wire:model="slug" class="form-input" placeholder="Ej: ganamos">
                        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Logo + Estado --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:flex-start">
                    <div class="form-group">
                        <x-upload-image label="Logo" model="logoUpload" :value="$logo_url" remove-action="removeLogo" aspect="1" icon="fa-solid fa-gamepad">
                            @error('logoUpload') <div class="form-error">{{ $message }}</div> @enderror
                        </x-upload-image>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <label class="pm-switch-row" x-data="{ on: {{ $is_active ? 'true' : 'false' }} }">
                            <div class="pm-switch">
                                <input type="checkbox" wire:model="is_active" value="1" x-on:change="on = $event.target.checked">
                                <span class="pm-switch-track"><span class="pm-switch-thumb"></span></span>
                            </div>
                            <span style="font-size:13px;color:var(--muted)" :style="on ? 'color:var(--white)' : ''">
                                <span x-text="on ? 'Activa — disponible para líneas' : 'Inactiva'"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Sitio web</label>
                    <input type="url" wire:model="website_url" class="form-input" placeholder="https://...">
                    @error('website_url') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea wire:model="description" rows="2" class="form-input" style="resize:vertical"></textarea>
                </div>

                <div class="modal-actions">
                    @if($editingPlatform)
                    <button type="button" class="btn-ghost" style="color:#ff4757;border-color:rgba(255,71,87,.4);margin-right:auto"
                        wire:click="deletePlatform({{ $editingPlatform->id }})"
                        wire:confirm="¿Eliminar '{{ $editingPlatform->name }}'? Se desasociará de todas las líneas.">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                    @endif
                    <button type="button" class="btn-ghost" wire:click="closeModal">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $editingPlatform ? 'Guardar cambios' : 'Crear plataforma' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif

    <style>
        /* ── Listado ─────────────────────────────────────────────────── */
        .pm-flash { position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;font-size:13px; }
        .pm-grid { padding:24px 28px 40px;display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:14px; }
        .pm-card { background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:14px;padding:18px;transition:all 0.2s; }
        .pm-card:hover { border-color:var(--orange); }
        .pm-card.inactive { opacity:0.55; }
        .pm-header { display:flex;gap:12px;align-items:flex-start;margin-bottom:10px; }
        .pm-logo { width:44px;height:44px;border-radius:8px;object-fit:contain;background:rgba(255,255,255,0.05);padding:4px;flex-shrink:0; }
        .pm-logo-placeholder { width:44px;height:44px;border-radius:8px;background:rgba(255,106,26,0.12);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
        .pm-info { flex:1;min-width:0; }
        .pm-name { font-weight:700;font-size:15px;color:var(--white);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .pm-slug { font-size:11px;color:var(--muted);font-family:var(--font-mono); }
        .pm-actions { display:flex;gap:4px;flex-shrink:0; }
        .pm-btn { width:28px;height:28px;padding:0;font-size:12px; }
        .pm-desc { font-size:12px;color:var(--muted);margin-bottom:10px;line-height:1.4; }
        .pm-meta { display:flex;gap:8px;align-items:center;flex-wrap:wrap; }
        .pm-link { font-size:11px;color:var(--orange);text-decoration:none; }
        .pm-link:hover { text-decoration:underline; }
        .pm-status { font-size:11px;padding:2px 8px;border-radius:6px;font-weight:700; }
        .pm-status.active { background:rgba(37,196,107,0.12);color:var(--good); }
        .pm-status.inactive { background:rgba(255,255,255,0.05);color:var(--muted); }
        .pm-toggle { padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;border:1px solid var(--line);background:transparent;color:var(--muted);cursor:pointer;transition:all 0.2s; }
        .pm-toggle:hover { border-color:var(--orange);color:var(--orange); }
        .pm-lines-count { font-size:10px;color:var(--muted-2);margin-left:auto; }
        .pm-empty { grid-column:1/-1;text-align:center;color:var(--muted);padding:60px 40px;background:rgba(255,255,255,0.02);border:1px dashed var(--line);border-radius:14px; }

        /* ── Modal ───────────────────────────────────────────────────── */
        .modal-overlay { position:fixed;inset:0;z-index:400;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.78); }
        .modal-panel { width:min(620px,100%);max-height:92vh;overflow-y:auto;border:1px solid var(--line-2);border-radius:8px;background:linear-gradient(180deg,#1c0e0e,#120909); }
        .modal-head { display:flex;justify-content:space-between;align-items:center;gap:16px;padding:18px 22px;border-bottom:1px solid var(--line);position:sticky;top:0;background:#1c0e0e;z-index:1; }
        .modal-head h3 { margin:0;font-family:var(--font-display);font-size:22px;letter-spacing:.03em;display:flex;align-items:center; }
        .modal-close { width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px; }
        .modal-close:hover { border-color:var(--orange);color:var(--orange); }
        .modal-form { padding:22px; }
        .modal-actions { display:flex;gap:10px;justify-content:flex-end;margin-top:24px;flex-wrap:wrap; }

        /* ── Form ────────────────────────────────────────────────────── */
        .form-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px; }
        .form-group { margin-bottom:16px; }
        .form-label { display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase; }
        .form-input { width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body); }
        .form-input:focus { outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        .form-input[readonly] { opacity:.5;cursor:default; }
        textarea.form-input { resize:vertical; }
        select.form-input { cursor:pointer; }
        .form-error { margin-top:4px;color:#ff4757;font-size:11px; }


        /* ── Switch ──────────────────────────────────────────────────── */
        .pm-switch-row { display:flex;align-items:center;gap:12px;cursor:pointer;user-select:none; }
        .pm-switch { position:relative;flex-shrink:0; }
        .pm-switch input { position:absolute;opacity:0;width:0;height:0; }
        .pm-switch-track { display:block;width:44px;height:24px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid var(--line-2);transition:background .2s,border-color .2s;position:relative; }
        .pm-switch input:checked ~ .pm-switch-track { background:rgba(255,106,26,.35);border-color:rgba(255,106,26,.6); }
        .pm-switch-thumb { position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:var(--muted);transition:transform .2s,background .2s; }
        .pm-switch input:checked ~ .pm-switch-track .pm-switch-thumb { transform:translateX(20px);background:var(--orange); }
    </style>
</div>
