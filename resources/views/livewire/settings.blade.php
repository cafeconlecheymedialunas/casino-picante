<div>
    @section('header')
        <x-livewire.components.page-header
            title="CONFIGURACION"
            subtitle="Preferencias del sistema y notificaciones de tu cuenta"
        />
    @endsection

    <style>
        .settings-layout { display: flex; gap: 24px; min-height: calc(100vh - 150px); }
        .settings-sidebar { width: 240px; flex-shrink: 0; background: rgba(255,255,255,.035); border: 1px solid var(--line); border-radius: 8px; padding: 20px 0; height: fit-content; }
        .sidebar-title { font-size: 18px; font-weight: 700; color: var(--white); padding: 0 20px 16px; margin: 0; border-bottom: 1px solid var(--line); }
        .sidebar-nav { display: flex; flex-direction: column; padding: 12px 8px; gap: 4px; }
        .sidebar-tab { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: none; background: none; color: var(--muted); font-size: 14px; font-weight: 700; border-radius: 8px; cursor: pointer; transition: all 0.15s; text-align: left; width: 100%; }
        .sidebar-tab span { min-width: 26px; height: 26px; border: 1px solid var(--line-2); border-radius: 7px; color: var(--orange); display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 900; }
        .sidebar-tab:hover { background: rgba(255,255,255,.06); color: var(--white); }
        .sidebar-tab.active { background: rgba(255,106,26,.12); color: var(--orange); }
        .settings-content { flex: 1; min-width: 0; }
        .settings-panel { border: 1px solid var(--line); border-radius: 8px; background: rgba(255,255,255,.025); overflow: hidden; }
        .settings-panel + .settings-panel { margin-top: 18px; }
        .settings-panel-head { padding: 18px 20px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .settings-panel-title { margin: 0; color: var(--white); font-size: 18px; letter-spacing: .02em; }
        .settings-panel-subtitle { margin: 4px 0 0; color: var(--muted); font-size: 13px; }
        .settings-form { padding: 20px; }
        .settings-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .settings-field { min-width: 0; }
        .settings-field.full { grid-column: 1 / -1; }
        .settings-label { display: block; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 7px; }
        .settings-input { width: 100%; background: rgba(255,255,255,.04); border: 1px solid var(--line-2); border-radius: 7px; padding: 10px 12px; color: var(--white); font-size: 13px; }
        .settings-input:focus { outline: none; border-color: var(--orange); }
        .settings-error { color: #ff5050; font-size: 12px; margin-top: 5px; }
        .settings-actions { display: flex; justify-content: flex-end; margin-top: 18px; }
        .settings-alert { margin-bottom: 16px; border: 1px solid rgba(37,196,107,.35); background: rgba(37,196,107,.1); color: #77e6a1; border-radius: 8px; padding: 10px 12px; font-size: 13px; font-weight: 700; }
        .theme-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .theme-option { position: relative; display: flex; align-items: center; gap: 12px; padding: 14px; border: 1px solid var(--line); border-radius: 8px; background: rgba(255,255,255,.035); cursor: pointer; transition: all .15s; }
        .theme-option:hover { border-color: var(--orange); background: rgba(255,106,26,.08); }
        .theme-option input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .theme-swatch { width: 46px; height: 34px; border-radius: 7px; border: 1px solid var(--line-2); display: grid; grid-template-columns: 15px 1fr; overflow: hidden; flex-shrink: 0; }
        .theme-swatch::before { content: ""; display: block; border-right: 1px solid var(--line-2); }
        .theme-swatch.dark { background: #120909; }
        .theme-swatch.dark::before { background: #0a0606; }
        .theme-swatch.light { background: #fffaf3; }
        .theme-swatch.light::before { background: #1a0d0d; }
        .theme-copy strong { display: block; color: var(--white); font-size: 13px; }
        .theme-copy small { display: block; color: var(--muted); font-size: 12px; margin-top: 2px; }
        .theme-option:has(input:checked) { border-color: var(--orange); background: rgba(255,106,26,.12); box-shadow: 0 0 0 1px rgba(255,106,26,.18) inset; }
        [data-dashboard-theme="light"] .settings-sidebar,
        [data-dashboard-theme="light"] .settings-panel {
            background: var(--panel);
            border-color: var(--line);
            box-shadow: var(--shadow-sm);
        }
        [data-dashboard-theme="light"] .settings-panel-head {
            background: var(--panel-2);
            border-bottom-color: var(--line);
        }
        [data-dashboard-theme="light"] .sidebar-tab,
        [data-dashboard-theme="light"] .settings-panel-title,
        [data-dashboard-theme="light"] .theme-copy strong {
            color: var(--white);
        }
        [data-dashboard-theme="light"] .settings-panel-subtitle,
        [data-dashboard-theme="light"] .settings-label,
        [data-dashboard-theme="light"] .theme-copy small {
            color: var(--muted);
        }
        [data-dashboard-theme="light"] .settings-input,
        [data-dashboard-theme="light"] .theme-option {
            background: #ffffff;
            border-color: var(--line-2);
            color: var(--white);
        }
        [data-dashboard-theme="light"] .theme-option:hover,
        [data-dashboard-theme="light"] .theme-option:has(input:checked) {
            background: #fff7ed;
            border-color: var(--orange);
        }
        [data-dashboard-theme="light"] .settings-alert {
            background: #ecfdf3;
            border-color: #b7efc9;
            color: #167344;
        }
        @media (max-width: 780px) { .settings-layout { flex-direction: column; } .settings-sidebar { width: 100%; } }
        @media (max-width: 760px) { .settings-grid, .theme-options { grid-template-columns: 1fr; } }
    </style>

    <div class="settings-layout page-container">
        <aside class="settings-sidebar">
            <h2 class="sidebar-title">Configuracion</h2>
            <nav class="sidebar-nav">
                @if($canEditVendor)
                <button type="button" wire:click="setTab('vendor')" class="sidebar-tab {{ $activeTab === 'vendor' ? 'active' : '' }}">
                    <span>CA</span> Cajero
                </button>
                @endif
                <button type="button" wire:click="setTab('notifications')" class="sidebar-tab {{ $activeTab === 'notifications' ? 'active' : '' }}">
                    <span>NO</span> Notificaciones
                </button>
                <button type="button" wire:click="setTab('appearance')" class="sidebar-tab {{ $activeTab === 'appearance' ? 'active' : '' }}">
                    <span>AP</span> Apariencia
                </button>
            </nav>
        </aside>
        <main class="settings-content">
            @if($activeTab === 'vendor' && $canEditVendor)
                <section class="settings-panel">
                    <div class="settings-panel-head">
                        <div>
                            <h3 class="settings-panel-title">Datos del cajero</h3>
                            <p class="settings-panel-subtitle">Edita la identidad publica, contactos y branding del vendor activo.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveVendor" class="settings-form">
                        @if(session('vendor_message'))
                            <div class="settings-alert">{{ session('vendor_message') }}</div>
                        @endif

                        <div class="settings-grid">
                            <div class="settings-field">
                                <label class="settings-label">Nombre</label>
                                <input class="settings-input" wire:model.live="name" type="text">
                                @error('name') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <label class="settings-label">Slug publico</label>
                                <input class="settings-input" wire:model="slug" type="text">
                                @error('slug') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field full">
                                <x-upload-image label="Logo del Cajero" model="logoUpload" :value="$logo" removeAction="removeLogo" aspect="1" hint="PNG/JPG cuadrado" />
                                @error('logoUpload') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <x-upload-image label="Imagen hero publica" model="heroImageUpload" :value="$heroImage" removeAction="removeHeroImage" aspect="16/9" hint="Fondo de /cajero/slug" />
                                @error('heroImageUpload') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <x-upload-image label="Imagen perfil publica" model="portraitImageUpload" :value="$portraitImage" removeAction="removePortraitImage" aspect="3/4" hint="Figura/tarjeta del cajero" />
                                @error('portraitImageUpload') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field full">
                                <label class="settings-label">Descripcion</label>
                                <textarea class="settings-input" wire:model="description" rows="4"></textarea>
                                @error('description') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field full">
                                <label class="settings-label">Contactos</label>
                                <livewire:components.contact-repeater wire:model="contacts" />
                                @error('contacts') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field full">
                                <label class="settings-label">Branding JSON</label>
                                <textarea class="settings-input" wire:model="brandingJson" rows="5" placeholder='{"primary":"#ff6a1a"}'></textarea>
                                @error('brandingJson') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button type="submit" class="btn-primary">Guardar cajero</button>
                        </div>
                    </form>
                </section>

                @if($cajeroUserId)
                <section class="settings-panel">
                    <div class="settings-panel-head">
                        <div>
                            <h3 class="settings-panel-title">Usuario cajero</h3>
                            <p class="settings-panel-subtitle">Edita los datos de acceso y perfil del usuario asociado a este cajero.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveCajeroUser" class="settings-form">
                        @if(session('cajero_user_message'))
                            <div class="settings-alert">{{ session('cajero_user_message') }}</div>
                        @endif

                        <div class="settings-grid">
                            <div class="settings-field">
                                <label class="settings-label">Username</label>
                                <input class="settings-input" wire:model="cajeroUsername" type="text">
                                @error('cajeroUsername') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <label class="settings-label">Email</label>
                                <input class="settings-input" wire:model="cajeroEmail" type="email">
                                @error('cajeroEmail') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <label class="settings-label">Nombre</label>
                                <input class="settings-input" wire:model="cajeroName" type="text">
                                @error('cajeroName') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <label class="settings-label">Apellido</label>
                                <input class="settings-input" wire:model="cajeroApellido" type="text">
                                @error('cajeroApellido') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <label class="settings-label">Telefono</label>
                                <input class="settings-input" wire:model="cajeroPhone" type="text">
                                @error('cajeroPhone') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field">
                                <label class="settings-label">Contacto</label>
                                <input class="settings-input" wire:model="cajeroContact" type="text">
                                @error('cajeroContact') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="settings-field full">
                                <label class="settings-label">Nueva contrasena</label>
                                <input class="settings-input" wire:model="cajeroPassword" type="password" autocomplete="new-password" placeholder="Dejar vacio para no cambiar">
                                @error('cajeroPassword') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button type="submit" class="btn-primary">Guardar usuario</button>
                        </div>
                    </form>
                </section>
                @endif
            @elseif($activeTab === 'notifications')
                @livewire('notification-settings')
            @elseif($activeTab === 'appearance')
                <section class="settings-panel">
                    <div class="settings-panel-head">
                        <div>
                            <h3 class="settings-panel-title">Tema del dashboard</h3>
                            <p class="settings-panel-subtitle">Elegi la apariencia global para todas las pantallas internas.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveDashboardTheme" class="settings-form">
                        @if(session('theme_message'))
                            <div class="settings-alert">{{ session('theme_message') }}</div>
                        @endif

                        <div class="theme-options">
                            <label class="theme-option">
                                <input type="radio" wire:model="dashboardTheme" value="dark">
                                <span class="theme-swatch dark"></span>
                                <span class="theme-copy">
                                    <strong>Oscuro</strong>
                                    <small>Fondo profundo con acentos naranja.</small>
                                </span>
                            </label>

                            <label class="theme-option">
                                <input type="radio" wire:model="dashboardTheme" value="light">
                                <span class="theme-swatch light"></span>
                                <span class="theme-copy">
                                    <strong>Claro</strong>
                                    <small>Superficies claras para operar de dia.</small>
                                </span>
                            </label>
                        </div>

                        @error('dashboardTheme') <div class="settings-error">{{ $message }}</div> @enderror

                        <div class="settings-actions">
                            <button type="submit" class="btn-primary">Guardar apariencia</button>
                        </div>
                    </form>
                </section>
            @endif
        </main>
    </div>
</div>
