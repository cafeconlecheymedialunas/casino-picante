@push('styles')
<style>
    .client-auth { padding:42px 0 0; }
    .client-auth-grid { display:grid; grid-template-columns:minmax(0,.9fr) minmax(360px,.7fr); gap:28px; align-items:stretch; }
    .client-auth-copy { border:1px solid rgba(255,106,26,.2); border-radius:12px; background:radial-gradient(80% 80% at 10% 0%, rgba(255,106,26,.22), transparent 62%), linear-gradient(180deg,#160807,#080302); padding:34px; overflow:hidden; }
    .client-auth-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .client-auth-title { font-family:var(--font-display); font-size:64px; line-height:.9; margin:10px 0 14px; letter-spacing:.02em; }
    .client-auth-title span { color:var(--orange); }
    .client-auth-copy p { color:var(--muted); font-size:15px; line-height:1.55; max-width:620px; }
    .client-auth-points { display:grid; gap:10px; margin-top:26px; }
    .client-auth-point { display:flex; align-items:center; gap:10px; color:rgba(255,255,255,.76); font-size:13px; font-weight:800; }
    .client-auth-point i { color:var(--orange); width:24px; }
    .client-auth-card { border:1px solid rgba(255,255,255,.1); border-radius:12px; background:linear-gradient(180deg,#160807,#090403); padding:24px; box-shadow:0 22px 60px rgba(0,0,0,.36); }
    .client-auth-card h1 { font-family:var(--font-display); font-size:42px; line-height:1; margin:0 0 16px; letter-spacing:.02em; }
    .client-field { margin-bottom:13px; }
    .client-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .client-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .client-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .client-error { color:#ff8a8a; font-size:11px; font-weight:800; margin-top:5px; }
    .client-check { display:flex; gap:9px; align-items:flex-start; color:var(--muted); font-size:12px; line-height:1.4; margin:4px 0 16px; }
    .client-auth-foot { margin-top:16px; color:var(--muted); font-size:12px; text-align:center; }
    .client-auth-foot a { color:var(--orange); font-weight:900; text-decoration:none; }
    @media (max-width: 860px) {
        .client-auth-grid { grid-template-columns:1fr; }
        .client-auth-title { font-size:46px; }
    }
    @media (max-width: 520px) {
        .client-auth { padding-top:30px; }
        .client-auth-copy, .client-auth-card { padding:20px; }
        .client-auth-title { font-size:38px; }
        .client-auth-card h1 { font-size:34px; }
        .client-auth-point { align-items:flex-start; }
    }
</style>
@endpush

<section class="client-auth">
    <div class="fe-shell">
        <div class="client-auth-grid">
            <div class="client-auth-copy">
                <div class="client-auth-kicker">Red Picantes</div>
                <h1 class="client-auth-title">Crea tu cuenta y <span>participa</span></h1>
                <p>Registrate para comentar novedades, valorar lineas, ver tus numeros asignados y participar en sorteos activos.</p>
                <div class="client-auth-points">
                    <div class="client-auth-point"><i class="fa-solid fa-ticket"></i> Acceso a tus numeros de sorteo.</div>
                    <div class="client-auth-point"><i class="fa-solid fa-star"></i> Valoraciones y comentarios con tu usuario.</div>
                    <div class="client-auth-point"><i class="fa-solid fa-headset"></i> Atencion directa desde las lineas disponibles.</div>
                </div>
            </div>

            <div class="client-auth-card">
                <h1>Registro</h1>
                <form wire:submit.prevent="register">
                    <div class="client-field">
                        <label class="client-label" for="name">Nombre</label>
                        <input id="name" class="client-input" type="text" wire:model.defer="name" autocomplete="name">
                        @error('name') <div class="client-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="client-field">
                        <label class="client-label" for="apellido">Apellido</label>
                        <input id="apellido" class="client-input" type="text" wire:model.defer="apellido" autocomplete="family-name">
                        @error('apellido') <div class="client-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="client-field">
                        <label class="client-label" for="username">Nombre de cliente</label>
                        <input id="username" class="client-input" type="text" wire:model.defer="username" autocomplete="username">
                        @error('username') <div class="client-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="client-field">
                        <label class="client-label" for="email">Email</label>
                        <input id="email" class="client-input" type="email" wire:model.defer="email" autocomplete="email">
                        @error('email') <div class="client-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="client-field">
                        <label class="client-label" for="phone">Telefono</label>
                        <input id="phone" class="client-input" type="text" wire:model.defer="phone" autocomplete="tel">
                        @error('phone') <div class="client-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="client-field">
                        <label class="client-label" for="password">Contrasena</label>
                        <input id="password" class="client-input" type="password" wire:model.defer="password" autocomplete="new-password">
                        @error('password') <div class="client-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="client-field">
                        <label class="client-label" for="password_confirmation">Confirmar contrasena</label>
                        <input id="password_confirmation" class="client-input" type="password" wire:model.defer="password_confirmation" autocomplete="new-password">
                    </div>
                    <label class="client-check">
                        <input type="checkbox" wire:model.defer="recibir_bonos">
                        <span>Quiero recibir bonos y novedades del blog por email.</span>
                    </label>
                    @error('recibir_bonos') <div class="client-error" style="margin-bottom:12px;">{{ $message }}</div> @enderror

                    <button type="submit" class="fe-btn primary" style="width:100%;height:46px;">Crear cuenta</button>
                </form>
                <div class="client-auth-foot">
                    Ya tenes cuenta? <a href="{{ route('login') }}" wire:navigate>Ingresar</a>
                    <br>
                    <a href="{{ route('client.password.request') }}" wire:navigate>Recuperar contrasena</a>
                </div>
            </div>
        </div>
    </div>
</section>
