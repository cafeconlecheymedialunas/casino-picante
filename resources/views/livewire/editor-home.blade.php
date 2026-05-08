<div class="page-container">
    <style>
        .eh-page { display:flex; flex-direction:column; gap:28px; }
        .eh-section { border:1px solid var(--line); border-radius:14px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; }
        .eh-section-head { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid var(--line); }
        .eh-section-title { font-family:var(--font-display); font-size:20px; letter-spacing:.04em; display:flex; align-items:center; gap:10px; }
        .eh-section-badge { font-size:10px; font-weight:800; color:var(--orange); background:rgba(255,106,26,.12); padding:3px 9px; border-radius:999px; }
        .eh-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:10px; padding:16px 20px; }
        .eh-card { border:1px solid var(--line); border-radius:10px; background:rgba(255,255,255,.02); padding:12px; cursor:pointer; transition:all .18s; position:relative; }
        .eh-card:hover { border-color:var(--orange); background:rgba(255,106,26,.05); }
        .eh-card.selected { border-color:var(--orange); background:rgba(255,106,26,.1); }
        .eh-card.selected::after { content:'✓'; position:absolute; top:8px; right:10px; width:22px; height:22px; border-radius:999px; background:var(--orange); color:#190702; font-size:11px; font-weight:900; display:flex; align-items:center; justify-content:center; }
        .eh-card-img { width:100%; aspect-ratio:851/315; border-radius:6px; background:rgba(255,255,255,.04); object-fit:cover; display:block; margin-bottom:10px; }
        .eh-card-img.placeholder { display:flex; align-items:center; justify-content:center; color:var(--muted-2); font-size:28px; }
        .eh-card-title { font-weight:800; font-size:13px; margin-bottom:4px; }
        .eh-card-meta { font-size:11px; color:var(--muted-2); display:flex; align-items:center; gap:8px; }
        .eh-bonus-value { font-family:var(--font-display); font-size:24px; color:var(--green,var(--good)); }
        .eh-bonus-label { font-size:10px; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; margin-top:4px; }
        .eh-empty { padding:40px 20px; text-align:center; color:var(--muted-2); font-size:13px; }
        .eh-counter { font-size:12px; color:var(--muted); }
        .eh-counter .current { color:var(--orange); font-weight:800; }
        .flash-error { border:1px solid rgba(255,71,87,.35); background:rgba(255,71,87,.12); color:#ff4757; border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; margin-bottom:16px; }
        .flash-success { border:1px solid rgba(37,196,107,.35); background:rgba(37,196,107,.12); color:var(--good); border-radius:8px; padding:12px 14px; font-size:13px; font-weight:700; margin-bottom:16px; }
    </style>

    @section('header')
    <x-livewire.components.page-header title="EDITAR HOME" subtitle="Configura las secciones visibles en la pagina principal" />
@endsection

    @if(session()->has('message_error'))
        <div class="flash-error">{{ session('message_error') }}</div>
    @endif

    @if(session()->has('message_success'))
        <div class="flash-success">{{ session('message_success') }}</div>
    @endif

    <div class="eh-page">

        {{-- CARRUSEL --}}
        <div class="eh-section">
            <div class="eh-section-head">
                <div class="eh-section-title">
                    🖼 IMÁGENES CARROUSEL
                    <span class="eh-section-badge">MAX 5</span>
                </div>
                <div class="eh-counter">
                    Seleccionadas: <span class="current">{{ count($selectedCarousel) }}</span> / 5
                </div>
            </div>
            @if(count($carouselPosts) > 0)
            <div class="eh-grid">
                @foreach($carouselPosts as $post)
                <div class="eh-card {{ in_array($post['id'], $selectedCarousel) ? 'selected' : '' }}"
                     wire:click="toggleCarousel({{ $post['id'] }})">
                    @if($post['image'])
                    <img src="{{ asset('storage/' . $post['image']) }}" class="eh-card-img" alt="{{ $post['title'] }}">
                    @else
                    <div class="eh-card-img placeholder">🖼</div>
                    @endif
                    <div class="eh-card-title">{{ $post['title'] }}</div>
                    <div class="eh-card-meta">
                        <span>{{ \Carbon\Carbon::parse($post['published_at'])->format('d/m/Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="eh-empty">No hay publicaciones de tipo carrusel publicadas. Crea una en <strong>Novedades</strong> con tipo "Carrusel".</div>
            @endif
        </div>

        {{-- BONOS --}}
        <div class="eh-section">
            <div class="eh-section-head">
                <div class="eh-section-title">
                    🎁 BONOS DISPONIBLES
                    <span class="eh-section-badge">MAX 5</span>
                </div>
                <div class="eh-counter">
                    Seleccionados: <span class="current">{{ count($selectedBonuses) }}</span> / 5
                </div>
            </div>
            @if(count($bonusItems) > 0)
            <div class="eh-grid">
                @foreach($bonusItems as $bonus)
                <div class="eh-card {{ in_array($bonus['id'], $selectedBonuses) ? 'selected' : '' }}"
                     wire:click="toggleBonus({{ $bonus['id'] }})">
                    <div class="eh-bonus-value">
                        @if($bonus['bonus_percent'])
                            {{ $bonus['bonus_percent'] }}%
                        @elseif($bonus['bonus_amount'])
                            ${{ number_format($bonus['bonus_amount'], 2) }}
                        @else
                            🎁
                        @endif
                    </div>
                    <div class="eh-card-title">{{ $bonus['title'] }}</div>
                    <div class="eh-card-meta">
                        <span>{{ $bonus['code'] ?? 'Sin código' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="eh-empty">No hay bonos activos disponibles. Crea uno en el módulo de <strong>Bonos</strong>.</div>
            @endif
        </div>

        {{-- BLOG --}}
        <div class="eh-section">
            <div class="eh-section-head">
                <div class="eh-section-title">
                    📝 ENTRADAS DE BLOG
                    <span class="eh-section-badge">MAX 3</span>
                </div>
                <div class="eh-counter">
                    Seleccionadas: <span class="current">{{ count($selectedBlogs) }}</span> / 3
                </div>
            </div>
            @if(count($blogPosts) > 0)
            <div class="eh-grid">
                @foreach($blogPosts as $post)
                <div class="eh-card {{ in_array($post['id'], $selectedBlogs) ? 'selected' : '' }}"
                     wire:click="toggleBlog({{ $post['id'] }})">
                    @if($post['image'])
                    <img src="{{ asset('storage/' . $post['image']) }}" class="eh-card-img" alt="{{ $post['title'] }}">
                    @else
                    <div class="eh-card-img placeholder">📝</div>
                    @endif
                    <div class="eh-card-title">{{ $post['title'] }}</div>
                    <div class="eh-card-meta">
                        <span>{{ \Carbon\Carbon::parse($post['published_at'])->format('d/m/Y') }}</span>
                        @if($post['excerpt'])
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit($post['excerpt'], 40) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="eh-empty">No hay entradas de blog publicadas. Crea una en <strong>Novedades</strong> con tipo "Blog".</div>
            @endif
        </div>

    </div>
</div>
