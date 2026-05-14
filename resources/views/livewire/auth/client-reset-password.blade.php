@push('styles')
<style>
    .auth-reset { padding:54px 0 0; }
    .auth-reset-card { width:min(460px, 100%); margin:0 auto; border:1px solid rgba(255,255,255,.1); border-radius:12px; background:radial-gradient(80% 70% at 50% 0%, rgba(255,106,26,.18), transparent 58%), linear-gradient(180deg,#160807,#080302); padding:28px; box-shadow:0 24px 70px rgba(0,0,0,.42); }
    .auth-reset-brand { text-align:center; margin-bottom:24px; }
    .auth-reset-brand h1 { font-family:var(--font-display); font-size:38px; line-height:.9; margin:0; letter-spacing:.03em; }
    .auth-reset-brand h1 span { color:var(--orange); }
    .auth-reset-brand p { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin:6px 0 0; }
    .auth-field { margin-bottom:15px; }
    .auth-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .auth-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .auth-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .auth-error-box { background:rgba(255,71,87,.1); border:1px solid rgba(255,71,87,.35); border-radius:8px; padding:12px 14px; margin-bottom:18px; color:#ff8a8a; font-size:13px; font-weight:800; }
    .auth-foot { margin-top:16px; color:var(--muted); font-size:12px; text-align:center; }
    .auth-foot a { color:var(--orange); font-weight:900; text-decoration:none; }
</style>
@endpush

<section class="auth-reset">
    <div class="fe-shell">
        <div class="auth-reset-card">
            <div class="auth-reset-brand">
                <h1>Red <span>Picantes</span></h1>
                <p>Nueva contraseña</p>
            </div>

            <form wire:submit.prevent="resetPassword">
                @if($errors->any())
                    <div class="auth-error-box">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <input type="hidden" wire:model="token">

                <div class="auth-field">
                    <label class="auth-label" for="email">Email</label>
                    <input id="email" class="auth-input" type="email" wire:model.defer="email" autocomplete="email" required>
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="password">Nueva contraseña</label>
                    <input id="password" class="auth-input" type="password" wire:model.defer="password" autocomplete="new-password" required>
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" class="auth-input" type="password" wire:model.defer="password_confirmation" autocomplete="new-password" required>
                </div>

                <button type="submit" class="fe-btn primary" style="width:100%;height:46px;">
                    <span wire:loading.remove>Restablecer</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </form>

            <div class="auth-foot">
                ¿Ya tenés cuenta? <a href="{{ route('login') }}" wire:navigate>Ingresar</a>
            </div>
        </div>
    </div>
</section>