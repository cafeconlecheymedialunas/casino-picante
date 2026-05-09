<div class="page-container">
    <style>
        .bonus-page { display:flex; flex-direction:column; gap:18px; }
        .header-tools, .table-tools, .action-row, .modal-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .search-input, .filter-select, .form-input { background:rgba(255,255,255,.04); border:1px solid var(--line-2); border-radius:7px; padding:9px 12px; color:var(--white); font-size:13px; font-family:var(--font-body); }
        .search-input { width:280px; }
        .search-input:focus, .filter-select:focus, .form-input:focus { outline:none; border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        .stats-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
        .stat-card { border:1px solid var(--line); border-radius:8px; padding:16px 18px; background:linear-gradient(180deg,#170b0b,#0f0707); position:relative; overflow:hidden; }
        .stat-card::before { content:''; position:absolute; inset:0 0 auto; height:2px; background:linear-gradient(90deg,var(--orange),var(--amber)); }
        .stat-label { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
        .stat-value { font-family:var(--font-display); font-size:32px; line-height:1; }
        .table-card { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; }
        .table-top { display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap; padding:16px 18px; border-bottom:1px solid var(--line); }
        .table-title { font-family:var(--font-display); font-size:22px; letter-spacing:.03em; }
        .table-count { color:var(--muted-2); font-size:11px; }
        .table-scroll { overflow-x:auto; }
        .t-head, .t-row { display:grid; grid-template-columns:64px 1fr 1fr 1fr 1fr 1fr 1fr 150px; gap:12px; align-items:center; min-width:1180px; padding:11px 18px; }
        .t-head { color:var(--muted-2); font-size:10px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; border-bottom:1px solid var(--line); }
        .t-row { border-bottom:1px solid var(--line); font-size:13px; }
        .t-row:hover { background:rgba(255,106,26,.04); }
        .mono { font-family:var(--font-mono); color:var(--muted-2); font-size:11px; }
        .strong { font-weight:800; }
        .truncate { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .badge { display:inline-flex; width:fit-content; align-items:center; border-radius:999px; padding:4px 10px; font-size:10px; font-weight:800; }
        .b-active { color:var(--good); background:rgba(37,196,107,.12); }
        .b-upcoming { color:var(--amber); background:rgba(255,179,71,.12); }
        .b-expired { color:#ff4757; background:rgba(255,71,87,.12); }
        .b-inactive { color:var(--muted); background:rgba(255,255,255,.06); }
        .btn-icon, .btn-soft { height:32px; border:1px solid var(--line); border-radius:7px; background:rgba(255,255,255,.03); color:var(--white); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; }
        .btn-icon { width:32px; color:var(--muted); }
        .btn-soft { padding:0 10px; font-size:11px; font-weight:800; }
        .btn-icon:hover, .btn-soft:hover { border-color:var(--orange); background:rgba(255,106,26,.15); color:var(--white); }
        .btn-danger:hover { border-color:#ff4757; background:rgba(255,71,87,.15); }
        .mini-icon { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .empty-state { padding:56px 24px; color:var(--muted-2); text-align:center; }
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
        .form-input { width:100%; }
        textarea.form-input { resize:vertical; min-height:84px; }
        .form-error { margin-top:4px; color:#ff4757; font-size:11px; }
        .check-row { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:12px; font-weight:700; margin-top:8px; }
        .form-section-title { font-size:10px; font-weight:800; color:var(--orange); letter-spacing:.1em; text-transform:uppercase; margin:18px 0 10px; padding-bottom:6px; border-bottom:1px solid var(--line); }
        .type-btn { flex:1; height:36px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; background:rgba(255,255,255,.04); border:1px solid var(--line-2); color:var(--muted); transition:all .2s; display:flex; align-items:center; justify-content:center; gap:6px; }
        .type-btn.active { background:rgba(255,106,26,.15); color:var(--orange); border-color:rgba(255,106,26,.5); }
        .input-suffix-wrap { position:relative; }
        .input-suffix-wrap .form-input { padding-right:36px; }
        .input-suffix { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:13px; font-weight:700; color:var(--muted); pointer-events:none; }
        .ms-tags { display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px; }
        .ms-tag { display:inline-flex;align-items:center;gap:4px;background:rgba(255,106,26,.15);color:var(--orange);border:1px solid rgba(255,106,26,.4);border-radius:6px;padding:3px 8px;font-size:12px;font-weight:700; }
        .ms-tag-remove { background:none;border:none;color:var(--orange);cursor:pointer;font-size:14px;line-height:1;padding:0 0 0 2px; }
        .ms-dropdown { position:absolute;top:calc(100% + 4px);left:0;right:0;background:#1c0e0e;border:1px solid var(--line-2);border-radius:10px;max-height:220px;overflow-y:auto;z-index:100;box-shadow:0 8px 32px rgba(0,0,0,.5); }
        .ms-option { padding:9px 14px;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .15s; }
        .ms-option:hover { background:rgba(255,255,255,.05); }
        .ms-option.selected { background:rgba(255,106,26,.1);color:var(--orange); }
        .ms-check { color:var(--orange);font-size:11px;width:14px; }
        .ms-empty { padding:14px;text-align:center;color:var(--muted);font-size:13px; }
        .assignments { grid-column:1 / -1; padding:10px 18px 14px; background:rgba(0,0,0,.18); border-bottom:1px solid var(--line); }
        .assignment-row { display:grid; grid-template-columns:1fr 130px 120px; gap:10px; align-items:center; padding:8px 0; border-top:1px solid var(--line); font-size:12px; }
        @media (max-width:900px) { .stats-grid,.form-grid{grid-template-columns:1fr;} .search-input{width:100%;} .assignment-row{grid-template-columns:1fr;} }
    </style>

@section('header')
    <x-livewire.components.page-header title="BONOS" subtitle="Creacion, disponibilidad y otorgamiento de bonos por linea" />
@endsection

    @if($canCreateBonus)
    <div class="module-top-bar">
        <button type="button" class="btn-primary" wire:click="openCreateModal">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Crear bono
        </button>
    </div>
    @endif

    <div class="bonus-page">
        <div style="margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" class="search-input" placeholder="Buscar codigo, nombre o linea">
            <select wire:model.live="filter" class="filter-select">
                <option value="all">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="upcoming">Proximos</option>
                <option value="expired">Vencidos</option>
            </select>
        </div>

        @if(session()->has('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card"><div class="stat-label">Total bonos</div><div class="stat-value">{{ $metrics['total'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Activos</div><div class="stat-value" style="color:var(--good);">{{ $metrics['active'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Proximos</div><div class="stat-value" style="color:var(--amber);">{{ $metrics['upcoming'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Reclamados</div><div class="stat-value" style="color:var(--orange);">{{ $metrics['claimed'] }}</div></div>
        </div>

        <div class="table-card">
            <div class="table-top">
                <div>
                    <div class="table-title">LISTA DE BONOS</div>
                    <div class="table-count">{{ $bonuses->count() }} bono{{ $bonuses->count() !== 1 ? 's' : '' }}</div>
                </div>
            </div>

            @if($bonuses->isEmpty())
                <div class="empty-state">No hay bonos para los filtros seleccionados.</div>
            @else
                <div class="table-scroll">
                    <div class="t-head">
                        <div>ID</div>
                        <div>Codigo</div>
                        <div>Nombre</div>
                        <div>Fecha inicio</div>
                        <div>Fecha fin</div>
                        <div>Bonos reclamados</div>
                        <div>Bonos disponibles</div>
                        <div>Editar</div>
                    </div>

                    @foreach($bonuses as $bonus)
                        <div class="t-row">
                            <div class="mono">#{{ $bonus->id }}</div>
                            <div class="strong truncate">{{ $bonus->code ?? '-' }}</div>
                            <div class="truncate">
                                {{ $bonus->title }}
                                <div><span class="badge b-{{ $bonus->status }}">{{ $bonus->status === 'upcoming' ? 'Proximo' : ($bonus->status === 'expired' ? 'Vencido' : 'Activo') }}</span></div>
                            </div>
                            <div>{{ $bonus->start_date?->format('d/m/Y H:i') }}</div>
                            <div>{{ $bonus->end_date?->format('d/m/Y H:i') }}</div>
                            <div>{{ $bonus->claimed_count }}</div>
                            <div>{{ is_null($bonus->total_quantity) ? 'Ilimitados' : $bonus->remaining_quantity.' / '.$bonus->total_quantity }}</div>
                            <div class="action-row">
                                <button class="btn-icon" wire:click="openAssignModal({{ $bonus->id }})" title="Otorgar bono">
                                    <svg class="mini-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><path d="M12 7a4 4 0 1 0 0 .1"/><path d="M19 8v6M22 11h-6"/></svg>
                                </button>
                                @if($this->hasLinePermission(\App\Support\Permissions::BONO_UPDATE))
                                    <button class="btn-icon" wire:click="openEditModal({{ $bonus->id }})" title="Editar">
                                        <svg class="mini-icon" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>
                                    </button>
                                @endif
                                @if($this->hasLinePermission(\App\Support\Permissions::BONO_DELETE))
                                    <button class="btn-icon btn-danger" wire:click="deleteBonus({{ $bonus->id }})" wire:confirm="Eliminar bono {{ $bonus->code }}?" title="Eliminar">
                                        <svg class="mini-icon" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($bonus->assignments->isNotEmpty())
                            <div class="assignments">
                                @foreach($bonus->assignments->take(6) as $assignment)
                                    <div class="assignment-row">
                                        <div>{{ $assignment->user?->username ?? $assignment->user?->email ?? '-' }}</div>
                                        <div><span class="badge {{ $assignment->status === 'used' ? 'b-active' : 'b-inactive' }}">{{ $assignment->status === 'used' ? 'Reclamado' : 'Otorgado' }}</span></div>
                                        <div>
                                            @if($assignment->status !== 'used')
                                                <button class="btn-soft" wire:click="markClaimed({{ $assignment->id }})">Reclamado</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-head">
                    <h3>{{ $editingBonusId ? 'EDITAR BONO' : 'CREAR BONO' }}</h3>
                    <button class="modal-close" wire:click="closeModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="saveBonus">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" wire:model="title" class="form-input">
                            @error('title') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Codigo</label>
                            <input type="text" wire:model="code" class="form-input">
                            @error('code') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" wire:model="startDate" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" wire:model="startTime" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha fin</label>
                            <input type="date" wire:model="endDate" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hora fin</label>
                            <input type="time" wire:model="endTime" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Descripcion</label>
                        <textarea wire:model="description" class="form-input"></textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Plataforma</label>
                            <select wire:model="platformId" class="form-input">
                                <option value="">Todas las plataformas</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                            @error('platformId') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <div class="form-input" style="color:var(--muted);">
                                Se calcula por fecha: activo, próximo o vencido.
                            </div>
                        </div>
                    </div>
                    {{-- Tipo de bono --}}
                    <div class="form-group">
                        <label class="form-label">Tipo</label>
                        <div x-data style="display:flex;gap:6px;">
                            <button type="button"
                                wire:click="$set('bonusType','general')"
                                class="type-btn {{ $bonusType === 'general' ? 'active' : '' }}">
                                <i class="fa-solid fa-globe"></i> General
                            </button>
                            <button type="button"
                                wire:click="$set('bonusType','specific')"
                                class="type-btn {{ $bonusType === 'specific' ? 'active' : '' }}">
                                <i class="fa-solid fa-user"></i> Específico
                            </button>
                        </div>
                    </div>
                    @if($bonusType === 'specific')
                    <div class="form-group">
                        <label class="form-label">Usuario</label>
                        <input type="text" wire:model="specificUsername" class="form-input" placeholder="Email o username del cliente">
                        @error('specificUsername') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    @endif

                    {{-- Valor del bono --}}
                    <div class="form-section-title">Valor del bono</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">% de bonificación</label>
                            <div class="input-suffix-wrap">
                                <input type="number" wire:model="bonusPercent" class="form-input" placeholder="Ej: 100" min="0" max="100" step="0.01">
                                <span class="input-suffix">%</span>
                            </div>
                            @error('bonusPercent') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Depósito mínimo</label>
                            <input type="number" wire:model="minDeposit" class="form-input" placeholder="0.00" min="0" step="0.01">
                            @error('minDeposit') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bonus máximo</label>
                            <input type="number" wire:model="maxBonus" class="form-input" placeholder="0.00 (sin límite)" min="0" step="0.01">
                            @error('maxBonus') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Cantidades --}}
                    <div class="form-section-title">Disponibilidad</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Cant. bonos disponibles</label>
                            <input type="number" wire:model="totalQuantity" class="form-input" placeholder="Cantidad" @disabled($unlimitedQuantity)>
                            <label class="check-row"><input type="checkbox" wire:model.live="unlimitedQuantity"> Ilimitados</label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Veces por cliente</label>
                            <input type="number" wire:model="perUserLimit" class="form-input" placeholder="1" @disabled($unlimitedPerUser)>
                            <label class="check-row"><input type="checkbox" wire:model.live="unlimitedPerUser"> Ilimitado por cliente</label>
                        </div>
                    </div>
                    <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                        <button type="button" wire:click="closeModal" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary">{{ $editingBonusId ? 'Guardar cambios' : 'Crear bono' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showAssignModal && $selectedBonus)
        <div class="modal-overlay" wire:click.self="closeAssignModal">
            <div class="modal-panel"
                 x-data="{
                    search: '',
                    open: false,
                    users: @js($this->getUsersForAssign()),
                    selected: @entangle('assignUserIds'),
                    get filtered() { return this.search.length < 1 ? this.users : this.users.filter(u => u.label.toLowerCase().includes(this.search.toLowerCase())) },
                    toggle(id) { const i = this.selected.indexOf(id); i === -1 ? this.selected.push(id) : this.selected.splice(i, 1) },
                    isSelected(id) { return this.selected.includes(id) },
                    labelFor(id) { const u = this.users.find(u => u.id === id); return u ? u.label : id }
                 }"
                 @click.outside="open = false">
                <div class="modal-head">
                    <h3>OTORGAR BONO</h3>
                    <button class="modal-close" wire:click="closeAssignModal">x</button>
                </div>
                <form class="modal-form" wire:submit.prevent="assignToUser">
                    <div class="flash-message" style="margin-bottom:16px;">
                        <i class="fa-solid fa-ticket" style="color:var(--orange);margin-right:6px"></i>
                        {{ $selectedBonus->code }} — {{ $selectedBonus->title }}
                        <span style="color:var(--muted);font-size:11px;margin-left:8px;">{{ $selectedBonus->line->name ?? '' }}</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Usuarios <span x-show="selected.length" x-text="'(' + selected.length + ' seleccionados)'" style="color:var(--orange)"></span></label>

                        {{-- Tags de seleccionados --}}
                        <div class="ms-tags" x-show="selected.length > 0">
                            <template x-for="id in selected" :key="id">
                                <span class="ms-tag">
                                    <span x-text="labelFor(id)"></span>
                                    <button type="button" @click="toggle(id)" class="ms-tag-remove">×</button>
                                </span>
                            </template>
                        </div>

                        {{-- Input buscador + dropdown --}}
                        <div style="position:relative;">
                            <input type="text" x-model="search" @focus="open = true" @click="open = true"
                                placeholder="Buscar usuario..." class="form-input" autocomplete="off">
                            <div class="ms-dropdown" x-show="open" x-transition>
                                <template x-if="filtered.length === 0">
                                    <div class="ms-empty">Sin resultados</div>
                                </template>
                                <template x-for="user in filtered" :key="user.id">
                                    <div class="ms-option" :class="{ selected: isSelected(user.id) }" @click="toggle(user.id)">
                                        <span class="ms-check" x-show="isSelected(user.id)"><i class="fa-solid fa-check"></i></span>
                                        <span x-text="user.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('assignUserIds') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="modal-actions" style="justify-content:flex-end;border-top:1px solid var(--line);padding-top:18px;">
                        <button type="button" wire:click="closeAssignModal" class="btn-soft">Cancelar</button>
                        <button type="submit" class="btn-primary" :disabled="selected.length === 0">
                            Otorgar a <span x-text="selected.length || ''"></span> usuario<span x-show="selected.length !== 1">s</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
