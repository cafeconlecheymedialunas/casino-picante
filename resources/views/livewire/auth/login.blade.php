<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:radial-gradient(ellipse at 50% 0%,rgba(255,106,26,.08) 0%,transparent 60%),var(--black);">
    <div style="width:min(420px,100%);">

        <div style="text-align:center;margin-bottom:36px;">
            <div style="font-family:var(--font-display);font-size:48px;color:var(--orange);letter-spacing:.05em;line-height:1;">RED PICANTES</div>
            <div style="color:var(--muted-2);font-size:12px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-top:6px;">{{ $heading ?? 'Panel de administracion' }}</div>
        </div>

        <div style="background:linear-gradient(180deg,#1c0e0e,#120909);border:1px solid var(--line-2);border-radius:14px;padding:32px 28px;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0 0 auto;height:2px;background:linear-gradient(90deg,var(--orange),var(--amber));"></div>

            <form wire:submit.prevent="login">

                @if($errors->has('username'))
                    <div style="background:rgba(255,71,87,.1);border:1px solid rgba(255,71,87,.35);border-radius:8px;padding:12px 14px;margin-bottom:20px;color:#ff6b7a;font-size:13px;font-weight:700;">
                        {{ $errors->first('username') }}
                    </div>
                @endif

                <div style="margin-bottom:18px;">
                    <label style="display:block;margin-bottom:7px;color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Usuario</label>
                    <input
                        type="text"
                        wire:model="username"
                        placeholder="Username o email"
                        autocomplete="username"
                        autofocus
                        style="width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:8px;padding:11px 14px;color:var(--white);font-size:14px;font-family:var(--font-body);outline:none;transition:border-color .15s;"
                        onfocus="this.style.borderColor='var(--orange)';this.style.boxShadow='0 0 0 3px rgba(255,106,26,.12)'"
                        onblur="this.style.borderColor='var(--line-2)';this.style.boxShadow='none'"
                    >
                </div>

                <div style="margin-bottom:26px;">
                    <label style="display:block;margin-bottom:7px;color:var(--muted-2);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Contraseña</label>
                    <input
                        type="password"
                        wire:model="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        style="width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:8px;padding:11px 14px;color:var(--white);font-size:14px;font-family:var(--font-body);outline:none;transition:border-color .15s;"
                        onfocus="this.style.borderColor='var(--orange)';this.style.boxShadow='0 0 0 3px rgba(255,106,26,.12)'"
                        onblur="this.style.borderColor='var(--line-2)';this.style.boxShadow='none'"
                    >
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    style="width:100%;height:46px;background:linear-gradient(135deg,var(--orange),var(--orange-deep,#e6580f));border:none;border-radius:9px;color:#fff;font-family:var(--font-display);font-size:20px;letter-spacing:.06em;cursor:pointer;transition:opacity .15s;"
                    onmouseover="this.style.opacity='.88'"
                    onmouseout="this.style.opacity='1'"
                >
                    <span wire:loading.remove>{{ $submitLabel ?? 'INGRESAR' }}</span>
                    <span wire:loading style="font-size:13px;font-family:var(--font-body);letter-spacing:0;">Verificando...</span>
                </button>

            </form>
        </div>

        <div style="text-align:center;margin-top:20px;color:var(--muted-2);font-size:11px;">
            RED PICANTES &copy; {{ date('Y') }}
        </div>
    </div>
</div>
