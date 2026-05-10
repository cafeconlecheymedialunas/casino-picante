<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:radial-gradient(ellipse at 50% 0%,rgba(255,106,26,.08) 0%,transparent 60%),var(--black);">
    <div style="width:min(440px,100%);">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-family:var(--font-display);font-size:44px;color:var(--orange);letter-spacing:.05em;line-height:1;">RED PICANTES</div>
            <div style="color:rgba(255,255,255,0.42);font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-top:6px;">Nueva contraseña</div>
        </div>
        @if($reset)
        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid var(--good);border-radius:14px;padding:32px 28px;text-align:center;">
            <div style="font-size:40px;margin-bottom:14px;">✓</div>
            <h3 style="font-family:var(--font-display);font-size:22px;color:var(--good);margin-bottom:10px;">Contraseña actualizada</h3>
            <p style="color:rgba(255,255,255,0.42);font-size:13px;margin-bottom:22px;">Tu contraseña fue restablecida correctamente.</p>
            <a href="{{ route('admin.login') }}" wire:navigate class="btn-ghost">Iniciar sesión</a>
        </div>
        @elseif($error)
        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid rgba(255,71,87,.35);border-radius:14px;padding:32px 28px;text-align:center;">
            <div style="font-size:36px;margin-bottom:14px;">!</div>
            <h3 style="font-family:var(--font-display);font-size:20px;color:#ff4757;margin-bottom:10px;">Enlace inválido</h3>
            <p style="color:rgba(255,255,255,0.42);font-size:13px;margin-bottom:22px;">{{ $error }}</p>
            <a href="{{ route('admin.password.request') }}" wire:navigate class="btn-ghost">Solicitar nuevo enlace</a>
        </div>
        @else
        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid rgba(255,255,255,0.14);border-radius:14px;padding:32px 28px;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0 0 auto;height:2px;background:linear-gradient(90deg,var(--orange),var(--amber));"></div>
            <form wire:submit="resetPassword">
                <div style="margin-bottom:18px;">
                    <label style="display:block;margin-bottom:7px;color:rgba(255,255,255,0.62);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Email *</label>
                    <input type="email" wire:model="email" placeholder="tu@email.com" autocomplete="email" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid rgba(255,120,50,0.22);border-radius:8px;padding:11px 14px;color:#fff;font-size:14px;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--orange)';this.style.boxShadow='0 0 0 3px rgba(255,106,26,.12)'" onblur="this.style.borderColor='rgba(255,120,50,0.22)';this.style.boxShadow='none'">
                    @error('email')<div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block;margin-bottom:7px;color:rgba(255,255,255,0.62);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Nueva contraseña *</label>
                    <input type="password" wire:model="password" placeholder="Mínimo 6 caracteres" autocomplete="new-password" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid rgba(255,120,50,0.22);border-radius:8px;padding:11px 14px;color:#fff;font-size:14px;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--orange)';this.style.boxShadow='0 0 0 3px rgba(255,106,26,.12)'" onblur="this.style.borderColor='rgba(255,120,50,0.22)';this.style.boxShadow='none'">
                    @error('password')<div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block;margin-bottom:7px;color:rgba(255,255,255,0.62);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Confirmar contraseña *</label>
                    <input type="password" wire:model="password_confirmation" placeholder="Repetí la contraseña" autocomplete="new-password" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid rgba(255,120,50,0.22);border-radius:8px;padding:11px 14px;color:#fff;font-size:14px;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--orange)';this.style.boxShadow='0 0 0 3px rgba(255,106,26,.12)'" onblur="this.style.borderColor='rgba(255,120,50,0.22)';this.style.boxShadow='none'">
                    @error('password_confirmation')<div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary" style="width:100%;justify-content:center;height:46px;font-family:var(--font-display);font-size:17px;letter-spacing:.06em;">
                    <span wire:loading.remove>GUARDAR NUEVA CONTRASEÑA</span>
                    <span wire:loading style="font-size:13px;font-family:var(--font-body);letter-spacing:0;">Guardando...</span>
                </button>
            </form>
        </div>
        @endif
        <div style="text-align:center;margin-top:18px;color:rgba(255,255,255,0.42);font-size:12px;">¿Ya tenés cuenta? <a href="{{ route('admin.login') }}" wire:navigate style="color:var(--orange);text-decoration:none;font-weight:700;">Iniciar sesión</a></div>
        <div style="text-align:center;margin-top:18px;color:rgba(255,255,255,0.42);font-size:12px;">¿No tenés cuenta? <a href="{{ route('agent.register') }}" wire:navigate style="color:var(--orange);text-decoration:none;font-weight:700;">Registrate como agente</a></div>
        <div style="text-align:center;margin-top:14px;color:rgba(255,255,255,0.25);font-size:10px;">RED PICANTES &copy; {{ date('Y') }}</div>
    </div>
</div>
