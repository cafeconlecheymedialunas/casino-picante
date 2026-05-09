<div>
@section('header')
    <x-livewire.components.page-header title="BLOG" />
@endsection

    @if(session()->has('message'))
    <div style="position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;">
        {{ session('message') }}
    </div>
    @endif

    @if($this->canCreate())
    <div class="module-top-bar">
        <button type="button" class="btn-primary" wire:click="openCreatePanel()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Nuevo post
        </button>
    </div>
    @endif

    <div class="nv-grid {{ $showPanel ? 'panel-open' : '' }}">

        {{-- ── Lista ─────────────────────────────────────────────── --}}
        <div class="nv-list-col">
            <div class="filter-row">
                <div class="filter-box">
                    <input type="text" placeholder="Buscar..." wire:model.live="search" class="search-input">
                </div>
                <div class="status-filters">
                    <button class="status-filter {{ $statusFilter === 'all' ? 'active' : '' }}" wire:click="$set('statusFilter','all')">Todos</button>
                    <button class="status-filter {{ $statusFilter === 'published' ? 'active' : '' }}" wire:click="$set('statusFilter','published')">Publicados</button>
                    <button class="status-filter {{ $statusFilter === 'draft' ? 'active' : '' }}" wire:click="$set('statusFilter','draft')">Borradores</button>
                    <button class="status-filter {{ $statusFilter === 'hidden' ? 'active' : '' }}" wire:click="$set('statusFilter','hidden')">Ocultos</button>
                </div>
            </div>

            <div class="nv-list">
                @forelse($posts as $post)
                <div class="nv-item {{ $selectedPost && $selectedPost->id === $post->id ? 'selected' : '' }}"
                     wire:click="selectPost({{ $post->id }})">
                    <div class="nv-thumb">
                        @if($post->image)
                            <img src="{{ $post->image }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="fa-solid fa-newspaper" style="font-size:18px;color:var(--muted)"></i>
                        @endif
                    </div>
                    <div class="nv-info">
                        <div class="nv-title">{{ $post->title }}</div>
                        <div class="nv-date">{{ $post->created_at->format('d/m/Y') }}</div>
                    </div>
                    <span class="nv-status status-{{ $post->status }}">
                        @if($post->status === 'published')● Publicado
                        @elseif($post->status === 'draft')● Borrador
                        @else● Oculto
                        @endif
                    </span>
                </div>
                @empty
                <div class="nv-empty">No hay publicaciones</div>
                @endforelse
            </div>
        </div>

        {{-- ── Panel lateral (form) ──────────────────────────────── --}}
        @if($showPanel)
        <div class="nv-panel">
            <div class="nv-panel-head">
                <span class="nv-panel-label">
                    <i class="fa-solid {{ $editingPost ? 'fa-pen-to-square' : 'fa-newspaper' }}" style="color:var(--orange);margin-right:8px"></i>
                    {{ $editingPost ? 'Editar post' : 'Nuevo post' }}
                </span>
                <button class="modal-close" wire:click="closeSidePanel"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form wire:submit.prevent="savePost" class="nv-panel-body">

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
                    <textarea wire:model="content" rows="5" class="form-input" style="resize:vertical" placeholder="Contenido completo..."></textarea>
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

                <div class="nv-panel-actions">
                    @if($editingPost)
                    <button type="button" class="btn-ghost" style="color:#ff4757;border-color:rgba(255,71,87,.4);margin-right:auto"
                        wire:click="deletePost({{ $editingPost->id }})"
                        wire:confirm="¿Eliminar este post?">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                    @endif
                    <button type="button" wire:click="closeSidePanel" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $editingPost ? 'Guardar cambios' : 'Publicar post' }}
                    </button>
                </div>
            </form>

            {{-- Comentarios (solo al editar) --}}
            @if($editingPost && $selectedPost)
            <div class="nv-comments">
                <div class="nv-comments-title">COMENTARIOS ({{ $selectedPost->comments->count() }})</div>

                @foreach($selectedPost->comments as $comment)
                <div class="nv-comment">
                    <div class="nv-comment-head">
                        <div>
                            <div style="font-weight:700;font-size:13px;">{{ $comment->user?->name ?? 'Anónimo' }}</div>
                            <div style="font-size:10px;color:var(--muted);">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                        @if($canModerateComments)
                        <div style="display:flex;gap:4px;">
                            @if(!$comment->is_approved)
                            <button wire:click="approveComment({{ $comment->id }})" class="nv-comment-btn approve">Aprobar</button>
                            @endif
                            <button wire:click="deleteComment({{ $comment->id }})" class="nv-comment-btn delete">×</button>
                        </div>
                        @endif
                    </div>
                    <div style="font-size:13px;color:var(--muted-2);">{{ $comment->content }}</div>
                </div>
                @endforeach

                <form wire:submit.prevent="addComment" style="margin-top:12px;">
                    <div style="display:flex;gap:8px;">
                        <input type="text" wire:model="newComment" placeholder="Escribí un comentario..."
                            style="flex:1;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);border-radius:8px;padding:8px 12px;color:#fff;font-size:13px;">
                        <button type="submit" style="padding:8px 16px;background:var(--orange);color:#190702;border:none;border-radius:8px;font-weight:700;font-size:12px;cursor:pointer;">Comentar</button>
                    </div>
                    @error('newComment') <div style="color:#ff4757;font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </form>
            </div>
            @endif
        </div>
        @endif

    </div>

