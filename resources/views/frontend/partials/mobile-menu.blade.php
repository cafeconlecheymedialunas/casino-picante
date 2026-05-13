<nav class="fe-mobile-menu" data-fe-mobile-menu aria-label="Navegacion movil">
    <a href="{{ route('frontend.home') }}" wire:navigate>Inicio</a>
    <a href="#lineas">Lineas de atencion</a>
    <a href="#bonos">Bonos</a>
    <a href="{{ route('sorteo.publico') }}" wire:navigate>Sorteo</a>
    <a href="#blog">Blog</a>
    <a href="#como-empezar">Como empezar</a>
    @auth
        <a href="{{ route('perfil') }}" wire:navigate>Mi perfil</a>
        @if(auth()->user()?->hasRole(\App\Support\Roles::ADMIN) || auth()->user()?->hasRole(\App\Support\Roles::AGENTE))
            <a href="{{ route('dashboard') }}" wire:navigate>Panel</a>
        @endif
    @else
        <a href="{{ route('login') }}" wire:navigate>Ingresar</a>
    @endauth
</nav>
