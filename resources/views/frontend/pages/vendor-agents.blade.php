@push('styles')
<style>
    .agents-page { padding:46px 0 0; }
    .agents-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .agent-card { border:1px solid rgba(255,255,255,.06); border-radius:8px; padding:18px; }
    @media (max-width: 980px) { .agents-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px) { .agents-grid { grid-template-columns:1fr; } }
</style>
@endpush

<section class="agents-page">
    <div class="fe-shell">
        @include('frontend.components.breadcrumbs', [
            'items' => [
                ['label' => 'Cajeros', 'url' => route('frontend.cajeros')],
                ['label' => $vendor->name, 'url' => route('frontend.cajero.inicio', $vendor)],
                ['label' => 'Agentes'],
            ],
        ])
        <div class="bonuses-head">
            <div>
                <div class="bonuses-kicker">Agentes</div>
                <h1 class="bonuses-title">Equipo del cajero</h1>
                <p class="bonuses-copy">Lista de agentes asignados a las lineas de este cajero.</p>
            </div>
        </div>

        @if($agents->count())
            <div class="agents-grid">
                @foreach($agents as $agent)
                    @include('frontend.components.agent-card', ['agent' => $agent])
                @endforeach
            </div>
        @else
            <div class="bonuses-empty">No hay agentes para este cajero.</div>
        @endif
    </div>
</section>
