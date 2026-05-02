<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">AJUSTES</h1>
        </div>
        <button class="btn-primary" wire:click="saveSettings">GUARDAR CAMBIOS</button>
    </div>

    <div class="content">
        <div class="settings-grid">
            <nav class="settings-nav">
                <a class="settings-nav-item {{ $activeTab === 'general' ? 'active' : '' }}" wire:click="setTab('general')">General</a>
                <a class="settings-nav-item {{ $activeTab === 'users' ? 'active' : '' }}" wire:click="setTab('users')">Usuarios & Registros</a>
                <a class="settings-nav-item {{ $activeTab === 'payments' ? 'active' : '' }}" wire:click="setTab('payments')">Pagos</a>
                <a class="settings-nav-item {{ $activeTab === 'notifications' ? 'active' : '' }}" wire:click="setTab('notifications')">Notificaciones</a>
                <a class="settings-nav-item {{ $activeTab === 'security' ? 'active' : '' }}" wire:click="setTab('security')">Seguridad</a>
                <a class="settings-nav-item {{ $activeTab === 'api' ? 'active' : '' }}" wire:click="setTab('api')">API & Webhooks</a>
                <a class="settings-nav-item {{ $activeTab === 'emails' ? 'active' : '' }}" wire:click="setTab('emails')">Correos</a>
            </nav>
            
            <div>
                @if($activeTab === 'general')
                <div class="settings-section">
                    <h2 class="settings-title">CONFIGURACIÓN GENERAL</h2>
                    <div class="form-group">
                        <label class="form-label">Nombre del sitio</label>
                        <input type="text" class="form-input" wire:model="settings.site_name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL del sitio</label>
                        <input type="text" class="form-input" wire:model="settings.site_url">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zona horaria</label>
                        <select class="form-input" wire:model="settings.timezone">
                            <option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires (GMT-3)</option>
                            <option value="America/Lima">America/Lima (GMT-5)</option>
                            <option value="America/Bogota">America/Bogota (GMT-5)</option>
                        </select>
                    </div>
                </div>
                @endif

                @if($activeTab === 'users' || $activeTab === 'general')
                <div class="settings-section">
                    <h2 class="settings-title">PREFERENCIAS</h2>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label">Registro abierto</div>
                            <div class="toggle-desc">Permitir nuevos registros de usuarios</div>
                        </div>
                        <div class="toggle {{ $settings['open_registration'] ? 'active' : '' }}" wire:click="toggleSetting('open_registration')"></div>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label">Verificación de email</div>
                            <div class="toggle-desc">Requerir verificación de email para activar cuenta</div>
                        </div>
                        <div class="toggle {{ $settings['email_verification'] ? 'active' : '' }}" wire:click="toggleSetting('email_verification')"></div>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label">KYC obligatorio</div>
                            <div class="toggle-desc">Requerir verificación de identidad para retiros</div>
                        </div>
                        <div class="toggle {{ $settings['kyc_required'] ? 'active' : '' }}" wire:click="toggleSetting('kyc_required')"></div>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label">Modo mantenimiento</div>
                            <div class="toggle-desc">Sitio en mantenimiento (solo admins)</div>
                        </div>
                        <div class="toggle {{ $settings['maintenance_mode'] ? 'active' : '' }}" wire:click="toggleSetting('maintenance_mode')"></div>
                    </div>
                </div>
                @endif

                @if($activeTab === 'payments' || $activeTab === 'users')
                <div class="settings-section">
                    <h2 class="settings-title">LÍMITES</h2>
                    <div class="form-group">
                        <label class="form-label">Depósito mínimo ($)</label>
                        <input type="number" class="form-input" wire:model="settings.min_deposit">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Depósito máximo diario ($)</label>
                        <input type="number" class="form-input" wire:model="settings.max_deposit">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Retiro mínimo ($)</label>
                        <input type="number" class="form-input" wire:model="settings.min_withdrawal">
                    </div>
                </div>
                @endif

                @if($activeTab === 'security')
                <div class="settings-section">
                    <h2 class="settings-title">SEGURIDAD</h2>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label">Autenticación de dos factores</div>
                            <div class="toggle-desc">Requerir 2FA para usuarios</div>
                        </div>
                        <div class="toggle"></div>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label">Bloqueo por intentos fallidos</div>
                            <div class="toggle-desc">Bloquear después de 5 intentos</div>
                        </div>
                        <div class="toggle active"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="toast">{{ session('message') }}</div>
    @endif

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .content { padding: 0 28px 28px; }
        
        .settings-grid { display: grid; grid-template-columns: 250px 1fr; gap: 20px; }
        @media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }

        .settings-nav { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 16px; height: fit-content; }
        .settings-nav-item { display: block; padding: 12px 16px; color: var(--muted); text-decoration: none; font-size: 14px; border-radius: 10px; margin-bottom: 4px; transition: all 0.2s; cursor: pointer; }
        .settings-nav-item:hover { background: rgba(255,255,255,0.05); color: var(--white); }
        .settings-nav-item.active { background: var(--orange); color: #190702; font-weight: 700; }

        .settings-section { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 28px; margin-bottom: 20px; }
        .settings-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 8px; }
        .form-input { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .form-input:focus { outline: none; border-color: var(--orange); }

        .toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid var(--line); }
        .toggle-label { font-size: 14px; }
        .toggle-desc { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .toggle { width: 48px; height: 26px; background: var(--line); border-radius: 999px; position: relative; cursor: pointer; }
        .toggle.active { background: var(--orange); }
        .toggle::after { content: ''; position: absolute; width: 20px; height: 20px; background: var(--white); border-radius: 50%; top: 3px; left: 3px; transition: transform 0.2s; }
        .toggle.active::after { transform: translateX(22px); }

        .toast { position: fixed; bottom: 24px; right: 24px; padding: 16px 24px; background: var(--good); color: #190702; border-radius: 12px; font-weight: 600; z-index: 1001; }
    </style>
</div>