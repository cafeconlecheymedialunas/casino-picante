<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">PROMOCIONES</h1>
        </div>
        <button class="btn-primary" wire:click="openCreateModal"><span>+</span> Nueva promo</button>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingPromo ? 'EDITAR PROMOCIÓN' : 'NUEVA PROMOCIÓN' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="savePromo">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" placeholder="Título de la promoción" wire:model="title">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea placeholder="Descripción de la promoción" wire:model="description" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;min-height:80px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select wire:model="type" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="bonus">Bono de depósito</option>
                        <option value="deposit">Depósito</option>
                        <option value="free_spin">Giros gratis</option>
                        <option value="promo">Promoción general</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>% Bono</label>
                        <input type="number" placeholder="10" wire:model="bonus_percent">
                    </div>
                    <div class="form-group">
                        <label>Monto bono</label>
                        <input type="number" placeholder="0" wire:model="bonus_amount">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Depósito mínimo</label>
                        <input type="number" placeholder="0" wire:model="min_deposit">
                    </div>
                    <div class="form-group">
                        <label>Bono máximo</label>
                        <input type="number" placeholder="0" wire:model="max_bonus">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha inicio</label>
                        <input type="date" wire:model="start_date">
                    </div>
                    <div class="form-group">
                        <label>Fecha fin</label>
                        <input type="date" wire:model="end_date">
                    </div>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select wire:model="status" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                    </select>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" wire:model="is_recurring" id="is_recurring" style="width:20px;height:20px;">
                    <label for="is_recurring" style="margin:0;">Promoción recurrente</label>
                </div>
                @if($is_recurring)
                <div class="form-group">
                    <label>Días de la semana</label>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;">
                        @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $index => $day)
                        <button type="button" class="line-btn {{ in_array($index, $recurring_days) ? 'selected' : '' }}"
                            wire:click="
                                @if(in_array($index, $recurring_days))
                                    $set('recurring_days', array_filter($recurring_days, fn($d) => $d !== {{ $index }}))
                                @else
                                    $set('recurring_days', array_merge($recurring_days, [{{ $index }}]))
                                @endif
                            ">{{ $day }}</button>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingPromo ? 'Guardar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(session()->has('message'))
    <div style="position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;">
        {{ session('message') }}
    </div>
    @endif

    <div class="content-grid">
        <div>
            <div class="promo-section">
                <button class="promo-filter {{ $filter === 'all' ? 'active' : '' }}" wire:click="$set('filter', 'all')">Todas ({{ $promotions->count() }})</button>
                <button class="promo-filter {{ $filter === 'active' ? 'active' : '' }}" wire:click="$set('filter', 'active')">Activas</button>
                <button class="promo-filter {{ $filter === 'upcoming' ? 'active' : '' }}" wire:click="$set('filter', 'upcoming')">Próximas</button>
                <button class="promo-filter {{ $filter === 'ended' ? 'active' : '' }}" wire:click="$set('filter', 'ended')">Finalizadas</button>
                <button class="promo-filter {{ $filter === 'draft' ? 'active' : '' }}" wire:click="$set('filter', 'draft')">Borrador</button>
            </div>

            <div class="promo-list">
                @forelse($promotions as $promo)
                <div class="promo-card {{ $selectedPromo && $selectedPromo->id === $promo->id ? 'selected' : '' }}" wire:click="selectPromo({{ $promo->id }})">
                    <div class="promo-icon">{{ $promo->icon }}</div>
                    <div>
                        <div class="promo-title">{{ $promo->title }}</div>
                        <div class="promo-code">{{ $promo->code }}</div>
                    </div>
                    <div class="promo-status {{ $promo->status }}">
                        @if($promo->status === 'active')● Activa
                        @elseif($promo->status === 'upcoming')● Próxima
                        @elseif($promo->status === 'ended')● Finalizada
                        @else● Borrador
                        @endif
                    </div>
                    <div class="promo-lines">
                        @foreach($promo->lines ?? [] as $line)
                        <span class="promo-line">{{ $line }}</span>
                        @endforeach
                    </div>
                    <div class="promo-end">
                        @if($promo->end_date)
                        {{ $promo->end_date->format('d/m H:i') }}
                        @else
                        Recurrente
                        @endif
                    </div>
                    <div class="promo-actions">
                        <button class="btn-ghost promo-btn">✎</button>
                        <button class="btn-ghost promo-btn">···</button>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <p>No hay promociones</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="edit-panel">
            @if($selectedPromo)
            <div class="edit-label">EDITANDO</div>
            <h3 class="edit-title">{{ strtoupper($selectedPromo->title) }}</h3>

            <div class="edit-field">
                <div class="edit-field-label">Título</div>
                <input type="text" class="edit-input" value="{{ $selectedPromo->title }}">
            </div>
            <div class="edit-field">
                <div class="edit-field-label">Descripción</div>
                <textarea class="edit-input tall">{{ $selectedPromo->description }}</textarea>
            </div>
            <div class="edit-field">
                <div class="edit-field-label">Código promocional</div>
                <input type="text" class="edit-input" value="{{ $selectedPromo->code }}">
            </div>
            <div class="edit-field edit-row">
                <div>
                    <div class="edit-field-label">Inicio</div>
                    <input type="datetime-local" class="edit-input" value="{{ $selectedPromo->start_date?->format('Y-m-d H:i') }}">
                </div>
                <div>
                    <div class="edit-field-label">Fin</div>
                    <input type="datetime-local" class="edit-input" value="{{ $selectedPromo->end_date?->format('Y-m-d H:i') }}">
                </div>
            </div>
            <div class="edit-field">
                <div class="edit-field-label">Visible en líneas</div>
                <div class="line-grid">
                    @foreach(['L1','L2','L3','L4','L5','L6'] as $line)
                    <button class="line-btn {{ in_array($line, $selectedPromo->lines ?? []) ? '' : 'inactive' }}">{{ $line }}</button>
                    @endforeach
                </div>
            </div>
            <div class="edit-field">
                <div class="edit-field-label">Estado</div>
                <div class="state-grid">
                    <button class="state-btn {{ $selectedPromo->status === 'draft' ? 'active' : '' }}">Borrador</button>
                    <button class="state-btn {{ $selectedPromo->status === 'published' ? 'active' : '' }}">Publicado</button>
                    <button class="state-btn {{ $selectedPromo->status === 'hidden' ? 'active' : '' }}">Oculto</button>
                </div>
            </div>
            <button class="btn-primary edit-save">Guardar cambios</button>
            @else
            <div class="edit-empty">
                <p>Selecciona una promoción para editarla</p>
            </div>
            @endif
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .content-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; padding: 0 28px 28px; }
        @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }

        .promo-section { display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
        .promo-filter {
            padding: 8px 14px; border-radius: 999px; font-size: 11px; font-weight: 700;
            border: 1px solid var(--line-2); background: transparent; color: var(--muted); cursor: pointer;
        }
        .promo-filter.active { background: var(--orange); color: #190702; border: none; }

        .promo-list { display: grid; gap: 10px; }
        .promo-card {
            background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm);
            border-radius: 14px; padding: 14px; display: grid;
            grid-template-columns: 60px 1.6fr 1fr 1fr 1fr 90px; align-items: center; gap: 14px;
            cursor: pointer; transition: all 0.2s;
        }
        .promo-card:hover { border-color: var(--orange); }
        .promo-card.selected { border-color: rgba(255,106,26,0.5); background: linear-gradient(180deg, rgba(255,106,26,0.06), rgba(20,8,8,0.85)); }
        .promo-icon { width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(135deg, var(--orange), var(--amber)); display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .promo-title { font-weight: 700; font-size: 13px; }
        .promo-code { font-family: var(--font-mono); font-size: 11px; color: var(--muted); margin-top: 2px; }
        .promo-status { font-size: 11px; font-weight: 700; }
        .promo-status.active { color: var(--good); }
        .promo-status.upcoming { color: var(--orange); }
        .promo-status.ended { color: var(--muted-2); }
        .promo-status.draft { color: var(--warn); }
        .promo-lines { display: flex; gap: 3px; flex-wrap: wrap; }
        .promo-line { padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 10px; font-weight: 700; }
        .promo-end { font-size: 11px; color: var(--muted); }
        .promo-actions { display: flex; gap: 6px; justify-content: flex-end; }
        .promo-btn { width: 30px; height: 28px; padding: 0; font-size: 11px; }

        .edit-panel { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; padding: 20px; height: fit-content; position: sticky; top: 20px; }
        .edit-empty { text-align: center; color: var(--muted); padding: 40px; }
        .edit-label { font-size: 10px; color: var(--orange); font-weight: 800; letter-spacing: 0.14em; }
        .edit-title { font-family: var(--font-display); font-size: 24px; margin: 4px 0 14px; letter-spacing: 0.02em; }
        .edit-field { margin-bottom: 12px; }
        .edit-field-label { font-size: 10px; color: var(--muted); font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 6px; }
        .edit-input { padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 12px; width: 100%; color: var(--white); }
        .edit-input.tall { min-height: 56px; }
        .edit-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .line-grid { display: flex; gap: 6px; }
        .line-btn { flex: 1; height: 30px; border-radius: 8px; font-size: 11px; font-weight: 700; background: rgba(255,106,26,0.18); border: 1px solid var(--orange); color: var(--orange); cursor: pointer; }
        .line-btn.inactive { background: rgba(255,255,255,0.04); border-color: var(--line-2); color: var(--muted); }
        .state-grid { display: flex; gap: 6px; }
        .state-btn { flex: 1; height: 30px; border-radius: 8px; font-size: 11px; font-weight: 700; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); color: var(--muted); cursor: pointer; }
        .state-btn.active { background: var(--orange); color: #190702; border: none; }
        .edit-save { height: 38px; font-size: 12px; margin-top: 6px; }

        .empty-state { text-align: center; color: var(--muted); padding: 40px; }
        
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 520px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }
        .line-btn.selected { background: var(--orange); color: #190702; }
    </style>
</div>