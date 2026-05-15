@push('styles')
<style>
    .lines-page { padding:56px 0 0; }
    .lines-hero { display:grid; grid-template-columns:minmax(0, 1fr) minmax(260px, 360px); gap:24px; align-items:end; }
    .lines-title { font-family:var(--font-display); font-size:72px; line-height:.9; letter-spacing:.02em; margin:0; max-width:760px; }
    .lines-title span { color:var(--orange); }
    .public-lines-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:16px; margin-top:28px; }
    .lines-empty { margin-top:28px; }
    @media (max-width: 980px) {
        .lines-hero, .public-lines-grid { grid-template-columns:1fr; }
        .lines-title { font-size:56px; }
    }
    @media (max-width: 620px) {
        .lines-page { padding-top:34px; }
        .lines-title { font-size:44px; }
    }
</style>
@endpush

<div>
    <section class="lines-page">
        <div class="fe-shell">
            <div class="lines-hero">
                <div>
                    <div class="fe-kicker">Lineas disponibles</div>
                    <h1 class="lines-title">Elegí una línea <span>activa</span></h1>
                    <p class="fe-subtitle">Pedí tu usuario, consultá plataformas disponibles y contactá al canal asignado para empezar a jugar.</p>
                </div>

            </div>

            @if($lines->count())
                <div class="public-lines-grid">
                    @foreach($lines as $line)
                        @include('frontend.components.line-card', ['line' => $line])
                    @endforeach
                </div>
            @else
                <div class="empty-panel lines-empty">No hay líneas activas publicadas por ahora.</div>
            @endif
        </div>
    </section>
</div>
