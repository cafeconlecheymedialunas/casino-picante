@props([
    'contacts' => collect(),
    'limit' => null,
    'class' => '',
    'itemClass' => '',
    'emptyText' => null,
])

@php
    $items = collect($contacts)
        ->filter(fn ($contact) => filled($contact['value'] ?? null))
        ->values();

    if ($limit) {
        $items = $items->take((int) $limit);
    }

    $channelIcons = [
        'wsp' => 'fa-brands fa-whatsapp',
        'wsap' => 'fa-brands fa-whatsapp',
        'wa' => 'fa-brands fa-whatsapp',
        'whatsapp' => 'fa-brands fa-whatsapp',
        'telegram' => 'fa-brands fa-telegram',
        'tg' => 'fa-brands fa-telegram',
        'instagram' => 'fa-brands fa-instagram',
        'ig' => 'fa-brands fa-instagram',
        'facebook' => 'fa-brands fa-facebook',
        'fb' => 'fa-brands fa-facebook',
        'phone' => 'fa-solid fa-phone',
        'telefono' => 'fa-solid fa-phone',
        'tel' => 'fa-solid fa-phone',
        'email' => 'fa-solid fa-envelope',
        'mail' => 'fa-solid fa-envelope',
        'web' => 'fa-solid fa-globe',
        'tiktok' => 'fa-brands fa-tiktok',
        'twitter' => 'fa-brands fa-x-twitter',
        'x' => 'fa-brands fa-x-twitter',
        'youtube' => 'fa-brands fa-youtube',
    ];

    $contactHref = function (array $contact): string {
        $value = trim((string) ($contact['value'] ?? ''));
        $type = strtolower(trim((string) ($contact['type'] ?? 'web')));

        if ($value === '') {
            return '#';
        }

        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return $value;
        }

        if (in_array($type, ['email', 'mail'], true)) {
            return 'mailto:'.$value;
        }

        if (in_array($type, ['phone', 'telefono', 'tel'], true)) {
            return 'tel:'.preg_replace('/\s+/', '', $value);
        }

        if (in_array($type, ['whatsapp', 'wsp', 'wsap', 'wa'], true)) {
            $digits = preg_replace('/\D+/', '', $value);
            return $digits ? 'https://wa.me/'.$digits : $value;
        }

        return $value;
    };
@endphp

@once
@push('styles')
<style>
    .contact-icons { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .contact-icon-link { width: 46px; height: 46px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,.09); border-radius: 12px; background: rgba(255,255,255,.045); color: var(--orange); text-decoration: none; transition: border-color .18s ease, background .18s ease, transform .18s ease; }
    .contact-icon-link:hover { transform: translateY(-1px); border-color: rgba(255,106,26,.36); background: rgba(255,106,26,.07); }
    .contact-icon-link i { width: 100%; height: 100%; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; background: rgba(255,106,26,.10); font-size: 18px; }
    .contact-icons.compact { gap: 8px; }
    .contact-icons.compact .contact-icon-link { width: 38px; height: 38px; border-radius: 10px; }
    .contact-icons.compact .contact-icon-link i { border-radius: 10px; font-size: 15px; }
</style>
@endpush
@endonce

@if($items->isNotEmpty())
    <div class="contact-icons {{ $class }}">
        @foreach($items as $contact)
            @php
                $type = strtolower(trim((string) ($contact['type'] ?? 'web')));
                $icon = $channelIcons[$type] ?? 'fa-solid fa-link';
                $label = $contact['name'] ?: ucfirst($type ?: 'Contacto');
            @endphp
            <a href="{{ $contactHref($contact) }}" target="_blank" rel="noopener" class="contact-icon-link {{ $itemClass }}" title="{{ $label }}" aria-label="{{ $label }}">
                <i class="{{ $icon }}"></i>
            </a>
        @endforeach
    </div>
@elseif($emptyText)
    <div class="empty-panel">{{ $emptyText }}</div>
@endif
