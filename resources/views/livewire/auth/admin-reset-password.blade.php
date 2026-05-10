<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:radial-gradient(ellipse at 50% 0%,rgba(255,106,26,.08) 0%,transparent 60%),var(--black);">
    <div style="width:min(440px,100%);">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-family:var(--font-display);font-size:44px;color:var(--orange);letter-spacing:.05em;line-height:1;">RED PICANTES</div>
            <div style="color:var(--muted-2);font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-top:6px;">Nueva contraseña</div>
        </div>
        @if($reset)
        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid var(--good);border-radius:14px;padding:32px 28px;text-align:center;">
            <div style="font-size:40px;margin-bottom:14px;">✓</div>
            <h3 style="font-family:var(--font-display);font-size:22px;color:var(--good);margin-bottom:10px;">Contraseña actualizada</h3>
            <p style="color:var(--muted-2);font-size:13px;margin-bottom:22px;">Tu contraseña fue restablecida correctamente.</p>
            <a href="{{ route('admin.login') }}" wire:navigate class="btn-link">Iniciar sesión</a>
        </div>
        @elseif($error)
        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid rgba(255,71,87,.35);border-radius:14px;padding:32px 28px;text-align:center;">
            <div style="font-size:36px;margin-bottom:14px;">!</div>
            <h3 style="font-family:var(--font-display);font-size:20px;color:#ff4757;margin-bottom:10px;">Enlace inválido</h3>
            <p style="color:var(--muted-2);font-size:13px;margin-bottom:22px;">{{ $error }}</p>
            <a href="{{ route('admin.password.request') }}" wire:navigate class="btn-link">Solicitar nuevo enlace</a>
        </div>
        @else
        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid var(--line-2);border-radius:14px;padding:32px 28px;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0 0 auto;height:2px;background:linear-gradient(90deg,var(--orange),var(--amber));"></div>
            <form wire:submit="resetPassword">
                <div style="margin-bottom:18px;">
                    <label style="display:block;margin-bottom:7px;color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Email *</label>
                    <input type="email" wire:model="email" placeholder="tu@email.com" autocomplete="email" style="width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:8px;padding:11px 14px;color:var(--white);font-size:14px;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--line-2)'">
                    @error('email')<div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block;margin-bottom:7px;color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Nueva contraseña *</label>
                    <input type="password" wire:model="password" placeholder="Mínimo 6 caracteres" autocomplete="new-password" style="width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:8px;padding:11px 14px;color:var(--white);font-size:14px;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--line-2)'">
                    @error('password')<div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block;margin-bottom:7px;color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Confirmar contraseña *</label>
                    <input type="password" wire:model="password_confirmation" placeholder="Repetí la contraseña" autocomplete="new-password" style="width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:8px;padding:11px 14px;color:var(--white);font-size:14px;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--line-2)'">
                    @error('password_confirmation')<div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" style="width:100%;height:46px;background:linear-gradient(135deg,var(--orange),var(--orange-deep,#e6580f));border:none;border-radius:9px;color:#fff;font-family:var(--font-display);font-size:17px;letter-spacing:.06em;cursor:pointer;" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <span wire:loading.remove>GUARDAR NUEVA CONTRASEÑA</span>
                    <span wire:loading style="font-size:13px;font-family:var(--font-body);letter-spacing:0;">Guardando...</span>
                </button>
            </form>
        </div>
        @endif
        <div style="text-align:center;margin-top:18px;color:var(--muted-2);font-size:12px;">¿Ya tenés cuenta? <a href="{{ route('admin.login') }}" wire:navigate style="color:var(--orange);text-decoration:none;font-weight:700;">Iniciar sesión</a></div>
        <div style="text-align:center;margin-top:14px;color:var(--muted);font-size:10px;">RED PICANTES &copy; {{ date('Y') }}</div>
    </div>
</div>
<style>.btn-link { display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 20px;border-radius:8px;background:linear-gradient(135deg,var(--orange),var(--orange-deep,#e6580f));color:#fff;font-family:var(--font-display);font-size:15px;letter-spacing:.06em;text-decoration:none;transition:opacity .15s; } .btn-link:hover { opacity:.88;text-decoration:none; }</style>
