@push('styles')
<style>
    .auth-forgot { padding:54px 0 0; }
    .auth-forgot-card { width:min(460px, 100%); margin:0 auto; border:1px solid rgba(255,255,255,.1); border-radius:12px; background:radial-gradient(80% 70% at 50% 0%, rgba(255,106,26,.18), transparent 58%), linear-gradient(180deg,#160807,#080302); padding:28px; box-shadow:0 24px 70px rgba(0,0,0,.42); }
    .auth-forgot-brand { text-align:center; margin-bottom:24px; }
    .auth-forgot-brand h1 { font-family:var(--font-display); font-size:38px; line-height:.9; margin:0; letter-spacing:.03em; }
    .auth-forgot-brand h1 span { color:var(--orange); }
    .auth-forgot-brand p { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin:6px 0 0; }
    .auth-field { margin-bottom:15px; }
    .auth-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .auth-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .auth-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .auth-error-box { background:rgba(255,71,87,.1); border:1px solid rgba(255,71,87,.35); border-radius:8px; padding:12px 14px; margin-bottom:18px; color:#ff8a8a; font-size:13px; font-weight:800; }
    .auth-success-box { background:rgba(37,196,107,.1); border:1px solid rgba(37,196,107,.35); border-radius:8px; padding:12px 14px; margin-bottom:18px; color:#adffd0; font-size:13px; font-weight:800; }
    .auth-foot { margin-top:16px; color:var(--muted); font-size:12px; text-align:center; }
    .auth-foot a { color:var(--orange); font-weight:900; text-decoration:none; }
    @media (max-width: 520px) {
        .auth-forgot { padding-top:34px; }
        .auth-forgot-card { padding:22px 18px; }
        .auth-forgot-brand h1 { font-size:34px; }
    }
</style>
@endpush

<section class="auth-forgot">
    <div class="fe-shell">
        <div class="auth-forgot-card">
            <div class="auth-forgot-brand">
                <h1>Red <span>Picantes</span></h1>
                <p>Recuperar contraseña</p>
            </div>

            @if(session('success'))
                <div class="auth-success-box">{{ session('success') }}</div>
            @endif

            <form wire:submit.prevent="sendResetLink">
                @if($errors->has('email'))
                    <div class="auth-error-box">{{ $errors->first('email') }}</div>
                @endif

                <div class="auth-field">
                    <label class="auth-label" for="email">Email</label>
                    <input id="email" class="auth-input" type="email" wire:model.defer="email" autocomplete="email" autofocus>
                </div>

                <button type="submit" class="fe-btn primary" style="width:100%;height:46px;">
                    <span wire:loading.remove>Enviar enlace</span>
                    <span wire:loading>Enviando...</span>
                </button>
            </form>

            <div class="auth-foot">
                ¿Recordaste tu contraseña? <a href="{{ route('login') }}" wire:navigate>Ingresar</a>
            </div>
        </div>
    </div>
</section>
