@push('styles')
<style>
    .account-page { padding:42px 0 0; }
    .account-head { display:flex; align-items:end; justify-content:space-between; gap:18px; flex-wrap:wrap; margin-bottom:22px; }
    .account-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .account-title { font-family:var(--font-display); font-size:48px; line-height:.9; margin:7px 0 0; letter-spacing:.02em; }
    .account-title span { color:var(--orange); }
    .account-grid { display:grid; grid-template-columns:1fr; gap:18px; }
    .account-summary { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; margin-bottom:18px; }
    .account-stat { border:1px solid rgba(255,255,255,.08); border-radius:10px; background:rgba(255,255,255,.035); padding:14px; }
    .account-stat strong { display:block; font-family:var(--font-display); font-size:30px; line-height:1; color:var(--orange); }
    .account-stat span { display:block; margin-top:4px; color:var(--muted); font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .account-card { border:1px solid rgba(255,255,255,.09); border-radius:12px; background:linear-gradient(180deg,#150807,#080302); padding:24px; box-shadow:0 18px 50px rgba(0,0,0,.28); }
    .account-card h2 { font-family:var(--font-display); font-size:28px; line-height:1; margin:0 0 16px; letter-spacing:.02em; }
    .account-section-title { font-family:var(--font-display); font-size:22px; line-height:1; margin:0 0 14px; letter-spacing:.02em; color:var(--orange); }
    .account-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .account-field.full { grid-column:1 / -1; }
    .account-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .account-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .account-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .account-error { color:#ff8a8a; font-size:11px; font-weight:800; margin-top:5px; }
    .account-flash { margin-bottom:16px; border:1px solid rgba(37,196,107,.38); border-radius:10px; background:rgba(37,196,107,.1); color:#adffd0; padding:12px 14px; font-size:13px; font-weight:800; }
    .account-flash-ticket { margin-bottom:16px; border:1px solid rgba(255,170,80,.38); border-radius:10px; background:rgba(255,170,80,.1); color:#ffd0a0; padding:12px 14px; font-size:13px; font-weight:800; }

    /* Tabs */
    .account-tabs { display:flex; gap:4px; margin-bottom:20px; flex-wrap:wrap; }
    .account-tab { padding:10px 20px; border-radius:999px; font-size:13px; font-weight:800; cursor:pointer; background:rgba(255,255,255,.04); color:var(--muted); border:1px solid transparent; transition:all .2s; }
    .account-tab:hover { color:var(--orange); background:rgba(255,106,26,.08); }
    .account-tab.active { color:#fff; background:rgba(255,106,26,.18); border-color:rgba(255,106,26,.3); }
    .account-tab-alert { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; margin-left:6px; border-radius:999px; background:var(--orange); color:#190702; font-size:10px; }
    .account-empty { border:1px dashed rgba(255,255,255,.14); border-radius:10px; padding:26px; color:var(--muted); text-align:center; font-size:13px; font-weight:800; }
    .account-muted { color:var(--muted); font-size:12px; line-height:1.45; }
    .avatar-panel { grid-column:1 / -1; }
    .avatar-panel .form-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }

    /* Numbers table */
    .account-table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .numbers-table { width:100%; border-collapse:collapse; }
    .numbers-table th { text-align:left; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; padding:8px 12px; border-bottom:1px solid rgba(255,255,255,.08); }
    .numbers-table td { padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.04); font-size:13px; }
    .numbers-table strong { color:var(--orange); font-family:var(--font-mono); font-size:16px; }
    .numbers-table .line-name { color:var(--muted); font-weight:700; }

    /* Bonuses */
    .bonus-item { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border:1px solid rgba(255,255,255,.06); border-radius:8px; margin-bottom:8px; }
    .bonus-item-info { flex:1; }
    .bonus-item-title { font-weight:800; font-size:14px; margin-bottom:2px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:10px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .badge-available { background:rgba(37,196,107,.15); color:#25c46b; }
    .badge-used { background:rgba(255,255,255,.08); color:var(--muted); }
    .badge-expired { background:rgba(255,71,87,.12); color:#ff8a8a; }

    /* Ticket form */
    .ticket-select { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .ticket-select:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .ticket-textarea { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; resize:vertical; min-height:100px; }
    .ticket-textarea:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }

    /* Tickets list */
    .ticket-item { border:1px solid rgba(255,255,255,.06); border-radius:8px; padding:14px; margin-bottom:8px; }
    .ticket-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
    .ticket-code { font-family:var(--font-mono); font-weight:900; font-size:13px; color:var(--orange); }
    .ticket-status { font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; padding:3px 10px; border-radius:999px; }
    .ticket-status-open { background:rgba(255,106,26,.15); color:var(--orange); }
    .ticket-status-progress { background:rgba(255,170,80,.15); color:#ffaa50; }
    .ticket-status-closed { background:rgba(37,196,107,.15); color:#25c46b; }
    .account-messages { margin-top:10px; display:grid; gap:8px; }
    .account-message { border-left:2px solid rgba(255,106,26,.5); padding:8px 10px; background:rgba(255,255,255,.03); border-radius:8px; color:var(--muted); font-size:12px; }

    @media (max-width: 640px) {
        .account-page { padding-top:30px; }
        .account-title { font-size:38px; }
        .account-card { padding:18px; }
        .account-card h2 { font-size:24px; }
        .account-tabs { flex-wrap:nowrap; overflow-x:auto; padding-bottom:8px; -webkit-overflow-scrolling:touch; }
        .account-tab { flex:0 0 auto; padding:9px 14px; }
        .account-form-grid { grid-template-columns:1fr; }
        .account-summary { grid-template-columns:1fr 1fr; }
        .account-head { align-items:flex-start; }
        .account-muted { overflow-wrap:anywhere; }
        .bonus-item { align-items:flex-start; flex-direction:column; }
        .bonus-item .badge { align-self:flex-start; }
        .ticket-header { align-items:flex-start; flex-direction:column; gap:8px; }
        .account-card .fe-btn { width:100%; }
        .numbers-table th, .numbers-table td { padding:6px 8px; font-size:12px; }
        .numbers-table { min-width:640px; }
    }
    @media (max-width: 420px) {
        .account-summary { grid-template-columns:1fr; }
    }
</style>
@endpush

@php
    $user = auth()->user();
    $statusLabels = ['active' => 'Disponible', 'used' => 'Usado', 'expired' => 'Vencido'];
    $statusClasses = ['active' => 'badge-available', 'used' => 'badge-used', 'expired' => 'badge-expired'];
    $ticketStatusLabels = ['open' => 'Abierto', 'progress' => 'En proceso', 'closed' => 'Cerrado'];
    $categoryLabels = ['juego' => 'Juego', 'bono' => 'Bono', 'sorteo' => 'Sorteo', 'atencion' => 'Atencion', 'otro' => 'Otro'];
@endphp

<section class="account-page">
    <div class="fe-shell">
        <div class="account-head">
            <div>
                <div class="account-kicker">Mi cuenta</div>
                <h1 class="account-title">Hola, <span>{{ $user->name }}</span></h1>
            </div>
            <div class="account-muted">{{ $user->email }}</div>
        </div>

        <div class="account-summary">
            <div class="account-stat"><strong>{{ $activeNumbersCount }}</strong><span>Numeros activos</span></div>
            <div class="account-stat"><strong>{{ $recentBonuses->count() }}</strong><span>Bonos semana</span></div>
            <div class="account-stat"><strong>{{ $myTickets->count() }}</strong><span>Tickets recientes</span></div>
            <div class="account-stat"><strong>{{ $unreadNotificationsCount }}</strong><span>Alertas nuevas</span></div>
        </div>

        @if (session()->has('client_message'))
            <div class="account-flash">{{ session('client_message') }}</div>
        @endif
        @if (session()->has('ticket_success'))
            <div class="account-flash-ticket">{{ session('ticket_success') }}</div>
        @endif

        <div class="account-tabs">
            <button type="button" wire:click="setTab('perfil')" class="account-tab {{ $activeTab === 'perfil' ? 'active' : '' }}">Perfil</button>
            <button type="button" wire:click="setTab('password')" class="account-tab {{ $activeTab === 'password' ? 'active' : '' }}">Contrasena</button>
            <button type="button" wire:click="setTab('tickets')" class="account-tab {{ $activeTab === 'tickets' ? 'active' : '' }}">Tickets <span class="account-tab-count">{{ $allTicketsCount ?? 0 }}</span></button>
            <button type="button" wire:click="setTab('sorteo')" class="account-tab {{ $activeTab === 'sorteo' ? 'active' : '' }}">Mi sorteo</button>
            <button type="button" wire:click="setTab('bonos')" class="account-tab {{ $activeTab === 'bonos' ? 'active' : '' }}">Bonos semana</button>
            <button type="button" wire:click="setTab('todos_bonos')" class="account-tab {{ $activeTab === 'todos_bonos' ? 'active' : '' }}">Todos los bonos <span class="account-tab-count">{{ $allBonusesCount ?? 0 }}</span></button>
            <button type="button" wire:click="setTab('notificaciones')" class="account-tab {{ $activeTab === 'notificaciones' ? 'active' : '' }}">Notificaciones @if($unreadNotificationsCount > 0)<span class="account-tab-alert">{{ $unreadNotificationsCount }}</span>@endif</button>
        </div>
        <style>
            .account-tab-count { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; margin-left:6px; border-radius:999px; background:rgba(255,255,255,.15); color:var(--muted); font-size:10px; }
            .account-filters { display:flex; gap:6px; flex-wrap:wrap; }
            .account-filter-btn { padding:6px 12px; border-radius:999px; font-size:11px; font-weight:800; background:rgba(255,255,255,.05); color:var(--muted); border:1px solid transparent; cursor:pointer; }
            .account-filter-btn:hover { background:rgba(255,106,26,.1); color:var(--orange); }
            .account-filter-btn.active { background:rgba(255,106,26,.2); color:var(--orange); border-color:rgba(255,106,26,.3); }
        </style>

        <div class="account-grid">
            @if($activeTab === 'perfil')
                <div class="account-card">
                    <h2>Datos personales</h2>
                    <form wire:submit.prevent="saveProfile">
                        <div class="account-form-grid">
                            <div class="account-field">
                                <label class="account-label" for="name">Nombre</label>
                                <input id="name" class="account-input" type="text" wire:model.defer="name">
                                @error('name') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="account-field">
                                <label class="account-label" for="apellido">Apellido</label>
                                <input id="apellido" class="account-input" type="text" wire:model.defer="apellido">
                                @error('apellido') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="account-field">
                                <label class="account-label" for="username">Nombre de cliente</label>
                                <input id="username" class="account-input" type="text" wire:model.defer="username">
                                @error('username') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="account-field">
                                <label class="account-label" for="email">Email</label>
                                <input id="email" class="account-input" type="email" wire:model.defer="email">
                                @error('email') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="account-field">
                                <label class="account-label" for="phone">Celular</label>
                                <input id="phone" class="account-input" type="text" wire:model.defer="phone">
                                @error('phone') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="account-field">
                                <label class="account-label" for="preferred_line_id">Linea preferida</label>
                                <select id="preferred_line_id" class="ticket-select" wire:model.defer="preferred_line_id">
                                    <option value="0">Sin seleccionar</option>
                                    @foreach($availableLines as $line)
                                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                                    @endforeach
                                </select>
                                @error('preferred_line_id') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="account-field full">
                                <label class="account-label" for="contact">Contacto extra (WhatsApp, Telegram, etc)</label>
                                <input id="contact" class="account-input" type="text" wire:model.defer="contact" placeholder="Ej: +54 9 11 1234-5678">
                                @error('contact') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="avatar-panel">
                                <x-avatar-library label="Foto de perfil" model="avatar" :selected="$avatar" />
                                @error('avatar') <div class="account-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <button type="submit" class="fe-btn primary" style="margin-top:16px;">Guardar cambios</button>
                    </form>
                </div>
            @endif

            @if($activeTab === 'password')
                <div class="account-card">
                    <h2>Cambiar contrasena</h2>
                    <form wire:submit.prevent="savePassword" class="account-form-grid">
                        <div class="account-field full">
                            <label class="account-label" for="current_password">Contrasena actual</label>
                            <input id="current_password" class="account-input" type="password" wire:model.defer="current_password" autocomplete="current-password">
                            @error('current_password') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field">
                            <label class="account-label" for="password">Nueva contrasena</label>
                            <input id="password" class="account-input" type="password" wire:model.defer="password" autocomplete="new-password">
                            @error('password') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field">
                            <label class="account-label" for="password_confirmation">Confirmar contrasena</label>
                            <input id="password_confirmation" class="account-input" type="password" wire:model.defer="password_confirmation" autocomplete="new-password">
                            @error('password_confirmation') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field full">
                            <button type="submit" class="fe-btn primary">Actualizar contrasena</button>
                        </div>
                    </form>
                </div>
            @endif

            @if($activeTab === 'tickets')
                @if($showTicketForm)
                <div class="account-card">
                    <h2>Nuevo ticket</h2>
                    <form wire:submit.prevent="createTicket" class="account-form-grid">
                        <div class="account-field">
                            <label class="account-label" for="ticket_subject">Asunto</label>
                            <input id="ticket_subject" class="account-input" type="text" wire:model.defer="ticket_subject">
                            @error('ticket_subject') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field">
                            <label class="account-label" for="ticket_category">Categoria</label>
                            <select id="ticket_category" class="ticket-select" wire:model.defer="ticket_category">
                                <option value="">Seleccionar</option>
                                @foreach($categoryLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('ticket_category') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field full">
                            <label class="account-label" for="ticket_line_id">Linea</label>
                            <select id="ticket_line_id" class="ticket-select" wire:model.defer="ticket_line_id">
                                <option value="0">No necesario</option>
                                @foreach($availableLines as $line)
                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                @endforeach
                            </select>
                            @error('ticket_line_id') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field full">
                            <label class="account-label" for="ticket_description">Descripcion</label>
                            <textarea id="ticket_description" class="ticket-textarea" wire:model.defer="ticket_description"></textarea>
                            @error('ticket_description') <div class="account-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="account-field full" style="display:flex;gap:10px;">
                            <button type="submit" class="fe-btn primary" wire:loading.attr="disabled" style="flex:1;">
                                <span wire:loading.remove wire:target="createTicket">Enviar ticket</span>
                                <span wire:loading wire:target="createTicket">Enviando...</span>
                            </button>
                            <button type="button" class="fe-btn ghost" wire:click="$set('showTicketForm', false)" style="flex:0 0 auto;">Cancelar</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="account-card">
                    <button type="button" class="fe-btn primary" wire:click="showTicketForm" style="width:100%;">Crear nuevo ticket</button>
                </div>
                @endif

                <div class="account-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
                        <h2 style="margin:0;">Mis Tickets</h2>
                        <div class="account-filters">
                            <button type="button" wire:click="setTicketFilter('all')" class="account-filter-btn {{ $ticketFilter === 'all' ? 'active' : '' }}">Todos</button>
                            <button type="button" wire:click="setTicketFilter('open')" class="account-filter-btn {{ $ticketFilter === 'open' ? 'active' : '' }}">Abiertos</button>
                            <button type="button" wire:click="setTicketFilter('progress')" class="account-filter-btn {{ $ticketFilter === 'progress' ? 'active' : '' }}">En proceso</button>
                            <button type="button" wire:click="setTicketFilter('closed')" class="account-filter-btn {{ $ticketFilter === 'closed' ? 'active' : '' }}">Cerrados</button>
                        </div>
                    </div>
                    <style>
                        .account-filters { display:flex; gap:6px; flex-wrap:wrap; }
                        .account-filter-btn { padding:6px 12px; border-radius:999px; font-size:11px; font-weight:800; background:rgba(255,255,255,.05); color:var(--muted); border:1px solid transparent; cursor:pointer; }
                        .account-filter-btn:hover { background:rgba(255,106,26,.1); color:var(--orange); }
                        .account-filter-btn.active { background:rgba(255,106,26,.2); color:var(--orange); border-color:rgba(255,106,26,.3); }
                    </style>
                    @forelse($myTickets as $ticket)
                        <article class="ticket-item">
                            <div class="ticket-header">
                                <span class="ticket-code">{{ $ticket->tracking_code }}</span>
                                <span class="ticket-status ticket-status-{{ $ticket->status }}">{{ $ticketStatusLabels[$ticket->status] ?? $ticket->status }}</span>
                            </div>
                            <strong>{{ $ticket->subject }}</strong>
                            <p class="account-muted">{{ $categoryLabels[$ticket->category] ?? 'Sin categoria' }} @if($ticket->line) - {{ $ticket->line->name }} @endif - {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            <div class="account-messages">
                                @foreach($ticket->messages as $message)
                                    <div class="account-message" style="{{ $message->user_id === auth()->id() ? 'background:rgba(255,106,26,0.1);border-left:3px solid var(--orange);' : '' }}">
                                        <small style="opacity:0.6;display:block;margin-bottom:4px;">{{ $message->user_id === auth()->id() ? 'Vos' : ($message->agent?->name ?? 'Soporte') }} - {{ $message->created_at->format('d/m H:i') }}</small>
                                        {{ $message->message }}
                                    </div>
                                @endforeach
                            </div>
                            @if($ticket->status !== 'closed')
                                <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                                    @if($replyTicketId === $ticket->id)
                                        <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                                            <textarea wire:model.defer="replyMessage" class="account-input" placeholder="Escribe tu respuesta..." rows="2" style="resize:vertical;min-height:60px;"></textarea>
                                            <div style="display:flex;gap:8px;">
                                                <button type="button" wire:click="sendReply" wire:loading.attr="disabled" class="fe-btn primary" style="height:32px;padding:0 16px;font-size:12px;">Enviar</button>
                                                <button type="button" wire:click="cancelReply" class="fe-btn ghost" style="height:32px;padding:0 16px;font-size:12px;">Cancelar</button>
                                            </div>
                                        </div>
                                    @else
                                        <button type="button" wire:click="openReplyForm({{ $ticket->id }})" class="fe-btn primary" style="height:32px;padding:0 16px;font-size:11px;">Responder</button>
                                        <button type="button" wire:click="closeTicket({{ $ticket->id }})" class="fe-btn ghost" style="height:32px;padding:0 12px;font-size:11px;">Cerrar</button>
                                    @endif
                                </div>
                            @else
                                <button type="button" wire:click="reopenTicket({{ $ticket->id }})" class="fe-btn primary" style="margin-top:10px;height:32px;padding:0 16px;font-size:11px;">Reabrir ticket</button>
                            @endif
                        </article>
                    @empty
                        <div class="account-empty">No hay tickets con ese filtro.</div>
                    @endforelse
                </div>
            @endif

            @if($activeTab === 'sorteo')
                <div class="account-card">
                    <h2>Mis numeros otorgados</h2>
                    @if($myNumbers->count())
                        <div class="account-table-scroll">
                            <table class="numbers-table">
                                <thead>
                                    <tr>
                                        <th>Linea otorgadora</th>
                                        <th>Numero</th>
                                        <th>Sorteo</th>
                                        <th>Horario otorgado</th>
                                        <th>Info</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myNumbers as $number)
                                        <tr>
                                            <td class="line-name">{{ $number->line?->name ?? 'Sin linea' }}</td>
                                            <td><strong>{{ str_pad((string) $number->number, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td>{{ $number->raffle?->title ?? 'Sorteo' }}</td>
                                            <td>{{ $number->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($number->raffle)
                                                    <a href="{{ route('frontend.raffles.show', $number->raffle->id) }}" wire:navigate class="fe-btn ghost" style="height:32px;padding:0 12px;">Ver sorteo</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="account-empty">Aun no tenes numeros otorgados.</div>
                    @endif
                </div>
            @endif

            @if($activeTab === 'bonos')
                <div class="account-card">
                    <h2>Bonos reclamados en la ultima semana</h2>
                    @forelse($recentBonuses as $assignment)
                        @php
                            $bonus = $assignment->bonus;
                            $status = $assignment->status;
                            if ($status === 'active' && $bonus?->end_date?->isPast()) {
                                $status = 'expired';
                            }
                        @endphp
                        <article class="bonus-item">
                            <div class="bonus-item-info">
                                <div class="bonus-item-title">{{ $bonus?->title ?? 'Bono eliminado' }}</div>
                                <div class="account-muted">
                                    {{ $bonus?->line?->name ?? 'Sin linea' }} - reclamado {{ $assignment->created_at->format('d/m/Y H:i') }}
                                    @if($bonus?->end_date) - vence {{ $bonus->end_date->format('d/m/Y') }} @endif
                                </div>
                            </div>
                            <span class="badge {{ $statusClasses[$status] ?? 'badge-used' }}">{{ $statusLabels[$status] ?? $status }}</span>
                        </article>
                    @empty
                        <div class="account-empty">No hay bonos reclamados en los ultimos 7 dias.</div>
                    @endforelse
                </div>
            @endif

            @if($activeTab === 'todos_bonos')
                <div class="account-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
                        <h2 style="margin:0;">Todos los bonos</h2>
                        <div class="account-filters">
                            <button type="button" wire:click="setBonusFilter('all')" class="account-filter-btn {{ $bonusFilter === 'all' ? 'active' : '' }}">Todos</button>
                            <button type="button" wire:click="setBonusFilter('active')" class="account-filter-btn {{ $bonusFilter === 'active' ? 'active' : '' }}">Disponibles</button>
                            <button type="button" wire:click="setBonusFilter('used')" class="account-filter-btn {{ $bonusFilter === 'used' ? 'active' : '' }}">Usados</button>
                            <button type="button" wire:click="setBonusFilter('expired')" class="account-filter-btn {{ $bonusFilter === 'expired' ? 'active' : '' }}">Vencidos</button>
                        </div>
                    </div>
                    @forelse($allBonuses as $assignment)
                        @php
                            $bonus = $assignment->bonus;
                            $status = $assignment->computed_status;
                        @endphp
                        <article class="bonus-item">
                            <div class="bonus-item-info">
                                <div class="bonus-item-title">{{ $bonus?->title ?? 'Bono eliminado' }}</div>
                                <div class="account-muted">
                                    {{ $bonus?->line?->name ?? 'Sin linea' }} -iado {{ $assignment->created_at->format('d/m/Y H:i') }}
                                    @if($bonus?->end_date) - vence {{ $bonus->end_date->format('d/m/Y') }} @endif
                                </div>
                            </div>
                            <span class="badge {{ $statusClasses[$status] ?? 'badge-used' }}">{{ $statusLabels[$status] ?? $status }}</span>
                        </article>
                    @empty
                        <div class="account-empty">No hay bonos con ese filtro.</div>
                    @endforelse
                </div>
            @endif

            @if($activeTab === 'notificaciones')
                <div class="account-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
                        <h2 style="margin:0;">Mis notificaciones</h2>
                        @if($unreadNotificationsCount > 0)
                            <button type="button" wire:click="markAllNotificationsRead" class="fe-btn ghost" style="height:32px;padding:0 12px;font-size:11px;">Marcar todas como leídas</button>
                        @endif
                    </div>
                    @forelse($notifications as $notification)
                        <article class="notification-item" style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;gap:12px;align-items:flex-start;">
                            <span class="notification-dot type-{{ $notification->type }}" style="width:8px;height:8px;border-radius:50%;background:{{ $notification->type === 'success' ? 'var(--green)' : ($notification->type === 'warning' ? 'var(--orange)' : 'var(--blue)') }};flex-shrink:0;margin-top:6px;"></span>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;margin-bottom:4px;">{{ $notification->title }}</div>
                                <div style="font-size:13px;opacity:0.7;margin-bottom:4px;">{{ $notification->message }}</div>
                                <div style="font-size:11px;opacity:0.5;">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notification->read_at)
                                <button type="button" wire:click="markNotificationRead({{ $notification->id }})" class="fe-btn ghost" style="height:28px;padding:0 8px;font-size:10px;flex-shrink:0;">Marcar</button>
                            @endif
                        </article>
                    @empty
                        <div class="account-empty">No hay notificaciones.</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</section>
