@php
    $encargados   = $line->lineAgents->where('role', 'encargado');
    // $line->platforms as property returns the JSON cast (null) due to name collision
    // with the BelongsToMany. Use getRelation() to bypass the cast.
    $linePlatforms = $line->relationLoaded('platforms')
        ? $line->getRelation('platforms')
        : $line->platforms()->get();
    $contactLinks  = $line->contact_links ?? [];
    $canManage     = $this->canManageLine($line);
    $isActive      = $line->status === 'active';
    $initial       = strtoupper(mb_substr($line->name, 0, 2));
@endphp

<div class="line-card {{ $isActive ? '' : 'inactive' }}" wire:key="line-card-{{ $line->id }}">

    {{-- PORTADA --}}
    <div class="line-cover">
        @if($line->portada_url)
            <img src="{{ $line->portada_url }}" alt="{{ $line->name }}">
        @endif

        {{-- Perfil circular --}}
        <div class="line-profile">
            @if($line->perfil_url)
                <img src="{{ $line->perfil_url }}" alt="">
            @else
                <span>{{ $initial }}</span>
            @endif
        </div>

        {{-- Estado + toggle --}}
        <div style="position:absolute;top:10px;right:10px;display:flex;align-items:center;gap:6px">
            <span class="status-badge {{ $isActive ? 'status-active' : 'status-inactive' }}">
                {{ $isActive ? '● Activa' : '○ Inactiva' }}
            </span>
            <button type="button"
                    wire:click="toggleLine({{ $line->id }})"
                    class="btn-icon"
                    title="Cambiar estado"
                    style="background:rgba(0,0,0,.55);border-color:rgba(255,255,255,.18)">
                @if($isActive)
                    <svg class="mini-icon" viewBox="0 0 15 15"><rect x="3" y="2" width="3" height="11" rx="1"/><rect x="9" y="2" width="3" height="11" rx="1"/></svg>
                @else
                    <svg class="mini-icon" viewBox="0 0 15 15"><path d="M5 3l8 4.5L5 12V3z"/></svg>
                @endif
            </button>
        </div>
    </div>

    {{-- BODY --}}
    <div class="line-body">

        <div class="line-top">
            <div>
                <div class="line-name">{{ strtoupper($line->name) }}</div>
                <div class="line-id">#{{ str_pad($line->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        {{-- INFO BOXES --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Encargado</div>
                @forelse($encargados as $la)
                    <div style="font-size:11px;line-height:1.6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ $la->agent?->name ?? '—' }}
                        @if($la->porcentaje_ganancia)
                        <span style="color:var(--orange);font-weight:800;font-size:10px"> {{ number_format($la->porcentaje_ganancia,0) }}%</span>
                        @endif
                    </div>
                @empty
                    <div style="color:var(--muted-2);font-size:11px">Sin asignar</div>
                @endforelse
            </div>

            <div class="info-box">
                <div class="info-label">Canales</div>
                @if(!empty($contactLinks))
                    <div class="chip-row">
                        @foreach(array_slice($contactLinks, 0, 3) as $link)
                        @php
                            $n = strtolower($link['name'] ?? '');
                            $icon = match(true) {
                                str_contains($n,'whatsapp')  => '💬',
                                str_contains($n,'telegram')  => '✈️',
                                str_contains($n,'instagram') => '📷',
                                str_contains($n,'facebook')  => '📘',
                                str_contains($n,'phone')||str_contains($n,'tel') => '📞',
                                default => '🔗',
                            };
                        @endphp
                        <span class="chip" title="{{ $link['name'] ?? '' }}">{{ $icon }}</span>
                        @endforeach
                        @if(count($contactLinks) > 3)
                            <span class="chip" style="opacity:.6">+{{ count($contactLinks)-3 }}</span>
                        @endif
                    </div>
                @else
                    <div style="color:var(--muted-2);font-size:11px">—</div>
                @endif
            </div>

            <div class="info-box">
                <div class="info-label">Plataformas</div>
                @if($linePlatforms->isNotEmpty())
                    <div class="chip-row">
                        @foreach($linePlatforms->take(3) as $plat)
                            <span class="chip">{{ $plat->name }}</span>
                        @endforeach
                        @if($linePlatforms->count() > 3)
                            <span class="chip" style="opacity:.6">+{{ $linePlatforms->count()-3 }}</span>
                        @endif
                    </div>
                @else
                    <div style="color:var(--muted-2);font-size:11px">—</div>
                @endif
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="card-footer">
            <div class="line-actions">
                @if($canManage)
                    <button type="button" class="btn-soft" wire:click="openEditModal({{ $line->id }})">
                        <svg class="mini-icon" viewBox="0 0 15 15"><path d="M10.5 2.5l2 2-8.5 8.5H2.5v-2l8.5-8.5z"/></svg>
                        Editar
                    </button>
                @endif
            </div>
            <a href="{{ route('lineas.detail', $line->id) }}" class="btn-soft">
                Ver más →
            </a>
        </div>

    </div>
</div>
