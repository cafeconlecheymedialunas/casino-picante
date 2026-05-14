<div>
@section('header')
    <x-livewire.components.page-header title="BLOG" />
@endsection

@if(session()->has('message'))
<div style="position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;">
    {{ session('message') }}
</div>
@endif

{{-- Modal crear --}}
@if($showPanel)
<div class="nv-modal-overlay" wire:click="closeSidePanel">
    <div class="nv-modal" wire:click.stop>
        <div class="nv-modal-head">
            <span><i class="fa-solid fa-newspaper" style="color:var(--orange);margin-right:8px"></i>Nuevo post</span>
            <button class="modal-close" wire:click="closeSidePanel"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form wire:submit.prevent="savePost" class="nv-modal-body">
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
                <label class="form-label">Categoría</label>
                <select wire:model="category_id" class="form-input">
                    <option value="">Sin categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
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
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                <button type="button" wire:click="closeSidePanel" class="btn-ghost">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Publicar post
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if($this->canCreate())
<div class="module-top-bar" style="display:flex;gap:10px;">
    <button type="button" class="btn-primary" wire:click="openCreatePanel()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Nuevo post
    </button>
    <button type="button" class="btn-ghost" wire:click="openCategoryPanel()">
        <i class="fa-solid fa-tags"></i> Gestionar Categorías
    </button>
</div>
@endif

{{-- Modal Categorías --}}
@if($showCategoryPanel)
<div class="nv-modal-overlay" wire:click="$set('showCategoryPanel', false)">
    <div class="nv-modal" wire:click.stop style="width:min(400px, 100%)">
        <div class="nv-modal-head">
            <span><i class="fa-solid fa-tags" style="color:var(--orange);margin-right:8px"></i>Categorías</span>
            <button class="modal-close" wire:click="$set('showCategoryPanel', false)"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="nv-modal-body">
            @if(session()->has('category_message'))
                <div style="background:var(--good);color:#000;padding:8px 12px;border-radius:6px;font-size:12px;margin-bottom:12px;font-weight:700;">
                    {{ session('category_message') }}
                </div>
            @endif

            <form wire:submit.prevent="saveCategory" style="display:flex;gap:8px;margin-bottom:16px;">
                <input type="text" wire:model="newCategoryName" class="form-input" placeholder="Nueva categoría..." style="flex:1">
                <button type="submit" class="btn-primary" style="padding:0 12px;"><i class="fa-solid fa-plus"></i></button>
            </form>
            @error('newCategoryName') <div class="form-error" style="margin-top:-12px;margin-bottom:12px;">{{ $message }}</div> @enderror

            <div style="display:grid;gap:6px;">
                @foreach($categories as $category)
                <div style="display:flex;justify-content:between;align-items:center;padding:8px 12px;background:rgba(255,255,255,0.03);border:1px solid var(--line);border-radius:8px;">
                    <span style="font-size:13px;flex:1">{{ $category->name }}</span>
                    <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="¿Eliminar categoría? Los posts quedarán sin categoría." 
                        class="nv-action-btn danger" style="width:24px;height:24px;font-size:10px;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div style="padding:0 28px 28px;">
    <div class="filter-row">
        <div class="filter-box">
            <input type="text" placeholder="Buscar..." wire:model.live="search" class="search-input">
        </div>
        <div class="filter-box" style="max-width:200px">
            <select wire:model.live="categoryFilter" class="form-input" style="height:38px;font-size:12px;">
                <option value="all">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
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
        <a href="{{ route('blog.edit', $post->id) }}" class="nv-item" wire:navigate>
            <div class="nv-thumb">
                @if($post->image)
                    <img src="{{ $post->image }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="fa-solid fa-newspaper" style="font-size:18px;color:var(--muted)"></i>
                @endif
            </div>
            <div class="nv-info">
                <div class="nv-title">{{ $post->title }}</div>
                <div class="nv-date">
                    {{ $post->created_at->format('d/m/Y') }} 
                    @if($post->category)
                        · <span style="color:var(--orange)">{{ $post->category->name }}</span>
                    @endif
                </div>
            </div>
            <span class="nv-status status-{{ $post->status }}">
                @if($post->status === 'published')● Publicado
                @elseif($post->status === 'draft')● Borrador
                @else● Oculto
                @endif
            </span>
            <div class="nv-actions">
                <span class="nv-action-btn" title="Editar"><i class="fa-solid fa-pen"></i></span>
                @if($canDelete)
                <button class="nv-action-btn danger"
                    wire:click.prevent.stop="deletePost({{ $post->id }})"
                    wire:confirm="¿Eliminar este post?"
                    title="Eliminar">
                    <i class="fa-solid fa-trash"></i>
                </button>
                @endif
            </div>
        </a>
        @empty
        <div class="nv-empty">No hay publicaciones</div>
        @endforelse
    </div>
