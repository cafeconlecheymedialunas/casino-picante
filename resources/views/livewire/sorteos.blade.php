<div class="page-container">
    <x-livewire.components.page-header title="SORTEOS" subtitle="Gestión de sorteos, números y ganadores" buttonText="Nuevo Sorteo" buttonAction="openCreate" />

    <style>
        .sorteos-layout { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
        .sort-card { background: linear-gradient(180deg,#170b0b,#0f0707); border: 1px solid var(--line); border-radius: 20px; padding: 22px; }
        .raffle-item { padding: 14px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--line); margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
        .raffle-item:hover { background: rgba(255,255,255,0.06); border-color: var(--line-2); }
        .raffle-item.selected { background: rgba(255,106,26,0.07); border-color: var(--orange); }
        .raffle-name { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
        .raffle-meta { font-size: 11px; color: var(--muted); }
        .badge-active-r { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); }
        .badge-inactive { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); }
        .detail-section { margin-bottom: 22px; }
        .detail-section-title { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: 0.08em; margin-bottom: 10px; text-transform: uppercase; }
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
        .modal-box { background: #1a0909; border: 1px solid var(--line-warm); border-radius: 20px; padding: 28px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-family: var(--font-display); font-size: 24px; margin-bottom: 20px; }
        .mf { margin-bottom: 14px; }
        .mf label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: 0.06em; margin-bottom: 5px; }
        .mf input, .mf select, .mf textarea { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--line-2); border-radius: 8px; padding: 9px 12px; color: #fff; font-size: 13px; font-family: var(--font-body); outline: none; }
        .mf input:focus, .mf select:focus { border-color: var(--orange); }
        .mf select option { background: #1a0909; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .fe-err { color: #ff4757; font-size: 11px; margin-top: 3px; }

        /* Board Styles */
        .raffle-board {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 6px;
            margin-top: 15px;
            max-height: 500px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .board-slot {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .slot-free {
            background: rgba(37,196,107,0.05);
            color: var(--good);
            border-color: rgba(37,196,107,0.15);
        }
        .slot-free:hover {
            background: rgba(37,196,107,0.15);
            border-color: var(--good);
            transform: scale(1.05);
        }
        .slot-taken {
            background: rgba(255,106,26,0.15);
            color: var(--orange);
            border-color: var(--orange);
        }
        .slot-taken:hover {
            background: rgba(255,106,26,0.3);
        }
        .tab-btn {
            height: 32px;
            padding: 0 15px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 8px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--line);
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn.active {
            background: var(--orange);
            color: #190702;
            border-color: var(--orange);
        }

        /* Tooltip board */
        .board-slot { position: relative; }
        .slot-info {
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: #1a0909;
            border: 1px solid var(--orange);
            padding: 8px 12px;
            border-radius: 8px;
            z-index: 100;
            width: max-content;
            min-width: 120px;
            display: none;
            pointer-events: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.8);
            text-align: center;
        }
        .slot-info::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--orange);
        }
        .board-slot:hover .slot-info {
            display: block;
        }
        .info-name { color: #fff; font-weight: 700; font-size: 12px; display: block; margin-bottom: 2px; }
        .info-detail { color: var(--muted); font-size: 10px; display: block; }
    </style>

    @if (session()->has('message'))
        <div style="background:rgba(37,196,107,0.12);border:1px solid var(--good);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:var(--good);font-size:13px;font-weight:700;">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div style="background:rgba(52,152,219,0.12);border:1px solid #3498db;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#3498db;font-size:13px;font-weight:700;">
            {{ session('info') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background:rgba(255,71,87,0.12);border:1px solid #ff4757;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#ff4757;font-size:13px;font-weight:700;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;margin-top:10px;">
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
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>

            @forelse($raffles as $raffle)
            <div class="raffle-item {{ $selectedRaffleId == $raffle->id ? 'selected' : '' }}" wire:click="selectRaffle({{ $raffle->id }})">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;">
                    <div class="raffle-name">{{ $raffle->title }}</div>
                    @if($raffle->status === 'active')
                        <span class="badge-active-r">Activo</span>
                    @else
                        <span class="badge-inactive">Inactivo</span>
                    @endif
                </div>
                <div class="raffle-meta">
                    {{ $raffle->start_date->format('d/m/Y') }} - {{ $raffle->end_date->format('d/m/Y') }}
                    @if($raffle->platform) · <span style="color:var(--orange);">{{ $raffle->platform->name }}</span> @endif
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
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
                    <div>
                        <div style="font-family:var(--font-display);font-size:26px;letter-spacing:0.02em;">{{ $selectedRaffle->title }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            Rango del Tablero: <strong style="color:#fff;">{{ $selectedRaffle->start_number }} - {{ $selectedRaffle->end_number }}</strong>
                            @if($selectedRaffle->platform) · Plataforma: <span style="color:var(--orange);">{{ $selectedRaffle->platform->name }}</span> @endif
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <button wire:click="openEdit({{ $selectedRaffle->id }})" class="btn-ghost" style="height:32px;padding:0 12px;font-size:11px;">Editar</button>
                        <button wire:click="openWinnerModal({{ $selectedRaffle->id }})" class="btn-primary" style="height:32px;padding:0 12px;font-size:11px;">🏆 Ganador</button>
                        <button wire:click="delete({{ $selectedRaffle->id }})" onclick="confirm('¿Eliminar sorteo?') || event.stopImmediatePropagation()" class="btn-ghost" style="height:32px;padding:0 12px;font-size:11px;color:#ff4757;">Borrar</button>
                    </div>
                </div>

                <div style="display:flex;gap:15px;margin-bottom:24px;padding:15px;background:rgba(255,255,255,0.02);border:1px solid var(--line);border-radius:12px;">
                    <div style="flex:1;">
                        <div style="font-size:10px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Estado</div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($selectedRaffle->status === 'active')
                                <span class="badge-active-r">Activo</span>
                            @else
                                <span class="badge-inactive">Inactivo</span>
                            @endif
                            <button wire:click="toggleStatus({{ $selectedRaffle->id }})" class="btn-ghost" style="height:24px;padding:0 8px;font-size:10px;">Cambiar</button>
                        </div>
                    </div>
                    <div style="flex:1;border-left:1px solid var(--line);padding-left:15px;">
                        <div style="font-size:10px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Vigencia</div>
                        <div style="font-size:13px;">
                            @if($selectedRaffle->isAvailable())
                                <span style="color:var(--good);">● Vigente</span>
                            @elseif($selectedRaffle->isExpired())
                                <span style="color:#ff4757;">● Finalizado</span>
                            @else
                                <span style="color:var(--warn);">● Próximamente</span>
                            @endif
                        </div>
                    </div>
                    <div style="flex:1;border-left:1px solid var(--line);padding-left:15px;">
                        <div style="font-size:10px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Ganador</div>
                        <div style="font-size:13px;">
                            @if($selectedRaffle->winner_user_id)
                                <span style="font-weight:700;color:var(--orange);">{{ $selectedRaffle->winner->name }}</span>
                                <span style="font-family:var(--font-mono);font-size:11px;">(#{{ $selectedRaffle->winner_number }})</span>
                            @else
                                <span style="color:var(--muted);">Sin definir</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Selection Header --}}
                <div style="margin-bottom:20px;padding:18px;background:rgba(255,106,26,0.05);border:1px solid rgba(255,106,26,0.2);border-radius:15px;">
                    <div style="font-size:11px;color:var(--orange);font-weight:800;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.05em;">1. SELECCIONAR CLIENTE PARA ASIGNAR</div>
                    <div style="display:flex;gap:12px;align-items:center;">
                        <div style="flex:1;">
                            <select wire:model="assignUserId" style="width:100%;background:#000;border:1px solid var(--orange);border-radius:10px;padding:10px 15px;color:#fff;font-size:14px;outline:none;">
                                <option value="">— Elegir Cliente —</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        @if($assignUserId)
                            <div style="font-size:12px;color:var(--good);font-weight:700;">✓ Cliente seleccionado</div>
                        @endif
                    </div>
                </div>

                {{-- Assignment Methods --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:25px;">
                    {{-- Method A: Auto --}}
                    <div style="padding:15px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:12px;">
                        <div style="font-size:10px;color:var(--muted);font-weight:700;margin-bottom:10px;text-transform:uppercase;">Opción A: Carga Secuencial</div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <div class="assign-controls">
                                @foreach([1,5,10,20] as $qty)
                                <button wire:click="$set('assignCount',{{ $qty }})" class="qty-btn {{ $assignCount == $qty ? 'selected-qty' : '' }}">
                                    +{{ $qty }}
                                </button>
                                @endforeach
                            </div>
                            <button wire:click="assignNumbers" class="btn-primary" style="height:32px;padding:0 12px;font-size:11px;">Cargar</button>
                        </div>
                        <div style="font-size:10px;color:var(--muted);margin-top:8px;">Asigna los primeros números disponibles en el rango.</div>
                    </div>

                    {{-- Method B: Manual Entry --}}
                    <div style="padding:15px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:12px;">
                        <div style="font-size:10px;color:var(--muted);font-weight:700;margin-bottom:10px;text-transform:uppercase;">Opción B: Ingreso Manual</div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" wire:model="manualNumbers" placeholder="Ej: 7, 15, 22" style="flex:1;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:8px;padding:6px 10px;color:#fff;font-size:12px;outline:none;">
                            <button wire:click="assignManual" class="btn-primary" style="height:32px;padding:0 12px;font-size:11px;">Asignar</button>
                        </div>
                        <div style="font-size:10px;color:var(--muted);margin-top:8px;">Escribe números del tablero separados por comas.</div>
                    </div>
                </div>

                {{-- Tabs for visualization --}}
                <div style="display:flex;gap:10px;margin-bottom:20px;border-bottom:1px solid var(--line);padding-bottom:12px;">
                    <button wire:click="$set('viewMode', 'board')" class="tab-btn {{ $viewMode === 'board' ? 'active' : '' }}">2. ELECCIÓN EN TABLERO (CLIC)</button>
                    <button wire:click="$set('viewMode', 'list')" class="tab-btn {{ $viewMode === 'list' ? 'active' : '' }}">LISTA DE PARTICIPANTES</button>
                </div>

                @if($viewMode === 'board')
                    {{-- Board View --}}
                    <div class="detail-section">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <div class="detail-section-title" style="margin:0;">Mapa de Números ({{ $selectedRaffle->start_number }} - {{ $selectedRaffle->end_number }})</div>
                            <div style="display:flex;gap:15px;align-items:center;">
                                <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--muted);"><div style="width:10px;height:10px;background:rgba(37,196,107,0.2);border:1px solid var(--good);border-radius:2px;"></div> Libre</div>
                                <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--muted);"><div style="width:10px;height:10px;background:rgba(255,106,26,0.2);border:1px solid var(--orange);border-radius:2px;"></div> Ocupado</div>
                            </div>
                        </div>

                        <div class="raffle-board">
                            @php
                                $start = $selectedRaffle->start_number;
                                $end = $selectedRaffle->end_number;
                                // Optimize to avoid N+1 and repeated lookups
                                $takenMap = $selectedRaffle->numbers->keyBy('number');
                            @endphp
                            @for($n = $start; $n <= $end; $n++)
                                @php 
                                    $numModel = $takenMap->get($n);
                                    $isTaken = (bool)$numModel;
                                @endphp
                                <div 
                                    wire:click="toggleNumber({{ $n }})"
                                    class="board-slot {{ $isTaken ? 'slot-taken' : 'slot-free' }}"
                                >
                                    {{ $n }}
                                    @if($isTaken)
                                        <div class="slot-info">
                                            <span class="info-name">{{ $numModel->user->name ?? 'Usuario' }}</span>
                                            <span class="info-detail">ID: {{ $numModel->user_id }}</span>
                                            <span class="info-detail">{{ $numModel->created_at->format('d/m H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                @else
                    {{-- Numbers list --}}
                    <div class="detail-section">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <div class="detail-section-title" style="margin:0;">Participantes y Números</div>
                            <input wire:model.live="numbersSearch" type="text" placeholder="Filtrar por nombre..." style="background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:6px 12px;color:#fff;font-size:11px;outline:none;width:200px;">
                        </div>
                        @php
                            $byUser = $selectedRaffle->numbers->groupBy('user_id');
                        @endphp
                        <div style="max-height:400px;overflow-y:auto;padding-right:5px;">
                            @forelse($byUser as $userId => $nums)
                            @php $user = $nums->first()->user; @endphp
                            <div class="user-numbers-group">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <div class="user-numbers-name">{{ $user->name ?? 'Usuario #'.$userId }}</div>
                                    <div style="font-size:10px;color:var(--muted);">{{ $nums->count() }} números</div>
                                </div>
                                <div style="display:flex;flex-wrap:wrap;">
                                    @foreach($nums as $num)
                                    <span class="number-chip">
                                        {{ $num->number }}
                                        <button wire:click="removeNumber({{ $num->id }})" title="Eliminar">✕</button>
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <div style="text-align:center;padding:40px;color:var(--muted);background:rgba(255,255,255,0.01);border:1px dashed var(--line);border-radius:12px;">
                                No hay números asignados aún en este sorteo
                            </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
            @else
            <div class="sort-card" style="display:flex;align-items:center;justify-content:center;padding:80px;flex-direction:column;gap:15px;text-align:center;">
                <div style="font-size:50px;filter:grayscale(1);opacity:0.3;">🎯</div>
                <div>
                    <div style="font-family:var(--font-display);font-size:22px;color:var(--muted);">Panel de Sorteos</div>
                    <div style="font-size:14px;color:var(--muted-2);max-width:300px;margin:8px auto;">Selecciona un sorteo de la izquierda para gestionar las participaciones y definir al ganador.</div>
                </div>
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
                    <input type="text" wire:model="title" placeholder="Ej: Sorteo de Fin de Semana">
                    @error('title')<div class="fe-err">{{ $message }}</div>@enderror
                </div>
                <div class="mf">
                    <label>DESCRIPCIÓN (OPCIONAL)</label>
                    <textarea wire:model="description" rows="2" placeholder="Detalles del sorteo..."></textarea>
                </div>
                
                <div class="mf">
                    <label>PLATAFORMA</label>
                    <select wire:model="platform_id">
                        <option value="">— Ninguna —</option>
                        @foreach($platforms as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
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
                        <label>NÚMERO INICIAL DEL TABLERO</label>
                        <input type="number" wire:model="start_number" min="0">
                        @error('start_number')<div class="fe-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="mf">
                        <label>NÚMERO FINAL DEL TABLERO</label>
                        <input type="number" wire:model="end_number" min="1">
                        @error('end_number')<div class="fe-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mf">
                    <label>ESTADO</label>
                    <select wire:model="status">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary" style="padding:10px 25px;font-size:13px;">
                        {{ $editingRaffle ? 'Guardar Cambios' : 'Crear Sorteo' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- WINNER MODAL --}}
    @if($showWinnerModal)
    <div class="modal-overlay" wire:click.self="$set('showWinnerModal', false)">
        <div class="modal-box" style="max-width:400px;">
            <div class="modal-title">🏆 Registrar Ganador</div>
            <p style="font-size:13px;color:var(--muted);margin-bottom:20px;">Indica qué cliente resultó ganador y con qué número.</p>
            <form wire:submit.prevent="saveWinner">
                <div class="mf">
                    <label>CLIENTE GANADOR</label>
                    <select wire:model="winner_user_id">
                        <option value="">— Sin ganador —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mf">
                    <label>NÚMERO GANADOR</label>
                    <input type="number" wire:model="winner_number" placeholder="Ej: 777">
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="$set('showWinnerModal', false)" class="btn-ghost">Cerrar</button>
                    <button type="submit" class="btn-primary" style="padding:10px 25px;font-size:13px;">Guardar Ganador</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
