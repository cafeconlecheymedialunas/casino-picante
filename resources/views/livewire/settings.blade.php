<div>
    <x-livewire.components.page-header
        title="CONFIGURACION"
        subtitle="Preferencias del sistema y notificaciones de tu cuenta"
    />

    <div class="settings-layout page-container">
        <aside class="settings-sidebar">
            <h2 class="sidebar-title">Configuracion</h2>
            <nav class="sidebar-nav">
                <button
                    type="button"
                    wire:click="setTab('notifications')"
                    class="sidebar-tab {{ $activeTab === 'notifications' ? 'active' : '' }}"
                >
                    <span>NO</span>
                    Notificaciones
                </button>
            </nav>
        </aside>

        <main class="settings-content">
            @if($activeTab === 'notifications')
                @livewire('notification-settings')
            @endif
        </main>
    </div>
</div>

<style>
.settings-layout {
    display: flex;
    gap: 24px;
    min-height: calc(100vh - 150px);
}
.settings-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: rgba(255,255,255,.035);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 20px 0;
    height: fit-content;
}
.sidebar-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--white);
    padding: 0 20px 16px;
    margin: 0;
    border-bottom: 1px solid var(--line);
}
.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 12px 8px;
    gap: 4px;
}
.sidebar-tab {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: none;
    background: none;
    color: var(--muted);
    font-size: 14px;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    text-align: left;
    width: 100%;
}
.sidebar-tab span {
    min-width: 26px;
    height: 26px;
    border: 1px solid var(--line-2);
    border-radius: 7px;
    color: var(--orange);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 900;
}
.sidebar-tab:hover {
    background: rgba(255,255,255,.06);
    color: var(--white);
}
.sidebar-tab.active {
    background: rgba(255,106,26,.12);
    color: var(--orange);
}
.settings-content {
    flex: 1;
    min-width: 0;
}
@media (max-width: 780px) {
    .settings-layout {
        flex-direction: column;
    }
    .settings-sidebar {
        width: 100%;
    }
}
</style>