</div>

<style>
    .filter-row { display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap; }
    .filter-box { flex:1;min-width:150px; }
    .search-input { width:100%;padding:10px 16px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);font-size:12px;color:var(--muted); }
    .status-filters { display:flex;gap:4px;flex-wrap:wrap; }
    .status-filter { padding:8px 12px;border-radius:8px;font-size:10px;font-weight:700;cursor:pointer;background:rgba(255,255,255,0.04);border:1px solid var(--line-2);color:var(--muted);transition:all .2s; }
    .status-filter.active { background:var(--orange);color:#190702;border-color:var(--orange); }

    .nv-list { display:grid;gap:8px; }
    .nv-item { padding:12px 14px;display:grid;grid-template-columns:56px 1fr 100px auto;gap:12px;align-items:center;border-radius:14px;cursor:pointer;transition:all .2s;background:linear-gradient(180deg,#170b0b 0%,#0f0707 100%);border:1px solid var(--line);text-decoration:none;color:inherit; }
    .nv-item:hover { border-color:var(--line-2); }
    .nv-thumb { height:44px;border-radius:8px;overflow:hidden;background:linear-gradient(135deg,var(--black-3),var(--ink));display:flex;align-items:center;justify-content:center; }
    .nv-title { font-weight:700;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .nv-date { font-size:11px;color:var(--muted);margin-top:2px; }
    .nv-status { font-size:11px;font-weight:700;text-align:right; }
    .status-published { color:var(--good); }
    .status-draft { color:var(--warn); }
    .status-hidden { color:var(--muted-2); }
    .nv-actions { display:flex;gap:4px;justify-content:flex-end; }
    .nv-action-btn { width:28px;height:28px;padding:0;background:rgba(255,255,255,0.04);border:1px solid var(--line);border-radius:8px;color:var(--muted);cursor:pointer;font-size:11px;transition:all .2s;display:flex;align-items:center;justify-content:center;text-decoration:none; }
    .nv-action-btn:hover { background:var(--orange);color:#190702;border-color:var(--orange); }
    .nv-action-btn.danger:hover { background:rgba(255,71,87,.15);color:#ff4757;border-color:rgba(255,71,87,.5); }
    .nv-empty { text-align:center;color:var(--muted);padding:40px; }

    /* Modal crear */
    .nv-modal-overlay { position:fixed;inset:0;z-index:400;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.78); }
    .nv-modal { width:min(560px,100%);max-height:92vh;overflow-y:auto;border:1px solid var(--line-2);border-radius:16px;background:linear-gradient(180deg,#1c0e0e,#120909); }
    .nv-modal-head { display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--line);font-family:var(--font-display);font-size:18px;letter-spacing:.03em;position:sticky;top:0;background:#1c0e0e;z-index:1; }
    .nv-modal-body { padding:20px; }
    .modal-close { width:32px;height:32px;border:1px solid var(--line);border-radius:7px;background:rgba(255,255,255,.03);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px; }
    .modal-close:hover { border-color:var(--orange);color:var(--orange); }
    .form-group { margin-bottom:16px; }
    .form-label { display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase; }
    .form-input { width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body); }
    .form-input:focus { outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    textarea.form-input { resize:vertical; }
    select.form-input { cursor:pointer; }
    .form-error { margin-top:4px;color:#ff4757;font-size:11px; }
    @media (max-width:768px){
        .filter-row{ flex-direction:column; }
        .status-filters{ width:100%; }
        .status-filter{ flex:1;text-align:center;padding:7px 8px;font-size:9px; }
        .nv-item{ grid-template-columns:44px 1fr auto;padding:10px 12px;gap:8px; }
        .nv-thumb{ height:34px; }
        .nv-title{ font-size:12px; }
        .nv-date{ font-size:10px; }
        .nv-status{ display:none; }
        .nv-actions{ gap:2px; }
        .nv-action-btn{ width:24px;height:24px;font-size:10px; }
        .nv-modal{ border-radius:12px; }
        .nv-modal-head{ padding:14px 16px;font-size:16px; }
        .nv-modal-body{ padding:16px; }
        .nv-modal-overlay{ padding:12px;align-items:flex-end; }
    }
    @media (max-width:480px){
        .nv-item{ grid-template-columns:36px 1fr; }
        .nv-actions{ display:none; }
    }
</style>
</div>
