<header class="fe-nav">
    @php
        $homeUrl = route('frontend.home');
        $lineasUrl = route('frontend.lineas');
        $bonosUrl = route('frontend.bonos');
        $sorteosUrl = route('frontend.sorteos');
        $blogUrl = route('frontend.blog');
    @endphp
    <div class="fe-shell">
        <div class="fe-nav-inner">
            <a href="{{ $homeUrl }}" wire:navigate class="fe-brand" aria-label="RED PICANTES">
                @if($siteLogoUrl ?? false)
                    <img src="{{ $siteLogoUrl }}" alt="{{ $siteTitle ?? 'RED PICANTES' }}" class="fe-brand-logo">
                @else
                <span class="fe-brand-mark"></span>
                <span class="fe-brand-text">{{ $siteTitle ?? 'RED PICANTES' }}</span>
                @endif
            </a>

            <nav class="fe-nav-links" aria-label="Navegacion principal">
                <a href="{{ $homeUrl }}" wire:navigate class="{{ request()->routeIs('frontend.home') || request()->routeIs('frontend.cajero.inicio') ? 'active' : '' }}">Inicio</a>
                <a href="{{ route('frontend.cajeros') }}" wire:navigate class="{{ request()->routeIs('frontend.cajeros') || request()->routeIs('frontend.cajeros') ? 'active' : '' }}">Cajeros</a>
                <a href="{{ $lineasUrl }}" wire:navigate class="{{ request()->routeIs('frontend.lineas*') || request()->routeIs('frontend.cajero.lineas*') ? 'active' : '' }}">Lineas</a>
                <a href="{{ $bonosUrl }}" wire:navigate class="{{ request()->routeIs('frontend.bonos*') || request()->routeIs('frontend.cajero.bonos*') ? 'active' : '' }}">Bonos</a>
                <a href="{{ $sorteosUrl }}" wire:navigate class="{{ request()->routeIs('frontend.sorteos*') || request()->routeIs('frontend.cajero.sorteos*') || request()->routeIs('sorteo.publico') ? 'active' : '' }}">Sorteo</a>
                <a href="{{ $blogUrl }}" wire:navigate class="{{ request()->routeIs('frontend.blog*') || request()->routeIs('frontend.cajero.blog*') ? 'active' : '' }}">Novedades</a>
            </nav>

            <div class="fe-nav-actions">
                @auth
                    @if(auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
                        @php
                            $clientNotifications = \App\Models\DashboardNotification::where('user_id', auth()->id())->latest()->take(6)->get();
                            $clientUnreadCount = \App\Models\DashboardNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
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
                        <details class="fe-user-menu">
                            <summary class="fe-user-btn">
                                <i class="fa-solid fa-user"></i>
                                <span>{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
                            </summary>
                            <div class="fe-user-dropdown">
                                <a href="{{ route('client.account') }}" wire:navigate>
                                    <i class="fa-solid fa-user-circle"></i> Mi cuenta
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit">
                                        <i class="fa-solid fa-sign-out-alt"></i> Salir
                                    </button>
                                </form>
                            </div>
                        </details>
                    @endif
                    @if(auth()->user()?->hasRole(\App\Support\Roles::ADMIN) || auth()->user()?->hasRole(\App\Support\Roles::AGENTE) || auth()->user()?->hasRole(\App\Support\Roles::CAJERO))
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="fe-btn ghost">Panel</a>
                    @endif
                    @if(!auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
                        <form method="POST" action="{{ route('logout') }}" style="display:inline">
                            @csrf
                            <button type="submit" class="fe-btn ghost" style="cursor:pointer">Salir</button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" wire:navigate class="fe-btn ghost">Ingresar</a>
                    <a href="{{ route('client.register') }}" wire:navigate class="fe-btn primary">Registrarme</a>
                @endauth
            </div>

            <button type="button" class="fe-mobile-toggle" onclick="toggleFrontendMenu()" aria-label="Abrir menu">
                <span style="font-size:20px;line-height:1">=</span>
            </button>
        </div>

        @include('frontend.partials.mobile-menu')
    </div>
</header>
