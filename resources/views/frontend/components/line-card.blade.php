@props(['line'])

@php
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null));
    $cover = $line->portada_url ?: null;
    $avatar = $line->perfil_url ?: null;
@endphp

<article class="line-card">
    <div class="line-cover">
        @if($cover)
            <img src="{{ $cover }}" alt="{{ $line->name }}">
        @endif
        <div class="line-avatar">
            @if($avatar)
                <img src="{{ $avatar }}" alt="">
            @else
                <span>{{ strtoupper(mb_substr($line->name, 0, 2)) }}</span>
            @endif
        </div>
    </div>
    <div class="line-body">
        <div class="line-head">
            <div>
                <h3>{{ $line->name }}</h3>
                <p>{{ $line->description ?: 'Pedí tu usuario, cargá saldo y recibí ayuda por esta línea.' }}</p>
            </div>
            <span class="line-state">Activa</span>
        </div>
        <div class="line-actions">
            @forelse($contacts->take(3) as $contact)
                <a href="{{ $contact['value'] }}" target="_blank" rel="noopener" class="line-contact">
                    {{ $contact['name'] ?: ucfirst($contact['type'] ?? 'Contacto') }}
                </a>
            @empty
                @if($line->phone)
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $line->phone) }}" target="_blank" rel="noopener" class="line-contact">WhatsApp</a>
                @else
                    <span class="line-contact muted">Sin contacto</span>
                @endif
            @endforelse
        </div>
    </div>
</article>
