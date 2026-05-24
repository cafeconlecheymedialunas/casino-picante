<footer class="fe-footer">
    @php
        $scopedVendor = $publicVendor ?? null;
        $homeUrl = $scopedVendor ? route('frontend.cajero.inicio', $scopedVendor) : route('frontend.home');
        $lineasUrl = $scopedVendor ? route('frontend.cajero.lineas', $scopedVendor) : route('frontend.lineas');
        $bonosUrl = $scopedVendor ? route('frontend.cajero.bonos', $scopedVendor) : route('frontend.bonos');
        $blogUrl = $scopedVendor ? route('frontend.cajero.blog', $scopedVendor) : route('frontend.blog');
    @endphp
    <div class="fe-shell">
        <div class="fe-footer-grid">
            <div>
                <a href="{{ $homeUrl }}" wire:navigate class="fe-brand">
                    @if($siteLogoUrl ?? false)
                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteTitle ?? 'RED PICANTES' }}" class="fe-brand-logo">
                    @else
                    <span class="fe-brand-mark"></span>
                    <span class="fe-brand-text">{{ $siteTitle ?? 'RED PICANTES' }}</span>
                    @endif
                </a>
                <p>Casino online con atencion rapida, bonos activos, sorteos y novedades para jugar con mas chances.</p>
            </div>
            <div>
                <div class="fe-footer-title">Secciones</div>
                <ul>
                    <li><a href="{{ $homeUrl }}" wire:navigate>Inicio</a></li>
                    <li><a href="{{ route('frontend.cajeros') }}" wire:navigate>Cajeros</a></li>
                    <li><a href="{{ $lineasUrl }}" wire:navigate>Lineas</a></li>
                    <li><a href="{{ $bonosUrl }}" wire:navigate>Bonos</a></li>
                    <li><a href="{{ $blogUrl }}" wire:navigate>Novedades</a></li>
                </ul>
            </div>
            <div>
                <div class="fe-footer-title">Cuenta</div>
                <ul>
                    <li><a href="{{ route('login') }}" wire:navigate>Login clientes</a></li>
                    <li><a href="{{ route('admin.login') }}" wire:navigate>Login panel</a></li>
                    @auth
                        @if(auth()->user()?->hasRole(\App\Support\Roles::CLIENTE))
                            <li><a href="{{ route('client.account') }}" wire:navigate>Mi cuenta</a></li>
                        @else
                            <li><a href="{{ route('admin.perfil') }}" wire:navigate>Mi perfil</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
            <div>
                <div class="fe-footer-title">Ayuda</div>
                <ul>
                    <li>Atencion por lineas activas</li>
                    <li>Bonos vigentes</li>
                    <li>Sorteos activos</li>
                </ul>
            </div>
        </div>
        <div class="fe-footer-bottom">
            <span>{{ $siteTitle ?? 'RED PICANTES' }}</span>
            <span>Juego responsable. Solo mayores de edad.</span>
        </div>
    </div>
</footer>
