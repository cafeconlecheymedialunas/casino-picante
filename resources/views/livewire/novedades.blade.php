<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">NOVEDADES</h1>
        </div>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingPost ? 'EDITAR CONTENIDO' : 'NUEVO CONTENIDO' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="savePost">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" placeholder="Título del contenido" wire:model="title">
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select wire:model="type" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="novedad">Novedad</option>
                        <option value="blog">Blog</option>
                        <option value="aviso">Aviso</option>
                        <option value="carrusel">Carrusel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Resumen breve</label>
                    <input type="text" placeholder="Breve descripción..." wire:model="excerpt">
                </div>
                <div class="form-group">
                    <label>Contenido</label>
                    <textarea placeholder="Contenido completo..." wire:model="content" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;min-height:120px;"></textarea>
                </div>
                <div class="form-group">
                    <label>URL de imagen</label>
                    <input type="text" placeholder="https://..." wire:model="image">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select wire:model="status" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                        <option value="hidden">Oculto</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingPost ? 'Guardar' : 'Crear' }}</button>
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
<div class="tab-bar">
                <div class="tabs">
                    <button class="tab {{ $tab === 'novedad' ? 'active' : '' }}" wire:click="setTab('novedad')">Novedades</button>
                    <button class="tab {{ $tab === 'blog' ? 'active' : '' }}" wire:click="setTab('blog')">Blog</button>
                    <button class="tab {{ $tab === 'carrusel' ? 'active' : '' }}" wire:click="setTab('carrusel')">Carrusel</button>
                </div>
                @if($canCreate)
                <button class="btn-primary" style="height: 32px; padding: 0 14px; font-size: 12px;" wire:click="openCreateModal()">+ Nueva</button>
                @endif
            </div>
            <div class="filter-row">
                <div class="filter-box">
                    <input type="text" placeholder="Buscar..." wire:model="search" class="search-input" style="width:100%;padding:10px 16px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);font-size:12px;color:var(--muted);">
                </div>
                <div class="status-filters">
                    <button class="status-filter {{ $statusFilter === 'all' ? 'active' : '' }}" wire:click="setStatusFilter('all')">Todos</button>
                    <button class="status-filter {{ $statusFilter === 'published' ? 'active' : '' }}" wire:click="setStatusFilter('published')">Publicados</button>
                    <button class="status-filter {{ $statusFilter === 'draft' ? 'active' : '' }}" wire:click="setStatusFilter('draft')">Borradores</button>
                    <button class="status-filter {{ $statusFilter === 'hidden' ? 'active' : '' }}" wire:click="setStatusFilter('hidden')">Ocultos</button>
                </div>
            </div>
                <button class="btn-primary" style="height: 32px; padding: 0 14px; font-size: 12px;" wire:click="openCreateModal">+ Nueva</button>
            </div>
            <div class="search-box" style="margin-bottom:12px;">
                <input type="text" placeholder="Buscar..." wire:model="search" class="search-input" style="width:100%;padding:10px 16px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);font-size:12px;color:var(--muted);">
            </div>

            <div class="list">
                @forelse($posts as $post)
                <div class="list-item {{ $selectedPost && $selectedPost->id === $post->id ? 'selected' : '' }}" wire:click="selectPost({{ $post->id }})">
                    <div class="list-thumb">{{ $post->image ? '🖼️' : '📝' }}</div>
                    <div>
                        <div class="list-title">{{ $post->title }}</div>
                        <div class="list-date">{{ $post->published_at?->format('d/m/y') ?? $post->created_at->format('d/m/y') }}</div>
                    </div>
                    <span class="list-status status-{{ $post->status }}">
                        @if($post->status === 'published')● Published
                        @elseif($post->status === 'draft')● Borrador
                        @else● Hidden
                        @endif
                    </span>
                    <div class="list-actions">
                        <button class="action-btn" wire:click.stop="openEditModal({{ $post->id }})">✎</button>
                        <button class="action-btn" wire:click.stop="toggleStatus({{ $post->id }})">{{ $post->status === 'published' ? '🔒' : '👁' }}</button>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <p>No hay publicaciones</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="editor">
            @if($selectedPost)
            <div class="editor-label">EDITANDO</div>
            <h3 class="editor-title">{{ strtoupper($selectedPost->title) }}</h3>

            <div class="field">
                <div class="field-label">Imagen destacada</div>
                <div class="field-image">
                    <button class="field-image-btn">Cambiar imagen</button>
                </div>
            </div>

            <div class="field">
                <div class="field-label">Título</div>
                <input type="text" class="field-input" value="{{ $selectedPost->title }}">
            </div>

            <div class="field">
                <div class="field-label">Descripción breve</div>
                <input type="text" class="field-input" value="{{ $selectedPost->excerpt }}">
            </div>

            <div class="field">
                <div class="field-label">Contenido completo</div>
                <div class="field-textarea">
                    <div class="toolbar">
                        <button class="toolbar-btn">B</button>
                        <button class="toolbar-btn">I</button>
                        <button class="toolbar-btn">U</button>
                        <button class="toolbar-btn">H1</button>
                        <button class="toolbar-btn">H2</button>
                        <button class="toolbar-btn">"</button>
                        <button class="toolbar-btn">🔗</button>
                        <button class="toolbar-btn">📷</button>
                    </div>
                    {{ $selectedPost->content }}
                </div>
            </div>

            <div class="field field-row">
                <div>
                    <div class="field-label">Estado</div>
                    <div class="status-btns">
                        <button class="status-btn {{ $selectedPost->status === 'draft' ? 'active' : '' }}">Borrador</button>
                        <button class="status-btn {{ $selectedPost->status === 'published' ? 'active' : '' }}">Publicado</button>
                        <button class="status-btn {{ $selectedPost->status === 'hidden' ? 'active' : '' }}">Oculto</button>
                    </div>
                </div>
                <div>
                    <div class="field-label">Fecha de publicación</div>
                    <input type="datetime-local" class="field-input" value="{{ $selectedPost->published_at?->format('Y-m-d H:i') }}">
                </div>
            </div>

            <div class="editor-actions">
                <button class="editor-btn-ghost">Vista previa</button>
                <button class="editor-btn-primary">Guardar y publicar</button>
            </div>
            @else
            <div class="editor-empty">
                <p>Selecciona una publicación para editarla</p>
            </div>
            @endif
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .content-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 20px; padding: 0 28px 28px; }
        @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }

        .tab-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
        .tabs { display: flex; gap: 6px; }
        .tab { padding: 8px 14px; border-radius: 999px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s; background: transparent; color: var(--muted); border: 1px solid var(--line-2); }
        .tab.active { background: var(--orange); color: #190702; border: none; }
        
        .filter-row { display: flex; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; }
        .filter-box { flex: 1; min-width: 150px; }
        .status-filters { display: flex; gap: 4px; flex-wrap: wrap; }
        .status-filter { padding: 8px 12px; border-radius: 8px; font-size: 10px; font-weight: 700; cursor: pointer; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); color: var(--muted); transition: all 0.2s; }
        .status-filter.active { background: var(--orange); color: #190702; border-color: var(--orange); }

        .list { display: grid; gap: 10px; }
        .list-item { padding: 12px; display: grid; grid-template-columns: 70px 1fr 90px 80px; gap: 12px; align-items: center; border-radius: 14px; cursor: pointer; transition: all 0.2s; }
        .list-item.selected { border: 1px solid rgba(255,106,26,0.5); background: linear-gradient(180deg, rgba(255,106,26,0.06), rgba(20,8,8,0.85)); }
        .list-item:not(.selected) { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); }
        .list-thumb { height: 50px; border-radius: 8px; overflow: hidden; background: linear-gradient(135deg, var(--black-3), var(--ink)); display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .list-title { font-weight: 700; font-size: 13px; }
        .list-date { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .list-status { font-size: 11px; font-weight: 700; }
        .status-published, .status-published { color: var(--good); }
        .status-draft { color: var(--warn); }
        .status-hidden { color: var(--muted-2); }
        .list-actions { display: flex; gap: 4px; justify-content: flex-end; }
        .action-btn { width: 28px; height: 28px; padding: 0; background: rgba(255,255,255,0.04); border: 1px solid var(--line); border-radius: 999px; color: var(--muted); cursor: pointer; font-size: 11px; transition: all 0.2s; }
        .action-btn:hover { background: var(--orange); color: #190702; border-color: var(--orange); }

        .editor { padding: 20px; background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 20px; }
        .editor-empty { text-align: center; color: var(--muted); padding: 40px; }
        .editor-label { font-size: 10px; color: var(--orange); font-weight: 800; letter-spacing: 0.14em; }
        .editor-title { font-family: var(--font-display); font-size: 22px; margin: 4px 0 16px; letter-spacing: 0.02em; }

        .field { margin-bottom: 14px; }
        .field-label { font-size: 10px; color: var(--muted); font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 6px; }
        .field-image { height: 140px; border-radius: 12px; position: relative; overflow: hidden; border: 1px dashed var(--line-warm); background: radial-gradient(60% 60% at 50% 50%, #ff8a3d, #1a0606); display: flex; align-items: center; justify-content: center; }
        .field-image-btn { position: absolute; right: 10px; bottom: 10px; height: 28px; padding: 0 12px; font-size: 11px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); border-radius: 999px; color: #fff; cursor: pointer; }
        .field-input { padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 13px; color: #fff; width: 100%; }
        .field-textarea { padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 13px; color: var(--muted); width: 100%; min-height: 120px; resize: vertical; }

        .toolbar { display: flex; gap: 6px; padding-bottom: 8px; border-bottom: 1px solid var(--line); margin-bottom: 8px; }
        .toolbar-btn { width: 26px; height: 24px; padding: 0; background: rgba(255,255,255,0.04); border: 1px solid var(--line); border-radius: 4px; color: var(--muted); cursor: pointer; font-size: 10px; font-weight: 700; transition: all 0.2s; }
        .toolbar-btn:hover { background: var(--orange); color: #190702; border-color: var(--orange); }

        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .status-btns { display: flex; gap: 4px; }
        .status-btn { flex: 1; height: 30px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); color: var(--muted); transition: all 0.2s; }
        .status-btn.active { background: var(--orange); color: #190702; border: none; }

        .editor-actions { display: flex; gap: 8px; margin-top: 6px; }
        .editor-btn-ghost { flex: 1; height: 38px; font-size: 12px; font-weight: 700; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); border-radius: 999px; color: #fff; cursor: pointer; }
        .editor-btn-primary { flex: 2; height: 38px; font-size: 12px; background: linear-gradient(180deg, var(--orange-2) 0%, var(--orange) 60%, var(--orange-deep) 100%); border: none; border-radius: 999px; color: #190702; font-weight: 800; cursor: pointer; }

        .empty-state { text-align: center; color: var(--muted); padding: 40px; }
        
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
        .modal-content { background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%); border: 1px solid var(--line); border-radius: 20px; width: 100%; max-width: 520px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--line); }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 10px; padding: 12px 16px; color: var(--white); font-size: 14px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }
    </style>
</div>