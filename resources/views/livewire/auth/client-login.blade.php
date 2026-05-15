@push('styles')
<style>
    .client-login { padding:54px 0; min-height:calc(100vh - 200px); display:flex; align-items:center; }
    .client-login-grid { display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:center; }
    .client-login-welcome { text-align:left; }
    .client-login-welcome h1 { font-family:var(--font-display); font-size:64px; line-height:.95; margin:0 0 16px; letter-spacing:.02em; }
    .client-login-welcome h1 span { color:var(--orange); }
    .client-login-welcome p { color:var(--muted); font-size:16px; line-height:1.5; max-width:400px; }
    .client-login-card { border:1px solid rgba(255,255,255,.1); border-radius:16px; background:radial-gradient(80% 70% at 50% 0%, rgba(255,106,26,.18), transparent 58%), linear-gradient(180deg,#160807,#080302); padding:32px; box-shadow:0 24px 70px rgba(0,0,0,.42); }
    .client-login-card h2 { font-family:var(--font-display); font-size:28px; margin:0 0 20px; text-align:center; }
    .client-field { margin-bottom:15px; }
    .client-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .client-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .client-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .client-error-box { background:rgba(255,71,87,.1); border:1px solid rgba(255,71,87,.35); border-radius:8px; padding:12px 14px; margin-bottom:18px; color:#ff8a8a; font-size:13px; font-weight:800; }
    .client-login-foot { margin-top:16px; color:var(--muted); font-size:12px; text-align:center; }
    .client-login-foot a { color:var(--orange); font-weight:900; text-decoration:none; }
    .client-login-foot a:hover { text-decoration:underline; }
    .client-login-links { display:flex; justify-content:space-between; margin-top:12px; }
    @media (max-width: 768px) {
        .client-login { padding:40px 0; }
        .client-login-grid { grid-template-columns:1fr; gap:30px; text-align:center; }
        .client-login-welcome { text-align:center; }
        .client-login-welcome p { margin:0 auto; }
        .client-login-card { padding:24px 20px; }
    }
    @media (max-width: 520px) {
        .client-login-welcome h1 { font-size:42px; }
    }
</style>
@endpush

<section class="client-login">
    <div class="fe-shell">
        <div class="client-login-grid">
            <div class="client-login-welcome">
                <h1>BIENVENIDO</h1>
                <p>Ingresá con tu usuario o email a tu cuenta de cliente</p>
            </div>
            
            <div class="client-login-card">
                <h2>Iniciar sesión</h2>

                <form wire:submit.prevent="login">
                    @if($errors->has('username'))
                        <div class="client-error-box">{{ $errors->first('username') }}</div>
                    @endif

                    <div class="client-field">
                        <label class="client-label" for="username">Usuario o Email</label>
                        <input id="username" class="client-input" type="text" wire:model.defer="username" autocomplete="username" autofocus placeholder="Tu usuario o email">
                    </div>

                    <div class="client-field">
                        <label class="client-label" for="password">Contraseña</label>
                        <input id="password" class="client-input" type="password" wire:model.defer="password" autocomplete="current-password" placeholder="••••••••">
                    </div>

                    <button type="submit" class="fe-btn primary" style="width:100%;height:46px;">
                        <span wire:loading.remove>Ingresar</span>
                        <span wire:loading>Verificando...</span>
                    </button>
                </form>

                <div class="client-login-links">
                    <a href="{{ route('client.password.request') }}" wire:navigate>¿Olvidaste tu contraseña?</a>
                    <a href="{{ route('client.register') }}" wire:navigate>Registrarse</a>
                </div>
            </div>
        </div>
    </div>
</section>
