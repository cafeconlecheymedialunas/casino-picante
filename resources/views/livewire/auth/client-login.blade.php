@push('styles')
<style>
    .client-login { padding:54px 0 0; }
    .client-login-card { width:min(430px, 100%); margin:0 auto; border:1px solid rgba(255,255,255,.1); border-radius:12px; background:radial-gradient(80% 70% at 50% 0%, rgba(255,106,26,.18), transparent 58%), linear-gradient(180deg,#160807,#080302); padding:28px; box-shadow:0 24px 70px rgba(0,0,0,.42); }
    .client-login-brand { text-align:center; margin-bottom:24px; }
    .client-login-brand h1 { font-family:var(--font-display); font-size:52px; line-height:.9; margin:0; letter-spacing:.03em; }
    .client-login-brand h1 span { color:var(--orange); }
    .client-login-brand p { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin:8px 0 0; }
    .client-field { margin-bottom:15px; }
    .client-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .client-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .client-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .client-error-box { background:rgba(255,71,87,.1); border:1px solid rgba(255,71,87,.35); border-radius:8px; padding:12px 14px; margin-bottom:18px; color:#ff8a8a; font-size:13px; font-weight:800; }
    .client-login-foot { margin-top:16px; color:var(--muted); font-size:12px; text-align:center; }
    .client-login-foot a { color:var(--orange); font-weight:900; text-decoration:none; }
    .client-login-foot a:hover { text-decoration:underline; }
    .client-login-links { display:flex; justify-content:space-between; margin-top:12px; }
    @media (max-width: 520px) {
        .client-login { padding-top:34px; }
        .client-login-card { padding:22px 18px; }
        .client-login-brand h1 { font-size:44px; }
        .client-login-links { display:grid; gap:10px; text-align:center; }
    }
</style>
@endpush

<section class="client-login">
    <div class="fe-shell">
        <div class="client-login-card">
            <div class="client-login-brand">
                <h1>Red <span>Picantes</span></h1>
                <p>Iniciar sesión</p>
            </div>

            <form wire:submit.prevent="login">
                @if($errors->has('username'))
                    <div class="client-error-box">{{ $errors->first('username') }}</div>
                @endif

                <div class="client-field">
                    <label class="client-label" for="username">Nombre de Cliente o Email</label>
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
</section>
