<div class="fe-container">
    <style>
        .profile-header { display: flex; align-items: center; gap: 16px; margin-bottom: 28px; }
        .profile-avatar-lg { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; color: #190702; font-weight: 800; font-size: 22px; flex-shrink: 0; }
        .profile-tabs { display: flex; gap: 4px; margin-bottom: 24px; flex-wrap: wrap; }
        .profile-tab { padding: 8px 16px; border-radius: 999px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; border: none; }
        .profile-tab.active { background: var(--orange); color: #190702; }
        .profile-tab.inactive { background: rgba(255,255,255,0.06); color: var(--muted); }
        .profile-tab.inactive:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .tab-badge { background: #ff4757; color: #fff; border-radius: 999px; padding: 1px 6px; font-size: 10px; margin-left: 4px; }
        .form-field { margin-bottom: 16px; }
        .form-field label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: 0.08em; margin-bottom: 6px; }
        .form-input { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--line-2); border-radius: var(--r-sm); padding: 10px 14px; color: #fff; font-size: 13px; font-family: var(--font-body); outline: none; transition: border 0.2s; }
        .form-input:focus { border-color: var(--orange); }
        .notif-item { padding: 14px; border-radius: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); margin-bottom: 8px; display: flex; gap: 12px; align-items: flex-start; cursor: pointer; transition: all 0.2s; }
        .notif-item:hover { background: rgba(255,255,255,0.06); }
        .notif-item.unread { border-color: var(--orange); background: rgba(255,106,26,0.05); }
        .notif-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--orange); margin-top: 5px; flex-shrink: 0; }
        .notif-dot.read { background: transparent; border: 1px solid var(--line-2); }
        .bonus-card { padding: 16px; border-radius: 14px; border: 1px solid var(--line-warm); background: linear-gradient(180deg, #1c0d0a, #120909); margin-bottom: 10px; }
        .bonus-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
        .bonus-title { font-weight: 700; font-size: 14px; }
        .bonus-badge { padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .bonus-active { background: rgba(37,196,107,0.12); color: var(--good); }
        .bonus-expiring { background: rgba(255,179,71,0.15); color: var(--warn); }
        .bonus-expired { background: rgba(255,255,255,0.06); color: var(--muted-2); }
        .bonus-dates { font-size: 11px; color: var(--muted); margin-top: 4px; }
        .number-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 8px; margin-top: 16px; }
        .number-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px; text-align: center; }
        .number-val { font-family: var(--font-display); font-size: 24px; color: var(--orange); }
        .number-lbl { font-size: 9px; color: var(--muted); letter-spacing: 0.08em; margin-top: 2px; }
        .winners-list { margin-top: 16px; }
        .winner-row { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 10px; background: rgba(255,106,26,0.05); border: 1px solid var(--line-warm); margin-bottom: 8px; }
        .winner-pos { font-family: var(--font-display); font-size: 28px; color: var(--orange); width: 40px; text-align: center; }
        .winner-info { flex: 1; }
        .ticket-row { padding: 12px 16px; border-radius: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .status-open { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,179,71,0.15); color: var(--warn); }
        .status-progress { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); }
        .status-closed { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); }
        .fe-error { color: #ff4757; font-size: 11px; margin-top: 4px; }
    </style>

    @if (session()->has('message'))
        <div style="background:rgba(37,196,107,0.12);border:1px solid var(--good);border-radius:10px;padding:12px 16px;margin-bottom:18px;color:var(--good);font-size:13px;font-weight:700;">
            {{ session('message') }}
        </div>
    @endif

    <div class="profile-header">
        <div class="profile-avatar-lg">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
        <div>
            <div style="font-family:var(--font-display);font-size:26px;letter-spacing:0.02em;">{{ $user->name }}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $user->email }}</div>
        </div>
    </div>

    <div class="profile-tabs">
        <button wire:click="setTab('bonos')" class="profile-tab {{ $tab === 'bonos' ? 'active' : 'inactive' }}">
            🎁 Mis Bonos
        </button>
        <button wire:click="setTab('sorteo')" class="profile-tab {{ $tab === 'sorteo' ? 'active' : 'inactive' }}">
            🎯 Sorteo
        </button>
        <button wire:click="setTab('tickets')" class="profile-tab {{ $tab === 'tickets' ? 'active' : 'inactive' }}">
            ✉ Tickets
        </button>
        <button wire:click="setTab('notifications')" class="profile-tab {{ $tab === 'notifications' ? 'active' : 'inactive' }}">
            🔔 Notificaciones
            @if($unreadCount > 0)
                <span class="tab-badge">{{ $unreadCount }}</span>
            @endif
        </button>
        <button wire:click="setTab('password')" class="profile-tab {{ $tab === 'password' ? 'active' : 'inactive' }}">
            🔒 Contraseña
        </button>
    </div>

    {{-- BONOS --}}
    @if($tab === 'bonos')
    <div class="fe-card">
        <h3 style="font-family:var(--font-display);font-size:22px;margin-bottom:16px;">MIS BONOS</h3>
        @forelse($bonuses as $bonus)
        @php
            $isExpired = $bonus->end_date->isPast();
            $isExpiringSoon = !$isExpired && $bonus->end_date->diffInHours(now()) < 24;
        @endphp
        <div class="bonus-card">
            <div class="bonus-header">
                <div class="bonus-title">{{ $bonus->title }}</div>
                @if($isExpired)
                    <span class="bonus-badge bonus-expired">Vencido</span>
                @elseif($isExpiringSoon)
                    <span class="bonus-badge bonus-expiring">⚠ Vence pronto</span>
                @else
                    <span class="bonus-badge bonus-active">● Activo</span>
                @endif
            </div>
            @if($bonus->description)
            <p style="font-size:13px;color:var(--muted);margin-bottom:6px;">{{ $bonus->description }}</p>
            @endif
            <div class="bonus-dates">
                Vigente: {{ $bonus->start_date->format('d/m/Y') }} → {{ $bonus->end_date->format('d/m/Y') }}
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px 0;color:var(--muted);">
            <div style="font-size:32px;margin-bottom:8px;">🎁</div>
            <div>No tenés bonos activos en este momento</div>
        </div>
        @endforelse
    </div>
    @endif

    {{-- SORTEO --}}
    @if($tab === 'sorteo')
    <div class="fe-card">
        @if($activeRaffle)
        <div style="margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px;">
                <div>
                    <h3 style="font-family:var(--font-display);font-size:26px;letter-spacing:0.02em;">{{ $activeRaffle->title }}</h3>
                    @if($activeRaffle->description)
                    <p style="font-size:13px;color:var(--muted);margin-top:4px;">{{ $activeRaffle->description }}</p>
                    @endif
                </div>
                <span style="padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:rgba(37,196,107,0.12);color:var(--good);">● ACTIVO</span>
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:8px;">
                Finaliza: <strong style="color:#fff;">{{ $activeRaffle->end_date->format('d/m/Y H:i') }}</strong>
            </div>
        </div>

        {{-- Premios --}}
        @if($activeRaffle->positions->count())
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);letter-spacing:0.08em;margin-bottom:10px;">PREMIOS</div>
            @foreach($activeRaffle->positions as $pos)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.03);border:1px solid var(--line);margin-bottom:6px;">
                <span style="font-weight:700;">{{ $pos->position }}° Lugar</span>
                <span style="color:var(--orange);font-weight:700;">
                    {{ $pos->prize_description }}
                    @if($pos->prize_amount) · ${{ number_format($pos->prize_amount, 0, ',', '.') }}@endif
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Mis números --}}
        <div style="font-size:11px;font-weight:700;color:var(--muted);letter-spacing:0.08em;margin-bottom:8px;">
            MIS NÚMEROS ({{ $myNumbers->count() }})
        </div>
        @if($myNumbers->count())
        <div class="number-grid">
            @foreach($myNumbers as $num)
            <div class="number-card">
                <div class="number-val">{{ str_pad($num->number, $activeRaffle->number_type === '4digits' ? 4 : strlen($num->number), '0', STR_PAD_LEFT) }}</div>
                <div class="number-lbl">NÚMERO</div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center;padding:24px;color:var(--muted);font-size:13px;">
            Aún no tenés números asignados para este sorteo
        </div>
        @endif

        @else
        <div style="text-align:center;padding:40px 0;">
            <div style="font-size:40px;margin-bottom:10px;">🎯</div>
            <div style="font-family:var(--font-display);font-size:22px;margin-bottom:6px;">NO HAY SORTEO ACTIVO</div>
            <div style="color:var(--muted);font-size:13px;">Próximamente habrá nuevos sorteos disponibles</div>
        </div>
        @endif

        {{-- Sorteo terminado con ganadores --}}
        @if($endedRaffle && $endedRaffle->hasWinners())
        <div style="border-top:1px solid var(--line);margin-top:24px;padding-top:24px;">
            <h4 style="font-family:var(--font-display);font-size:20px;margin-bottom:14px;color:var(--amber);">🏆 ÚLTIMOS GANADORES — {{ $endedRaffle->title }}</h4>
            <div class="winners-list">
                @foreach($endedRaffle->positions->whereNotNull('winner_user_id') as $pos)
                <div class="winner-row">
                    <div class="winner-pos">{{ $pos->position }}°</div>
                    <div class="winner-info">
                        <div style="font-weight:700;font-size:14px;">{{ $pos->winner->name ?? '–' }}</div>
                        <div style="font-size:11px;color:var(--muted);">Número: <strong style="font-family:var(--font-mono);color:var(--orange);">{{ str_pad($pos->winner_number, 4, '0', STR_PAD_LEFT) }}</strong></div>
                        <div style="font-size:11px;color:var(--muted);">{{ $pos->prize_description }}</div>
                    </div>
                    @if($pos->prize_amount)
                    <div style="font-family:var(--font-display);font-size:20px;color:var(--good);">${{ number_format($pos->prize_amount, 0, ',', '.') }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- TICKETS --}}
    @if($tab === 'tickets')
    <div class="fe-card">
        <h3 style="font-family:var(--font-display);font-size:22px;margin-bottom:16px;">MIS CONSULTAS</h3>
        @forelse($tickets as $ticket)
        <div class="ticket-row">
            <div>
                <div style="font-weight:700;font-size:13px;">{{ $ticket->subject }}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                    {{ $ticket->created_at->format('d/m/Y H:i') }} · {{ $ticket->messages->count() }} mensajes
                </div>
            </div>
            @php $s = $ticket->status; @endphp
            @if($s === 'open')
                <span class="status-open">● Abierto</span>
            @elseif($s === 'progress')
                <span class="status-progress">● En proceso</span>
            @else
                <span class="status-closed">✓ Cerrado</span>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:40px 0;color:var(--muted);">
            <div style="font-size:32px;margin-bottom:8px;">✉</div>
            <div>No tenés tickets registrados</div>
        </div>
        @endforelse
    </div>
    @endif

    {{-- NOTIFICATIONS --}}
    @if($tab === 'notifications')
    <div class="fe-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--font-display);font-size:22px;">NOTIFICACIONES</h3>
            @if($unreadCount > 0)
            <button wire:click="markAllRead" class="fe-btn-ghost" style="font-size:11px;padding:6px 14px;">Marcar todas como leídas</button>
            @endif
        </div>
        @forelse($notifications as $notif)
        <div class="notif-item {{ $notif->read_at ? '' : 'unread' }}" wire:click="markNotificationRead({{ $notif->id }})">
            <div class="notif-dot {{ $notif->read_at ? 'read' : '' }}"></div>
            <div style="flex:1;">
                <div style="font-weight:700;font-size:13px;margin-bottom:2px;">{{ $notif->title }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ $notif->message }}</div>
                <div style="font-size:10px;color:var(--muted-2);margin-top:4px;">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px 0;color:var(--muted);">
            <div style="font-size:32px;margin-bottom:8px;">🔔</div>
            <div>No tenés notificaciones</div>
        </div>
        @endforelse
    </div>
    @endif

    {{-- PASSWORD --}}
    @if($tab === 'password')
    <div class="fe-card" style="max-width:440px;">
        <h3 style="font-family:var(--font-display);font-size:22px;margin-bottom:20px;">CAMBIAR CONTRASEÑA</h3>
        <form wire:submit.prevent="changePassword">
            <div class="form-field">
                <label>CONTRASEÑA ACTUAL</label>
                <input type="password" wire:model="current_password" class="form-input" placeholder="••••••••">
                @error('current_password')<div class="fe-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label>NUEVA CONTRASEÑA</label>
                <input type="password" wire:model="new_password" class="form-input" placeholder="Mínimo 8 caracteres">
                @error('new_password')<div class="fe-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label>CONFIRMAR NUEVA CONTRASEÑA</label>
                <input type="password" wire:model="new_password_confirmation" class="form-input" placeholder="Repetir contraseña">
            </div>
            <button type="submit" class="fe-btn-primary" style="width:100%;margin-top:8px;">Actualizar contraseña</button>
        </form>
    </div>
    @endif
</div>
