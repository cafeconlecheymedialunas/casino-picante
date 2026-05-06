<div class="page-header">
    <div>
        <h1 class="page-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="page-header-right">
        <div class="notification-menu" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" class="header-icon-btn" @click="open = !open" aria-label="Notificaciones">
                <span>!</span>
                @if($unreadCount > 0)
                    <b>{{ $unreadCount > 9 ? '9+' : $unreadCount }}</b>
                @endif
            </button>
            <div class="header-dropdown notifications-dropdown" x-show="open" x-cloak>
                <div class="dropdown-head">
                    <strong>Notificaciones</strong>
                    @if($unreadCount > 0)
                        <button type="button" wire:click="markAllRead">Marcar leidas</button>
                    @endif
                </div>
                @forelse($notifications as $notification)
                    <a href="{{ $notification->link ?: '#' }}" class="notification-item {{ $notification->read_at ? '' : 'unread' }}" wire:click="markRead({{ $notification->id }})">
                        <span class="notification-dot type-{{ $notification->type }}"></span>
                        <span>
                            <strong>{{ $notification->title }}</strong>
                            <small>{{ $notification->message }}</small>
                            <em>{{ $notification->created_at->diffForHumans() }}</em>
                        </span>
                    </a>
                @empty
                    <div class="dropdown-empty">No hay notificaciones.</div>
                @endforelse
            </div>
        </div>

        <div class="profile-menu" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" class="profile-trigger" @click="open = !open">
                <img src="{{ $avatarUrl }}" alt="{{ $displayName }}">
                <span>
                    <strong>{{ $displayName }}</strong>
                    <small>{{ $role }}</small>
                </span>
            </button>
            <div class="header-dropdown profile-dropdown" x-show="open" x-cloak>
                <a href="{{ route('perfil') }}" wire:navigate>Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Cerrar sesion</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($buttonText)
    <div class="page-action-strip">
        <button type="button" class="btn-primary" wire:click="{{ $buttonAction }}">{{ $buttonText }}</button>
    </div>
@endif
</div>
