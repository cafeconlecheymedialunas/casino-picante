<div class="page-container" x-data="toastManager()" @toast.window="show($event.detail)">
    <style>
        .ar-wrap { max-width: 600px; margin: 0 auto; }
        .ar-card {
            background: linear-gradient(180deg, #1c0e0e, #120909);
            border: 1px solid var(--line-2);
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
            background: linear-gradient(90deg, var(--orange), var(--amber));
        }
        .ar-title { font-family: var(--font-display); font-size: 28px; letter-spacing: .04em; margin-bottom: 6px; }
        .ar-sub { color: var(--muted-2); font-size: 12px; margin-bottom: 28px; }
        .ar-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .ar-group { margin-bottom: 16px; }
        .ar-label { display: block; font-size: 11px; font-weight: 800; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; }
        .ar-input {
            width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--line-2);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--white);
            font-size: 13px;
            font-family: var(--font-body);
        }
        .ar-input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,.12); }
        .ar-error { color: #ff4757; font-size: 11px; margin-top: 4px; }
        .ar-hint { font-size: 10px; color: var(--muted); margin-top: 4px; }
        .ar-line-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .ar-line-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,.03);
            cursor: pointer;
            font-size: 12px;
            color: var(--muted-2);
            transition: all .15s;
        }
        .ar-line-chip:hover { border-color: var(--orange); }
        .ar-line-chip.selected { border-color: var(--orange); background: rgba(255,106,26,.12); color: var(--orange); }
        .ar-line-chip input { accent-color: var(--orange); }
        .ar-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); }
        .ar-success-card {
            background: linear-gradient(180deg, #1c0e0e, #120909);
            border: 1px solid var(--good);
            border-radius: 14px;
            padding: 48px 32px;
            text-align: center;
        }
        .ar-success-icon { font-size: 56px; margin-bottom: 16px; }
        .ar-success-title { font-family: var(--font-display); font-size: 26px; color: var(--good); margin-bottom: 10px; }
        .ar-success-msg { color: var(--muted-2); font-size: 13px; line-height: 1.6; }
        .toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 500; display: flex; flex-direction: column; gap: 8px; }
        .toast-item { padding: 13px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,.4); }
        .toast-success { background: var(--good); color: #002b14; }
        .toast-danger { background: #ff4757; color: #fff; }
        @media (max-width: 600px) { .ar-grid { grid-template-columns: 1fr; } }
    </style>

    @if($registered)
    <div class="ar-wrap" style="padding: 40px 0;">
        <div class="ar-success-card">
            <div class="ar-success-icon">✓</div>
            <h2 class="ar-success-title">SOLICITUD ENVIADA</h2>
            <p class="ar-success-msg">
                Tu registro fue recibido y quedó en espera de aprobación.<br>
                Un administrador te asignará líneas y activará tu cuenta.<br>
                Te notificaremos cuando esté lista.
            </p>
            <a href="{{ route('admin.login') }}" wire:navigate class="btn-primary" style="display:inline-flex;margin-top:24px;padding:10px 24px;border-radius:8px;text-decoration:none;">
                Volver al login
            </a>
        </div>
    </div>
    @else
    <div class="ar-wrap" style="padding: 40px 0;">
        <div class="ar-card">
            <h1 class="ar-title">REGISTRAR AGENTE</h1>
            <p class="ar-sub">Completá tus datos para solicitar acceso al panel.</p>

            <form wire:submit.prevent="register">
                <div class="ar-grid">
                    <div class="ar-group">
                        <label class="ar-label">Nombre *</label>
                        <input type="text" wire:model="name" class="ar-input @error('name') is-error @enderror" placeholder="Nombre">
                        @error('name') <div class="ar-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="ar-group">
                        <label class="ar-label">Apellido</label>
                        <input type="text" wire:model="apellido" class="ar-input" placeholder="Apellido">
                    </div>
                </div>

                <div class="ar-group">
                    <label class="ar-label">Email *</label>
                    <input type="email" wire:model="email" class="ar-input @error('email') is-error @enderror" placeholder="agente@ejemplo.com">
                    @error('email') <div class="ar-error">{{ $message }}</div> @enderror
                </div>

                <div class="ar-group">
                    <label class="ar-label">Usuario</label>
                    <input type="text" wire:model="username" class="ar-input @error('username') is-error @enderror" placeholder="usuario_agente">
                    <div class="ar-hint">Ejemplo: usuario01 · Si lo dejás vacío se genera automáticamente.</div>
                    @error('username') <div class="ar-error">{{ $message }}</div> @enderror
                </div>

                <div class="ar-grid">
                    <div class="ar-group">
                        <label class="ar-label">Contraseña *</label>
                        <input type="password" wire:model="password" class="ar-input @error('password') is-error @enderror" placeholder="Mínimo 6 caracteres">
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

                @if($lines->count())
                <div class="ar-group">
                    <label class="ar-label">Líneas de interés <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);">(opcional)</span></label>
                    <div class="ar-line-grid">
                        @foreach($lines as $line)
                        <label class="ar-line-chip {{ in_array($line->id, $selectedLines) ? 'selected' : '' }}">
                            <input type="checkbox" value="{{ $line->id }}" wire:model="selectedLines">
                            {{ $line->name }}
                        </label>
                        @endforeach
                    </div>
                    <div class="ar-hint">Podés dejarlo vacío. Un administrador te asignará las líneas.</div>
                </div>
                @endif

                @if($error)
                <div style="background:rgba(255,71,87,.1);border:1px solid rgba(255,71,87,.35);border-radius:8px;padding:12px 14px;margin-bottom:16px;color:#ff6b7a;font-size:13px;font-weight:700;">
                    {{ $error }}
                </div>
                @endif

                <div class="ar-actions">
                    <a href="{{ route('admin.login') }}" wire:navigate class="btn-ghost" style="padding:10px 20px;border-radius:8px;text-decoration:none;color:var(--muted)">Cancelar</a>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" style="padding:10px 24px;border-radius:8px;">
                        <span wire:loading.remove>Solicitar registro</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="toast-wrap">
        <template x-for="t in toasts" :key="t.id">
            <div class="toast-item" :class="'toast-' + t.type" x-text="t.message"></div>
        </template>
    </div>

    <script>
        function toastManager() {
            return {
                toasts: [],
                show({ message, type = 'success' }) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => { this.toasts = this.toasts.filter((toast) => toast.id !== id); }, 3500);
                }
            }
        }
    </script>
</div>
