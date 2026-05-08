<div class="page-header" wire:poll.15s>
    <div class="page-header-left">
        <h1 class="page-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="page-header-right">
        @if($buttonText && $buttonAction)
            <button type="button" class="page-header-btn" wire:click="{{ $buttonAction }}">
                {{ $buttonText }}
            </button>
        @endif

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
                    <div style="display:flex;gap:6px;align-items:center;">
                        @if($unreadCount > 0)
                            <button type="button" wire:click="markAllRead" title="Marcar todas leídas" style="font-size:10px;padding:3px 8px;">✓ Leídas</button>
                        @endif
                        <button type="button" wire:click="deleteAllRead" title="Borrar notificaciones leídas" style="font-size:10px;padding:3px 8px;color:var(--muted);">Limpiar</button>
                        @if($canOpenSettings)
                            <a href="{{ route('settings') }}" wire:navigate class="settings-link">⚙</a>
                        @endif
                    </div>
                </div>

                <div class="dropdown-body">
                    @forelse($notifications as $notification)
                        <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                            <span class="notification-dot type-{{ $notification->type }}"></span>
                            <span class="notification-content" wire:click="markRead({{ $notification->id }})" style="cursor:pointer;flex:1;">
                                @if($notification->link)
                                    <a href="{{ $notification->link }}" wire:navigate>{{ $notification->title }}</a>
                                @else
                                    <strong>{{ $notification->title }}</strong>
                                @endif
                                <small>{{ $notification->message }}</small>
                                <em>{{ $notification->created_at->diffForHumans() }}</em>
                            </span>
                            <button
                                type="button"
                                wire:click="deleteNotification({{ $notification->id }})"
                                title="Eliminar"
                                style="flex-shrink:0;background:none;border:none;color:var(--muted-2);cursor:pointer;padding:2px 4px;font-size:13px;line-height:1;"
                                onmouseover="this.style.color='#ff4757'"
                                onmouseout="this.style.color='var(--muted-2)'"
                            >×</button>
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
                    <button type="submit">Cerrar sesion</button>
                </form>
            </div>
        </div>
    </div>
</div>
