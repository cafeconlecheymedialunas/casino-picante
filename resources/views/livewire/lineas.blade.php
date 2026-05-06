<div class="page-container">
    <style>
        .lines-page { display: flex; flex-direction: column; gap: 18px; }
        .header-tools, .line-actions, .modal-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .search-input, .filter-select, .form-input { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; font-family:var(--font-body); }
        .search-input { width:280px; }
        .search-input:focus, .filter-select:focus, .form-input:focus { outline:none; border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        .line-section { display:flex; flex-direction:column; gap:12px; }
        .section-title { display:flex; align-items:center; justify-content:space-between; gap:12px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .line-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px; }
        .line-card { overflow:hidden; border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg,#170b0b,#0f0707); }
        .line-card.inactive { opacity:.72; }
        .line-cover { height:150px; background:linear-gradient(135deg,rgba(255,106,26,.16),rgba(255,255,255,.04)); position:relative; overflow:hidden; }
        .line-cover img { width:100%; height:100%; object-fit:cover; display:block; }
        .line-profile { position:absolute; left:16px; bottom:12px; width:64px; height:64px; border-radius:8px; border:2px solid rgba(255,255,255,.75); background:#210f0f; overflow:hidden; box-shadow:0 10px 22px rgba(0,0,0,.35); }
        .line-profile img { width:100%; height:100%; object-fit:cover; }
        .line-profile span { display:flex; width:100%; height:100%; align-items:center; justify-content:center; font-family:var(--font-display); font-size:28px; color:var(--orange); }
        .line-body { padding:14px 16px 16px; }
        .line-top { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:12px; }
        .line-name { font-family:var(--font-display); font-size:25px; line-height:1; letter-spacing:.03em; }
        .line-id { color:var(--muted-2); font-family:var(--font-mono); font-size:11px; margin-top:4px; }
        .status-badge, .chip { display:inline-flex; align-items:center; width:fit-content; border-radius:999px; padding:4px 10px; font-size:10px; font-weight:800; white-space:nowrap; }
        .status-active { color:var(--good); background:rgba(37,196,107,.12); }
        .status-inactive { color:#ff4757; background:rgba(255,71,87,.12); }
        .info-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px; }
        .info-box { border:1px solid var(--line); border-radius:8px; padding:10px; background:rgba(255,255,255,.03); min-width:0; }
        .info-label { color:var(--muted-2); font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; margin-bottom:5px; }
        .info-value { font-size:12px; font-weight:800; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .chip-row { display:flex; gap:6px; flex-wrap:wrap; min-height:24px; }
        .chip { color:var(--white); background:rgba(255,255,255,.06); border:1px solid var(--line); }
        .card-footer { display:flex; justify-content:space-between; align-items:center; gap:12px; padding-top:12px; border-top:1px solid var(--line); }
        .btn-icon, .btn-soft { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; }
        .btn-icon { width:32px; color:var(--muted); }
        .btn-soft { padding:0 10px; font-size:11px; font-weight:800; }
        .btn-icon:hover, .btn-soft:hover { border-color:var(--orange); background:rgba(255,106,26,.15); color:var(--white); }
        .btn-danger:hover { border-color:#ff4757; background:rgba(255,71,87,.15); }
        .mini-icon { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .empty-state { border:1px dashed var(--line-2); border-radius:8px; padding:38px 20px; text-align:center; color:var(--muted-2); background:rgba(255,255,255,.02); }
        .flash-message { border:1px solid rgba(37,196,107,.35); background:rgba(37,196,107,.12); color:var(--good); border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; }
        .modal-overlay { position:fixed; inset:0; z-index:240; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.78); }
        .modal-panel { width:min(920px,100%); max-height:92vh; overflow-y:auto; border:1px solid var(--line-2); border-radius:8px; background:linear-gradient(180deg,#1c0e0e,#120909); }
        .modal-panel.narrow { width:min(680px,100%); }
        .modal-head { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-head h3 { margin:0; font-family:var(--font-display); font-size:24px; letter-spacing:.03em; }
        .modal-close { width:32px; height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--muted); cursor:pointer; }
        .modal-form, .modal-body { padding:22px; }
        .form-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-label { display:block; margin-bottom:6px; color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .form-input { width:100%; }
        textarea.form-input { min-height:72px; resize:vertical; }
        .form-error { margin-top:4px; color:#ff4757; font-size:11px; }
        .repeat-list { display:flex; flex-direction:column; gap:8px; }
        .repeat-row { display:grid; grid-template-columns:180px 1fr 36px; gap:8px; align-items:center; }
        .preview-grid { display:grid; grid-template-columns:1.6fr .8fr; gap:12px; }
        .image-preview { border:1px solid var(--line); border-radius:8px; min-height:120px; background:rgba(255,255,255,.03); overflow:hidden; display:flex; align-items:center; justify-content:center; color:var(--muted-2); }
        .image-preview.profile { aspect-ratio:1; min-height:120px; }
        .image-preview img { width:100%; height:100%; object-fit:cover; }
        .stat-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
        .stat-box { border:1px solid var(--line); border-radius:8px; padding:14px; background:rgba(255,255,255,.03); }
        .stat-value { font-family:var(--font-display); font-size:28px; line-height:1; }
        .sales-table { border:1px solid var(--line); border-radius:8px; overflow:hidden; }
        .sales-row { display:grid; grid-template-columns:1fr 1fr 1fr 88px; gap:10px; padding:10px 12px; border-bottom:1px solid var(--line); align-items:center; font-size:12px; }
        .sales-row:last-child { border-bottom:0; }
        @media (max-width:1000px){ .line-grid,.form-grid,.preview-grid,.stat-grid{grid-template-columns:1fr;} .info-grid{grid-template-columns:1fr 1fr;} .repeat-row,.sales-row{grid-template-columns:1fr;} .search-input{width:100%;} }
    </style>

    <x-livewire.components.page-header title="LINEAS" subtitle="Gestion operativa, encargado, canales, plataformas y ventas" @if($this->hasLinePermission('line.create')) buttonText="Crear linea" buttonAction="openCreateModal" @endif />

    <div class="lines-page">
        <div style="margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" class="search-input" placeholder="Buscar linea, encargado o plataforma">
            <select wire:model.live="statusFilter" class="filter-select">
                <option value="all">Todos los estados</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
            </select>
        </div>

        @if(session()->has('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif

        <div class="line-section">
            <div class="section-title"><span>Lineas activas</span><span>{{ $activeLines->count() }}</span></div>
            @if($activeLines->isEmpty())
                <div class="empty-state">No hay lineas activas para mostrar.</div>
            @else
                <div class="line-grid">
                    @foreach($activeLines as $line)
                        @include('livewire.partials.line-card', ['line' => $line])
                    @endforeach
                </div>
            @endif
        </div>

        <div class="line-section">
            <div class="section-title"><span>Lineas inactivas</span><span>{{ $inactiveLines->count() }}</span></div>
            @if($inactiveLines->isEmpty())
                <div class="empty-state">No hay lineas inactivas.</div>
            @else
                <div class="line-grid">
                    @foreach($inactiveLines as $line)
                        @include('livewire.partials.line-card', ['line' => $line])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>{{ $editingLineId ? 'EDITAR LINEA' : 'CREAR LINEA' }}</h3>
                    <button class="modal-close" wire:click="closeModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveLine">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">ID automatico</label>
                            <input class="form-input" value="{{ $editingLineId ? '#'.$editingLineId : 'Se genera al guardar' }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select wire:model="status" class="form-input">
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre de la linea</label>
                        <input type="text" wire:model="name" class="form-input" placeholder="Ej: Linea principal">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="preview-grid">
                        <div class="form-group">
                            <x-image-uploader label="Portada 851px x 315px" model="portadaUpload" :upload="$portadaUpload" :value="$portada_url" remove-action="removeImage('portada')" variant="wide">
                                @error('portadaUpload') <div class="form-error">{{ $message }}</div> @enderror
                            </x-image-uploader>
                        </div>
                        <div class="form-group">
                            <x-image-uploader label="Perfil 800px x 800px" model="perfilUpload" :upload="$perfilUpload" :value="$perfil_url" remove-action="removeImage('perfil')" variant="square">
                                @error('perfilUpload') <div class="form-error">{{ $message }}</div> @enderror
                            </x-image-uploader>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Encargado <span style="color:var(--orange)">*</span></label>
                        @error('encargadoId') <div class="form-error">{{ $message }}</div> @enderror
                        <div class="repeat-list" style="margin-bottom:8px">
                            <div class="repeat-row" style="grid-template-columns:1fr 110px 36px">
                                <select wire:model="encargadoId" class="form-input">
                                    <option value="">Seleccionar encargado</option>
                                    @foreach($availableEncargados as $agent)
                                        <option value="{{ $agent->id }}">{{ trim($agent->name.' '.($agent->apellido ?? '')) }}</option>
                                    @endforeach
                                </select>
                                <div style="display:flex;align-items:center;gap:4px">
                                    <input type="number" step="0.5" min="0" max="100"
                                           wire:model="encargadoPercent"
                                           class="form-input" placeholder="0" style="width:70px;text-align:center">
                                    <span style="color:var(--muted);font-size:13px;flex-shrink:0">%</span>
                                </div>
                                <span></span>
                            </div>
                        </div>
                        @error('encargadoPercent') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Canales</label>
                        <div class="repeat-list">
                            @foreach($channels as $index => $channel)
                                <div class="repeat-row" wire:key="channel-{{ $index }}">
                                    <input type="text" wire:model="channels.{{ $index }}.name" class="form-input" placeholder="Nombre de canal">
                                    <input type="text" wire:model="channels.{{ $index }}.url" class="form-input" placeholder="Enlace">
                                    <button type="button" class="btn-icon btn-danger" wire:click="removeChannel({{ $index }})">x</button>
                                </div>
                            @endforeach
                            <button type="button" class="btn-soft" wire:click="addChannel">Agregar canal</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Plataformas disponibles</label>
                        <div class="repeat-list">
                            @foreach($platformRows as $index => $platform)
                                <div class="repeat-row" wire:key="platform-{{ $index }}">
                                    <input type="text" wire:model="platformRows.{{ $index }}.name" class="form-input" placeholder="Nombre plataforma">
                                    <input type="text" wire:model="platformRows.{{ $index }}.url" class="form-input" placeholder="Enlace">
                                    <button type="button" class="btn-icon btn-danger" wire:click="removePlatformRow({{ $index }})">x</button>
                                </div>
                            @endforeach
                            <button type="button" class="btn-soft" wire:click="addPlatformRow">Agregar plataforma</button>
                        </div>
                    </div>

                    <div class="modal-actions" style="justify-content:space-between;border-top:1px solid var(--line);padding-top:18px;">
                        @if($editingLineId)
                            <button type="button" class="btn-soft btn-danger" wire:click="deleteLine({{ $editingLineId }})" wire:confirm="Eliminar esta linea?">Eliminar</button>
                        @else
                            <span></span>
                        @endif
                        <div class="modal-actions">
                            <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                            <button type="submit" class="btn-primary">{{ $editingLineId ? 'Guardar cambios' : 'Crear linea' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showSalesModal && $salesLine)
        <div class="modal-overlay" wire:click.self="closeSalesModal">
            <div class="modal-panel narrow">
                <div class="modal-head">
                    <h3>EDITAR VENTAS</h3>
                    <button class="modal-close" wire:click="closeSalesModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveSale">
                    <div class="form-group">
                        <label class="form-label">Plataforma</label>
                        <select wire:model="salePlatformId" class="form-input">
                            <option value="0">Elegir plataforma</option>
                            @foreach(($salesLine->relationLoaded('platforms') ? $salesLine->getRelation('platforms') : $salesLine->platforms()->get()) as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                        @error('salePlatformId') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Mes de reporte</label>
                            <select wire:model="saleMes" class="form-input">
                                @foreach($months as $num => $label)
                                    <option value="{{ $num }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Anio</label>
                            <input type="number" wire:model="saleAnio" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" wire:model="saleFechaInicio" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha fin</label>
                            <input type="date" wire:model="saleFechaFin" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monto fichas vendidas</label>
                        <input type="number" step="0.01" min="0" wire:model="saleMontoFichas" class="form-input">
                        @error('saleMontoFichas') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                        <button type="button" wire:click="closeSalesModal" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar venta</button>
                    </div>
                </form>
                <div class="modal-body">
                    <div class="sales-table">
                        @forelse($salesLine->sales()->with('platform')->orderByDesc('anio')->orderByDesc('mes')->get() as $sale)
                            <div class="sales-row">
                                <div>{{ $sale->platform?->name ?? '-' }}</div>
                                <div>{{ $this->monthLabel($sale->mes, $sale->anio) }}</div>
                                <div>${{ number_format((float) $sale->monto_fichas, 2) }}</div>
                                <div class="line-actions">
                                    <button class="btn-icon" wire:click="openSalesModal({{ $salesLine->id }}, {{ $sale->id }})">E</button>
                                    <button class="btn-icon btn-danger" wire:click="deleteSale({{ $sale->id }})">x</button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Todavia no hay ventas cargadas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showDetailsModal && $detailLine)
        @php($stats = $this->statsFor($detailLine))
        <div class="modal-overlay" wire:click.self="closeDetailsModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>VER MAS - {{ strtoupper($detailLine->name) }}</h3>
                    <button class="modal-close" wire:click="closeDetailsModal">x</button>
                </div>
                <div class="modal-body">
                    <div class="stat-grid">
                        <div class="stat-box"><div class="info-label">Mejor mes</div><div class="stat-value">{{ $stats['bestMonth'] ? $this->monthLabel($stats['bestMonth']->mes, $stats['bestMonth']->anio) : '-' }}</div><div class="line-id">${{ number_format((float) ($stats['bestMonth']->total ?? 0), 2) }}</div></div>
                        <div class="stat-box"><div class="info-label">Mejor plataforma del mes</div><div class="stat-value">{{ $stats['bestPlatform']?->platform?->name ?? '-' }}</div><div class="line-id">${{ number_format((float) ($stats['bestPlatform']->total ?? 0), 2) }}</div></div>
                        <div class="stat-box"><div class="info-label">Ventas mes actual</div><div class="stat-value">${{ number_format((float) $stats['monthTotal'], 2) }}</div></div>
                        <div class="stat-box"><div class="info-label">Encargado</div><div class="stat-value">{{ $detailLine->lineAgents->firstWhere('role', 'encargado')?->agent?->name ?? '-' }}</div></div>
                    </div>

                    <div class="form-grid">
                        <div class="stat-box">
                            <div class="info-label">Ultimos 3 meses registrados</div>
                            @forelse($stats['lastMonths'] as $month)
                                <div class="sales-row" style="grid-template-columns:1fr auto;">
                                    <span>{{ $this->monthLabel($month->mes, $month->anio) }}</span>
                                    <strong>${{ number_format((float) $month->total, 2) }}</strong>
                                </div>
                            @empty
                                <div class="line-id">Sin ventas registradas</div>
                            @endforelse
                        </div>
                        <div class="stat-box">
                            <div class="info-label">Ganancia del encargado en el mes</div>
                            @forelse($stats['earnings'] as $earning)
                                <div class="sales-row" style="grid-template-columns:1fr auto;">
                                    <span>{{ $earning['name'] }} ({{ $earning['porcentaje'] }}%)</span>
                                    <strong>${{ number_format((float) $earning['ganancia'], 2) }}</strong>
                                </div>
                            @empty
                                <div class="line-id">Sin encargado asignado</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
