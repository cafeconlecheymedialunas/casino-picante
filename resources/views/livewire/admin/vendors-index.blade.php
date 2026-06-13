<div class="vendors-page">
@section('header')
    <x-livewire.components.page-header title="GESTIÓN DE CAJEROS" subtitle="Administra vendors, usuarios cajero y configuraciones" />
@endsection

<style>
    /* ── Tabla ── */
    .search-input{background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;width:260px;}
    .search-input:focus{outline:none;border-color:var(--orange);}
    .admin-table{width:100%;border-collapse:collapse;background:#120909;border:1px solid var(--line);border-radius:8px;overflow:hidden;}
    .admin-table th{padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:var(--muted-2);text-transform:uppercase;letter-spacing:.08em;border-bottom:1px solid var(--line);}
    .admin-table td{padding:14px 16px;font-size:13px;border-bottom:1px solid var(--line);}
    .admin-table tr:last-child td{border-bottom:none;}
    .slug{font-family:var(--font-mono);font-size:12px;color:var(--muted);}
    .badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;}
    .b-active{background:rgba(37,196,107,.15);color:#25c46b;}
    .b-inactive{background:rgba(255,80,80,.15);color:#ff5050;}
    .b-direct{background:rgba(255,106,26,.15);color:var(--orange);}
    .btn-icon{width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;}
    .btn-icon:hover{border-color:var(--orange);color:var(--white);}

    /* ── Modal ── */
    .modal-overlay{position:fixed;inset:0;z-index:240;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.78);}
    .modal-panel{width:min(680px,100%);max-height:92vh;overflow-y:auto;border:1px solid var(--line-2);border-radius:8px;background:linear-gradient(180deg,#1c0e0e,#120909);}
    .modal-head{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:18px 22px;border-bottom:1px solid var(--line);position:sticky;top:0;background:#1c0e0e;z-index:1;}
    .modal-head h3{margin:0;font-family:var(--font-display);font-size:22px;letter-spacing:.03em;}
    .modal-close{width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;}
    .modal-close:hover{border-color:var(--orange);color:var(--orange);}
    .modal-form{padding:22px;}

    /* ── Form ── */
    .form-group{margin-bottom:14px;}
    .form-label{display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;}
    .form-input{width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:10px 12px;color:var(--white);font-size:13px;}
    .form-input:focus{outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12);}
    textarea.form-input{resize:vertical;}
    .form-error{color:#ff4757;font-size:11px;margin-top:4px;}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
    .form-section-title{font-size:10px;font-weight:800;color:var(--orange);letter-spacing:.1em;text-transform:uppercase;margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid var(--line);}
    .check-row{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:13px;font-weight:700;}
    .contact-icon{width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}

    /* ── Light theme ── */
    [data-dashboard-theme="light"] .modal-panel,
    [data-dashboard-theme="light"] .admin-table{background:#fffdf8 !important;background-image:none !important;border-color:var(--line) !important;box-shadow:0 12px 28px rgba(42,20,20,.06);}
    [data-dashboard-theme="light"] .modal-head,
    [data-dashboard-theme="light"] .admin-table th{background:rgba(244,234,220,.88) !important;border-color:var(--line) !important;}
    [data-dashboard-theme="light"] .admin-table td{border-color:var(--line) !important;}
    [data-dashboard-theme="light"] .form-input{background:#fff !important;border-color:var(--line-2) !important;}
    [data-dashboard-theme="light"] .btn-icon,[data-dashboard-theme="light"] .modal-close{background:rgba(244,234,220,.78) !important;color:var(--muted) !important;border-color:var(--line) !important;}
</style>

    {{-- Barra superior --}}
    <div class="module-top-bar">
        <button type="button" class="btn-primary" wire:click="openCreateModal">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Nuevo Cajero
        </button>
        <div style="margin-left:auto">
            <input wire:model.live="search" type="text" placeholder="Buscar cajero..." class="search-input">
        </div>
    </div>

    {{-- Tabla --}}
    <div class="table-scroll" style="margin:0 24px">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Usuario Cajero</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendors as $vendor)
                <tr>
                    <td><strong>{{ $vendor->name }}</strong></td>
                    <td><span class="slug">{{ $vendor->slug }}</span></td>
                    <td>
                        @if($vendor->user)
                            <div style="font-weight:600">{{ $vendor->user->username }}</div>
                            <div style="font-size:11px;color:var(--muted)">{{ $vendor->user->email }}</div>
                        @else
                            <span style="color:#ff5050;font-size:12px">Sin usuario</span>
                        @endif
                    </td>
                    <td>
                        @if($vendor->is_direct)
                            <span class="badge b-direct"><i class="fa-solid fa-crown" style="font-size:9px;margin-right:4px"></i>Oficial</span>
                        @else
                            <span class="badge" style="background:rgba(255,255,255,.06);color:var(--muted)">Cajero</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $vendor->is_active ? 'b-active' : 'b-inactive' }}">
                            {{ $vendor->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <button class="btn-icon" wire:click="edit({{ $vendor->id }})" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;display:flex;justify-content:center;">
        {{ $vendors->links() }}
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal-panel">

            <div class="modal-head">
                <h3><i class="fa-solid fa-store" style="color:var(--orange);margin-right:10px;font-size:18px"></i>{{ $vendorId ? 'EDITAR CAJERO' : 'NUEVO CAJERO' }}</h3>
                <button class="modal-close" wire:click="closeModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form class="modal-form" wire:submit.prevent="save">

                {{-- ── Información básica ── --}}
                <div class="form-section-title">Información básica</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nombre del Cajero *</label>
                        <input class="form-input" wire:model="name" placeholder="Ej: Cajero Oficial">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input class="form-input" wire:model="slug" readonly style="opacity:.55;cursor:not-allowed">
                        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-input" wire:model="description" rows="3" placeholder="Breve descripción del cajero..."></textarea>
                </div>

                {{-- ── Usuario Cajero ── --}}
                <div class="form-section-title">Usuario Cajero</div>

                <div style="display:flex;gap:20px;margin-bottom:12px">
                    <label style="display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer">
                        <input type="radio" wire:model.live="user_mode" value="select"> Seleccionar existente
                    </label>
                    <label style="display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer">
                        <input type="radio" wire:model.live="user_mode" value="create"> Crear nuevo usuario
                    </label>
                </div>

                @if($user_mode === 'select')
                    <div class="form-group">
                        <select class="form-input" wire:model="selected_user_id">
                            <option value="">-- Seleccionar usuario cajero --</option>
                            @foreach($cajeros as $c)
                                <option value="{{ $c->id }}">{{ $c->username }} — {{ $c->email }}</option>
                            @endforeach
                        </select>
                        @error('selected_user_id') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                @else
                    <div style="background:rgba(255,106,26,.07);border:1px solid rgba(255,106,26,.2);border-radius:8px;padding:16px">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input class="form-input" wire:model="username" placeholder="nombre_cajero">
                                @error('username') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input class="form-input" type="email" wire:model="email" placeholder="cajero@email.com">
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label">Contraseña inicial</label>
                            <input class="form-input" type="password" wire:model="password">
                            @error('password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endif

                {{-- ── Imágenes ── --}}
                <div class="form-section-title">Imágenes</div>

                <div class="form-grid">
                    <div class="form-group">
                        <x-upload-image label="Logo (cuadrado)" model="logoUpload" :value="$logo" removeAction="removeLogo" aspect="1" hint="PNG/JPG" />
                    </div>
                    <div class="form-group">
                        <x-upload-image label="Foto del cajero (cara)" model="heroImageUpload" :value="$heroImage" removeAction="removeHeroImage" aspect="3/4" hint="Foto de perfil / avatar" />
                    </div>
                    <div class="form-group">
                        <x-upload-image label="Portada / Banner de fondo" model="portraitImageUpload" :value="$portraitImage" removeAction="removePortraitImage" aspect="16/9" hint="Imagen grande del hero" />
                    </div>
                </div>

                {{-- ── Canales de contacto ── --}}
                <div class="form-section-title">Canales de contacto</div>
                <p style="font-size:12px;color:var(--muted-2);margin:-6px 0 14px">Completá solo los que uses. Dejá vacío para omitir.</p>

                @php
                $contactFields = [
                    'contactWhatsapp'  => ['icon' => 'fa-brands fa-whatsapp',  'color' => '#25d366', 'label' => 'WhatsApp',  'placeholder' => '+549 11 0000-0000'],
                    'contactTelegram'  => ['icon' => 'fa-brands fa-telegram',   'color' => '#2AABEE', 'label' => 'Telegram',  'placeholder' => '@usuario o https://t.me/...'],
                    'contactInstagram' => ['icon' => 'fa-brands fa-instagram',  'color' => '#e1306c', 'label' => 'Instagram', 'placeholder' => 'https://instagram.com/...'],
                    'contactFacebook'  => ['icon' => 'fa-brands fa-facebook',   'color' => '#1877f2', 'label' => 'Facebook',  'placeholder' => 'https://facebook.com/...'],
                    'contactEmail'     => ['icon' => 'fa-solid fa-envelope',    'color' => '#ff6a1a', 'label' => 'Email',     'placeholder' => 'contacto@email.com'],
                    'contactPhone'     => ['icon' => 'fa-solid fa-phone',       'color' => '#a78bfa', 'label' => 'Teléfono',  'placeholder' => '+549 11 0000-0000'],
                    'contactWeb'       => ['icon' => 'fa-solid fa-globe',       'color' => '#38bdf8', 'label' => 'Web',       'placeholder' => 'https://...'],
                ];
                @endphp

                @foreach($contactFields as $prop => $field)
                <div class="form-group">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                        <span class="contact-icon" style="background:{{ $field['color'] }}1a;border:1px solid {{ $field['color'] }}40;color:{{ $field['color'] }}">
                            <i class="{{ $field['icon'] }}"></i>
                        </span>
                        <span class="form-label" style="margin:0">{{ $field['label'] }}</span>
                    </div>
                    <div class="form-grid">
                        <div>
                            <input class="form-input" wire:model="{{ $prop }}.value" placeholder="{{ $field['placeholder'] }}">
                        </div>
                        <div>
                            <input class="form-input" wire:model="{{ $prop }}.name" placeholder="Etiqueta (ej: Soporte, Canal Oficial...)">
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- ── Características ── --}}
                <div class="form-section-title" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Características</span>
                    <button type="button" class="btn-soft" style="font-size:11px;padding:0 10px;height:26px" wire:click="addFeature">+ Agregar</button>
                </div>

                @foreach($features as $index => $char)
                <div style="background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:8px;padding:10px 12px;display:grid;grid-template-columns:44px 1fr 1fr auto;gap:10px;align-items:center;margin-bottom:8px"
                     @icon-selected.window="if($event.detail.name === 'feature_icon_{{ $index }}') $wire.set('features.{{ $index }}.icon', $event.detail.value)"
                     x-data="{ iconOpen: false }">
                    <div style="position:relative">
                        <button type="button" @click="iconOpen = !iconOpen"
                            style="width:44px;height:44px;border-radius:8px;border:1px solid rgba(255,106,26,.3);background:rgba(255,106,26,.10);color:var(--orange);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <i class="{{ $char['icon'] ?? 'fa-solid fa-star' }}"></i>
                        </button>
                        <div x-show="iconOpen" @click.outside="iconOpen = false" x-cloak
                            style="position:absolute;top:52px;left:0;z-index:50;width:320px;background:#120707;border:1px solid var(--line);border-radius:10px;padding:10px;box-shadow:0 20px 48px rgba(0,0,0,.5)">
                            <x-icon-library label="" name="feature_icon_{{ $index }}" :selected="$char['icon'] ?? 'fa-solid fa-star'" />
                        </div>
                    </div>
                    <div>
                        <div class="form-label" style="margin-bottom:4px">Título</div>
                        <input class="form-input" wire:model="features.{{ $index }}.title" placeholder="Ej: Atención 24/7">
                    </div>
                    <div>
                        <div class="form-label" style="margin-bottom:4px">Descripción</div>
                        <input class="form-input" wire:model="features.{{ $index }}.description" placeholder="Ej: Soporte personalizado">
                    </div>
                    <button type="button" class="btn-icon" style="color:#ff5050;border-color:rgba(255,80,80,.2)" wire:click="removeFeature({{ $index }})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                @endforeach

                {{-- ── Estado ── --}}
                <div class="form-section-title">Estado</div>
                <div style="background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:8px;padding:14px 16px;display:flex;flex-direction:column;gap:12px">
                    <div class="check-row">
                        <input type="checkbox" wire:model="is_active" id="v-active">
                        <label for="v-active">Cajero activo</label>
                    </div>
                    <div class="check-row">
                        <input type="checkbox" wire:model="is_direct" id="v-direct">
                        <label for="v-direct" style="display:flex;align-items:center;gap:6px">
                            <i class="fa-solid fa-crown" style="color:var(--orange);font-size:12px"></i>
                            Cajero oficial / directo
                            <span style="font-size:11px;font-weight:400;color:var(--muted-2)">(tab "Directas" del frontend)</span>
                        </label>
                    </div>
                </div>

                {{-- ── Acciones ── --}}
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:22px">
                    <button type="button" class="btn-soft" wire:click="closeModal">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Cajero
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif
</div>
