<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">BANNERS & NOTIFICACIONES</h1>
            <p class="page-subtitle">Gestión de banners del carrusel y notificaciones push</p>
        </div>
        <button class="btn-primary"><span>+</span> Nuevo Banner</button>
    </div>

    <div class="content" style="padding: 0 28px 28px;">
        <div class="banners-list">
            @for($i = 1; $i <= 4; $i++)
            <div class="banner-card">
                <div class="banner-thumb"></div>
                <div class="banner-info">
                    <div class="banner-title">Banner Principal {{ $i }}</div>
                    <div class="banner-meta">Posición {{ $i }} · {{ $i % 2 == 0 ? 'Activo' : 'Inactivo' }}</div>
                </div>
                <div class="banner-actions">
                    <button class="btn-ghost">✎</button>
                    <button class="btn-ghost">{{ $i % 2 == 0 ? '⏸' : '▶' }}</button>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding: 0 28px; }
        .page-title { font-family: var(--font-display); font-size: 36px; color: var(--white); margin: 0; }
        .page-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .banners-list { display: grid; gap: 12px; }
        .banner-card { display: flex; align-items: center; gap: 16px; padding: 14px; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line-warm); border-radius: 14px; }
        .banner-thumb { width: 100px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, var(--orange), var(--amber)); }
        .banner-info { flex: 1; }
        .banner-title { font-weight: 700; font-size: 14px; }
        .banner-meta { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .banner-actions { display: flex; gap: 6px; }
    </style>
</div>