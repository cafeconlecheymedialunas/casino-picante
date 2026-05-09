<div>
@section('header')
    <x-livewire.components.page-header title="BLOG" />
@endsection

    @if($showModal)
    <div class="nv-modal-overlay" wire:click="closeModal">
        <div class="modal-panel" wire:click.stop>
            <div class="modal-head">
                <h3><i class="fa-solid {{ $editingPost ? 'fa-pen-to-square' : 'fa-newspaper' }}" style="color:var(--orange);margin-right:8px"></i>{{ $editingPost ? 'Editar post' : 'Nuevo post' }}</h3>
                <button class="modal-close" wire:click="closeModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form class="modal-form" wire:submit.prevent="savePost">

                <div class="form-group">
                    <label class="form-label">Título</label>
                    <input type="text" wire:model="title" class="form-input" placeholder="Título del post">
                    @error('title') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Resumen breve</label>
                    <input type="text" wire:model="excerpt" class="form-input" placeholder="Breve descripción...">
                </div>

                <div class="form-group">
                    <label class="form-label">Contenido</label>
                    <textarea wire:model="content" rows="4" class="form-input" style="resize:vertical" placeholder="Contenido completo..."></textarea>
                </div>

                <div class="form-group">
                    <x-upload-image label="Imagen destacada" model="imageUpload" :value="$image" remove-action="removeImage" aspect="16/9">
                        @error('imageUpload') <div class="form-error">{{ $message }}</div> @enderror
                    </x-upload-image>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select wire:model="status" class="form-input">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                        <option value="hidden">Oculto</option>
                    </select>
                </div>

                <div class="modal-actions">
                    @if($editingPost)
                    <button type="button" class="btn-ghost" style="color:#ff4757;border-color:rgba(255,71,87,.4);margin-right:auto"
                        wire:click="deletePost({{ $editingPost->id }})"
                        wire:confirm="¿Eliminar esta novedad?">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                    @endif
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $editingPost ? 'Guardar cambios' : 'Publicar post' }}
                    </button>
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

    @if($this->canCreate())
    <div class="module-top-bar">
        <button type="button" class="btn-primary" wire:click="openCreateModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Nuevo post
        </button>
    </div>
    @endif

    <div class="content-grid">
        <div>
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
            <div class="search-box" style="margin-bottom:12px;">
                <input type="text" placeholder="Buscar..." wire:model="search" class="search-input" style="width:100%;padding:10px 16px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);font-size:12px;color:var(--muted);">
            </div>

            <div class="list">
                @forelse($posts as $post)
                <div class="list-item {{ $selectedPost && $selectedPost->id === $post->id ? 'selected' : '' }}" wire:click="selectPost({{ $post->id }})">
                    <div class="list-thumb">{{ $post->image ? '🖼️' : '📝' }}</div>
                    <div>
                        <div class="list-title">{{ $post->title }}</div>
                        <div class="list-date">{{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</div>
                    </div>
                    <span class="list-status status-{{ $post->status }}">
                        @if($post->status === 'published')● Publicado
                        @elseif($post->status === 'draft')● Borrador
                        @else● Oculto
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
                    @if($selectedPost->image)
                    <img src="{{ $selectedPost->image }}" alt="{{ $selectedPost->title }}" style="width:100%;height:100%;object-fit:cover;">
                    @endif
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

            {{-- Comments Section --}}
            @if($selectedPost->comments->count())
            <div style="margin-top:24px; border-top:1px solid var(--line); padding-top:20px;">
                <div style="font-size:11px; font-weight:700; color:var(--orange); letter-spacing:0.08em; margin-bottom:12px;">
                    COMENTARIOS ({{ $selectedPost->comments->count() }})
                </div>
                @foreach($selectedPost->comments as $comment)
                <div style="padding:12px; border-radius:10px; background:rgba(255,255,255,0.03); border:1px solid var(--line); margin-bottom:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                        <div>
                            <div style="font-weight:700; font-size:13px;">{{ $comment->user?->name ?? 'Anónimo' }}</div>
                            <div style="font-size:10px; color:var(--muted);">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                        @if($canModerateComments)
                        <div style="display:flex; gap:4px;">
                            @if(!$comment->is_approved)
                            <button wire:click="approveComment({{ $comment->id }})" style="padding:4px 8px; font-size:10px; background:rgba(37,196,107,0.12); color:var(--good); border:1px solid rgba(37,196,107,0.3); border-radius:4px; cursor:pointer;">Aprobar</button>
                            @endif
                            <button wire:click="deleteComment({{ $comment->id }})" style="padding:4px 8px; font-size:10px; background:rgba(255,71,87,0.12); color:#ff4757; border:1px solid rgba(255,71,87,0.3); border-radius:4px; cursor:pointer;">×</button>
                        </div>
                        @endif
                    </div>
                    <div style="font-size:13px; color:var(--muted-2);">{{ $comment->content }}</div>
                </div>
                @endforeach
            </div>
            @endif>

            {{-- Add Comment --}}
            <div style="margin-top:16px;">
                <form wire:submit.prevent="addComment">
                    <div style="display:flex; gap:8px;">
                        <input type="text" wire:model="newComment" placeholder="Escribí un comentario..." style="flex:1; background:rgba(255,255,255,0.04); border:1px solid var(--line-2); border-radius:8px; padding:8px 12px; color:#fff; font-size:13px;">
                        <button type="submit" style="padding:8px 16px; background:var(--orange); color:#190702; border:none; border-radius:8px; font-weight:700; font-size:12px; cursor:pointer;">Comentar</button>
                    </div>
                    @error('newComment') <div style="color:#ff4757; font-size:11px; margin-top:4px;">{{ $message }}</div> @enderror
                </form>
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
        .field-input { padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); color: #fff; font-size: 13px; width: 100%; }
        .field-textarea { padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); color: var(--muted); width: 100%; min-height: 120px; resize: vertical; }
        
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
        
        /* ── Modal ───────────────────────────────────────────────────── */
        .nv-modal-overlay { position:fixed;inset:0;z-index:400;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.78); }
        .modal-panel { width:min(580px,100%);max-height:92vh;overflow-y:auto;border:1px solid var(--line-2);border-radius:8px;background:linear-gradient(180deg,#1c0e0e,#120909); }
        .modal-head { display:flex;justify-content:space-between;align-items:center;gap:16px;padding:18px 22px;border-bottom:1px solid var(--line);position:sticky;top:0;background:#1c0e0e;z-index:1; }
        .modal-head h3 { margin:0;font-family:var(--font-display);font-size:22px;letter-spacing:.03em;display:flex;align-items:center; }
        .modal-close { width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px; }
        .modal-close:hover { border-color:var(--orange);color:var(--orange); }
        .modal-form { padding:22px; }
        .modal-actions { display:flex;gap:10px;justify-content:flex-end;margin-top:24px;flex-wrap:wrap; }
        /* ── Form ────────────────────────────────────────────────────── */
        .nv-form-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
        .form-group { margin-bottom:16px; }
        .form-label { display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase; }
        .form-input { width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body); }
        .form-input:focus { outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        textarea.form-input { resize:vertical; }
        select.form-input { cursor:pointer; }
        .form-error { margin-top:4px;color:#ff4757;font-size:11px; }
    </style>
</div>
