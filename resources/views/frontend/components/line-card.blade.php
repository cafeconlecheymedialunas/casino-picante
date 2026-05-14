@props(['line'])

@php
    $contacts = collect($line->contact_links ?? [])->filter(fn ($contact) => filled($contact['value'] ?? null));
    $cover = $line->portada_url ?: null;
    $avatar = $line->perfil_url ?: null;
    $channelIcons = [
        'wsp' => 'fa-brands fa-whatsapp', 'wsap' => 'fa-brands fa-whatsapp', 'wa' => 'fa-brands fa-whatsapp', 'whatsapp' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram', 'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram', 'ig' => 'fa-brands fa-instagram',
        'facebook' => 'fa-brands fa-facebook', 'fb' => 'fa-brands fa-facebook',
        'phone' => 'fa-solid fa-phone', 'telefono' => 'fa-solid fa-phone', 'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope', 'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe', 'tiktok' => 'fa-brands fa-tiktok', 'twitter' => 'fa-brands fa-x-twitter', 'x' => 'fa-brands fa-x-twitter', 'youtube' => 'fa-brands fa-youtube',
    ];
    $channelColors = collect($channelIcons)->mapWithKeys(fn ($icon, $type) => [$type => '#9a9a9a'])->all();
    $normalizeChannelType = fn (?string $type): string => strtolower(trim((string) $type));
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
                    $type = $normalizeChannelType($contact['type'] ?? 'other');
                    $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                    $color = $channelColors[$type] ?? 'var(--orange)';
                    $name = $contact['name'] ?: ucfirst($type);
                @endphp
                <a href="{{ $contact['value'] }}" target="_blank" rel="noopener" class="line-contact" style="color:#9a9a9a;border-color:rgba(154,154,154,.24);background:rgba(154,154,154,.06)">
                        <i class="{{ $icon }} fa-lg" style="color:{{ $color }}"></i>
                        <span>{{ $name }}</span>
                    </a>
            @endforeach
            <a href="{{ route('frontend.lines.show', $line) }}" wire:navigate class="line-contact">
                <i class="fa-solid fa-circle-info" style="color:#9a9a9a"></i>
                <span>Ver detalle</span>
            </a>
        </div>
    </div>
</article>
