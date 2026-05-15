@push('styles')
<style>
    .blog-page-hero { padding:56px 0 0; }
    .blog-page-head {
        display:grid;
        grid-template-columns:minmax(0, 1fr) minmax(280px, 390px);
        gap:24px;
        align-items:end;
    }
    .blog-page-title {
        font-family:var(--font-display);
        font-size:72px;
        line-height:.9;
        letter-spacing:.02em;
        margin:0;
        max-width:780px;
    }
    .blog-page-title span { color:var(--orange); }
    .blog-search {
        height:46px;
        width:100%;
        border:1px solid var(--line-2);
        border-radius:999px;
        background:rgba(255,255,255,.05);
        color:#fff;
        outline:none;
        padding:0 18px;
        font:800 13px var(--font-body);
    }
    .blog-search:focus { border-color:var(--orange); box-shadow:0 0 0 4px rgba(255,106,26,.12); }
    .blog-layout {
        display:grid;
        grid-template-columns:minmax(0, 1.25fr) minmax(260px, .75fr);
        gap:16px;
        margin-top:28px;
    }
    .blog-featured {
        display:grid;
        grid-template-columns:minmax(260px, .95fr) minmax(0, 1fr);
        min-height:360px;
        overflow:hidden;
        border:1px solid var(--line-warm);
        border-radius:var(--r-xl);
        background:radial-gradient(100% 80% at 0% 0%, rgba(255,106,26,.22), transparent 62%), linear-gradient(135deg,#1b0808,#090505);
        box-shadow:0 22px 64px rgba(0,0,0,.36);
    }
    .blog-featured-media {
        min-height:280px;
        background:radial-gradient(90% 80% at 70% 20%, rgba(255,106,26,.42), transparent 66%), #130807;
        overflow:hidden;
    }
    .blog-featured-media img { width:100%; height:100%; object-fit:cover; display:block; }
    .blog-featured-body { padding:28px; align-self:end; }
    .blog-date { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; }
    .blog-featured h2 {
        font-family:var(--font-display);
        font-size:48px;
        line-height:.96;
        letter-spacing:.02em;
        margin:10px 0 12px;
    }
    .blog-featured p, .blog-aside-card p { color:var(--muted); font-size:14px; line-height:1.55; margin:0; }
    .blog-aside { display:grid; gap:12px; }
    .blog-aside-card {
        min-height:112px;
        padding:18px;
        border:1px solid var(--line);
        border-radius:var(--r-md);
        background:linear-gradient(180deg,#170b0b,#0f0707);
    }
    .blog-aside-card h3 { margin:8px 0; font-size:18px; line-height:1.2; }
    .blog-page-grid {
        display:grid;
        grid-template-columns:repeat(3, minmax(0, 1fr));
        gap:14px;
        margin-top:18px;
    }
    .blog-card {
        display:flex;
        flex-direction:column;
        min-width:0;
        overflow:hidden;
        border:1px solid var(--line);
        border-radius:var(--r-md);
        background:linear-gradient(180deg,#170b0b,#0f0707);
        box-shadow:0 16px 42px rgba(0,0,0,.32);
    }
    .blog-thumb {
        height:168px;
        background:radial-gradient(80% 100% at 80% 10%, rgba(255,106,26,.35), transparent 70%), #140909;
        display:flex;
        align-items:end;
        justify-content:flex-end;
        padding:12px;
        overflow:hidden;
    }
    .blog-thumb img {
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
        margin:-12px;
    }
    .blog-thumb span {
        font-family:var(--font-display);
        font-size:24px;
        color:rgba(255,255,255,.82);
    }
    .blog-body { padding:16px; }
    .blog-body time {
        display:block;
        color:var(--orange);
        font-size:11px;
        font-weight:900;
        letter-spacing:.08em;
        line-height:1.35;
    }
    .blog-author { display:block; margin-top:6px; color:rgba(255,255,255,.62); font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .blog-body h3 {
        font-size:18px;
        line-height:1.2;
        margin:8px 0;
        color:#fff;
    }
    .blog-body p {
        color:var(--muted);
        font-size:13px;
        line-height:1.45;
        margin:0;
    }
    .blog-mobile-grid { display:none; }
    .blog-empty { margin-top:28px; }
    @media (max-width: 980px) {
        .blog-page-head, .blog-layout, .blog-featured { grid-template-columns:1fr; }
        .blog-page-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); }
        .blog-page-title { font-size:56px; }
    }
    @media (max-width: 620px) {
        .blog-page-hero { padding-top:34px; }
        .blog-page-title { font-size:44px; }
        .blog-layout, .blog-page-grid { display:none; }
        .blog-mobile-grid {
            display:grid;
            grid-template-columns:1fr;
            gap:14px;
            margin-top:22px;
        }
        .blog-thumb { height:190px; }
        .blog-body h3 { font-size:17px; }
        .blog-featured-body, .blog-aside-card, .blog-body { padding:18px; }
        .blog-featured h2 { font-size:36px; overflow-wrap:anywhere; }
        .blog-date, .blog-author { overflow-wrap:anywhere; }
    }
</style>
@endpush

<div>
    <section class="blog-page-hero">
        <div class="fe-shell">
            <div class="blog-page-head">
                <div>
                    <div class="fe-kicker">Noticias y jugadas</div>
                    <h1 class="blog-page-title">Novedades de <span>RED PICANTES</span></h1>
                    <p class="fe-subtitle">Promos, sorteos activos, recomendaciones para jugar mejor y avisos importantes del casino.</p>
                </div>

                <input
                    wire:model.live.debounce.300ms="search"
                    class="blog-search"
                    type="search"
                    placeholder="Buscar novedades"
                    aria-label="Buscar novedades"
                >
            </div>

            @if($featuredPost)
                <div class="blog-layout">
                    @php
                        $featuredImage = $featuredPost->image
                            ? (\Illuminate\Support\Str::startsWith($featuredPost->image, ['http://', 'https://', '/storage/']) ? $featuredPost->image : asset('storage/'.$featuredPost->image))
                            : null;
                    @endphp
                    <a class="blog-featured" href="{{ route('frontend.blog.show', $featuredPost->slug) }}" wire:navigate style="text-decoration:none;color:inherit">
                        <div class="blog-featured-media">
                            @if($featuredImage)
                                <img src="{{ $featuredImage }}" alt="{{ $featuredPost->title }}">
                            @endif
                        </div>
                        <div class="blog-featured-body">
                            <div class="blog-date">
                                {{ $featuredPost->published_at?->format('d/m/Y') }}
                                @if($featuredPost->category)
                                    · {{ $featuredPost->category->name }}
                                @endif
                            </div>
                            <span class="blog-author">Autor: {{ $featuredPost->authorAgent?->username ?: $featuredPost->authorAgent?->name ?: 'RED PICANTES BET' }}</span>
                            <h2>{{ $featuredPost->title }}</h2>
                            <p>{{ $featuredPost->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($featuredPost->content), 180) }}</p>
                        </div>
                    </a>

                    <div class="blog-aside">
                        @foreach($asidePosts as $post)
                            <a class="blog-aside-card" href="{{ route('frontend.blog.show', $post->slug) }}" wire:navigate style="text-decoration:none;color:inherit">
                                <div class="blog-date">
                                    {{ $post->published_at?->format('d/m/Y') }}
                                    @if($post->category)
                                        · {{ $post->category->name }}
                                    @endif
                                </div>
                                <span class="blog-author">Autor: {{ $post->authorAgent?->username ?: $post->authorAgent?->name ?: 'RED PICANTES BET' }}</span>
                                <h3>{{ $post->title }}</h3>
                                <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 92) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($remainingPosts->count())
                    <div class="blog-page-grid">
                        @foreach($remainingPosts as $post)
                            @include('frontend.components.blog-card', ['post' => $post])
                        @endforeach
                    </div>
                @endif

                <div class="blog-mobile-grid">
                    @foreach($posts as $post)
                        @include('frontend.components.blog-card', ['post' => $post])
                    @endforeach
                </div>
            @else
                <div class="empty-panel blog-empty">No hay novedades publicadas por ahora.</div>
            @endif
        </div>
    </section>
</div>
