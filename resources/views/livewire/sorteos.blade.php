<div class="page-container">
    @livewire(\App\Livewire\Components\PageHeader::class, [
        'title' => 'SORTEOS',
        'subtitle' => 'Gestión de sorteos, números y ganadores',
        'buttonText' => 'Nuevo Sorteo',
        'buttonAction' => 'openCreate',
    ])

    <style>
        .sorteos-layout { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
        .sort-card { background: linear-gradient(180deg,#170b0b,#0f0707); border: 1px solid var(--line); border-radius: 20px; padding: 22px; }
        .raffle-item { padding: 14px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
        .raffle-item:hover { background: rgba(255,255,255,0.06); border-color: var(--line-2); }
        .raffle-item.selected { background: rgba(255,106,26,0.07); border-color: var(--orange); }
        .raffle-name { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
        .raffle-meta { font-size: 11px; color: var(--muted); }
        .badge-upcoming { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,179,71,0.12); color: var(--warn); }
        .badge-active-r { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); }
        .badge-ended { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); }
        .detail-section { margin-bottom: 22px; }
        .detail-section-title { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: 0.08em; margin-bottom: 10px; text-transform: uppercase; }
        .pos-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); margin-bottom: 6px; }
        .assign-controls { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .qty-btn { height: 32px; padding: 0 12px; border-radius: 8px; font-size: 12px; font-weight: 700; background: rgba(255,106,26,0.1); color: var(--orange); border: 1px solid var(--orange); cursor: pointer; transition: all 0.2s; }
        .qty-btn:hover { background: rgba(255,106,26,0.2); }
        .qty-btn.selected-qty { background: var(--orange); color: #190702; }
        .number-chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; background: rgba(255,106,26,0.08); border: 1px solid var(--line-warm); font-family: var(--font-mono); font-size: 13px; color: var(--orange); margin: 3px; }
        .number-chip button { background: none; border: none; color: var(--muted-2); cursor: pointer; font-size: 11px; padding: 0; line-height: 1; }
        .number-chip button:hover { color: #ff4757; }
        .user-numbers-group { margin-bottom: 12px; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); }
        .user-numbers-name { font-weight: 700; font-size: 13px; margin-bottom: 6px; }
        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .modal-box { background: #1a0909; border: 1px solid var(--line-warm); border-radius: 20px; padding: 28px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-family: var(--font-display); font-size: 24px; margin-bottom: 20px; }
        .mf { margin-bottom: 14px; }
        .mf label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: 0.06em; margin-bottom: 5px; }
        .mf input, .mf select, .mf textarea { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--line-2); border-radius: 8px; padding: 9px 12px; color: #fff; font-size: 13px; font-family: var(--font-body); outline: none; }
        .mf input:focus, .mf select:focus { border-color: var(--orange); }
        .mf select option { background: #1a0909; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .fe-err { color: #ff4757; font-size: 11px; margin-top: 3px; }
        .pos-input-row { display: grid; grid-template-columns: 30px 1fr 120px 30px; gap: 8px; align-items: center; margin-bottom: 8px; }
        .winner-input-row { margin-bottom: 14px; padding: 14px; border-radius: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); }
    </style>

    @if (session()->has('message'))
        <div style="background:rgba(37,196,107,0.12);border:1px solid var(--good);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:var(--good);font-size:13px;font-weight:700;">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background:rgba(255,71,87,0.12);border:1px solid #ff4757;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#ff4757;font-size:13px;font-weight:700;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <div style="font-size:11px;color:var(--muted);letter-spacing:0.12em;font-weight:700;">OPERACIÓN</div>
            <div style="font-family:var(--font-display);font-size:32px;margin-top:2px;">Sorteos</div>
        </div>
        <button wire:click="openCreate" class="btn-primary" style="height:36px;padding:0 18px;font-size:12px;">+ Nuevo sorteo</button>
    </div>

    <div class="sorteos-layout">
        {{-- LEFT: Raffle list --}}
        <div class="sort-card">
            <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
                <input wire:model.live="search" type="text" placeholder="Buscar..." style="flex:1;min-width:120px;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:7px 12px;color:#fff;font-size:12px;outline:none;">
                <select wire:model.live="filterStatus" style="background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:8px;padding:7px 10px;color:#fff;font-size:11px;outline:none;">
                    <option value="all">Todos</option>
                    <option value="upcoming">Próximos</option>
                    <option value="active">Activos</option>
                    <option value="ended">Terminados</option>
                </select>
            </div>

            @forelse($raffles as $raffle)
            <div class="raffle-item {{ $selectedRaffleId == $raffle->id ? 'selected' : '' }}" wire:click="selectRaffle({{ $raffle->id }})">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;">
                    <div class="raffle-name">{{ $raffle->title }}</div>
                    @if($raffle->status === 'upcoming')
                        <span class="badge-upcoming">Próximo</span>
                    @elseif($raffle->status === 'active')
                        <span class="badge-active-r">● Activo</span>
                    @else
                        <span class="badge-ended">Terminado</span>
                    @endif
                </div>
                <div class="raffle-meta">
                    {{ $raffle->start_date->format('d/m/Y') }} → {{ $raffle->end_date->format('d/m/Y') }}
                    · {{ $raffle->numbers_count }} números
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--muted);font-size:13px;">No hay sorteos</div>
            @endforelse
        </div>

        {{-- RIGHT: Detail panel --}}
        <div>
            @if($selectedRaffle)
            <div class="sort-card" style="margin-bottom:16px;">
                {{-- Header --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                    <div>
                        <div style="font-family:var(--font-display);font-size:26px;letter-spacing:0.02em;">{{ $selectedRaffle->title }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            {{ $selectedRaffle->start_date->format('d/m/Y') }} → {{ $selectedRaffle->end_date->format('d/m/Y') }}
                            · Números: {{ $selectedRaffle->number_type === '4digits' ? '4 dígitos (hasta 9999)' : 'Infinitos' }}
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <button wire:click="openEdit({{ $selectedRaffle->id }})" class="btn-ghost" style="height:30px;padding:0 12px;font-size:11px;">Editar</button>
                        <button wire:click="openWinners({{ $selectedRaffle->id }})" class="btn-primary" style="height:30px;padding:0 12px;font-size:11px;">🏆 Ganadores</button>
                    </div>
                </div>

                {{-- Quick status change --}}
                <div style="display:flex;gap:6px;margin-bottom:18px;">
                    <button wire:click="updateStatus({{ $selectedRaffle->id }},'upcoming')" class="{{ $selectedRaffle->status === 'upcoming' ? 'btn-primary' : 'btn-ghost' }}" style="height:28px;padding:0 12px;font-size:11px;">Próximo</button>
                    <button wire:click="updateStatus({{ $selectedRaffle->id }},'active')" class="{{ $selectedRaffle->status === 'active' ? 'btn-primary' : 'btn-ghost' }}" style="height:28px;padding:0 12px;font-size:11px;">Activo</button>
                    <button wire:click="updateStatus({{ $selectedRaffle->id }},'ended')" class="{{ $selectedRaffle->status === 'ended' ? 'btn-primary' : 'btn-ghost' }}" style="height:28px;padding:0 12px;font-size:11px;">Terminado</button>
                </div>

                {{-- Positions --}}
                @if($selectedRaffle->positions->count())
                <div class="detail-section">
                    <div class="detail-section-title">Premios por puesto</div>
                    @foreach($selectedRaffle->positions as $pos)
                    <div class="pos-row">
                        <div>
                            <span style="font-weight:700;">{{ $pos->position }}°</span>
                            <span style="margin-left:8px;font-size:13px;">{{ $pos->prize_description }}</span>
                            @if($pos->prize_amount)
                            <span style="margin-left:8px;color:var(--orange);font-weight:700;">${{ number_format($pos->prize_amount, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        @if($pos->winner_user_id)
                        <div style="font-size:11px;text-align:right;">
                            <div style="font-weight:700;color:var(--good);">🏆 {{ $pos->winner->name ?? '?' }}</div>
                            <div style="font-family:var(--font-mono);color:var(--orange);">{{ str_pad($pos->winner_number, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        @else
                        <span style="font-size:11px;color:var(--muted);">Sin ganador</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Assign numbers --}}
                <div class="detail-section">
                    <div class="detail-section-title">Asignar números</div>
                    <div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap;align-items:flex-end;">
                        <div style="flex:1;min-width:180px;">
                            <div style="font-size:10px;color:var(--muted);font-weight:700;margin-bottom:4px;">USUARIO</div>
                            <select wire:model="assignUserId" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:8px;padding:8px 10px;color:#fff;font-size:12px;outline:none;">
                                <option value="">— Seleccionar —</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div style="font-size:10px;color:var(--muted);font-weight:700;margin-bottom:4px;">CANTIDAD</div>
                            <div class="assign-controls">
                                @foreach([1,3,5,10,20] as $qty)
                                <button wire:click="$set('assignCount',{{ $qty }})" class="qty-btn {{ $assignCount == $qty ? 'selected-qty' : '' }}">
                                    +{{ $qty }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        <button wire:click="assignNumbers" class="btn-primary" style="height:36px;padding:0 16px;font-size:12px;align-self:flex-end;">Asignar</button>
                    </div>
                    <div style="font-size:11px;color:var(--muted);">
                        Próximo número disponible: <strong style="color:var(--orange);font-family:var(--font-mono);">
                            {{ str_pad($selectedRaffle->next_number, $selectedRaffle->number_type === '4digits' ? 4 : strlen($selectedRaffle->next_number), '0', STR_PAD_LEFT) }}
                        </strong>
                        · Total asignados: <strong>{{ $selectedRaffle->numbers->count() }}</strong>
                    </div>
                </div>

                {{-- Numbers list --}}
                <div class="detail-section">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div class="detail-section-title" style="margin:0;">Números asignados</div>
                        <input wire:model.live="numbersSearch" type="text" placeholder="Buscar usuario..." style="background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:6px 12px;color:#fff;font-size:11px;outline:none;width:200px;">
                    </div>
                    @php
                        $byUser = $selectedRaffle->numbers->groupBy('user_id');
                    @endphp
                    @forelse($byUser as $userId => $nums)
                    @php $firstNum = $nums->first(); @endphp
                    <div class="user-numbers-group">
                        <div class="user-numbers-name">{{ $firstNum->user->name ?? 'Usuario #'.$userId }}</div>
                        <div style="display:flex;flex-wrap:wrap;">
                            @foreach($nums as $num)
                            <span class="number-chip">
                                {{ str_pad($num->number, $selectedRaffle->number_type === '4digits' ? 4 : strlen($num->number), '0', STR_PAD_LEFT) }}
                                <button wire:click="removeNumber({{ $num->id }})" title="Eliminar">✕</button>
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:24px;color:var(--muted);font-size:13px;">No hay números asignados aún</div>
                    @endforelse
                </div>
            </div>
            @else
            <div class="sort-card" style="display:flex;align-items:center;justify-content:center;padding:60px;flex-direction:column;gap:12px;">
                <div style="font-size:40px;">🎯</div>
                <div style="font-family:var(--font-display);font-size:20px;color:var(--muted);">Seleccioná un sorteo</div>
                <div style="font-size:13px;color:var(--muted-2);">Elegí un sorteo de la lista para gestionar sus números y ganadores</div>
            </div>
            @endif
        </div>
    </div>

    {{-- CREATE/EDIT MODAL --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal-box">
            <div class="modal-title">{{ $editingRaffle ? 'Editar Sorteo' : 'Nuevo Sorteo' }}</div>
            <form wire:submit.prevent="save">
                <div class="mf">
                    <label>TÍTULO</label>
                    <input type="text" wire:model="title" placeholder="Ej: Gran Sorteo Mayo 2026">
                    @error('title')<div class="fe-err">{{ $message }}</div>@enderror
                </div>
                <div class="mf">
                    <label>DESCRIPCIÓN</label>
                    <textarea wire:model="description" rows="2" placeholder="Descripción del sorteo..."></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="mf">
                        <label>FECHA INICIO</label>
                        <input type="date" wire:model="start_date">
                    </div>
                    <div class="mf">
                        <label>FECHA FIN</label>
                        <input type="date" wire:model="end_date">
                        @error('end_date')<div class="fe-err">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="mf">
                        <label>ESTADO</label>
                        <select wire:model="status">
                            <option value="upcoming">Próximo</option>
                            <option value="active">Activo</option>
                            <option value="ended">Terminado</option>
                        </select>
                    </div>
                    <div class="mf">
                        <label>TIPO DE NÚMERO</label>
                        <select wire:model="number_type">
                            <option value="infinite">Infinitos (1, 2, 3...)</option>
                            <option value="4digits">4 dígitos (0001–9999)</option>
                        </select>
                    </div>
                </div>

                {{-- Positions --}}
                <div style="margin-top:4px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <label style="font-size:11px;font-weight:700;color:var(--muted);letter-spacing:0.06em;">PUESTOS / PREMIOS</label>
                        <button type="button" wire:click="addPosition" class="btn-ghost" style="height:24px;padding:0 10px;font-size:10px;">+ Agregar puesto</button>
                    </div>
                    @foreach($positions as $i => $pos)
                    <div class="pos-input-row">
                        <div style="font-family:var(--font-display);font-size:20px;color:var(--orange);text-align:center;">{{ $pos['position'] }}</div>
                        <input type="text" wire:model="positions.{{ $i }}.prize_description" placeholder="Descripción del premio">
                        <input type="number" wire:model="positions.{{ $i }}.prize_amount" placeholder="Monto $" min="0" step="0.01">
                        <button type="button" wire:click="removePosition({{ $i }})" style="background:none;border:none;color:#ff4757;cursor:pointer;font-size:16px;">✕</button>
                    </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary" style="padding:10px 22px;font-size:13px;">
                        {{ $editingRaffle ? 'Actualizar' : 'Crear sorteo' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- WINNERS MODAL --}}
    @if($showWinnersModal && $selectedRaffle)
    <div class="modal-overlay" wire:click.self="closeWinners">
        <div class="modal-box">
            <div class="modal-title">🏆 Registrar Ganadores</div>
            <p style="font-size:13px;color:var(--muted);margin-bottom:20px;">{{ $selectedRaffle->title }}</p>
            <form wire:submit.prevent="saveWinners">
                @foreach($selectedRaffle->positions as $pos)
                <div class="winner-input-row">
                    <div style="font-weight:700;font-size:13px;margin-bottom:10px;">
                        <span style="color:var(--orange);">{{ $pos->position }}°</span> {{ $pos->prize_description }}
                        @if($pos->prize_amount) · <span style="color:var(--good);">${{ number_format($pos->prize_amount, 0, ',', '.') }}</span>@endif
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 140px;gap:10px;">
                        <div>
                            <div style="font-size:10px;color:var(--muted);font-weight:700;margin-bottom:4px;">USUARIO GANADOR</div>
                            <select wire:model="winners.{{ $pos->id }}.user_id" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:8px;padding:8px 10px;color:#fff;font-size:12px;outline:none;">
                                <option value="">— Sin ganador —</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div style="font-size:10px;color:var(--muted);font-weight:700;margin-bottom:4px;">NÚMERO GANADOR</div>
                            <input type="number" wire:model="winners.{{ $pos->id }}.number" placeholder="Ej: 1234" min="1" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:8px;padding:8px 10px;color:#fff;font-size:12px;outline:none;">
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="modal-footer">
                    <button type="button" wire:click="closeWinners" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary" style="padding:10px 22px;font-size:13px;">Guardar ganadores</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