<style>
    .nv-grid { display:grid; grid-template-columns:1fr; gap:20px; padding:0 28px 28px; transition:grid-template-columns .25s; }
    .nv-grid.panel-open { grid-template-columns:1fr 420px; }
    @media (max-width:1100px) { .nv-grid.panel-open { grid-template-columns:1fr; } }

    .filter-row { display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap; }
    .filter-box { flex:1;min-width:150px; }
    .search-input { width:100%;padding:10px 16px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);font-size:12px;color:var(--muted); }
    .status-filters { display:flex;gap:4px;flex-wrap:wrap; }
    .status-filter { padding:8px 12px;border-radius:8px;font-size:10px;font-weight:700;cursor:pointer;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);color:var(--muted);transition:all .2s; }
    .status-filter.active { background:var(--orange);color:#190702;border-color:var(--orange); }

    .nv-list { display:grid;gap:8px; }
    .nv-item { padding:12px 14px;display:grid;grid-template-columns:56px 1fr 100px;gap:12px;align-items:center;border-radius:14px;cursor:pointer;transition:all .2s;background:linear-gradient(180deg,#170b0b 0%,#0f0707 100%);border:1px solid var(--line); }
    .nv-item.selected { border-color:rgba(255,106,26,0.5);background:linear-gradient(180deg,rgba(255,106,26,0.06),rgba(20,8,8,0.85)); }
    .nv-item:hover:not(.selected) { border-color:var(--line-2); }
    .nv-thumb { height:44px;border-radius:8px;overflow:hidden;background:linear-gradient(135deg,var(--black-3),var(--ink));display:flex;align-items:center;justify-content:center; }
    .nv-title { font-weight:700;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .nv-date { font-size:11px;color:var(--muted);margin-top:2px; }
    .nv-status { font-size:11px;font-weight:700;text-align:right; }
    .status-published { color:var(--good); }
    .status-draft { color:var(--warn); }
    .status-hidden { color:var(--muted-2); }
    .nv-empty { text-align:center;color:var(--muted);padding:40px; }

    /* Panel */
    .nv-panel { background:linear-gradient(180deg,#170b0b 0%,#0f0707 100%);border:1px solid var(--line);border-radius:20px;display:flex;flex-direction:column;overflow:hidden; }
    .nv-panel-head { display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--line);background:#1c0e0e;position:sticky;top:0;z-index:1; }
    .nv-panel-label { font-family:var(--font-display);font-size:18px;letter-spacing:.03em;display:flex;align-items:center; }
    .nv-panel-body { padding:20px;flex:1;overflow-y:auto; }
    .nv-panel-actions { display:flex;gap:10px;justify-content:flex-end;margin-top:20px;flex-wrap:wrap; }

    /* Form */
    .form-group { margin-bottom:16px; }
    .form-label { display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase; }
    .form-input { width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body); }
    .form-input:focus { outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    textarea.form-input { resize:vertical; }
    select.form-input { cursor:pointer; }
    .form-error { margin-top:4px;color:#ff4757;font-size:11px; }

    /* Comments */
    .nv-comments { padding:20px;border-top:1px solid var(--line); }
    .nv-comments-title { font-size:11px;font-weight:700;color:var(--orange);letter-spacing:.08em;margin-bottom:12px; }
    .nv-comment { padding:12px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid var(--line);margin-bottom:8px; }
    .nv-comment-head { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px; }
    .nv-comment-btn { padding:4px 8px;font-size:10px;border-radius:4px;cursor:pointer; }
    .nv-comment-btn.approve { background:rgba(37,196,107,.12);color:var(--good);border:1px solid rgba(37,196,107,.3); }
    .nv-comment-btn.delete { background:rgba(255,71,87,.12);color:#ff4757;border:1px solid rgba(255,71,87,.3); }

    .modal-close { width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px; }
    .modal-close:hover { border-color:var(--orange);color:var(--orange); }
</style>
</div>
