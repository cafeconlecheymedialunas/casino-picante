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

    // Canonical brand key for CSS class
    $channelBrand = [
        'wsp' => 'whatsapp', 'wsap' => 'whatsapp', 'wa' => 'whatsapp', 'whatsapp' => 'whatsapp',
        'telegram' => 'telegram', 'tg' => 'telegram',
        'instagram' => 'instagram', 'ig' => 'instagram',
        'facebook' => 'facebook', 'fb' => 'facebook',
        'phone' => 'phone', 'telefono' => 'phone', 'tel' => 'phone',
        'email' => 'email', 'mail' => 'email',
        'tiktok' => 'tiktok',
        'twitter' => 'twitter', 'x' => 'twitter',
        'youtube' => 'youtube',
        'web' => 'web',
    ];

    $channelLabels = [
        'whatsapp' => 'WhatsApp',
        'telegram' => 'Telegram',
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'phone' => 'Teléfono',
        'email' => 'Email',
        'tiktok' => 'TikTok',
        'twitter' => 'Twitter / X',
        'youtube' => 'YouTube',
        'web' => 'Sitio web',
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
    /* ── Base ── */
    .contact-icons { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }

    .contact-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 0 18px; height: 46px; border-radius: 14px;
        border: 1px solid rgba(255,255,255,.10);
        background: rgba(255,255,255,.04);
        color: #fff; text-decoration: none; font-size: 13px; font-weight: 800;
        letter-spacing: .01em; white-space: nowrap;
        transition: transform .18s ease, filter .18s ease, border-color .18s ease;
    }
    .contact-btn:hover { transform: translateY(-2px); filter: brightness(1.12); }
    .contact-btn i { font-size: 18px; flex-shrink: 0; }

    /* ── Brands ── */
    .contact-btn.brand-whatsapp  { background: #25D366; border-color: #25D366; color: #fff; }
    .contact-btn.brand-whatsapp:hover { background: #1ebe58; border-color: #1ebe58; }

    .contact-btn.brand-telegram  { background: #229ED9; border-color: #229ED9; color: #fff; }
    .contact-btn.brand-telegram:hover { background: #1a8ec4; border-color: #1a8ec4; }

    .contact-btn.brand-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); border-color: #dc2743; color: #fff; }
    .contact-btn.brand-instagram:hover { filter: brightness(1.1); }

    .contact-btn.brand-facebook  { background: #1877F2; border-color: #1877F2; color: #fff; }
    .contact-btn.brand-facebook:hover { background: #1166d6; border-color: #1166d6; }

    .contact-btn.brand-tiktok    { background: #010101; border-color: #69c9d0; color: #fff; }
    .contact-btn.brand-tiktok:hover { background: #111; border-color: #69c9d0; }

    .contact-btn.brand-twitter   { background: #000; border-color: #333; color: #fff; }
    .contact-btn.brand-twitter:hover { background: #111; border-color: #555; }

    .contact-btn.brand-youtube   { background: #FF0000; border-color: #FF0000; color: #fff; }
    .contact-btn.brand-youtube:hover { background: #e00000; border-color: #e00000; }

    .contact-btn.brand-phone     { background: #128C7E; border-color: #128C7E; color: #fff; }
    .contact-btn.brand-phone:hover { background: #0e7a6d; border-color: #0e7a6d; }

    .contact-btn.brand-email     { background: #EA4335; border-color: #EA4335; color: #fff; }
    .contact-btn.brand-email:hover { background: #d33426; border-color: #d33426; }

    .contact-btn.brand-web       { background: #475569; border-color: #475569; color: #fff; }
    .contact-btn.brand-web:hover { background: #374558; border-color: #374558; }

    /* ── Compact (icon only, used in line cards) ── */
    .contact-icons.compact .contact-btn {
        width: 38px; height: 38px; padding: 0;
        border-radius: 10px; justify-content: center; gap: 0;
    }
    .contact-icons.compact .contact-btn span { display: none; }
    .contact-icons.compact .contact-btn i { font-size: 16px; }
</style>
@endpush
@endonce

@if($items->isNotEmpty())
    <div class="contact-icons {{ $class }}">
        @foreach($items as $contact)
            @php
                $type  = strtolower(trim((string) ($contact['type'] ?? 'web')));
                $brand = $channelBrand[$type] ?? 'web';
                $icon  = $channelIcons[$type] ?? 'fa-solid fa-link';
                $label = filled($contact['name'] ?? '') ? $contact['name'] : ($channelLabels[$brand] ?? ucfirst($type ?: 'Contacto'));
            @endphp
            <a href="{{ $contactHref($contact) }}"
               target="_blank" rel="noopener"
               class="contact-btn brand-{{ $brand }} {{ $itemClass }}"
               title="{{ $label }}" aria-label="{{ $label }}">
                <i class="{{ $icon }}"></i>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </div>
@elseif($emptyText)
    <div class="empty-panel">{{ $emptyText }}</div>
@endif
