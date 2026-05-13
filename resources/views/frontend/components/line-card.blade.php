@props(['line'])

@php
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null));
    $cover = $line->portada_url ?: null;
    $avatar = $line->perfil_url ?: null;

    $channelIcons = [
        'whatsapp'  => 'fa-brands fa-whatsapp',
        'telegram'  => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram',
        'facebook'  => 'fa-brands fa-facebook',
        'phone'     => 'fa-solid fa-phone',
        'email'     => 'fa-solid fa-envelope',
        'web'       => 'fa-solid fa-globe',
        'tiktok'    => 'fa-brands fa-tiktok',
        'twitter'   => 'fa-brands fa-x-twitter',
        'youtube'   => 'fa-brands fa-youtube',
    ];

    $channelColors = [
        'whatsapp'  => '#25d366',
        'telegram'  => '#2aabee',
        'instagram' => '#e1306c',
        'facebook'  => '#1877f2',
        'phone'     => 'var(--good)',
        'email'     => '#ea4335',
        'web'       => 'var(--orange)',
        'tiktok'    => '#010101',
        'twitter'   => '#1da1f2',
        'youtube'   => '#ff0000',
    ];
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
        </div>
        <div class="line-actions">
            @foreach($contacts as $contact)
                @php
                    $type = $contact['type'] ?? 'other';
                    $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                    $color = $channelColors[$type] ?? 'var(--orange)';
                    $name = $contact['name'] ?: ucfirst($type);
                @endphp
                <a href="{{ $contact['value'] }}" target="_blank" rel="noopener" class="line-contact" style="color:#888;border-color:rgba(136,136,136,.2)">
                        <i class="{{ $icon }} fa-lg" style="color:#888"></i>
                        <span>{{ $name }}</span>
                    </a>
            @endforeach
        </div>
    </div>
</article>