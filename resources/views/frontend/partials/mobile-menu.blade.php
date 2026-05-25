<nav class="fe-mobile-menu" data-fe-mobile-menu aria-label="Navegacion movil">
    @php
        $homeUrl = route('frontend.home');
        $lineasUrl = route('frontend.lineas');
        $bonosUrl = route('frontend.bonos');
        $sorteosUrl = route('frontend.sorteos');
        $blogUrl = route('frontend.blog');
    @endphp
    <a href="{{ $homeUrl }}" wire:navigate>Inicio</a>
    <a href="{{ $lineasUrl }}" wire:navigate>Lineas de atencion</a>
    <a href="{{ route('frontend.cajeros') }}" wire:navigate>Cajeros</a>
    <a href="{{ $bonosUrl }}" wire:navigate>Bonos</a>
    <a href="{{ $sorteosUrl }}" wire:navigate>Sorteo</a>
    <a href="{{ $blogUrl }}" wire:navigate>Novedades</a>
    @auth
        @if(auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
            <a href="{{ route('client.account') }}" wire:navigate>Mi cuenta</a>
        @endif
        @if(auth()->user()?->hasRole(\App\Support\Roles::ADMIN) || auth()->user()?->hasRole(\App\Support\Roles::AGENTE) || auth()->user()?->hasRole(\App\Support\Roles::CAJERO))
            <a href="{{ route('admin.dashboard') }}" wire:navigate>Panel</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;color:inherit;font:inherit;cursor:pointer;padding:0;">Salir</button>
        </form>
    @else
        <a href="{{ route('login') }}" wire:navigate>Ingresar</a>
    @endauth
</nav>
