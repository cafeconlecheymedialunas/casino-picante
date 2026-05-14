<div>
    <style>
        .blog-bg-glow {
            background: radial-gradient(70% 45% at 80% -5%, rgba(255,106,26,0.55) 0%, transparent 60%),
                        radial-gradient(50% 35% at -10% 30%, rgba(255,138,61,0.18) 0%, transparent 60%),
                        #0a0606;
        }

        .page-header { padding: 40px 0 0; }
        .page-label { font-size: 11px; color: var(--orange); letter-spacing: 0.18em; font-weight: 800; margin-bottom: 8px; }
        .page-title { font-family: var(--font-display); font-size: 60px; margin: 0; letter-spacing: 0.02em; line-height: 0.9; text-transform: uppercase; }
        .page-title span { color: var(--orange); }

        /* Featured */
        .featured-section { padding: 32px 0 0; }
        .featured-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; }
        @media (max-width: 768px) {
            .featured-grid { grid-template-columns: 1fr; }
            .page-title { font-size: 40px; }
        }
        .featured-card {
            overflow: hidden; cursor: pointer; height: 100%;
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line); border-radius: var(--r-lg);
            display: flex; flex-direction: column;
        }
        .featured-img { height: 320px; position: relative; overflow: hidden; }
        .featured-img img { width: 100%; height: 100%; object-fit: cover; }
        .featured-img-placeholder { width: 100%; height: 100%; background: radial-gradient(60% 60% at 50% 50%, #ff8a3d, #1a0606); }
        .featured-chip {
            position: absolute; top: 16px; left: 16px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.06em;
            padding: 4px 12px; border-radius: 999px;
            background: #0a0606; color: var(--orange); border: 1px solid var(--line-warm);
        }
        .featured-body { padding: 22px; flex: 1; display: flex; flex-direction: column; }
        .featured-date { font-size: 11px; color: var(--muted); letter-spacing: 0.1em; font-weight: 700; }
        .featured-title {
            font-family: var(--font-display); font-size: 36px; margin: 8px 0 8px;
            letter-spacing: 0.02em; line-height: 1;
        }
        .featured-desc { font-size: 14px; color: var(--muted); margin: 0; line-height: 1.5; }

        .aside-list { display: grid; gap: 12px; }
        .aside-item {
            padding: 12px; display: flex; gap: 12px; cursor: pointer;
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line); border-radius: var(--r-lg);
            transition: all 0.2s;
        }
        .aside-item:hover { border-color: var(--orange); transform: translateY(-2px); }
        .aside-thumb { width: 110px; height: 80px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
        .aside-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .aside-thumb-placeholder { width: 100%; height: 100%; background: radial-gradient(60% 60% at 50% 50%, #ff6a1a, #1a0606); }
        .aside-content { flex: 1; }
        .aside-date { font-size: 10px; color: var(--muted); letter-spacing: 0.1em; font-weight: 700; }
        .aside-title { font-size: 16px; margin: 4px 0 6px; font-weight: 700; line-height: 1.2; color: #fff; }
        .aside-link { font-size: 11px; color: var(--orange); font-weight: 700; }

        /* Grid */
        .articles-section { padding: 40px 0 40px; }
        .articles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .article-card {
            overflow: hidden; cursor: pointer;
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line); border-radius: var(--r-lg);
            transition: all 0.2s;
        }
        .article-card:hover { border-color: var(--orange); transform: translateY(-2px); }
        .article-img { height: 160px; position: relative; overflow: hidden; }
        .article-img img { width: 100%; height: 100%; object-fit: cover; }
        .article-chip {
            position: absolute; top: 12px; left: 12px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.06em;
            padding: 4px 10px; border-radius: 999px;
            background: rgba(0,0,0,0.8); color: var(--orange); border: 1px solid var(--line-warm);
        }
        .article-body { padding: 16px; }
        .article-date { font-size: 10px; color: var(--muted); letter-spacing: 0.1em; font-weight: 700; }
        .article-title { font-size: 17px; margin: 6px 0 8px; font-weight: 700; line-height: 1.2; color: #fff; }
        .article-link { font-size: 12px; color: var(--orange); font-weight: 700; }
    </style>

    <div class="fe-shell blog-bg-glow">
        <div class="page-header">
            <div class="page-label">● BLOG</div>
            <h1 class="page-title">ESTRATEGIA, NEWS<br/><span>Y MÁS PICANTE</span></h1>
        </div>

        @if($featuredPost)
        <div class="featured-section">
            <div class="featured-grid">
                <a href="#" style="text-decoration: none; color: inherit;">
                    <div class="featured-card">
                        <div class="featured-img">
                            @php
                                $featuredImage = $featuredPost->image
                                    ? (\Illuminate\Support\Str::startsWith($featuredPost->image, ['http://', 'https://', '/storage/']) ? $featuredPost->image : asset('storage/'.$featuredPost->image))
                                    : null;
                            @endphp
                            @if($featuredImage)
                                <img src="{{ $featuredImage }}" alt="{{ $featuredPost->title }}">
                            @else
                                <div class="featured-img-placeholder"></div>
                            @endif
                            <span class="featured-chip">DESTACADO</span>
                        </div>
                        <div class="featured-body">
                            <div class="featured-date">{{ $featuredPost->published_at?->format('d M Y') ?? $featuredPost->created_at->format('d M Y') }} · {{ ceil(str_word_count(strip_tags($featuredPost->content)) / 200) }} MIN LECTURA</div>
                            <h2 class="featured-title">{{ $featuredPost->title }}</h2>
                            <p class="featured-desc">{{ $featuredPost->excerpt ?? Str::limit(strip_tags($featuredPost->content), 120) }}</p>
                        </div>
                    </div>
                </a>

                <div class="aside-list">
                    @foreach($asidePosts as $post)
                    @php
                        $postImage = $post->image
                            ? (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://', '/storage/']) ? $post->image : asset('storage/'.$post->image))
                            : null;
                    @endphp
                    <a href="#" style="text-decoration: none; color: inherit;">
                        <div class="aside-item">
                            <div class="aside-thumb">
                                @if($postImage)
                                    <img src="{{ $postImage }}" alt="{{ $post->title }}">
                                @else
                                    <div class="aside-thumb-placeholder"></div>
                                @endif
                            </div>
                            <div class="aside-content">
                                <div class="aside-date">{{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }} · <span style="color:var(--orange)">{{ strtoupper($post->category?->name ?? 'GENERAL') }}</span></div>
                                <div class="aside-title">{{ $post->title }}</div>
                                <div class="aside-desc" style="font-size: 12px; color: var(--muted); line-height: 1.4; margin-bottom: 8px;">
                                    {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 60) }}
                                </div>
                                <div class="aside-link">Leer →</div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="articles-section">
            <div class="articles-grid">
                @foreach($remainingPosts as $post)
                @php
                    $postImage = $post->image
                        ? (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://', '/storage/']) ? $post->image : asset('storage/'.$post->image))
                        : null;
                @endphp
                <a href="#" style="text-decoration: none; color: inherit;">
                    <div class="article-card">
                        <div class="article-img">
                            @if($postImage)
                                <img src="{{ $postImage }}" alt="{{ $post->title }}">
                            @else
                                <div class="featured-img-placeholder"></div>
                            @endif
                            <span class="article-chip">NEWS</span>
                        </div>
                        <div class="article-body">
                            <div class="article-date">{{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }} · <span style="color:var(--orange)">{{ strtoupper($post->category?->name ?? 'GENERAL') }}</span></div>
                            <h3 class="article-title">{{ $post->title }}</h3>
                            <div class="article-desc" style="font-size: 13px; color: var(--muted); line-height: 1.4; margin-bottom: 12px;">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 80) }}
                            </div>
                            <div class="article-link">Leer artículo →</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            @if($remainingPosts->isEmpty() && (!$featuredPost))
                <div style="text-align: center; padding: 60px 0; color: var(--muted);">
                    <p>No hay novedades publicadas por el momento.</p>
                </div>
            @endif
        </div>
    </div>
</div>
