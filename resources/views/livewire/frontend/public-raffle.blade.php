@push('styles')
<style>
    .raffles-page { padding:42px 0 60px; }
    .raffles-header { text-align:center; margin-bottom:42px; }
    .raffles-header h1 { font-family:var(--font-display); font-size:56px; line-height:.9; letter-spacing:.03em; margin:0 0 12px; }
    .raffles-header h1 span { color:var(--orange); }
    .raffles-header p { color:var(--muted); font-size:15px; max-width:600px; margin:0 auto; }
    .raffles-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:20px; }
    .raffle-card { display:flex; flex-direction:column; border:1px solid var(--line); border-radius:var(--r-xl); background:linear-gradient(180deg,#180b08,#0d0707); overflow:hidden; text-decoration:none; transition:transform .25s ease, border-color .25s ease, box-shadow .25s ease; }
    .raffle-card:hover { transform:translateY(-6px); border-color:var(--orange); box-shadow:0 20px 50px rgba(255,106,26,.15); }
    .raffle-card-image { position:relative; height:180px; display:flex; align-items:center; justify-content:center; background:radial-gradient(60% 60% at 50% 30%, rgba(255,106,26,.15), transparent 70%); }
    .raffle-card-image img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
    .raffle-card-placeholder { width:90px; height:90px; display:flex; align-items:center; justify-content:center; }
    .raffle-card-badge { position:absolute; top:12px; right:12px; padding:6px 12px; border-radius:999px; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; }
    .raffle-card-badge.active { background:var(--orange); color:#190702; }
    .raffle-card-badge.finished { background:rgba(255,71,87,.9); color:#fff; }
    .raffle-card-badge.inactive { background:rgba(255,179,71,.9); color:#190702; }
    .raffle-card-body { padding:20px; flex:1; display:flex; flex-direction:column; }
    .raffle-card-title { font-family:var(--font-display); font-size:24px; line-height:1.1; margin:0 0 8px; letter-spacing:.01em; }
    .raffle-card-desc { color:var(--muted); font-size:13px; line-height:1.5; margin:0 0 16px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .raffle-card-meta { display:flex; justify-content:space-between; align-items:center; margin-top:auto; padding-top:16px; border-top:1px solid var(--line); }
    .raffle-card-info { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:12px; }
    .raffle-card-info i { color:var(--orange); }
    .raffle-card-timer { display:flex; align-items:center; gap:6px; font-size:11px; font-weight:800; color:var(--orange); }
    .raffle-card-timer i { font-size:10px; }
    .raffle-card-prizes { display:flex; flex-wrap:wrap; gap:6px; margin-top:12px; padding-top:12px;padding-bottom:12px; border-top:1px solid var(--line); }
    .raffle-prize-tag { display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:rgba(255,106,26,.1); border:1px solid rgba(255,106,26,.25); border-radius:6px; font-size:10px; font-weight:800; color:var(--orange); }
    .raffle-prize-tag i { font-size:8px; }
    .raffle-prize-tag.more { background:rgba(255,255,255,.04); border-color:var(--line); color:var(--muted); }
    .raffle-card-btn { display:block; text-align:center; padding:10px 16px; margin-top:16px; border:1px solid var(--line); border-radius:var(--r-md); color:var(--muted-2); font-size:12px; font-weight:800; transition:all .2s; }
    .raffle-card:hover .raffle-card-btn { border-color:var(--orange); color:var(--orange); }
    .raffles-empty { border:1px dashed var(--line); border-radius:var(--r-xl); padding:60px 20px; color:var(--muted); text-align:center; }
    .raffles-empty i { font-size:48px; margin-bottom:16px; display:block; opacity:.5; }
    @media (max-width: 740px) {
        .raffles-page { padding:28px 0 40px; }
        .raffles-header h1 { font-size:40px; }
        .raffles-grid { grid-template-columns:1fr; gap:16px; }
    }
</style>
@endpush

<section class="raffles-page">
    <div class="fe-shell">
        @if($raffles->isEmpty())
            <div class="raffles-empty">
                <i class="fa-solid fa-ticket-simple"></i>
                <p>No hay sorteos disponibles en este momento.</p>
            </div>
        @else
            <div class="raffles-header">
                <h1>SORTEOS <span>ACTIVOS</span></h1>
                <p>Participa en los sorteos de las lineas activas y acumula chances de ganar premios exclusivos.</p>
            </div>
            <div class="raffles-grid">
                @foreach($raffles as $raffle)
                    @php
                        $prizes = collect($raffle->prizes ?? []);
                        $firstPrize = $prizes->first();
                        $prizeImg = $firstPrize['image'] ?? null;
                        $prizeImageUrl = $prizeImg ? (\Illuminate\Support\Str::startsWith($prizeImg, ['http://', 'https://', '/storage/']) ? $prizeImg : asset('storage/'.$prizeImg)) : null;
                        $statusLabel = $raffle->status === 'active' ? 'Activo' : ($raffle->status === 'finished' ? 'Finalizado' : 'Próximo');
                    @endphp
                    <a class="raffle-card" href="{{ route('frontend.raffles.show', $raffle) }}" wire:navigate>
                        <div class="raffle-card-image">
                            @if($prizeImageUrl)
                                <img src="{{ $prizeImageUrl }}" alt="{{ $firstPrize['name'] ?? 'Premio' }}">
                            @endif
                            <span class="raffle-card-badge {{ $raffle->status }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="raffle-card-body">
                            <h3 class="raffle-card-title">{{ $raffle->title }}</h3>
                            <p class="raffle-card-desc">{{ $raffle->description ?: 'Sorteo disponible para usuarios de Red Picantes.' }}</p>
                            @if($prizes->count())
                                <div class="raffle-card-prizes">
                                    @foreach($prizes->take(3) as $idx => $prize)
                                        <span class="raffle-prize-tag">
                                            <i class="fa-solid fa-trophy"></i>
                                            {{ $prize['position'] ?? $idx + 1 }}° {{ $prize['name'] ?? 'Premio' }}
                                        </span>
                                    @endforeach
                                    @if($prizes->count() > 3)
                                        <span class="raffle-prize-tag more">+{{ $prizes->count() - 3 }} más</span>
                                    @endif
                                </div>
                            @endif
                            <div class="raffle-card-meta">
                                <span class="raffle-card-info">
                                    <i class="fa-solid fa-ticket"></i>
                                    {{ $raffle->numbers_count }} participantes
                                </span>
                                @if($raffle->status === 'active' && ! $raffle->isFinished())
                                    <span class="raffle-card-timer">
                                        <i class="fa-regular fa-clock"></i>
                                        <span class="raffle-countdown" data-end="{{ $raffle->end_date->toIso8601String() }}">calculando...</span>
                                    </span>
                                @else
                                    <span class="raffle-card-timer" style="color: var(--muted);">{{ $raffle->end_date?->format('d/m') }}</span>
                                @endif
                            </div>
                            <span class="raffle-card-btn">Ver detalles</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCountdowns() {
        document.querySelectorAll('.raffle-countdown').forEach(function(el) {
            const end = new Date(el.dataset.end);
            const now = new Date();
            const diff = end - now;
            
            if (diff <= 0) {
                el.textContent = 'Terminado';
                return;
            }
            
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            if (days > 0) {
                el.textContent = days + 'd ' + hours + 'h';
            } else if (hours > 0) {
                el.textContent = hours + 'h ' + minutes + 'm';
            } else {
                el.textContent = minutes + 'm';
            }
        });
    }
    
    updateCountdowns();
    setInterval(updateCountdowns, 60000);
});
</script>
@endpush