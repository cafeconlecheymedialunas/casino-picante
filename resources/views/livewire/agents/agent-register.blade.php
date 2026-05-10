<div>
    <style>
        .ar-wrap { max-width: 600px; margin: 0 auto; padding: 40px 0; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; }
        .ar-card {
            background: linear-gradient(180deg, #1c0e0e, #120909);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 14px;
            padding: 32px 28px;
            position: relative;
            overflow: hidden;
        }
        .ar-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 2px;
            background: linear-gradient(90deg, #ff6a1a, #ffb347);
        }
        .ar-title { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: .04em; margin-bottom: 6px; color: #ff6a1a; }
        .ar-sub { color: rgba(255,255,255,0.42); font-size: 12px; margin-bottom: 28px; }
        .ar-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .ar-group { margin-bottom: 16px; }
        .ar-label { display: block; font-size: 11px; font-weight: 800; letter-spacing: .08em; color: rgba(255,255,255,0.62); text-transform: uppercase; margin-bottom: 6px; }
        .ar-input {
            width: 100%;
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid rgba(255,120,50,0.22);
            border-radius: 8px;
            padding: 10px 14px;
            color: #fff;
            font-size: 13px;
            font-family: 'Manrope', sans-serif;
        }
        .ar-input:focus { outline: none; border-color: #ff6a1a; box-shadow: 0 0 0 3px rgba(255,106,26,.12); }
        .ar-error { color: #ff4757; font-size: 11px; margin-top: 4px; }
        .ar-hint { font-size: 10px; color: rgba(255,255,255,0.42); margin-top: 4px; }
        .ar-line-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .ar-line-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            cursor: pointer;
            font-size: 12px;
            color: rgba(255,255,255,0.42);
            transition: all .15s;
        }
        .ar-line-chip:hover { border-color: #ff6a1a; }
        .ar-line-chip.selected { border-color: #ff6a1a; background: rgba(255,106,26,.12); color: #ff6a1a; }
        .ar-line-chip input { accent-color: #ff6a1a; }
        .ar-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.08); }
        .ar-success-card {
            background: linear-gradient(180deg, #1c0e0e, #120909);
            border: 1px solid #25c46b;
            border-radius: 14px;
            padding: 48px 32px;
            text-align: center;
        }
        .ar-success-icon { font-size: 56px; margin-bottom: 16px; }
        .ar-success-title { font-family: 'Bebas Neue', sans-serif; font-size: 26px; color: #25c46b; margin-bottom: 10px; }
        .ar-success-msg { color: rgba(255,255,255,0.42); font-size: 13px; line-height: 1.6; }
        @media (max-width: 600px) { .ar-grid { grid-template-columns: 1fr; } }
    </style>

    @if($registered)
    <div class="ar-wrap">
        <div class="ar-success-card">
            <div class="ar-success-icon">✓</div>
            <h2 class="ar-success-title">¡CUENTA CREADA!</h2>
            <p class="ar-success-msg">
                Tu cuenta fue creada exitosamente.<br>
                Ya podés iniciar sesión con tus credenciales.
            </p>
            <a href="{{ route('admin.login') }}" class="btn-primary" style="display:inline-flex;margin-top:24px;padding:10px 24px;border-radius:8px;text-decoration:none;">
                Iniciar sesión
            </a>
        </div>
    </div>
    @else
    <div class="ar-wrap">
        <div class="ar-card">
            <h1 class="ar-title">REGISTRATE COMO AGENTE</h1>
            <p class="ar-sub">Completá tus datos para acceder al panel.</p>

            <form wire:submit.prevent="register">
                <div class="ar-grid">
                    <div class="ar-group">
                        <label class="ar-label">Nombre *</label>
                        <input type="text" wire:model="name" class="ar-input" placeholder="Nombre">
                        @error('name') <div class="ar-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="ar-group">
                        <label class="ar-label">Apellido</label>
                        <input type="text" wire:model="apellido" class="ar-input" placeholder="Apellido">
                    </div>
                </div>

                <div class="ar-group">
                    <label class="ar-label">Email *</label>
                    <input type="email" wire:model="email" class="ar-input" placeholder="agente@ejemplo.com">
                    @error('email') <div class="ar-error">{{ $message }}</div> @enderror
                </div>

                <div class="ar-group">
                    <label class="ar-label">Usuario</label>
                    <input type="text" wire:model="username" class="ar-input" placeholder="usuario_agente">
                    <div class="ar-hint">Ejemplo: usuario01 · Si lo dejás vacío se genera automáticamente.</div>
                    @error('username') <div class="ar-error">{{ $message }}</div> @enderror
                </div>

                <div class="ar-grid">
                    <div class="ar-group">
                        <label class="ar-label">Contraseña *</label>
                        <input type="password" wire:model="password" class="ar-input" placeholder="Mínimo 6 caracteres">
                        @error('password') <div class="ar-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="ar-group">
                        <label class="ar-label">Confirmar *</label>
                        <input type="password" wire:model="password_confirmation" class="ar-input" placeholder="Repetí la contraseña">
                    </div>
                </div>

                <div class="ar-group">
                    <label class="ar-label">Teléfono</label>
                    <input type="text" wire:model="phone" class="ar-input" placeholder="+54 9 11 0000 0000">
                </div>

                <div class="ar-group">
                    <label class="ar-label">Líneas donde vas a trabajar *</label>
                    <div class="ar-line-grid">
                        @forelse($lines as $line)
                        <label class="ar-line-chip {{ in_array($line->id, $selectedLines) ? 'selected' : '' }}">
                            <input type="checkbox" value="{{ $line->id }}" wire:model="selectedLines">
                            {{ $line->name }}
                        </label>
                        @empty
                        <div class="ar-hint">No hay líneas disponibles.</div>
                        @endforelse
                    </div>
                    @error('selectedLines')
                    <div class="ar-error">{{ $message }}</div>
                    @else
                    <div class="ar-hint">Seleccioná al menos una línea donde vas a trabajar.</div>
                    @enderror
                </div>

                <div class="ar-actions">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" style="padding:10px 24px;border-radius:8px;">
                        <span wire:loading.remove>Crear cuenta</span>
                        <span wire:loading>Creando...</span>
                    </button>
                </div>
            </form>
        </div>

        <div style="text-align:center;margin-top:20px;color:rgba(255,255,255,0.42);font-size:12px;">
            ¿Ya tenés cuenta? <a href="{{ route('admin.login') }}" wire:navigate style="color:#ff6a1a;text-decoration:none;font-weight:700;">Iniciar sesión</a>
        </div>
        <div style="text-align:center;margin-top:14px;color:rgba(255,255,255,0.25);font-size:10px;">RED PICANTES &copy; {{ date('Y') }}</div>
    </div>
    @endif
</div>
