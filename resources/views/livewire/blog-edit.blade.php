<div>
@section('header')
    <x-livewire.components.page-header title="EDITAR POST" />
@endsection

@if(session()->has('message'))
<div style="position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;">
    {{ session('message') }}
</div>
@endif

<div class="be-wrap">

    {{-- ── Columna izquierda: form ──────────────────────────── --}}
    <div class="be-form-col">
        <div class="be-card">
            <div class="be-card-head">
                <span><i class="fa-solid fa-pen-to-square" style="color:var(--orange);margin-right:8px"></i>Contenido</span>
                <a href="{{ route('novedades') }}" class="btn-ghost" style="height:32px;font-size:11px;padding:0 14px;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
            <form wire:submit.prevent="savePost" class="be-form">

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
                    <textarea wire:model="content" rows="10" class="form-input" style="resize:vertical" placeholder="Contenido completo..."></textarea>
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

                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Columna derecha: comentarios ─────────────────────── --}}
    <div class="be-comments-col">
        <div class="be-card">
            <div class="be-card-head">
                <span>
                    <i class="fa-solid fa-comments" style="color:var(--orange);margin-right:8px"></i>
                    Comentarios
                </span>
                @php $pendingCount = $post->comments->where('is_approved', false)->count(); @endphp
                @if($pendingCount)
                <span class="nv-badge-pending">{{ $pendingCount }} pendiente{{ $pendingCount > 1 ? 's' : '' }}</span>
                @endif
            </div>

            <div class="be-comments">
                @forelse($post->comments as $comment)
                <div class="nv-comment {{ !$comment->is_approved ? 'pending' : '' }}">
                    <div class="nv-comment-head">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="nv-comment-avatar">{{ strtoupper(substr($comment->user?->name ?? 'A', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:700;font-size:13px;">{{ $comment->user?->name ?? 'Anónimo' }}</div>
                                <div style="font-size:10px;color:var(--muted);">{{ $comment->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$comment->is_approved)
                            <span class="nv-tag-pending">Pendiente</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:4px;">
                            @if(!$comment->is_approved)
                            <button wire:click="approveComment({{ $comment->id }})" class="nv-comment-btn approve" title="Aprobar">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            @endif
                            <button wire:click="startReply({{ $comment->id }})" class="nv-comment-btn reply" title="Responder">
                                <i class="fa-solid fa-reply"></i>
                            </button>
                            <button wire:click="deleteComment({{ $comment->id }})" class="nv-comment-btn delete" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="nv-comment-body">{{ $comment->content }}</div>

                    {{-- Respuestas del staff --}}
                    @foreach($comment->replies as $reply)
                    <div class="nv-reply">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                            <div class="nv-comment-avatar staff">{{ strtoupper(substr($reply->user?->name ?? 'S', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:700;font-size:12px;color:var(--orange);">
                                    {{ $reply->user?->name ?? 'Staff' }}
                                    <span style="color:var(--muted);font-weight:400;">· Staff</span>
                                </div>
                                <div style="font-size:10px;color:var(--muted);">{{ $reply->created_at->diffForHumans() }}</div>
                            </div>
                            <button wire:click="deleteComment({{ $reply->id }})" class="nv-comment-btn delete" style="margin-left:auto" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        <div style="font-size:13px;color:var(--muted-2);">{{ $reply->content }}</div>
                    </div>
                    @endforeach

                    {{-- Input de respuesta --}}
                    @if($replyTo === $comment->id)
                    <div class="nv-reply-form">
                        <textarea wire:model="replyContent" rows="3" placeholder="Escribí tu respuesta..."
                            class="form-input" style="resize:none;font-size:13px;"></textarea>
                        @error('replyContent') <div class="form-error">{{ $message }}</div> @enderror
                        <div style="display:flex;gap:6px;margin-top:6px;justify-content:flex-end;">
                            <button type="button" wire:click="cancelReply" class="btn-ghost" style="height:30px;font-size:11px;padding:0 12px;">Cancelar</button>
                            <button type="button" wire:click="submitReply" class="btn-primary" style="height:30px;font-size:11px;padding:0 14px;">
                                <i class="fa-solid fa-paper-plane"></i> Enviar
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div style="text-align:center;color:var(--muted);padding:40px 20px;font-size:13px;">
                    <i class="fa-regular fa-comments" style="font-size:28px;display:block;margin-bottom:10px;opacity:.4"></i>
                    Sin comentarios aún
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<style>
    .be-wrap { display:grid;grid-template-columns:1fr 400px;gap:20px;padding:0 28px 28px;align-items:start; }
    @media (max-width:1100px) { .be-wrap { grid-template-columns:1fr; } }

    .be-card { background:linear-gradient(180deg,#170b0b 0%,#0f0707 100%);border:1px solid var(--line);border-radius:20px;overflow:hidden; }
    .be-card-head { display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--line);font-family:var(--font-display);font-size:16px;letter-spacing:.03em; }
    .be-form { padding:20px; }
    .be-comments { padding:16px;display:flex;flex-direction:column;gap:0; }

    /* Form */
    .form-group { margin-bottom:16px; }
    .form-label { display:block;margin-bottom:6px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase; }
    .form-input { width:100%;background:rgba(255,255,255,.04);border:1px solid var(--line-2);border-radius:7px;padding:9px 12px;color:var(--white);font-size:13px;font-family:var(--font-body); }
    .form-input:focus { outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    textarea.form-input { resize:vertical; }
    select.form-input { cursor:pointer; }
    .form-error { margin-top:4px;color:#ff4757;font-size:11px; }

    /* Comments */
    .nv-badge-pending { background:rgba(255,106,26,.15);color:var(--orange);border:1px solid rgba(255,106,26,.35);border-radius:999px;padding:2px 8px;font-size:10px; }
    .nv-tag-pending { background:rgba(255,193,7,.12);color:#ffc107;border:1px solid rgba(255,193,7,.3);border-radius:4px;padding:1px 6px;font-size:10px;font-weight:700; }
    .nv-comment { padding:12px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid var(--line);margin-bottom:10px; }
    .nv-comment.pending { border-color:rgba(255,193,7,.3);background:rgba(255,193,7,.04); }
    .nv-comment-head { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px; }
    .nv-comment-body { font-size:13px;color:var(--muted-2);padding-left:36px; }
    .nv-comment-avatar { width:28px;height:28px;border-radius:50%;background:rgba(255,106,26,.2);color:var(--orange);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0; }
    .nv-comment-avatar.staff { background:rgba(255,106,26,.35); }
    .nv-comment-btn { width:26px;height:26px;padding:0;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px; }
    .nv-comment-btn.approve { background:rgba(37,196,107,.12);color:var(--good);border:1px solid rgba(37,196,107,.3); }
    .nv-comment-btn.reply { background:rgba(255,255,255,.05);color:var(--muted);border:1px solid var(--line-2); }
    .nv-comment-btn.reply:hover { border-color:var(--orange);color:var(--orange); }
    .nv-comment-btn.delete { background:rgba(255,71,87,.12);color:#ff4757;border:1px solid rgba(255,71,87,.3); }
    .nv-reply { margin-top:10px;margin-left:36px;padding:10px 12px;border-radius:8px;background:rgba(255,106,26,.05);border-left:2px solid rgba(255,106,26,.4); }
    .nv-reply-form { margin-top:10px;margin-left:36px; }
</style>
</div>
