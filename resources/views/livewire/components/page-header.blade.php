<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="page-header-right">
        <div class="notification-menu" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" class="header-icon-btn" @click="open = !open" aria-label="Notificaciones">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                @if($unreadCount > 0)
                    <b>{{ $unreadCount > 9 ? '9+' : $unreadCount }}</b>
                @endif
            </button>
            <div class="header-dropdown notifications-dropdown" x-show="open" x-cloak x-transition>
                <div class="dropdown-head">
                    <strong>Notificaciones</strong>
                    <a href="{{ route('settings') }}" wire:navigate class="settings-link">Configurar</a>
                    @if($unreadCount > 0)
                        <button type="button" wire:click="markAllRead">Marcar todas leídas</button>
                    @endif
                </div>
                <div class="dropdown-body">
                    @forelse($notifications as $notification)
                        <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}" wire:click="markRead({{ $notification->id }})">
                            <span class="notification-dot type-{{ $notification->type }}"></span>
                            <span class="notification-content">
                                <strong>{{ $notification->title }}</strong>
                                <small>{{ $notification->message }}</small>
                                <em>{{ $notification->created_at->diffForHumans() }}</em>
                            </span>
                        </div>
                    @empty
                        <div class="dropdown-empty">No hay notificaciones pendientes.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="profile-menu" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" class="profile-trigger" @click="open = !open">
                <img src="{{ $avatarUrl }}" alt="{{ $displayName }}">
                <span>
                    <strong>{{ $displayName }}</strong>
                    <small>{{ $role }}</small>
                </span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--muted-2);flex-shrink:0;">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>
            <div class="header-dropdown profile-dropdown" x-show="open" x-cloak x-transition>
                <a href="{{ route('perfil') }}" wire:navigate>Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Cerrar sesión</button>
                </form>
            </div>
        </div>

        @if($buttonText)
            <button type="button" className="btn-primary" wire:click="$dispatch('header-action', { action: '{{ $buttonAction }}' })">{{ $buttonText }}</button>
        @endif
    </div>
</div>
