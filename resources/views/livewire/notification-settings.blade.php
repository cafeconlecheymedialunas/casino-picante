<div class="notification-settings-content">
    <div class="content-header">
        <h2>Notificaciones</h2>
        <p>Elegi que notificaciones queres recibir en tu cuenta.</p>
    </div>

    <div class="settings-grid">
        @foreach($modules as $module => $data)
            <div class="setting-card">
                <div class="setting-info">
                    <span class="setting-icon">{{ $data['icon'] }}</span>
                    <span class="setting-label">{{ $data['label'] }}</span>
                </div>
                <button
                    type="button"
                    wire:click="toggle('{{ $module }}')"
                    class="toggle-switch {{ $preferences[$module] ? 'active' : '' }}"
                >
                    <span class="toggle-track">
                        <span class="toggle-thumb"></span>
                    </span>
                    <span class="toggle-label">
                        {{ $preferences[$module] ? 'Activado' : 'Desactivado' }}
                    </span>
                </button>
            </div>
        @endforeach
    </div>
</div>

<style>
.notification-settings-content {
    padding: 20px;
}
.content-header {
    margin-bottom: 24px;
}
.content-header h2 {
    font-size: 22px;
    font-weight: 600;
    color: var(--text, #e0e0e0);
    margin: 0 0 8px;
}
.content-header p {
    color: var(--muted, #888);
    margin: 0;
    font-size: 14px;
}
.settings-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.setting-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface, #1e1e2e);
    border: 1px solid var(--border, #2e2e3e);
    border-radius: 8px;
    padding: 16px 20px;
}
.setting-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.setting-icon {
    min-width: 28px;
    height: 28px;
    border: 1px solid var(--line-2, rgba(255,255,255,.16));
    border-radius: 7px;
    color: var(--orange, #ff6a1a);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 900;
}
.setting-label {
    font-size: 15px;
    font-weight: 500;
    color: var(--text, #e0e0e0);
}
.toggle-switch {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    background: none;
    border: none;
    color: var(--text, #e0e0e0);
}
.toggle-track {
    width: 40px;
    height: 22px;
    background: #4a4a5a;
    border-radius: 11px;
    position: relative;
    transition: background 0.2s;
}
.toggle-switch.active .toggle-track {
    background: #10b981;
}
.toggle-thumb {
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: left 0.2s;
}
.toggle-switch.active .toggle-thumb {
    left: 20px;
}
.toggle-label {
    font-size: 13px;
    color: var(--muted, #888);
}
</style>
