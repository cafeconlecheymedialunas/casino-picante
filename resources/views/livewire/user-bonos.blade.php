<div class="page-container">
    @livewire(\App\Livewire\Components\PageHeader::class, [
        'title' => 'BONOS USUARIOS',
        'subtitle' => 'Bonos asignados a usuarios específicos',
        'buttonText' => 'Nuevo Bono',
        'buttonAction' => 'openCreate',
    ])

    <style>
        .ub-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 20px; }
        .ub-stat { background: linear-gradient(180deg,#1c0d0a,#120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 16px; }
        .ub-stat-lbl { font-size: 10px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; }
        .ub-stat-val { font-family: var(--font-display); font-size: 28px; margin-top: 4px; }
        .ub-tabs { display: flex; gap: 4px; margin-bottom: 20px; }
        .ub-tab { padding: 8px 18px; border-radius: 999px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; transition: all 0.2s; }
        .ub-tab.active { background: var(--orange); color: #190702; }
        .ub-tab.off { background: rgba(255,255,255,0.06); color: var(--muted); }
        .ub-card { background: linear-gradient(180deg,#170b0b,#0f0707); border: 1px solid var(--line); border-radius: 20px; padding: 22px; }
        .ub-thead { display: grid; grid-template-columns: 2fr 1fr 1fr 90px 90px 80px; gap: 12px; font-size: 10px; color: var(--muted); padding: 8px 0; border-bottom: 1px solid var(--line); font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; }
        .ub-row { display: grid; grid-template-columns: 2fr 1fr 1fr 90px 90px 80px; gap: 12px; font-size: 12px; padding: 12px 0; border-bottom: 1px solid var(--line); align-items: center; }
        .ub-row:last-child { border-bottom: none; }
        .badge-general { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,106,26,0.12); color: var(--orange); }
        .badge-specific { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,179,71,0.12); color: var(--warn); }
        .badge-active { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); }
        .badge-paused { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); }
        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .modal-box { background: #1a0909; border: 1px solid var(--line-warm); border-radius: 20px; padding: 28px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-family: var(--font-display); font-size: 24px; margin-bottom: 20px; }
        .mf { margin-bottom: 14px; }
        .mf label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: 0.06em; margin-bottom: 5px; }
        .mf input, .mf select, .mf textarea { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--line-2); border-radius: 8px; padding: 9px 12px; color: #fff; font-size: 13px; font-family: var(--font-body); outline: none; }
        .mf input:focus, .mf select:focus, .mf textarea:focus { border-color: var(--orange); }
        .mf select option { background: #1a0909; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .fe-err { color: #ff4757; font-size: 11px; margin-top: 3px; }
        /* Assignment table */
        .assign-thead { display: grid; grid-template-columns: 2fr 2fr 100px 110px; gap: 12px; font-size: 10px; color: var(--muted); padding: 8px 0; border-bottom: 1px solid var(--line); font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; }
        .assign-row { display: grid; grid-template-columns: 2fr 2fr 100px 110px; gap: 12px; font-size: 12px; padding: 10px 0; border-bottom: 1px solid var(--line); align-items: center; }
        .assign-row:last-child { border-bottom: none; }
        .status-btn { height: 26px; padding: 0 8px; border-radius: 6px; font-size: 10px; font-weight: 700; cursor: pointer; border: none; }
    </style>

    @if (session()->has('message'))
        <div style="background:rgba(37,196,107,0.12);border:1px solid var(--good);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:var(--good);font-size:13px;font-weight:700;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <div style="font-size:11px;color:var(--muted);letter-spacing:0.12em;font-weight:700;">OPERACIÓN</div>
            <div style="font-family:var(--font-display);font-size:32px;margin-top:2px;">Bonos de Usuarios</div>
        </div>
        <button wire:click="openCreate" class="btn-primary" style="height:36px;padding:0 18px;font-size:12px;">+ Nuevo bono</button>
    </div>

    {{-- Stats --}}
    <div class="ub-stats">
        <div class="ub-stat">
            <div class="ub-stat-lbl">TOTAL BONOS</div>
            <div class="ub-stat-val">{{ $bonuses->total() }}</div>
        </div>
        <div class="ub-stat">
            <div class="ub-stat-lbl">ACTIVOS</div>
            <div class="ub-stat-val" style="color:var(--good);">{{ \App\Models\Bonus::where('status','active')->where('end_date','>=',now())->count() }}</div>
        </div>
        <div class="ub-stat">
            <div class="ub-stat-lbl">GENERALES</div>
            <div class="ub-stat-val">{{ \App\Models\Bonus::where('type','general')->count() }}</div>
        </div>
        <div class="ub-stat">
            <div class="ub-stat-lbl">ESPECÍFICOS</div>
            <div class="ub-stat-val">{{ \App\Models\Bonus::where('type','specific')->count() }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="ub-tabs">
        <button wire:click="$set('activeTab','bonuses')" class="ub-tab {{ $activeTab === 'bonuses' ? 'active' : 'off' }}">Bonos</button>
        <button wire:click="$set('activeTab','assignments')" class="ub-tab {{ $activeTab === 'assignments' ? 'active' : 'off' }}">Usuarios con bonos</button>
    </div>

    {{-- BONUSES TAB --}}
    @if($activeTab === 'bonuses')
    <div class="ub-card">
        <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
            <input wire:model.live="search" type="text" placeholder="Buscar bono..." style="flex:1;min-width:200px;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:8px 14px;color:#fff;font-size:12px;outline:none;">
            <select wire:model.live="filterType" style="background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:8px 12px;color:#fff;font-size:12px;outline:none;">
                <option value="all">Todos los tipos</option>
                <option value="general">General</option>
                <option value="specific">Específico</option>
            </select>
            <select wire:model.live="filterStatus" style="background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:8px 12px;color:#fff;font-size:12px;outline:none;">
                <option value="all">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="paused">Pausado</option>
            </select>
        </div>
        <div class="ub-thead">
            <div>Bono</div><div>Tipo</div><div>Vigencia</div><div>Estado</div><div>Usuario</div><div></div>
        </div>
        @forelse($bonuses as $bonus)
        <div class="ub-row">
            <div>
                <div style="font-weight:700;">{{ $bonus->title }}</div>
                @if($bonus->description)
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ Str::limit($bonus->description, 60) }}</div>
                @endif
            </div>
            <div>
                <span class="{{ $bonus->type === 'general' ? 'badge-general' : 'badge-specific' }}">
                    {{ $bonus->type === 'general' ? 'General' : 'Específico' }}
                </span>
            </div>
            <div style="font-size:11px;color:var(--muted);">
                {{ $bonus->start_date->format('d/m/y') }}<br>→ {{ $bonus->end_date->format('d/m/y') }}
            </div>
            <div>
                <span class="{{ $bonus->status === 'active' ? 'badge-active' : 'badge-paused' }}">
                    {{ $bonus->status === 'active' ? '● Activo' : '● Pausado' }}
                </span>
                @if($bonus->isExpired())
                <div style="font-size:10px;color:var(--muted);margin-top:2px;">Vencido</div>
                @endif
            </div>
            <div style="font-size:11px;color:var(--muted);">
                {{ $bonus->type === 'specific' && $bonus->user ? $bonus->user->name : '—' }}
            </div>
            <div style="display:flex;gap:6px;">
                <button wire:click="openEdit({{ $bonus->id }})" class="btn-ghost" style="height:26px;padding:0 10px;font-size:10px;font-weight:700;">Editar</button>
                <button wire:click="delete({{ $bonus->id }})" class="btn-ghost" style="height:26px;padding:0 10px;font-size:10px;color:#ff4757;border-color:#ff4757;" onclick="return confirm('¿Eliminar este bono?')">✕</button>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--muted);">No hay bonos registrados</div>
        @endforelse

        @if($bonuses->hasPages())
        <div style="margin-top:16px;">{{ $bonuses->links() }}</div>
        @endif
    </div>
    @endif

    {{-- ASSIGNMENTS TAB --}}
    @if($activeTab === 'assignments')
    <div class="ub-card">
        <div style="margin-bottom:16px;">
            <input wire:model.live="assignSearch" type="text" placeholder="Buscar usuario..." style="width:100%;max-width:360px;background:rgba(255,255,255,0.05);border:1px solid var(--line-2);border-radius:999px;padding:8px 14px;color:#fff;font-size:12px;outline:none;">
        </div>
        <div class="assign-thead">
            <div>Usuario</div><div>Bono</div><div>Estado</div><div>Acciones</div>
        </div>
        @forelse($assignments as $assignment)
        <div class="assign-row">
            <div>
                <div style="font-weight:700;">{{ $assignment->user->name ?? '–' }}</div>
                <div style="font-size:11px;color:var(--muted);">{{ $assignment->user->email ?? '' }}</div>
            </div>
            <div style="font-size:12px;">{{ $assignment->bonus->title ?? '–' }}</div>
            <div>
                @if($assignment->status === 'active')
                    <span class="badge-active">● Activo</span>
                @elseif($assignment->status === 'used')
                    <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:700;background:rgba(255,179,71,0.12);color:var(--warn);">Usado</span>
                @else
                    <span class="badge-paused">Expirado</span>
                @endif
            </div>
            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                <button wire:click="setAssignmentStatus({{ $assignment->bonus_id }},{{ $assignment->user_id }},'active')" class="status-btn" style="background:rgba(37,196,107,0.12);color:var(--good);">Activo</button>
                <button wire:click="setAssignmentStatus({{ $assignment->bonus_id }},{{ $assignment->user_id }},'used')" class="status-btn" style="background:rgba(255,179,71,0.12);color:var(--warn);">Usado</button>
                <button wire:click="setAssignmentStatus({{ $assignment->bonus_id }},{{ $assignment->user_id }},'expired')" class="status-btn" style="background:rgba(255,255,255,0.06);color:var(--muted-2);">Expirado</button>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--muted);">No hay asignaciones registradas</div>
        @endforelse
        @if($assignments->hasPages())
        <div style="margin-top:16px;">{{ $assignments->links() }}</div>
        @endif
    </div>
    @endif

    {{-- CREATE/EDIT MODAL --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal-box">
            <div class="modal-title">{{ $editingBonus ? 'Editar Bono' : 'Nuevo Bono' }}</div>
            <form wire:submit.prevent="save">
                <div class="mf">
                    <label>TÍTULO</label>
                    <input type="text" wire:model="title" placeholder="Ej: Bono de bienvenida">
                    @error('title')<div class="fe-err">{{ $message }}</div>@enderror
                </div>
                <div class="mf">
                    <label>DESCRIPCIÓN</label>
                    <textarea wire:model="description" rows="3" placeholder="Descripción del bono..."></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="mf">
                        <label>FECHA INICIO</label>
                        <input type="date" wire:model="start_date">
                        @error('start_date')<div class="fe-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="mf">
                        <label>FECHA FIN</label>
                        <input type="date" wire:model="end_date">
                        @error('end_date')<div class="fe-err">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mf">
                    <label>TIPO</label>
                    <select wire:model.live="type">
                        <option value="general">General (todos los usuarios)</option>
                        <option value="specific">Específico (usuario concreto)</option>
                    </select>
                </div>
                @if($type === 'specific')
                <div class="mf">
                    <label>USUARIO</label>
                    <select wire:model="user_id">
                        <option value="">— Seleccionar usuario —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="fe-err">{{ $message }}</div>@enderror
                </div>
                @endif
                <div class="mf">
                    <label>ESTADO</label>
                    <select wire:model="status">
                        <option value="active">Activo</option>
                        <option value="paused">Pausado</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary" style="padding:10px 22px;font-size:13px;">
                        {{ $editingBonus ? 'Actualizar' : 'Crear bono' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
