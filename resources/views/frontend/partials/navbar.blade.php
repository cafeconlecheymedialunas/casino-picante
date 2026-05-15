<header class="fe-nav">
    <div class="fe-shell">
        <div class="fe-nav-inner">
            <a href="{{ route('frontend.home') }}" wire:navigate class="fe-brand" aria-label="RED PICANTES">
                <span class="fe-brand-mark"></span>
                <span class="fe-brand-text">RED <span>PICANTES</span></span>
            </a>

            <nav class="fe-nav-links" aria-label="Navegacion principal">
                <a href="{{ route('frontend.home') }}" wire:navigate class="{{ request()->routeIs('frontend.home') ? 'active' : '' }}">Inicio</a>
                <a href="{{ route('frontend.lines') }}" wire:navigate class="{{ request()->routeIs('frontend.lines*') ? 'active' : '' }}">Lineas</a>
                <a href="{{ route('frontend.bonuses') }}" wire:navigate class="{{ request()->routeIs('frontend.bonuses*') ? 'active' : '' }}">Bonos</a>
                <a href="{{ route('frontend.raffles') }}" wire:navigate class="{{ request()->routeIs('frontend.raffles*') || request()->routeIs('sorteo.publico') ? 'active' : '' }}">Sorteo</a>
                <a href="{{ route('frontend.blog') }}" wire:navigate class="{{ request()->routeIs('frontend.blog') ? 'active' : '' }}">Novedades</a>
                <a href="{{ route('frontend.home') }}#como-empezar">Como empezar</a>
            </nav>

            <div class="fe-nav-actions">
                @auth
                    @if(auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
                        @php
                            $clientNotifications = \App\Models\UserNotification::where('user_id', auth()->id())->latest()->take(6)->get();
                            $clientUnreadCount = \App\Models\UserNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        <details class="fe-notification-menu">
                            <summary class="fe-bell-btn" aria-label="Notificaciones">
                                <i class="fa-solid fa-bell"></i>
                                @if($clientUnreadCount > 0)
                                    <b>{{ $clientUnreadCount > 9 ? '9+' : $clientUnreadCount }}</b>
                                @endif
                            </summary>
                            <div class="fe-notification-dropdown">
                                <div class="fe-dropdown-head">
                                    <strong>Notificaciones</strong>
                                    <a href="{{ route('client.account') }}" wire:navigate>Ver cuenta</a>
                                </div>
                                <div class="fe-dropdown-body">
                                    @forelse($clientNotifications as $notification)
                                        <a href="{{ route('client.account') }}" wire:navigate class="fe-notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                            <span class="fe-notification-dot type-{{ $notification->type }}"></span>
                                            <span class="fe-notification-content">
                                                <strong>{{ $notification->title }}</strong>
                                                <small>{{ $notification->message }}</small>
                                                <em>{{ $notification->created_at->diffForHumans() }}</em>
                                            </span>
                                        </a>
                                    @empty
                                        <div class="fe-dropdown-empty">No hay notificaciones.</div>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                        <a href="{{ route('client.account') }}" wire:navigate class="fe-btn ghost">Mi cuenta</a>
                    @endif
                    @if(auth()->user()?->hasRole(\App\Support\Roles::ADMIN) || auth()->user()?->hasRole(\App\Support\Roles::AGENTE))
                        <a href="{{ route('dashboard') }}" wire:navigate class="fe-btn ghost">Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="fe-btn ghost" style="cursor:pointer">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="fe-btn ghost">Ingresar</a>
                @endauth
                <a href="{{ route('frontend.lines') }}" wire:navigate class="fe-btn primary">Atencion</a>
            </div>

            <button type="button" class="fe-mobile-toggle" onclick="toggleFrontendMenu()" aria-label="Abrir menu">
                <span style="font-size:20px;line-height:1">=</span>
            </button>
        </div>

        @include('frontend.partials.mobile-menu')
    </div>
</header>
