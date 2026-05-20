<div class="vendors-page">
@section('header')
    <x-livewire.components.page-header title="GESTIÓN DE CAJEROS" subtitle="Administra vendors, usuarios cajero y configuraciones" />
@endsection

<style>
    .search-input { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; width:260px; }
    .search-input:focus { outline:none; border-color:var(--orange); }
    .admin-table { width:100%; border-collapse:collapse; background:#120909; border:1px solid var(--line); border-radius:8px; overflow:hidden; }
    .admin-table th { padding:12px 16px; text-align:left; font-size:11px; font-weight:800; color:var(--muted-2); text-transform:uppercase; letter-spacing:.08em; border-bottom:1px solid var(--line); }
    .admin-table td { padding:14px 16px; font-size:13px; border-bottom:1px solid var(--line); }
    .admin-table tr:last-child td { border-bottom:none; }
    .slug { font-family:var(--font-mono); font-size:12px; color:var(--muted); }
    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    .b-active { background:rgba(37,196,107,.15); color:#25c46b; }
    .b-inactive { background:rgba(255,80,80,.15); color:#ff5050; }
    .btn-icon { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; }
    .btn-icon:hover { border-color:var(--orange); color:var(--white); }
    .modal-overlay { position:fixed; inset:0; z-index:240; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.78); }
    .modal-panel { width:min(620px,100%); max-height:92vh; overflow-y:auto; border:1px solid var(--line-2); border-radius:8px; background:linear-gradient(180deg,#1c0e0e,#120909); }
    .modal-head { display:flex; justify-content:space-between; align-items:center; padding:18px 22px; border-bottom:1px solid var(--line); }
    .modal-head h3 { margin:0; font-size:18px; letter-spacing:.03em; }
    .modal-close { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; font-size:18px; }
    .modal-form { padding:22px; }
    .form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:14px; }
    .form-group { margin-bottom:14px; }
    .form-label { display:block; margin-bottom:6px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .form-input { width:100%; background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; }
    .form-error { color:#ff4757; font-size:11px; margin-top:4px; }
    .check-row { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:13px; font-weight:700; }
    .modal-actions { display:flex; gap:10px; }

    [data-dashboard-theme="light"] .vendors-page .admin-table,
    [data-dashboard-theme="light"] .vendors-page .modal-panel {
        background: #fffdf8 !important;
        background-image: none !important;
        border-color: var(--line) !important;
        color: var(--white);
        box-shadow: 0 12px 28px rgba(42,20,20,.06);
    }
    [data-dashboard-theme="light"] .vendors-page .admin-table th,
    [data-dashboard-theme="light"] .vendors-page .modal-head {
        background: rgba(244,234,220,.88) !important;
        border-color: var(--line) !important;
    }
    [data-dashboard-theme="light"] .vendors-page .admin-table td {
        border-color: var(--line) !important;
    }
    [data-dashboard-theme="light"] .vendors-page .admin-table tr:hover {
        background: rgba(255,106,26,.08) !important;
    }
    [data-dashboard-theme="light"] .vendors-page .search-input,
    [data-dashboard-theme="light"] .vendors-page .form-input {
        background: #fff !important;
        color: var(--white) !important;
        border-color: var(--line-2) !important;
    }
    [data-dashboard-theme="light"] .vendors-page .btn-icon,
    [data-dashboard-theme="light"] .vendors-page .modal-close {
        background: rgba(244,234,220,.78) !important;
        color: var(--muted) !important;
        border-color: var(--line) !important;
    }
</style>

    <div class="module-top-bar">
        <button type="button" class="btn-primary" wire:click="openCreateModal">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Nuevo Cajero
        </button>
        <div style="margin-left:auto">
            <input wire:model.live="search" type="text" placeholder="Buscar cajero..." class="search-input">
        </div>
    </div>

    <div class="table-scroll" style="margin: 0 24px;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Usuario Cajero</th>
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
                            <div>
                                <div style="font-weight:600">{{ $vendor->user->username }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $vendor->user->email }}</div>
                            </div>
                        @else
                            <span style="color:#ff5050;font-size:12px">Sin usuario</span>
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

    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal-panel" style="max-width:620px">
            <div class="modal-head">
                <h3>{{ $vendorId ? 'EDITAR CAJERO' : 'NUEVO CAJERO' }}</h3>
                <button class="modal-close" wire:click="closeModal">×</button>
            </div>

            <form class="modal-form" wire:submit.prevent="save">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nombre del Cajero</label>
                        <input class="form-input" wire:model="name" placeholder="Nombre del vendor">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input class="form-input" wire:model="slug" readonly>
                        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Usuario Cajero -->
                <div class="form-group">
                    <label class="form-label">Usuario Cajero</label>
                    <div style="display:flex;gap:16px;margin-bottom:8px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px">
                            <input type="radio" wire:model.live="user_mode" value="select"> Seleccionar existente
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px">
                            <input type="radio" wire:model.live="user_mode" value="create"> Crear nuevo
                        </label>
                    </div>

                    @if($user_mode === 'select')
                        <select class="form-input" wire:model="selected_user_id">
                            <option value="">-- Seleccionar usuario cajero --</option>
                            @foreach($cajeros as $c)
                                <option value="{{ $c->id }}">{{ $c->username }} — {{ $c->email }}</option>
                            @endforeach
                        </select>
                    @else
                        <div style="background:rgba(255,106,26,.07);border:1px solid rgba(255,106,26,.2);border-radius:8px;padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px">
                            <div>
                                <label class="form-label">Username</label>
                                <input class="form-input" wire:model="username">
                                @error('username') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input class="form-input" type="email" wire:model="email">
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div style="grid-column:1 / -1">
                                <label class="form-label">Contraseña inicial</label>
                                <input class="form-input" type="password" wire:model="password">
                                @error('password') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <x-upload-image label="Logo del Cajero" model="logoUpload" :value="$logo" removeAction="removeLogo" aspect="1" hint="PNG/JPG cuadrado" />
                    </div>
                    <div class="form-group">
                        <x-upload-image label="Imagen hero publica" model="heroImageUpload" :value="$heroImage" removeAction="removeHeroImage" aspect="16/9" hint="Fondo de /cajero/slug" />
                    </div>
                    <div class="form-group">
                        <x-upload-image label="Imagen perfil publica" model="portraitImageUpload" :value="$portraitImage" removeAction="removePortraitImage" aspect="3/4" hint="Figura/tarjeta del cajero" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-input" wire:model="description" rows="4"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contactos</label>
                    <livewire:components.contact-repeater wire:model="contacts" />
                </div>

                <div class="form-group">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                        <label class="form-label" style="margin:0">Características</label>
                        <button type="button" class="btn-soft" style="padding:2px 8px;font-size:11px" wire:click="addFeature">+ Agregar</button>
                    </div>
                    <div style="display:grid;gap:10px">
                        @foreach($features as $index => $char)
                        <div style="background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:8px;padding:12px;display:grid;grid-template-columns:1fr 1fr 40px;gap:10px;align-items:start">
                            <div style="grid-column:1 / span 2">
                                <label class="form-label" style="font-size:9px">Icono (FontAwesome)</label>
                                <input class="form-input" wire:model="features.{{$index}}.icon" placeholder="fa-solid fa-star">
                            </div>
                            <div>
                                <label class="form-label" style="font-size:9px">Título</label>
                                <input class="form-input" wire:model="features.{{$index}}.title" placeholder="Ej: Atención 24/7">
                            </div>
                            <div>
                                <label class="form-label" style="font-size:9px">Descripción</label>
                                <input class="form-input" wire:model="features.{{$index}}.description" placeholder="Ej: Soporte personalizado">
                            </div>
                            <div style="padding-top:20px">
                                <button type="button" class="btn-icon" style="color:#ff5050;border-color:rgba(255,80,80,.2)" wire:click="removeFeature({{$index}})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Branding (JSON)</label>
                    <textarea class="form-input" wire:model="brandingJson" rows="4" placeholder='{"primary":"#ff6a1a"}'></textarea>
                    @error('brandingJson') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="check-row">
                    <input type="checkbox" wire:model="is_active" id="active-check">
                    <label for="active-check">Cajero activo</label>
                </div>

                <div class="modal-actions" style="margin-top:24px;justify-content:flex-end">
                    <button type="button" class="btn-soft" wire:click="closeModal">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Cajero</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
