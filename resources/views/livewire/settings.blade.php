<div class="settings-layout">
    {{-- Sidebar Tabs --}}
    <aside class="settings-sidebar">
        <h2 class="sidebar-title">Configuración</h2>
        <nav class="sidebar-nav">
            <button
                type="button"
                wire:click="setTab('notifications')"
                class="sidebar-tab {{ $activeTab === 'notifications' ? 'active' : '' }}"
            >
                <span>🔔</span>
                Notificaciones
            </button>
            {{-- Más tabs aquí en el futuro --}}
        </nav>
    </aside>

    {{-- Content Area --}}
    <main class="settings-content">
        @if($activeTab === 'notifications')
            @livewire('notification-settings')
        @endif
    </main>
</div>

<style>
.settings-layout {
    display: flex;
    gap: 24px;
    min-height: calc(100vh - 100px);
}
.settings-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: var(--surface, #1e1e2e);
    border: 1px solid var(--border, #2e2e3e);
    border-radius: 12px;
    padding: 20px 0;
    height: fit-content;
}
.sidebar-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text, #e0e0e0);
    padding: 0 20px 16px;
    margin: 0;
    border-bottom: 1px solid var(--border, #2e2e3e);
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
    color: var(--muted, #888);
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    text-align: left;
    width: 100%;
}
.sidebar-tab:hover {
    background: var(--hover, #2a2a3a);
    color: var(--text, #e0e0e0);
}
.sidebar-tab.active {
    background: var(--primary, #6366f1);
    color: white;
}
.settings-content {
    flex: 1;
    min-width: 0;
}
</style>
