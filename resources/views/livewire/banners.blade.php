<div class="page-container">
    <x-livewire.components.page-header title="BANNERS" subtitle="Gestión de banners publicitarios" />

    <style>
        .banners-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; }
        .banners-main-card { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 20px; padding: 22px; box-shadow: 0 12px 40px rgba(0,0,0,0.5); }
        .banner-filter-pills { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
        .bnr-pill { height: 30px; padding: 0 12px; border-radius: 999px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .bnr-pill.active { background: var(--orange); color: #190702; border: none; }
        .bnr-pill.off { background: rgba(255,255,255,0.04); color: #fff; border: 1px solid var(--line-2); }
        .banner-rows { display: grid; gap: 10px; }
        .banner-item { display: grid; grid-template-columns: 56px 2fr 80px 80px 110px 80px; gap: 12px; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); align-items: center; }
        .bnr-thumb { width: 50px; height: 32px; border-radius: 6px; }
        .bnr-name { font-size: 13px; font-weight: 700; }
        .bnr-target { font-size: 10px; color: var(--muted); margin-top: 2px; }
        .bnr-lbl { color: var(--muted); font-size: 9px; font-weight: 700; letter-spacing: 0.08em; }
        .badge-live-b { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(37,196,107,0.12); color: var(--good); white-space: nowrap; }
        .badge-sched-b { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,179,71,0.15); color: var(--warn); white-space: nowrap; }
        .badge-draft-b { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.06); color: var(--muted-2); white-space: nowrap; }
        .preview-side { background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%); border: 1px solid var(--line); border-radius: 20px; padding: 22px; height: fit-content; box-shadow: 0 12px 40px rgba(0,0,0,0.5); }
        .preview-side-title { font-family: var(--font-display); font-size: 18px; margin: 0 0 12px; letter-spacing: 0.04em; }
        .preview-hero-box { aspect-ratio: 16/10; border-radius: 10px; background: linear-gradient(135deg, #1a0808, #3b0e08); border: 1px solid var(--line-2); padding: 16px; display: flex; flex-direction: column; justify-content: flex-end; }
        .preview-meta-grid { margin-top: 14px; display: grid; gap: 8px; font-size: 11px; }
        .preview-meta-row { display: flex; justify-content: space-between; }
    </style>

    @if (session()->has('message'))
        <div style="background: rgba(37,196,107,0.12); border: 1px solid var(--good); border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; color: var(--good); font-size: 13px; font-weight: 700;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div>
            <div style="font-size: 11px; color: var(--muted); letter-spacing: 0.12em; font-weight: 700;">OPERACIÓN</div>
            <div style="font-family: var(--font-display); font-size: 32px; margin-top: 2px; letter-spacing: 0.02em;">Banners y Notificaciones</div>
        </div>
        <button class="btn-primary" style="height: 34px; padding: 0 16px; font-size: 12px;">+ Crear banner</button>
    </div>

    <div class="banners-layout">
        <div class="banners-main-card">
            <p style="font-size: 11px; color: var(--muted); margin: 0 0 14px;">Hero del home, push notifications, banners promocionales</p>
            <div class="banner-filter-pills">
                <button class="bnr-pill active">Todos · 14</button>
                <button class="bnr-pill off">Hero · 3</button>
                <button class="bnr-pill off">Push · 6</button>
                <button class="bnr-pill off">Email · 4</button>
                <button class="bnr-pill off">In-app · 1</button>
            </div>
            <div class="banner-rows">
                @php
                $banners = [
                    ['bg' => 'linear-gradient(135deg,#ff6a1a,#c44d0a)', 'name' => 'Hero · "Bienvenida 200%"',       'target' => 'Todos los visitantes', 'views' => '14.820', 'ctr' => '4.2%',  'badge' => 'live'],
                    ['bg' => 'linear-gradient(135deg,#4a2c6a,#1a0d2a)', 'name' => 'Push · "Tu retiro fue aprobado"','target' => 'Tras aprobar retiro',  'views' => '184',    'ctr' => '92%',   'badge' => 'live'],
                    ['bg' => 'linear-gradient(135deg,#2a4a6a,#0a1a2a)', 'name' => 'Banner · "Mega Slot Inferno"',   'target' => 'Slots fans',           'views' => '8.420',  'ctr' => '3.8%',  'badge' => 'live'],
                    ['bg' => 'linear-gradient(135deg,#6a4a2a,#2a1a0a)', 'name' => 'Email · "Reactivación 7 días"',  'target' => 'Inactivos 7d+',        'views' => '0',      'ctr' => '–',     'badge' => 'sched'],
                    ['bg' => 'linear-gradient(135deg,#4a2a2a,#1a0a0a)', 'name' => 'Hero · "Black Friday Picante"',  'target' => 'Sin definir',          'views' => '0',      'ctr' => '–',     'badge' => 'draft'],
                    ['bg' => 'linear-gradient(135deg,#2a6a4a,#0a2a1a)', 'name' => 'Push · "Bono semanal listo"',   'target' => 'VIP Silver+',          'views' => '421',    'ctr' => '38%',   'badge' => 'live'],
                ];
                @endphp
                @foreach($banners as $banner)
                <div class="banner-item">
                    <div class="bnr-thumb" style="background: {{ $banner['bg'] }};"></div>
                    <div>
                        <div class="bnr-name">{{ $banner['name'] }}</div>
                        <div class="bnr-target">Target: {{ $banner['target'] }}</div>
                    </div>
                    <div>
                        <div class="bnr-lbl">VISTAS</div>
                        <strong>{{ $banner['views'] }}</strong>
                    </div>
                    <div>
                        <div class="bnr-lbl">CTR</div>
                        <strong>{{ $banner['ctr'] }}</strong>
                    </div>
                    <div>
                        @if($banner['badge'] === 'live')
                            <span class="badge-live-b">● Live</span>
                        @elseif($banner['badge'] === 'sched')
                            <span class="badge-sched-b">● Schedule</span>
                        @else
                            <span class="badge-draft-b">● Borrador</span>
                        @endif
                    </div>
                    <button class="btn-ghost" style="height:26px;padding:0 10px;font-size:10px;font-weight:700;">Editar</button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="preview-side">
            <h4 class="preview-side-title">PREVIEW HERO</h4>
            <div class="preview-hero-box">
                <div style="font-size:9px;color:var(--orange);font-weight:800;letter-spacing:0.16em;">● PROMO</div>
                <div style="font-family:var(--font-display);font-size:22px;color:#fff;line-height:1;margin-top:4px;">BIENVENIDA<br/>+200% EXTRA</div>
                <button style="margin-top:10px;height:26px;padding:0 12px;border-radius:6px;font-size:10px;font-weight:700;background:var(--orange);color:#190702;border:none;align-self:flex-start;cursor:pointer;">Reclamar →</button>
            </div>
            <div class="preview-meta-grid">
                <div class="preview-meta-row">
                    <span style="color:var(--muted);">Audiencia</span>
                    <strong>Visitantes nuevos</strong>
                </div>
                <div class="preview-meta-row">
                    <span style="color:var(--muted);">Programación</span>
                    <strong>15 Abr → 30 Abr</strong>
                </div>
                <div class="preview-meta-row">
                    <span style="color:var(--muted);">Variantes A/B</span>
                    <strong>2 activas</strong>
                </div>
                <div class="preview-meta-row">
                    <span style="color:var(--muted);">Conversión</span>
                    <strong style="color:var(--good);">4.2%</strong>
                </div>
            </div>
            <button class="btn-ghost" style="width:100%;height:32px;font-size:11px;font-weight:700;margin-top:12px;">Ver estadísticas completas →</button>
        </div>
    </div>
</div>
