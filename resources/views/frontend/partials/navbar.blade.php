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
                <a href="#bonos">Bonos</a>
                <a href="{{ route('frontend.raffles') }}" wire:navigate class="{{ request()->routeIs('frontend.raffles*') || request()->routeIs('sorteo.publico') ? 'active' : '' }}">Sorteo</a>
                <a href="{{ route('frontend.blog') }}" wire:navigate class="{{ request()->routeIs('frontend.blog') ? 'active' : '' }}">Novedades</a>
                <a href="{{ route('frontend.home') }}#como-empezar">Como empezar</a>
            </nav>

            <div class="fe-nav-actions">
                @auth
                    @if(auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
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
