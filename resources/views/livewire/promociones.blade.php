<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">PROMOCIONES</h1>
        </div>
        <button class="btn-primary"><span>+</span> Nueva promo</button>
    </div>

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
    </style>
</div>