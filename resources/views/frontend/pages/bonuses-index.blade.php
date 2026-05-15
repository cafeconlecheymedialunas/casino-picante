@push('styles')
<style>
    .bonuses-page { padding:46px 0 0; }
    .bonuses-head { display:flex; align-items:end; justify-content:space-between; gap:18px; margin-bottom:22px; flex-wrap:wrap; }
    .bonuses-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .bonuses-title { font-family:var(--font-display); font-size:58px; line-height:.9; margin:8px 0 0; letter-spacing:.02em; }
    .bonuses-title span { color:var(--orange); }
    .bonuses-copy { color:var(--muted); font-size:14px; line-height:1.55; max-width:560px; margin:8px 0 0; }
    .bonuses-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .bonus-public-card { position:relative; min-height:270px; display:flex; flex-direction:column; justify-content:space-between; gap:18px; border:1px solid rgba(255,255,255,.1); border-radius:12px; background:radial-gradient(110% 90% at 0% 0%, rgba(255,106,26,.2), transparent 56%), linear-gradient(180deg,#180907,#090403); padding:22px; box-shadow:0 18px 48px rgba(0,0,0,.34); overflow:hidden; }
    .bonus-public-card::before { content:""; position:absolute; inset:10px; border:2px dashed rgba(255,106,26,.34); border-radius:9px; pointer-events:none; }
    .bonus-public-card.is-disabled { opacity:.72; filter:saturate(.72); }
    .bonus-public-head, .bonus-public-actions { position:relative; z-index:1; }
    .bonus-public-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
    .bonus-public-kicker { display:block; color:var(--orange); font-size:10px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; margin-bottom:8px; }
    .bonus-public-card h3 { font-family:var(--font-display); font-size:34px; line-height:.92; margin:0; letter-spacing:.02em; text-transform:uppercase; }
    .bonus-public-code { max-width:136px; overflow-wrap:anywhere; border:1px solid rgba(255,106,26,.42); border-radius:8px; background:rgba(255,106,26,.1); color:var(--orange); padding:8px 10px; font-family:var(--font-mono); font-size:11px; font-weight:900; text-align:center; }
    .bonus-public-card p { position:relative; z-index:1; color:var(--muted); font-size:13px; line-height:1.45; margin:0; font-weight:700; }
    .bonus-public-meta { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:6px; }
    .bonus-public-meta span { border:1px solid rgba(255,255,255,.1); border-radius:999px; background:rgba(255,255,255,.04); color:rgba(255,255,255,.72); padding:5px 9px; font-size:10px; font-weight:900; }
    .bonus-public-actions { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .bonus-public-state { display:inline-flex; align-items:center; justify-content:center; height:40px; border-radius:999px; padding:0 18px; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.06); color:var(--muted); font-size:12px; font-weight:900; text-transform:uppercase; }
    .bonuses-empty { border:1px dashed var(--line-2); border-radius:12px; color:var(--muted); padding:30px; text-align:center; font-size:13px; font-weight:800; }
    @media (max-width: 980px) { .bonuses-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px) {
        .bonuses-page { padding-top:34px; }
        .bonuses-title { font-size:42px; }
        .bonuses-grid { grid-template-columns:1fr; }
        .bonuses-head .fe-btn { width:100%; }
        .bonus-public-card { min-height:0; padding:18px; }
        .bonus-public-head { display:block; }
        .bonus-public-card h3 { font-size:29px; overflow-wrap:anywhere; }
        .bonus-public-code { display:inline-block; max-width:100%; margin-top:12px; }
        .bonus-public-actions .fe-btn, .bonus-public-state { width:100%; }
    }
</style>
@endpush

<section class="bonuses-page">
    <div class="fe-shell">
        <div class="bonuses-head">
            <div>
                <div class="bonuses-kicker">Bonos disponibles</div>
                <h1 class="bonuses-title">Bonos <span>activos</span></h1>
                <p class="bonuses-copy">Listado completo conectado al modulo de bonos del dashboard. Revisa codigo, linea disponible y vigencia antes de reclamar.</p>
            </div>
            <a href="{{ route('frontend.lines') }}" wire:navigate class="fe-btn ghost">Lineas de atencion</a>
        </div>

        @if($bonuses->count())
            <div class="bonuses-grid">
                @foreach($bonuses as $bonus)
                    @include('frontend.components.bonus-public-card', [
                        'bonus' => $bonus,
                        'assignment' => $assignments->get($bonus->id),
                    ])
                @endforeach
            </div>
        @else
            <div class="bonuses-empty">No hay bonos cargados por el momento.</div>
        @endif
    </div>
</section>
