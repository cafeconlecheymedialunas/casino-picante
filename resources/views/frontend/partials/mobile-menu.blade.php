<nav class="fe-mobile-menu" data-fe-mobile-menu aria-label="Navegacion movil">
    <a href="{{ route('frontend.home') }}" wire:navigate>Inicio</a>
    <a href="{{ route('frontend.lines') }}" wire:navigate>Lineas de atencion</a>
    <a href="#bonos">Bonos</a>
    <a href="{{ route('frontend.raffles') }}" wire:navigate>Sorteo</a>
    <a href="{{ route('frontend.blog') }}" wire:navigate>Novedades</a>
    <a href="{{ route('frontend.home') }}#como-empezar">Como empezar</a>
    @auth
        @if(auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
            <a href="{{ route('client.account') }}" wire:navigate>Mi cuenta</a>
        @endif
        @if(auth()->user()?->hasRole(\App\Support\Roles::ADMIN) || auth()->user()?->hasRole(\App\Support\Roles::AGENTE))
            <a href="{{ route('dashboard') }}" wire:navigate>Panel</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;color:inherit;font:inherit;cursor:pointer;padding:0;">Salir</button>
        </form>
    @else
        <a href="{{ route('login') }}" wire:navigate>Ingresar</a>
    @endauth
</nav>
