@push('styles')
<style>
    [x-cloak] { display:none !important; }
    .post-page { padding:0; }
    .post-hero { display:block; margin-top:34px; }
    .post-title { font-family:var(--font-display); font-size:68px; line-height:.92; letter-spacing:.02em; margin:0; }
    .post-meta { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:10px; }
    .post-excerpt { color:var(--muted); font-size:15px; line-height:1.6; margin:16px 0 0; max-width:760px; }
    .post-image { width:100vw; margin-left:calc(50% - 50vw); min-height:420px; overflow:hidden; background:radial-gradient(90% 80% at 80% 0%, rgba(255,106,26,.35), transparent 70%), #130807; }
    .post-image img { width:100%; height:100%; object-fit:cover; display:block; }
    .post-body-grid { display:block; margin-top:34px; }
    .post-content { padding:0; min-width:0; }
    .post-comments-panel { border-top:1px solid rgba(255,255,255,.12); padding:32px 0 0; min-width:0; }
    .post-content { color:var(--muted); font-size:15px; line-height:1.75; }
    .post-content p { margin:0 0 18px; }
    .post-content h1, .post-content h2, .post-content h3 { font-family:var(--font-display); color:#fff; line-height:1; margin:28px 0 12px; letter-spacing:.02em; }
    .post-content h1 { font-size:48px; }
    .post-content h2 { font-size:38px; }
    .post-content h3 { font-size:30px; }
    .post-content img { max-width:100%; border-radius:var(--r-md); display:block; margin:18px 0; }
    .post-content ul, .post-content ol { padding-left:22px; margin:12px 0; }
    .post-comments-panel { margin-top:44px; }
    .post-comments-title { font-family:var(--font-display); font-size:38px; line-height:1; margin:0 0 14px; letter-spacing:.02em; }
    .post-comments-head { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:22px; }
    .post-comments-head .post-comments-title { margin:0; }
    .post-comment-form { display:grid; gap:10px; margin-bottom:20px; padding:16px; border:1px solid rgba(255,255,255,.1); border-radius:14px; background:rgba(255,255,255,.025); }
    .post-comment-input { width:100%; min-height:92px; resize:vertical; border:1px solid rgba(255,255,255,.12); border-radius:10px; background:rgba(0,0,0,.18); color:#fff; outline:none; padding:14px; font:600 13px var(--font-body); }
    .post-comment-input:focus { border-color:rgba(255,255,255,.32); box-shadow:0 0 0 3px rgba(255,255,255,.05); }
    .post-comment-error { color:#ff8a8a; font-size:12px; font-weight:800; }
    .post-comment-notice { margin:0 0 16px; padding:12px 14px; border:1px solid rgba(255,106,26,.34); border-radius:12px; background:rgba(255,106,26,.1); color:#fff; font-size:13px; font-weight:800; }
    .post-login-box { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:14px 0; border-bottom:1px solid rgba(255,255,255,.08); margin-bottom:16px; color:var(--muted); font-size:13px; }
    .post-comment-list { display:grid; gap:12px; }
    .post-comment { display:grid; grid-template-columns:42px minmax(0, 1fr); gap:14px; padding:16px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:linear-gradient(180deg, rgba(255,255,255,.045), rgba(255,255,255,.018)); }
    .post-comment.reply { margin-left:56px; background:rgba(255,255,255,.022); border-color:rgba(255,255,255,.07); position:relative; }
    .post-comment.reply::before { content:""; position:absolute; left:-28px; top:24px; width:28px; height:1px; background:rgba(255,255,255,.14); }
    .post-comment-avatar { width:42px; height:42px; border-radius:999px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:linear-gradient(180deg,var(--orange),var(--orange-deep)); color:#1a0702; font-weight:900; box-shadow:0 10px 26px rgba(255,106,26,.18); }
    .post-comment-avatar img { width:100%; height:100%; object-fit:cover; }
    .post-comment-head { display:flex; justify-content:space-between; gap:10px; margin-bottom:5px; }
    .post-comment-name { font-size:13px; font-weight:900; color:#fff; }
    .post-comment-badge { display:inline-flex; align-items:center; width:max-content; margin-left:8px; padding:3px 7px; border-radius:999px; background:rgba(255,106,26,.14); color:var(--orange); border:1px solid rgba(255,106,26,.26); font-size:9px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; vertical-align:middle; }
    .post-comment-date { font-size:10px; color:var(--muted-2); font-weight:800; white-space:nowrap; }
    .post-comment-body { color:rgba(255,255,255,.74); font-size:14px; line-height:1.55; margin:0; }
    .post-pending-note { color:var(--muted-2); font-size:12px; line-height:1.4; }
    .post-comment-actions { margin-top:8px; }
    .post-comment-action { border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.045); color:rgba(255,255,255,.72); font-size:11px; font-weight:900; cursor:pointer; padding:7px 11px; border-radius:999px; text-transform:uppercase; letter-spacing:.08em; }
    .post-comment-action:hover { color:#190702; background:var(--orange); border-color:var(--orange); }
    .post-reply-form { display:grid; gap:8px; margin:12px 0 0; padding:12px; border:1px solid rgba(255,255,255,.08); border-radius:12px; background:rgba(0,0,0,.18); }
    .post-reply-actions { display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap; }
    @media (max-width: 920px) {
        .post-title { font-size:52px; }
    }
    @media (max-width: 620px) {
        .post-hero { margin-top:24px; }
        .post-title { font-size:42px; }
        .post-image { min-height:240px; }
        .post-body-grid { margin-top:26px; }
        .post-comments-panel { margin-top:30px; padding-top:22px; }
        .post-comment, .post-comment.reply { grid-template-columns:1fr; margin-left:0; padding:14px; }
        .post-comment.reply::before { content:none; }
        .post-reply-form { margin-left:0; padding:12px; }
        .post-comment-head { display:block; }
        .post-comment-date { display:block; margin-top:3px; }
    }
</style>
@endpush

@php
    $image = $post->image
        ? (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://', '/storage/']) ? $post->image : asset('storage/'.$post->image))
        : null;
@endphp

<div>
    <section class="post-page">
        <div class="fe-shell">
            <div class="post-image">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $post->title }}">
                @endif
            </div>

            <div class="post-hero">
                <div>
                    <div class="post-meta">
                        {{ $post->published_at?->format('d/m/Y') }}
                        @if($post->category)
                            · {{ $post->category->name }}
                        @endif
                    </div>
                    <h1 class="post-title">{{ $post->title }}</h1>
                    <p class="post-excerpt">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 180) }}</p>
                </div>
            </div>

            <div class="post-body-grid">
                <article class="post-content">
                    {!! $post->content !!}
                </article>

                <aside class="post-comments-panel" x-data="{ openComment: false }">
                    <div class="post-comments-head">
                        <h2 class="post-comments-title">Comentarios</h2>
                        @auth
                            <button type="button" class="fe-btn ghost" @click="openComment = !openComment">
                                <span x-text="openComment ? 'Cancelar' : 'Comentar'"></span>
                            </button>
                        @endauth
                    </div>

                    @if($commentNotice)
                        <div class="post-comment-notice">{{ $commentNotice }}</div>
                    @endif

                    @auth
                        <form class="post-comment-form" x-show="openComment" x-cloak x-transition @submit.prevent="$wire.addComment().then(() => openComment = false)">
                            <textarea wire:model.defer="commentContent" class="post-comment-input" placeholder="Deja tu comentario sobre esta novedad" aria-label="Comentario"></textarea>
                            @error('commentContent') <div class="post-comment-error">{{ $message }}</div> @enderror
                            <div class="post-reply-actions">
                                <button type="button" class="fe-btn ghost" @click="openComment = false">Cancelar</button>
                                <button type="submit" class="fe-btn primary">Enviar comentario</button>
                            </div>
                            <div class="post-pending-note">Tu comentario queda pendiente de aprobación en el panel.</div>
                        </form>
                    @else
                        <div class="post-login-box">
                            <span>Inicia sesion para comentar.</span>
                            <a href="{{ route('login') }}" wire:navigate class="fe-btn ghost">Ingresar</a>
                        </div>
                    @endauth

                    @if($comments->count())
                        <div class="post-comment-list">
                            @foreach($comments as $comment)
                                <article class="post-comment">
                                    <div class="post-comment-avatar">
                                        @if($comment->user?->avatar)
                                            <img src="{{ $comment->user->avatar }}" alt="">
                                        @else
                                            {{ strtoupper(mb_substr($comment->user?->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="post-comment-head">
                                            <div class="post-comment-name">
                                                {{ $comment->user?->name ?? 'Usuario' }}
                                                @if(! $comment->is_approved)
                                                    <span class="post-comment-badge">Pendiente</span>
                                                @endif
                                            </div>
                                            <time class="post-comment-date">{{ $comment->created_at->diffForHumans() }}</time>
                                        </div>
                                        <p class="post-comment-body">{{ $comment->content }}</p>
                                        @auth
                                            <div class="post-comment-actions">
                                                <button type="button" wire:click="startReply({{ $comment->id }})" class="post-comment-action">Responder</button>
                                            </div>
                                            @if($replyTo === $comment->id)
                                                <form wire:submit.prevent="addReply" class="post-reply-form">
                                                    <textarea wire:model.defer="replyContent" class="post-comment-input" placeholder="Escribi tu respuesta" aria-label="Respuesta"></textarea>
                                                    @error('replyContent') <div class="post-comment-error">{{ $message }}</div> @enderror
                                                    <div class="post-reply-actions">
                                                        <button type="button" wire:click="cancelReply" class="fe-btn ghost">Cancelar</button>
                                                        <button type="submit" class="fe-btn primary">Enviar respuesta</button>
                                                    </div>
                                                    <div class="post-pending-note">Tu respuesta queda pendiente de aprobacion en el panel.</div>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </article>

                                @foreach($comment->replies as $reply)
                                    <article class="post-comment reply">
                                        <div class="post-comment-avatar">
                                            @if($reply->user?->avatar)
                                                <img src="{{ $reply->user->avatar }}" alt="">
                                            @else
                                                {{ strtoupper(mb_substr($reply->user?->name ?? 'A', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="post-comment-head">
                                                <div class="post-comment-name">
                                                    {{ $reply->user?->name ?? 'Admin' }}
                                                    @if(! $reply->is_approved)
                                                        <span class="post-comment-badge">Pendiente</span>
                                                    @endif
                                                </div>
                                                <time class="post-comment-date">{{ $reply->created_at->diffForHumans() }}</time>
                                            </div>
                                            <p class="post-comment-body">{{ $reply->content }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            @endforeach
                        </div>
                    @else
                        <div class="empty-panel">Todavia no hay comentarios aprobados.</div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</div>
