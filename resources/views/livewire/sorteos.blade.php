<div class="page-container">
    <x-livewire.components.page-header title="SORTEOS" subtitle="Gestion de sorteos, premios, numeros y participantes" />

    <style>
        .raffles-page { display:flex; flex-direction:column; gap:18px; }
        .raffle-tools, .action-row, .modal-actions, .assign-controls { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .search-input, .filter-select, .form-input { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; font-family:var(--font-body); }
        .search-input { width:260px; }
        .search-input:focus, .filter-select:focus, .form-input:focus { outline:none; border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        .stats-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
        .stat-card, .panel-card { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg,#170b0b,#0f0707); }
        .stat-card { padding:16px 18px; position:relative; overflow:hidden; }
        .stat-card::before { content:''; position:absolute; inset:0 0 auto; height:2px; background:linear-gradient(90deg,var(--orange),var(--amber)); }
        .stat-label { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
        .stat-value { font-family:var(--font-display); font-size:32px; line-height:1; }
        .raffles-layout { display:grid; grid-template-columns:330px 1fr; gap:18px; align-items:start; }
        .panel-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:16px 18px; border-bottom:1px solid var(--line); }
        .panel-title { font-family:var(--font-display); font-size:22px; letter-spacing:.03em; }
        .panel-body { padding:16px 18px; }
        .raffle-item { padding:12px; border-radius:8px; background:rgba(255,255,255,.03); border:1px solid var(--line); margin-bottom:8px; cursor:pointer; transition:all .15s; }
        .raffle-item:hover, .raffle-item.selected { border-color:var(--orange); background:rgba(255,106,26,.08); }
        .raffle-name { font-weight:800; font-size:13px; margin-bottom:4px; }
        .raffle-meta { font-size:11px; color:var(--muted); line-height:1.45; }
        .badge { display:inline-flex; width:fit-content; align-items:center; border-radius:999px; padding:4px 10px; font-size:10px; font-weight:800; }
        .b-active { color:var(--good); background:rgba(37,196,107,.12); }
        .b-inactive { color:var(--muted); background:rgba(255,255,255,.06); }
        .btn-icon, .btn-soft { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; }
        .btn-icon { width:32px; color:var(--muted); }
        .btn-soft { padding:0 10px; font-size:11px; font-weight:800; }
        .btn-icon:hover, .btn-soft:hover { border-color:var(--orange); background:rgba(255,106,26,.15); color:var(--white); }
        .btn-danger:hover { border-color:#ff4757; background:rgba(255,71,87,.15); }
        .qty-btn { height:32px; padding:0 12px; border-radius:7px; font-size:12px; font-weight:800; background:rgba(255,106,26,.1); color:var(--orange); border:1px solid var(--orange); cursor:pointer; }
        .qty-btn.selected-qty { background:var(--orange); color:#190702; }
        .mini-icon { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .flash-message { border:1px solid rgba(37,196,107,.35); background:rgba(37,196,107,.12); color:var(--good); border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; }
        .flash-error { border-color:rgba(255,71,87,.45); background:rgba(255,71,87,.12); color:#ff6b7a; }
        .info-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
        .info-box { border:1px solid var(--line); border-radius:8px; padding:12px; background:rgba(255,255,255,.03); }
        .info-label { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; margin-bottom:5px; }
        .info-value { font-size:13px; font-weight:800; }
        .prize-list { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:10px; margin-top:12px; }
        .prize-card { border:1px solid var(--line); border-radius:8px; overflow:hidden; background:rgba(255,255,255,.03); }
        .prize-card img { width:100%; aspect-ratio:1.4; object-fit:cover; display:block; background:rgba(255,255,255,.04); }
        .prize-body { padding:10px; }
        .raffle-board { display:grid; grid-template-columns:repeat(auto-fill,minmax(46px,1fr)); gap:6px; max-height:430px; overflow:auto; padding-right:4px; }
        .board-slot { appearance:none; -webkit-appearance:none; padding:0; aspect-ratio:1; display:flex; align-items:center; justify-content:center; border-radius:6px; font-family:var(--font-mono); font-size:11px; font-weight:800; cursor:pointer; position:relative; border:1px solid transparent; }
        .slot-free { background:rgba(37,196,107,.05); color:var(--good); border-color:rgba(37,196,107,.18); }
        .slot-selected { background:var(--orange); color:#190702; border-color:var(--amber); box-shadow:0 0 0 2px rgba(255,179,71,.18); }
        .slot-taken { background:rgba(255,106,26,.16); color:var(--orange); border-color:var(--orange); }
        .slot-taken.slot-selected { background:#ffb347; color:#190702; border-color:#fff; }
        .slot-info { display:none; position:absolute; bottom:120%; left:50%; transform:translateX(-50%); width:max-content; min-width:130px; padding:8px 10px; border-radius:8px; background:#1a0909; border:1px solid var(--orange); z-index:20; box-shadow:0 10px 24px rgba(0,0,0,.55); text-align:center; }
        .board-slot:hover .slot-info { display:block; }
        .tab-row { display:flex; gap:8px; border-bottom:1px solid var(--line); padding-bottom:12px; margin-bottom:16px; }
        .tab-btn { height:32px; padding:0 14px; font-size:11px; font-weight:800; border-radius:7px; background:rgba(255,255,255,.03); border:1px solid var(--line); color:var(--muted); cursor:pointer; }
        .tab-btn.active { background:var(--orange); color:#190702; border-color:var(--orange); }
        .table-scroll { overflow-x:auto; }
        .t-head, .t-row { display:grid; grid-template-columns:64px 1fr 1fr 120px 120px; gap:12px; align-items:center; min-width:760px; padding:10px 12px; }
        .t-head { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; border-bottom:1px solid var(--line); }
        .t-row { border-bottom:1px solid var(--line); font-size:13px; }
        .modal-overlay { position:fixed; inset:0; z-index:240; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.78); }
        .modal-panel { width:min(900px,100%); max-height:92vh; overflow-y:auto; border:1px solid var(--line-2); border-radius:8px; background:linear-gradient(180deg,#1c0e0e,#120909); }
        .modal-head { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-head h3 { margin:0; font-family:var(--font-display); font-size:24px; letter-spacing:.03em; }
        .modal-close { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; }
        .modal-form { padding:22px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-label { display:block; margin-bottom:6px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .form-input { width:100%; }
        textarea.form-input { resize:vertical; min-height:84px; }
        .form-error { margin-top:4px; color:#ff4757; font-size:11px; }
        .check-row { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:12px; font-weight:700; margin-top:8px; }
        .repeat-row { display:grid; grid-template-columns:80px 1fr 170px 36px; gap:8px; align-items:start; padding:10px; border:1px solid var(--line); border-radius:8px; background:rgba(255,255,255,.03); margin-bottom:8px; }
        @media (max-width:1000px){ .raffles-layout,.stats-grid,.info-grid,.form-grid,.repeat-row{grid-template-columns:1fr;} .search-input{width:100%;} }
    </style>

    @if (session()->has('message'))
        <div class="flash-message">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="flash-message flash-error">{{ session('error') }}</div>
    @endif
    @if (session()->has('info'))
        <div class="flash-message">{{ session('info') }}</div>
    @endif

    @if($this->hasLinePermission(\App\Support\Permissions::SORTEO_CREATE))
        <div class="page-action-strip">
            <button type="button" class="btn-primary" wire:click="openCreate">+ Crear sorteo</button>
        </div>
    @endif

    <div class="raffles-page">
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-label">Historico total</div><div class="stat-value">{{ $totalHistorical }}</div></div>
            <div class="stat-card"><div class="stat-label">Visibles</div><div class="stat-value">{{ $raffles->count() }}</div></div>
            <div class="stat-card"><div class="stat-label">Activos</div><div class="stat-value" style="color:var(--good);">{{ $raffles->where('status','active')->count() }}</div></div>
            <div class="stat-card"><div class="stat-label">Numeros asignados</div><div class="stat-value" style="color:var(--orange);">{{ $selectedRaffle?->numbers->count() ?? 0 }}</div></div>
        </div>

        <div class="raffles-layout">
            <div class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">SORTEOS</div>
                        <div class="raffle-meta">Buscar y seleccionar</div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="raffle-tools" style="margin-bottom:14px;">
                        <input wire:model.live.debounce.300ms="search" class="search-input" placeholder="Buscar sorteo">
                        <select wire:model.live="filterStatus" class="filter-select">
                            <option value="all">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                    </div>

                    @forelse($raffles as $raffle)
                        <div class="raffle-item {{ $selectedRaffleId == $raffle->id ? 'selected' : '' }}" wire:click="selectRaffle({{ $raffle->id }})">
                            <div style="display:flex;justify-content:space-between;gap:10px;">
                                <div class="raffle-name">{{ $raffle->title }}</div>
                                <span class="badge {{ $raffle->status === 'active' ? 'b-active' : 'b-inactive' }}">{{ $raffle->status === 'active' ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                            <div class="raffle-meta">
                                {{ $raffle->start_date->format('d/m/Y H:i') }} - {{ $raffle->end_date->format('d/m/Y H:i') }}<br>
                                {{ $raffle->numbers_count }} numeros · {{ $raffle->lines->pluck('name')->join(', ') ?: 'Sin linea' }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:30px;color:var(--muted);font-size:13px;">No hay sorteos</div>
                    @endforelse
                </div>
            </div>

            <div>
                @if($selectedRaffle)
                    <div class="panel-card" style="margin-bottom:16px;">
                        <div class="panel-head">
                            <div>
                                <div class="panel-title">{{ $selectedRaffle->title }}</div>
                                <div class="raffle-meta">{{ $selectedRaffle->description ?: 'Sin descripcion' }}</div>
                            </div>
                            <div class="action-row">
                                <button class="btn-soft" wire:click="openEdit({{ $selectedRaffle->id }})">Editar</button>
                                <button class="btn-soft" wire:click="openWinnerModal({{ $selectedRaffle->id }})">Ganador</button>
                                <button class="btn-soft" wire:click="toggleStatus({{ $selectedRaffle->id }})">Cambiar estado</button>
                                <button class="btn-icon btn-danger" wire:click="delete({{ $selectedRaffle->id }})" wire:confirm="Eliminar sorteo?">
                                    <svg class="mini-icon" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="info-grid">
                                <div class="info-box"><div class="info-label">Estado</div><div class="info-value">{{ $selectedRaffle->status === 'active' ? 'Activo' : 'Inactivo' }}</div></div>
                                <div class="info-box"><div class="info-label">Lineas</div><div class="info-value">{{ $selectedRaffle->lines->pluck('name')->join(', ') ?: '-' }}</div></div>
                                <div class="info-box"><div class="info-label">Limite</div><div class="info-value">{{ $selectedRaffle->numbers_limit ? $selectedRaffle->numbers_limit.' numeros' : 'Ilimitado' }}</div></div>
                                <div class="info-box"><div class="info-label">Linea que otorga</div><div class="info-value">{{ $assignmentLine?->name ?? '-' }}</div></div>
                            </div>

                            @if(!empty($selectedRaffle->prizes))
                                <div class="prize-list">
                                    @foreach($selectedRaffle->prizes as $prize)
                                        <div class="prize-card">
                                            @if(!empty($prize['image']))
                                                <img src="{{ $prize['image'] }}" alt="">
                                            @endif
                                            <div class="prize-body">
                                                <div class="raffle-meta">Puesto {{ $prize['position'] ?? '-' }}</div>
                                                <div class="raffle-name">{{ $prize['name'] ?? '-' }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="panel-card">
                        <div class="panel-head">
                            <div>
                                <div class="panel-title">NUMEROS Y PARTICIPANTES</div>
                                <div class="raffle-meta">Selecciona un cliente y administra los numeros desde el tablero</div>
                            </div>
                        </div>
                        <div class="panel-body">
                            @php
                                $selectedCollection = collect($selectedNumbers)->map(fn ($number) => (int) $number);
                                $selectedNumberMap = $selectedCollection->flip();
                                $occupiedSelection = $selectedRaffle->numbers->whereIn('number', $selectedCollection)->pluck('number');
                                $freeSelection = $selectedCollection->diff($occupiedSelection);
                            @endphp

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Cliente</label>
                                    <select class="form-input" wire:model.live="assignUserId">
                                        <option value="">Seleccionar cliente</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->username ?? $user->email ?? $user->name }}{{ $user->name ? ' - '.$user->name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assignUserId') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Linea que otorga</label>
                                    <div class="form-input" style="color:var(--muted);">{{ $assignmentLine?->name ?? 'Sin linea activa' }}</div>
                                </div>
                            </div>

                            <div class="assign-controls" style="margin-bottom:10px;">
                                <button type="button" wire:click="saveSelectedNumbers" class="btn-primary">
                                    Guardar seleccion ({{ $selectedCollection->count() }})
                                </button>
                                <button type="button" wire:click="unassignSelectedNumbers" class="btn-soft btn-danger">
                                    Desocupar seleccionados ({{ $occupiedSelection->count() }})
                                </button>
                                <button type="button" wire:click="clearSelectedNumbers" class="btn-soft">Limpiar</button>
                            </div>
                            @if (session()->has('message'))
                                <div class="flash-message" style="margin-bottom:10px;">{{ session('message') }}</div>
                            @endif
                            @if (session()->has('error'))
                                <div class="flash-message flash-error" style="margin-bottom:10px;">{{ session('error') }}</div>
                            @endif
                            @if (session()->has('info'))
                                <div class="flash-message" style="margin-bottom:10px;">{{ session('info') }}</div>
                            @endif

                            <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:12px;">
                                <div class="info-box"><div class="info-label">Seleccionados</div><div class="info-value">{{ $selectedCollection->count() }}</div></div>
                                <div class="info-box"><div class="info-label">Libres para asignar</div><div class="info-value" style="color:var(--good);">{{ $freeSelection->count() }}</div></div>
                                <div class="info-box"><div class="info-label">Ocupados para reasignar</div><div class="info-value" style="color:var(--orange);">{{ $occupiedSelection->count() }}</div></div>
                                <div class="info-box"><div class="info-label">Asignados total</div><div class="info-value">{{ $selectedRaffle->numbers->count() }}</div></div>
                            </div>

                            @if(count($selectedNumbers) > 0)
                                <div style="margin-bottom:12px;display:flex;gap:6px;flex-wrap:wrap;">
                                    @foreach($freeSelection as $selectedNumber)
                                        <span class="badge b-active">Por asignar {{ $selectedNumber }}</span>
                                    @endforeach
                                    @foreach($occupiedSelection as $selectedNumber)
                                        <span class="badge" style="background:#ffb347;color:#190702;">Ocupado {{ $selectedNumber }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @error('selectedNumbers') <div class="form-error">{{ $message }}</div> @enderror

                            <div class="tab-row" style="margin-top:4px;">
                                <button wire:click="$set('viewMode', 'board')" class="tab-btn {{ $viewMode === 'board' ? 'active' : '' }}">Tablero</button>
                                <button wire:click="$set('viewMode', 'list')" class="tab-btn {{ $viewMode === 'list' ? 'active' : '' }}">Lista agrupada</button>
                                <button wire:click="$set('viewMode', 'participants')" class="tab-btn {{ $viewMode === 'participants' ? 'active' : '' }}">Participantes</button>
                            </div>

                            @if($viewMode === 'board')
                                <div class="raffle-board">
                                    @php
                                        $start = (int) $selectedRaffle->start_number;
                                        $end = $selectedRaffle->numbers_limit
                                            ? $start + (int) $selectedRaffle->numbers_limit - 1
                                            : min((int) $selectedRaffle->end_number, $start + 999);
                                        $takenMap = $selectedRaffle->numbers->keyBy('number');
                                    @endphp
                                    @for($n = $start; $n <= $end; $n++)
                                        @php
                                            $numModel = $takenMap->get($n);
                                            $isSelected = $selectedNumberMap->has($n);
                                        @endphp
                                        <button type="button" wire:key="raffle-{{ $selectedRaffle->id }}-number-{{ $n }}" wire:click="toggleNumber({{ $n }})" class="board-slot {{ $numModel ? 'slot-taken' : 'slot-free' }} {{ $isSelected ? 'slot-selected' : '' }}">
                                            {{ $n }}
                                            @if($numModel)
                                                <span class="slot-info">
                                                    <strong>{{ $numModel->user?->username ?? $numModel->user?->name ?? 'Cliente' }}</strong><br>
                                                    <small>{{ $numModel->line?->name ?? '-' }}</small>
                                                </span>
                                            @endif
                                        </button>
                                    @endfor
                                </div>
                                @if(!$selectedRaffle->numbers_limit)
                                    <div class="raffle-meta" style="margin-top:8px;">Sorteo ilimitado: el tablero muestra una ventana operativa de 1000 numeros.</div>
                                @endif
                            @elseif($viewMode === 'list')
                                @php $byUser = $selectedRaffle->numbers->groupBy('user_id'); @endphp
                                @forelse($byUser as $userId => $nums)
                                    @php $user = $nums->first()->user; @endphp
                                    <div class="raffle-item">
                                        <div class="raffle-name">{{ $user?->username ?? $user?->email ?? 'Cliente #'.$userId }} · {{ $nums->count() }} numeros</div>
                                        <div class="raffle-meta">{{ $nums->pluck('number')->join(', ') }}</div>
                                    </div>
                                @empty
                                    <div style="padding:22px;color:var(--muted);text-align:center;">Todavia no hay numeros asignados.</div>
                                @endforelse
                            @else
                                <div class="table-scroll">
                                    <div class="raffle-tools" style="margin:0 0 14px;">
                                        <input wire:model.live.debounce.300ms="participantsSearch" class="search-input" placeholder="Buscar ID, username, nombre o email">
                                        <select wire:model.live="participantsLineFilter" class="filter-select">
                                            <option value="all">Todas las lineas</option>
                                            @foreach($selectedRaffle->lines as $line)
                                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="t-head">
                                        <div>ID</div>
                                        <div>Username</div>
                                        <div>Linea otorgada</div>
                                        <div>Numero</div>
                                        <div>Total</div>
                                    </div>
                                    @forelse($participants as $participant)
                                        <div class="t-row">
                                            <div>#{{ $participant->user?->id }}</div>
                                            <div>{{ $participant->user?->username ?? $participant->user?->email ?? '-' }}</div>
                                            <div>{{ $participant->line?->name ?? '-' }}</div>
                                            <div>{{ $participant->number }}</div>
                                            <div>{{ $participant->total_for_user }}</div>
                                        </div>
                                    @empty
                                        <div style="padding:22px;color:var(--muted);text-align:center;">Sin participantes para estos filtros.</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="panel-card">
                        <div class="panel-body" style="padding:70px;text-align:center;color:var(--muted);">
                            Selecciona un sorteo para gestionar numeros y participantes.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($showModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>{{ $editingRaffle ? 'EDITAR SORTEO' : 'CREAR SORTEO' }}</h3>
                    <button class="modal-close" wire:click="closeModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="save">
                    @if($errors->any())
                        <div class="flash-message flash-error" style="margin-bottom:16px;">
                            Revisar los campos marcados: {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Titulo del sorteo</label>
                            <input class="form-input" wire:model="title">
                            @error('title') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select class="form-input" wire:model="status">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                            @error('status') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripcion</label>
                        <textarea class="form-input" wire:model="description"></textarea>
                        @error('description') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lineas participantes</label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;">
                            @foreach($availableLines as $line)
                                <label class="check-row" style="margin:0;border:1px solid var(--line);border-radius:7px;padding:8px;">
                                    <input type="checkbox" wire:model="lineIds" value="{{ $line->id }}"> {{ $line->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('lineIds') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" class="form-input" wire:model="start_date">
                            @error('start_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" class="form-input" wire:model="start_time">
                            @error('start_time') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha fin</label>
                            <input type="date" class="form-input" wire:model="end_date">
                            @error('end_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hora fin</label>
                            <input type="time" class="form-input" wire:model="end_time">
                            @error('end_time') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Numero inicial</label>
                            <input type="number" class="form-input" wire:model="start_number" min="0">
                            @error('start_number') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Limite de numeros disponibles</label>
                            <input type="number" class="form-input" wire:model="numbersLimit" min="1" placeholder="Monto">
                            <label class="check-row"><input type="checkbox" wire:model.live="unlimitedNumbers"> Ilimitados</label>
                            @error('numbersLimit') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Premios ({{ count($prizes) }})</label>
                        @foreach($prizes as $idx => $prize)
                            <div class="repeat-row" wire:key="prize-{{ $idx }}">
                                <input type="number" class="form-input" wire:model="prizes.{{ $idx }}.position" placeholder="Puesto">
                                <input type="text" class="form-input" wire:model="prizes.{{ $idx }}.name" placeholder="Premio">
                                <x-image-uploader
                                    label="Imagen PNG"
                                    model="prizeUploads.{{ $idx }}"
                                    :value="$prizes[$idx]['image'] ?? null"
                                    :upload="$prizeUploads[$idx] ?? null"
                                    remove-action="removePrizeImage({{ $idx }})"
                                    hint="PNG"
                                    variant="logo"
                                />
                                <button type="button" class="btn-icon btn-danger" wire:click="removePrize({{ $idx }})">x</button>
                                @error('prizes.'.$idx.'.position') <div class="form-error">{{ $message }}</div> @enderror
                                @error('prizes.'.$idx.'.name') <div class="form-error">{{ $message }}</div> @enderror
                                @error('prizeUploads.'.$idx) <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        @endforeach
                        <button type="button" class="btn-soft" wire:click="addPrize">+ Agregar premio</button>
                    </div>

                    <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                        <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary">{{ $editingRaffle ? 'Guardar cambios' : 'Crear sorteo' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showWinnerModal)
        <div class="modal-overlay" wire:click.self="$set('showWinnerModal', false)">
            <div class="modal-panel" style="width:min(460px,100%);">
                <div class="modal-head">
                    <h3>REGISTRAR GANADOR</h3>
                    <button class="modal-close" wire:click="$set('showWinnerModal', false)">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveWinner">
                    <div class="form-group">
                        <label class="form-label">Cliente ganador</label>
                        <select class="form-input" wire:model="winner_user_id">
                            <option value="">Sin ganador</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Numero ganador</label>
                        <input type="number" class="form-input" wire:model="winner_number">
                    </div>
                    <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                        <button type="button" wire:click="$set('showWinnerModal', false)" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar ganador</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
