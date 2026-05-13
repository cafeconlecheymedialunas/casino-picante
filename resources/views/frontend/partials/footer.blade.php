<footer class="fe-footer">
    <div class="fe-shell">
        <div class="fe-footer-grid">
            <div>
                <a href="{{ route('frontend.home') }}" wire:navigate class="fe-brand">
                    <span class="fe-brand-mark"></span>
                    <span class="fe-brand-text">RED <span>PICANTES</span></span>
                </a>
                <p>Casino online con atencion rapida, bonos activos, sorteos y novedades para jugar con mas chances.</p>
            </div>
            <div>
                <div class="fe-footer-title">Secciones</div>
                <ul>
                    <li><a href="{{ route('frontend.home') }}" wire:navigate>Inicio</a></li>
                    <li><a href="#lineas">Lineas</a></li>
                    <li><a href="#bonos">Bonos</a></li>
                    <li><a href="#blog">Blog</a></li>
                </ul>
            </div>
            <div>
                <div class="fe-footer-title">Cuenta</div>
                <ul>
                    <li><a href="{{ route('login') }}" wire:navigate>Login clientes</a></li>
                    <li><a href="{{ route('admin.login') }}" wire:navigate>Login panel</a></li>
                    @auth
                        <li><a href="{{ route('perfil') }}" wire:navigate>Mi perfil</a></li>
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
            <span>RED PICANTES</span>
            <span>Juego responsable. Solo mayores de edad.</span>
        </div>
    </div>
</footer>
