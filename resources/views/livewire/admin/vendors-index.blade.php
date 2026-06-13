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
    .b-direct { background:rgba(255,106,26,.15); color:var(--orange); }
    .btn-icon { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; }
    .btn-icon:hover { border-color:var(--orange); color:var(--white); }

    /* Modal estándar del proyecto */
    .nv-modal-overlay { position:fixed; inset:0; z-index:400; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.78); }
    .nv-modal-wide { width:min(660px,100%); max-height:92vh; overflow-y:auto; border:1px solid var(--line-2); border-radius:16px; background:linear-gradient(180deg,#1c0e0e,#120909); }
    .nv-modal-head { display:flex; justify-content:space-between; align-items:center; padding:16px 22px; border-bottom:1px solid var(--line); font-family:var(--font-display); font-size:18px; letter-spacing:.03em; position:sticky; top:0; background:#1c0e0e; z-index:1; }
    .nv-modal-body { padding:22px; display:flex; flex-direction:column; gap:16px; }
    .modal-close { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; }
    .modal-close:hover { border-color:var(--orange); color:var(--orange); }
    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
    .check-row { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:13px; font-weight:700; }
    .section-box { background:rgba(255,255,255,.03); border:1px solid var(--line); border-radius:8px; padding:16px; display:flex; flex-direction:column; gap:12px; }
    .user-mode-tabs { display:flex; gap:16px; margin-bottom:8px; }
    .user-mode-tab { display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; }
    .create-user-grid { background:rgba(255,106,26,.07); border:1px solid rgba(255,106,26,.2); border-radius:8px; padding:16px; display:grid; grid-template-columns:1fr 1fr; gap:14px; }

    [data-dashboard-theme="light"] .nv-modal-wide,
    [data-dashboard-theme="light"] .vendors-page .admin-table {
        background:#fffdf8 !important;
        background-image:none !important;
        border-color:var(--line) !important;
        box-shadow:0 12px 28px rgba(42,20,20,.06);
    }
    [data-dashboard-theme="light"] .vendors-page .nv-modal-head,
    [data-dashboard-theme="light"] .vendors-page .admin-table th {
        background:rgba(244,234,220,.88) !important;
        border-color:var(--line) !important;
    }
    [data-dashboard-theme="light"] .vendors-page .admin-table td { border-color:var(--line) !important; }
    [data-dashboard-theme="light"] .vendors-page .admin-table tr:hover { background:rgba(255,106,26,.08) !important; }
    [data-dashboard-theme="light"] .vendors-page .search-input,
    [data-dashboard-theme="light"] .vendors-page .form-input { background:#fff !important; border-color:var(--line-2) !important; }
    [data-dashboard-theme="light"] .vendors-page .btn-icon,
    [data-dashboard-theme="light"] .vendors-page .modal-close { background:rgba(244,234,220,.78) !important; color:var(--muted) !important; border-color:var(--line) !important; }

    @media(max-width:600px) {
        .form-grid-2, .form-grid-3 { grid-template-columns:1fr; }
    }
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
                            <div>
                                <div style="font-weight:600">{{ $vendor->user->username }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $vendor->user->email }}</div>
                            </div>
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

    {{-- Modal crear / editar --}}
    @if($showModal)
    <div class="nv-modal-overlay" wire:click.self="closeModal">
        <div class="nv-modal-wide">

            {{-- Cabecera --}}
            <div class="nv-modal-head">
                <span>
                    <i class="fa-solid fa-store" style="color:var(--orange);margin-right:8px"></i>
                    {{ $vendorId ? 'EDITAR CAJERO' : 'NUEVO CAJERO' }}
                </span>
                <button class="modal-close" wire:click="closeModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form class="nv-modal-body" wire:submit.prevent="save">

                {{-- Nombre + Slug --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nombre del Cajero</label>
                        <input class="form-input" wire:model="name" placeholder="Nombre del cajero">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input class="form-input" wire:model="slug" readonly style="opacity:.6">
                        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Usuario Cajero --}}
                <div class="form-group">
                    <label class="form-label">Usuario Cajero</label>
                    <div class="user-mode-tabs">
                        <label class="user-mode-tab">
                            <input type="radio" wire:model.live="user_mode" value="select"> Seleccionar existente
                        </label>
                        <label class="user-mode-tab">
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
                        @error('selected_user_id') <div class="form-error">{{ $message }}</div> @enderror
                    @else
                        <div class="create-user-grid">
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input class="form-input" wire:model="username">
                                @error('username') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input class="form-input" type="email" wire:model="email">
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group" style="grid-column:1 / -1">
                                <label class="form-label">Contraseña inicial</label>
                                <input class="form-input" type="password" wire:model="password">
                                @error('password') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Imágenes fila 1: Logo + Hero --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <x-upload-image label="Logo" model="logoUpload" :value="$logo" removeAction="removeLogo" aspect="1" hint="PNG/JPG cuadrado" />
                    </div>
                    <div class="form-group">
                        <x-upload-image label="Hero (fondo)" model="heroImageUpload" :value="$heroImage" removeAction="removeHeroImage" aspect="16/9" hint="Banner principal" />
                    </div>
                </div>

                {{-- Imagen perfil + Descripción --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <x-upload-image label="Perfil" model="portraitImageUpload" :value="$portraitImage" removeAction="removePortraitImage" aspect="3/4" hint="Tarjeta del cajero" />
                    </div>
                    <div class="form-group" style="display:flex;flex-direction:column">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-input" wire:model="description" rows="6" style="resize:vertical;flex:1;min-height:140px;background:rgba(255,255,255,.06);border:1px solid var(--line-2);color:var(--white)"></textarea>
                    </div>
                </div>

                {{-- Contactos --}}
                <div class="form-group">
                    <label class="form-label">Contactos</label>
                    <livewire:components.contact-repeater wire:model="contacts" />
                </div>

                {{-- Características --}}
                <div class="form-group">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                        <label class="form-label" style="margin:0">Características</label>
                        <button type="button" class="btn-soft" style="padding:2px 10px;font-size:11px" wire:click="addFeature">+ Agregar</button>
                    </div>
                    <div style="display:grid;gap:8px">
                        @foreach($features as $index => $char)
                        <div
                            style="background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:8px;padding:10px 12px;display:grid;grid-template-columns:44px 1fr 1fr auto;gap:10px;align-items:center"
                            @icon-selected.window="if($event.detail.name === 'feature_icon_{{ $index }}') $wire.set('features.{{ $index }}.icon', $event.detail.value)"
                            x-data="{ iconOpen: false }"
                        >
                            <div style="position:relative">
                                <button
                                    type="button"
                                    title="Cambiar icono"
                                    @click="iconOpen = !iconOpen"
                                    style="width:44px;height:44px;border-radius:8px;border:1px solid rgba(255,106,26,.3);background:rgba(255,106,26,.10);color:var(--orange);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                                >
                                    <i class="{{ $char['icon'] ?? 'fa-solid fa-star' }}"></i>
                                </button>
                                <div
                                    x-show="iconOpen"
                                    @click.outside="iconOpen = false"
                                    x-cloak
                                    style="position:absolute;top:52px;left:0;z-index:50;width:320px;background:#120707;border:1px solid var(--line);border-radius:10px;padding:10px;box-shadow:0 20px 48px rgba(0,0,0,.5)"
                                >
                                    <x-icon-library label="" name="feature_icon_{{ $index }}" :selected="$char['icon'] ?? 'fa-solid fa-star'" />
                                </div>
                            </div>
                            <div>
                                <div style="font-size:9px;font-weight:900;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px">Título</div>
                                <input class="form-input" wire:model="features.{{$index}}.title" placeholder="Ej: Atención 24/7">
                            </div>
                            <div>
                                <div style="font-size:9px;font-weight:900;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px">Descripción</div>
                                <input class="form-input" wire:model="features.{{$index}}.description" placeholder="Ej: Soporte personalizado">
                            </div>
                            <button type="button" class="btn-icon" style="color:#ff5050;border-color:rgba(255,80,80,.2)" wire:click="removeFeature({{$index}})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Branding JSON --}}
                <div class="form-group">
                    <label class="form-label">Branding (JSON)</label>
                    <textarea class="form-input" wire:model="brandingJson" rows="3" style="font-family:var(--font-mono);font-size:12px;resize:vertical" placeholder='{"primary":"#ff6a1a"}'></textarea>
                    @error('brandingJson') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                {{-- Checkboxes --}}
                <div class="section-box">
                    <div class="check-row">
                        <input type="checkbox" wire:model="is_active" id="active-check">
                        <label for="active-check">Cajero activo</label>
                    </div>
                    <div class="check-row">
                        <input type="checkbox" wire:model="is_direct" id="direct-check">
                        <label for="direct-check" style="display:flex;align-items:center;gap:6px">
                            <i class="fa-solid fa-crown" style="color:var(--orange);font-size:12px"></i>
                            Cajero oficial / directo
                            <span style="font-size:11px;font-weight:400;color:var(--muted-2)">(aparece en la tab "Directas" del frontend)</span>
                        </label>
                    </div>
                </div>

                {{-- Acciones --}}
                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <button type="button" class="btn-ghost" wire:click="closeModal">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Cajero
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif
</div>
