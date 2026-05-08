<div class="page-container">
    <style>
        .sales-page { display:flex; flex-direction:column; gap:18px; }
        .toolbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .input, .select { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; font-family:var(--font-body); }
        .input:focus, .select:focus { outline:none; border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
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
        .btn-icon, .btn-soft { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; }
        .btn-icon { width:32px; color:var(--muted); }
        .btn-soft { padding:0 10px; font-size:11px; font-weight:800; }
        .btn-icon:hover, .btn-soft:hover { border-color:var(--orange); background:rgba(255,106,26,.15); color:var(--white); }
        .btn-danger:hover { border-color:#ff4757; background:rgba(255,71,87,.15); }
        .empty-state { border:1px dashed var(--line-2); border-radius:8px; padding:38px 20px; text-align:center; color:var(--muted-2); background:rgba(255,255,255,.02); }
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
    </style>

    @section('header')
    <x-livewire.components.page-header title="VENTAS" subtitle="Carga operativa y estadisticas calculadas desde ventas registradas" />
@endsection

<div class="module-top-bar">
    <button type="button" class="btn-primary" wire:click="openCreateModal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Cargar venta
    </button>
</div>

<div class="sales-page">
        <div style="margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" class="input" placeholder="Buscar linea o plataforma">
            <select wire:model.live="lineFilter" class="select">
                <option value="all">Todas las lineas</option>
                @foreach($lines as $line)
                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="monthFilter" class="select">
                @foreach($months as $num => $label)
                    <option value="{{ $num }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="number" wire:model.live="yearFilter" class="input" style="width:98px">
        </div>

        @if(session()->has('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total fichas vendidas</div>
                <div class="stat-value">${{ number_format((float) $stats['total'], 2) }}</div>
                <div class="stat-note">{{ $this->monthLabel($monthFilter, $yearFilter) }}</div>
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

        <div class="sales-table">
            <div class="sales-row head">
                <div>Linea</div>
                <div>Plataforma</div>
                <div>Periodo</div>
                <div>Monto fichas</div>
                <div>Acciones</div>
            </div>
            @forelse($sales as $sale)
                <div class="sales-row" wire:key="sale-{{ $sale->id }}">
                    <div>
                        <strong>{{ $sale->line?->name ?? '-' }}</strong>
                        <div class="line-meta">#{{ str_pad($sale->line_id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div>{{ $sale->platform?->name ?? '-' }}</div>
                    <div>
                        {{ $this->monthLabel($sale->fecha->month, $sale->fecha->year) }}
                        <div class="line-meta">{{ $sale->fecha->format('d/m/Y') }}</div>
                        @if($sale->descripcion)
                            <div class="line-meta">{{ $sale->descripcion }}</div>
                        @endif
                    </div>
                    <div><strong>${{ number_format((float) $sale->monto_fichas, 2) }}</strong></div>
                    <div class="actions">
                        <button type="button" class="btn-icon" wire:click="openEditModal({{ $sale->id }})" title="Editar">E</button>
                        <button type="button" class="btn-icon btn-danger" wire:click="deleteSale({{ $sale->id }})" wire:confirm="Eliminar esta venta?" title="Eliminar">x</button>
                    </div>
                </div>
            @empty
                <div class="empty-state">No hay ventas cargadas para este periodo.</div>
            @endforelse
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
                        <div class="form-group">
                            <label class="form-label">Linea</label>
                            <select wire:model.live="saleLineId" class="select" style="width:100%">
                                <option value="">Elegir linea</option>
                                @foreach($lines as $line)
                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                @endforeach
                            </select>
                            @error('saleLineId') <div class="form-error">{{ $message }}</div> @enderror
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
                            <label class="form-label">Mes de reporte</label>
                            <select wire:model.live="saleMes" class="select" style="width:100%">
                                @foreach($months as $num => $label)
                                    <option value="{{ $num }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Anio</label>
                            <input type="number" wire:model.live="saleAnio" class="input" style="width:100%">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha</label>
                            <input type="date" wire:model="saleFecha" class="input" style="width:100%">
                            @error('saleFecha') <div class="form-error">{{ $message }}</div> @enderror
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
