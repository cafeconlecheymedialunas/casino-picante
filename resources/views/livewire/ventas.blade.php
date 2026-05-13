<div class="page-container">
    <style>
        .sales-page { display:flex; flex-direction:column; gap:18px; }
        .toolbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .input, .select {
            background:rgba(255,255,255,.04);
            border:1px solid var(--line-2);
            border-radius:7px;
            padding:9px 12px;
            color:var(--white);
            font-size:13px;
            font-family:var(--font-body);
            min-height:40px;
            appearance:none;
            -webkit-appearance:none;
            -moz-appearance:none;
            background-image: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.02));
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px 12px;
        }
        .select {
            background-image: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.02)),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5' fill='none' stroke='%23ffffff' stroke-width='2'/%3E%3C/svg%3E");
            background-position: right 12px center, right 42px center;
            background-size: 12px 12px, 12px 12px;
        }
        .input:focus, .select:focus {
            outline:none;
            border-color:var(--orange);
            box-shadow:0 0 0 3px rgba(255,106,26,.12);
        }
        .select option {
            background: #100808;
            color: #fff;
        }
        .select:focus option {
            background: #120909;
        }
        .select::-ms-expand {
            display:none;
        }
        .stats-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
        .stat-card { border:1px solid var(--line); border-radius:8px; padding:14px; background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.025)); }
        .stat-label { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; margin-bottom:6px; }
        .stat-value { font-family:var(--font-display); font-size:31px; line-height:1; }
        .stat-note { margin-top:7px; color:var(--muted); font-size:12px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .sales-table { border:1px solid var(--line); border-radius:8px; overflow:hidden; background:#100808; }
        .sales-row { display:grid; grid-template-columns:1.2fr 1fr .9fr .9fr 130px; gap:12px; align-items:center; padding:12px 14px; border-bottom:1px solid var(--line); font-size:13px; }
        .sales-row.head { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; background:rgba(255,255,255,.03); }
        .sales-row:last-child { border-bottom:0; }
        .line-meta { color:var(--muted-2); font-size:11px; margin-top:3px; }
        .actions { display:flex; gap:8px; justify-content:flex-end; }
        .table-card { background: linear-gradient(180deg, #170b0b, #0f0707); border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
        .table-header-row { display:flex; justify-content:space-between; align-items:center; padding:16px 18px; border-bottom:1px solid var(--line); gap:14px; flex-wrap:wrap; }
        .table-header-left .tc-title { font-family: var(--font-display); font-size: 22px; letter-spacing: .03em; }
        .table-scroll { overflow-x:auto; }
        .t-head, .t-row { display:grid; grid-template-columns: 1.2fr 1fr 1fr 160px 130px; gap:12px; align-items:center; min-width:980px; padding:11px 18px; }
        .t-head { color: var(--muted-2); font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        .t-row { border-bottom: 1px solid var(--line); font-size: 13px; }
        .t-row:last-child { border-bottom: 0; }
        .t-row:hover { background: rgba(255,106,26,.04); }
        .action-row { display:flex; align-items:center; gap:6px; justify-content:flex-end; }
        .btn-icon, .btn-soft { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; }
        .btn-icon { width:32px; color:var(--muted); }
        .btn-soft { padding:0 10px; font-size:11px; font-weight:800; }
        .mini-icon { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
        .btn-icon:hover, .btn-soft:hover { border-color:var(--orange); background:rgba(255,106,26,.15); color:var(--white); }
        .btn-icon.danger:hover, .btn-icon.btn-danger:hover { border-color:#ff4757; background:rgba(255,71,87,.15); color:#fff; }
        .empty-state { padding:56px 24px; text-align:center; color:var(--muted-2); }
        .flash-message { border:1px solid rgba(37,196,107,.35); background:rgba(37,196,107,.12); color:var(--good); border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; }
        .modal-overlay { position:fixed; inset:0; z-index:240; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.78); }
        .modal-panel { width:min(720px,100%); max-height:92vh; overflow-y:auto; border:1px solid var(--line-2); border-radius:8px; background:linear-gradient(180deg,#1c0e0e,#120909); }
        .modal-head { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-head h3 { margin:0; font-family:var(--font-display); font-size:24px; letter-spacing:.03em; }
        .modal-close { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; }
        .modal-form { padding:22px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-label { display:block; margin-bottom:6px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .form-error { margin-top:4px; color:#ff4757; font-size:11px; }
        .modal-actions { display:flex; justify-content:flex-end; align-items:center; gap:10px; border-top:1px solid var(--line); padding-top:18px; }
        @media (max-width:900px){ .stats-grid,.form-grid{grid-template-columns:1fr;} .sales-row{grid-template-columns:1fr;} .toolbar .input{width:100%;} }
        @media (max-width:768px){
            .stat-card { padding:10px 12px; }
            .stat-value { font-size:22px; }
            .stat-note { font-size:11px; }
            .table-scroll { overflow-x:visible; }
            .t-head { display:none; }
            .t-row { grid-template-columns:1fr auto; gap:6px; padding:8px 12px; min-width:0; }
            .t-row .line-meta { font-size:10px; }
            .t-row > :nth-child(3) { display:none !important; }
            .action-row .btn-icon { width:26px; height:26px; }
            .action-row .mini-icon { width:12px; height:12px; }
            .modal-panel { border-radius:0; width:100%; max-height:100vh; min-height:100vh; }
            .modal-overlay { padding:0; align-items:flex-end; }
            .modal-head { padding:14px 16px; }
            .modal-head h3 { font-size:20px; }
            .modal-form { padding:16px; }
            .table-header-row { padding:12px 14px; }
            .table-header-left .tc-title { font-size:18px; }
        }
    </style>

    @section('header')
    <x-livewire.components.page-header title="VENTAS" subtitle="Carga operativa y estadisticas calculadas desde ventas registradas" />
@endsection

<div class="module-top-bar">
    @if($lineFilter !== 'all')
        <button type="button" class="btn-primary" wire:click="openCreateModal">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Cargar venta
        </button>
    @else
        <button type="button" class="btn-primary" style="opacity: 0.5; cursor: not-allowed;" title="Seleccione una linea primero">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Cargar venta
        </button>
    @endif
</div>

<div class="sales-page">
        <div style="margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" class="input" placeholder="Buscar linea o plataforma">
            @if($lineFilter !== 'all')
                <div class="input" style="background: rgba(255,106,26,0.1); border-color: var(--orange);">
                    <strong>{{ $lines->find($lineFilter)?->name ?? 'Línea seleccionada' }}</strong>
                    <button type="button" wire:click="$set('lineFilter', 'all')" style="margin-left: 10px; color: var(--muted); font-size: 12px;">×</button>
                </div>
            @else
                <select wire:model.live="lineFilter" class="select">
                    <option value="all">Todas las lineas</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                    @endforeach
                </select>
            @endif
            <input type="date" wire:model.live="dateInicioFilter" class="input" style="width:120px">
            <input type="date" wire:model.live="dateFinFilter" class="input" style="width:120px">
        </div>

        @if(session()->has('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total fichas vendidas</div>
                <div class="stat-value">${{ number_format((float) $stats['total'], 2) }}</div>
                <div class="stat-note">{{ $this->dateRangeLabel() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Linea top</div>
                <div class="stat-value">{{ $stats['bestLine']['line']?->name ?? '-' }}</div>
                <div class="stat-note">${{ number_format((float) ($stats['bestLine']['total'] ?? 0), 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Plataforma top</div>
                <div class="stat-value">{{ $stats['bestPlatform']['platform']?->name ?? '-' }}</div>
                <div class="stat-note">${{ number_format((float) ($stats['bestPlatform']['total'] ?? 0), 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Registros</div>
                <div class="stat-value">{{ $stats['records'] }}</div>
                <div class="stat-note">{{ $stats['lineCount'] }} lineas · {{ $stats['platformCount'] }} plataformas</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header-row">
                <div class="table-header-left">
                    <span class="tc-title">VENTAS REGISTRADAS</span>
                </div>
            </div>

            <div class="table-scroll">
                <div class="t-head">
                    <div>Linea Principal</div>
                    <div>Plataforma</div>
                    <div>Rango fechas</div>
                    <div>Monto fichas</div>
                    <div>Acciones</div>
                </div>
                @forelse($sales as $sale)
                    <div class="t-row">
                        <div>
                            <strong>{{ $sale->line?->name ?? '-' }}</strong>
                            <div class="line-meta">#{{ str_pad($sale->line_id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div>{{ $sale->platform?->name ?? '-' }}</div>
                        <div>{{ $sale->fecha_inicio?->format('d/m/Y') }} - {{ $sale->fecha_fin?->format('d/m/Y') }}</div>
                        <div><strong>${{ number_format((float) $sale->monto_fichas, 2) }}</strong></div>
                        <div class="action-row">
                            <button type="button" class="btn-icon" wire:click="openEditModal({{ $sale->id }})" title="Editar">
                                <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>
                            </button>
                            <button type="button" class="btn-icon danger" wire:click="deleteSale({{ $sale->id }})" wire:confirm="Eliminar esta venta?" title="Eliminar">
                                <svg class="mini-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No hay ventas cargadas para este periodo.</div>
                @endforelse
            </div>
        </div>
    </div>

    @if($showModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>{{ $editingSaleId ? 'EDITAR VENTA' : 'CARGAR VENTA' }}</h3>
                    <button class="modal-close" wire:click="closeModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveSale">
                    <div class="form-grid">
                        <!-- Line selector removed - line comes from global filter -->
                        <input type="hidden" wire:model="saleLineId" value="{{ $lineFilter }}">
                        <div class="form-group">
                            <label class="form-label">Linea</label>
                            <div class="input" style="background: rgba(255,106,26,0.1); border-color: var(--orange); width:100%">
                                <strong>{{ $lines->find($lineFilter)?->name ?? 'Línea seleccionada' }}</strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Plataforma</label>
                            <select wire:model="salePlatformId" class="select" style="width:100%">
                                <option value="">Elegir plataforma</option>
                                @foreach($formPlatforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                            @error('salePlatformId') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" wire:model.live="saleFechaInicio" class="input" style="width:100%">
                            @error('saleFechaInicio') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha fin</label>
                            <input type="date" wire:model.live="saleFechaFin" class="input" style="width:100%">
                            @error('saleFechaFin') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Descripcion</label>
                            <input type="text" wire:model="saleDescripcion" class="input" style="width:100%" maxlength="255">
                            @error('saleDescripcion') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Monto fichas vendidas</label>
                        <input type="number" min="0" step="0.01" wire:model="saleMontoFichas" class="input" style="width:100%">
                        @error('saleMontoFichas') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-soft" wire:click="closeModal">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar venta</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
