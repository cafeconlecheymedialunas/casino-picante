@push('styles')
<style>
    /* ── Page shell ── */
    .blog-page { padding: 52px 0 72px; --page-top-pad: 52px; }

    /* ── Hero ── */
    .blog-hero { margin-bottom: 48px; }

    /* ── Featured card ── */
    .blog-featured-card {
        position: relative; overflow: hidden;
        height: 480px; border-radius: 20px;
        border: 1px solid var(--line-warm);
        background: #0d0505;
        text-decoration: none; color: inherit; display: block;
        transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s;
    }
    .blog-featured-card:hover { transform: translateY(-3px); box-shadow: 0 32px 80px rgba(0,0,0,.52); }
    .blog-featured-card img {
        position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
        transition: transform .5s cubic-bezier(.4,0,.2,1);
    }
    .blog-featured-card:hover img { transform: scale(1.04); }
    .blog-featured-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(5,2,2,.95) 0%, rgba(5,2,2,.6) 45%, rgba(5,2,2,.1) 100%);
    }
    .blog-featured-badge {
        position: absolute; top: 20px; left: 20px;
        display: inline-flex; align-items: center; gap: 6px;
        height: 28px; padding: 0 12px; border-radius: 999px;
        background: var(--orange); color: #190702;
        font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em;
    }
    .blog-featured-content {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 32px;
    }
    .blog-featured-meta {
        display: flex; align-items: center; gap: 12px;
        font-size: 11px; font-weight: 800; color: rgba(255,255,255,.55);
        text-transform: uppercase; letter-spacing: .08em; margin-bottom: 12px;
    }
    .blog-featured-meta span { color: var(--orange); }
    .blog-featured-content h2 {
        font-family: var(--font-display);
        font-size: clamp(28px, 4vw, 48px);
        line-height: .95; letter-spacing: .02em;
        margin: 0 0 12px; color: #fff;
        max-width: 720px;
    }
    .blog-featured-content p {
        color: rgba(255,255,255,.62); font-size: 15px; line-height: 1.5;
        margin: 0; max-width: 560px;
    }

    /* ── Grid ── */
    .blog-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-top: 16px;
    }
    .blog-card-new {
        display: flex; flex-direction: column;
        border: 1px solid var(--line); border-radius: 14px;
        overflow: hidden; background: #0d0505;
        text-decoration: none; color: inherit;
        transition: border-color .2s, transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s;
    }
    .blog-card-new:hover {
        border-color: rgba(255,106,26,.35);
        transform: translateY(-4px);
        box-shadow: 0 20px 52px rgba(0,0,0,.42);
    }
    .blog-card-thumb {
        position: relative; height: 200px; overflow: hidden;
        background: radial-gradient(80% 100% at 70% 10%, rgba(255,106,26,.3), transparent 70%), #110606;
        flex-shrink: 0;
    }
    .blog-card-thumb img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform .4s cubic-bezier(.4,0,.2,1);
    }
    .blog-card-new:hover .blog-card-thumb img { transform: scale(1.06); }
    .blog-card-thumb-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(5,2,2,.7) 0%, transparent 55%);
    }
    .blog-card-category {
        position: absolute; top: 12px; left: 12px;
        height: 22px; padding: 0 10px; border-radius: 999px;
        background: rgba(255,106,26,.9); color: #190702;
        font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em;
        display: inline-flex; align-items: center;
    }
    .blog-card-body { padding: 18px; display: flex; flex-direction: column; flex: 1; }
    .blog-card-body h3 {
        font-size: 17px; line-height: 1.25; margin: 0 0 8px;
        color: #fff; font-weight: 800;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .blog-card-new:hover .blog-card-body h3 { color: rgba(255,255,255,.9); }
    .blog-card-excerpt {
        color: rgba(255,255,255,.5); font-size: 13px; line-height: 1.5; margin: 0;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        flex: 1;
    }
    .blog-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 14px; padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,.07);
        gap: 8px;
    }
    .blog-card-author { display: flex; align-items: center; gap: 7px; min-width: 0; }
    .blog-card-avatar {
        width: 24px; height: 24px; border-radius: 999px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--orange), var(--amber));
        display: flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: 900; color: #190702; font-family: var(--font-display);
        overflow: hidden;
    }
    .blog-card-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .blog-card-author-name { font-size: 11px; font-weight: 800; color: rgba(255,255,255,.5); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .blog-card-date { font-size: 10px; font-weight: 800; color: rgba(255,255,255,.3); flex-shrink: 0; }

    /* ── Empty ── */
    .blog-empty { margin-top: 28px; }

    /* ── Responsive ── */
    @media (max-width: 1020px) {
        .blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .blog-featured-card { height: 400px; }
    }
    @media (max-width: 680px) {
        .blog-page { padding-top: 32px; }
        .blog-hero-head { flex-direction: column; align-items: flex-start; gap: 16px; }
        .blog-search-wrap { width: 100%; }
        .blog-featured-card { height: 320px; }
        .blog-featured-content { padding: 20px; }
        .blog-featured-content h2 { font-size: 26px; }
        .blog-featured-content p { display: none; }
        .blog-grid { grid-template-columns: 1fr; }
        .blog-card-thumb { height: 180px; }
    }
</style>
@endpush

<div>
    <section class="blog-page">
        <div class="fe-shell">
            <x-page-header-novedades
                :section="$pageSection"
                :vendor="$publicVendor ?? null"
                search-model="search"
            />

                @if($featuredPost)
                    @php
                        $featuredImage = $featuredPost->image
                            ? (\Illuminate\Support\Str::startsWith($featuredPost->image, ['http://', 'https://', '/storage/']) ? $featuredPost->image : asset('storage/'.$featuredPost->image))
                            : null;
                    @endphp
                    <a class="blog-featured-card" href="{{ isset($publicVendor) ? route('frontend.cajero.blog.detalle', [$publicVendor, $featuredPost->slug]) : route('frontend.blog.detalle', $featuredPost->slug) }}" wire:navigate>
                        @if($featuredImage)
                            <img src="{{ $featuredImage }}" alt="{{ $featuredPost->title }}">
                        @endif
                        <div class="blog-featured-overlay"></div>
                        @if($featuredPost->category)
                            <div class="blog-featured-badge">
                                <i class="fa-solid fa-tag" style="font-size:9px"></i>
                                {{ $featuredPost->category->name }}
                            </div>
                        @endif
                        <div class="blog-featured-content">
                            <div class="blog-featured-meta">
                                <span>{{ $featuredPost->published_at?->format('d M Y') }}</span>
                                @if($featuredPost->authorAgent)
                                    <span style="opacity:.4">·</span>
                                    {{ $featuredPost->authorAgent->name }} {{ $featuredPost->authorAgent->apellido }}
                                @endif
                            </div>
                            <h2>{{ $featuredPost->title }}</h2>
                            <p>{{ $featuredPost->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($featuredPost->content), 160) }}</p>
                        </div>
                    </a>
                @endif
            </div>

            @if($posts->count() > 1)
                <div class="blog-grid">
                    @foreach($posts->slice(1) as $post)
                        @php
                            $img = $post->image
                                ? (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://', '/storage/']) ? $post->image : asset('storage/'.$post->image))
                                : null;
                            $authorName = $post->authorAgent?->name ?: 'RED PICANTES';
                            $initials = strtoupper(mb_substr($authorName, 0, 2));
                        @endphp
                        <a class="blog-card-new" href="{{ isset($publicVendor) ? route('frontend.cajero.blog.detalle', [$publicVendor, $post->slug]) : route('frontend.blog.detalle', $post->slug) }}" wire:navigate>
                            <div class="blog-card-thumb">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $post->title }}">
                                @endif
                                <div class="blog-card-thumb-overlay"></div>
                                @if($post->category)
                                    <span class="blog-card-category">{{ $post->category->name }}</span>
                                @endif
                            </div>
                            <div class="blog-card-body">
                                <h3>{{ $post->title }}</h3>
                                <p class="blog-card-excerpt">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</p>
                                <div class="blog-card-footer">
                                    <div class="blog-card-author">
                                        <div class="blog-card-avatar">
                                            @if($post->authorAgent?->avatar)
                                                <img src="{{ $post->authorAgent->avatar }}" alt="{{ $authorName }}">
                                            @else
                                                {{ $initials }}
                                            @endif
                                        </div>
                                        <span class="blog-card-author-name">{{ $authorName }}</span>
                                    </div>
                                    <span class="blog-card-date">{{ $post->published_at?->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @elseif(!$featuredPost)
                <div class="empty-panel blog-empty">No hay novedades publicadas por ahora.</div>
            @endif
        </div>
    </section>
</div>
