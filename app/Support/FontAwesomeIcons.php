<?php

namespace App\Support;

final class FontAwesomeIcons
{
    private const VERSION = '6.5.1';

    private const FALLBACK = [
        ['class' => 'fa-solid fa-bolt', 'label' => 'bolt', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-lock', 'label' => 'lock', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-headset', 'label' => 'headset', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-shield-halved', 'label' => 'shield-halved', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-star', 'label' => 'star', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-fire', 'label' => 'fire', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-gift', 'label' => 'gift', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-trophy', 'label' => 'trophy', 'style' => 'Solid'],
        ['class' => 'fa-solid fa-wallet', 'label' => 'wallet', 'style' => 'Solid'],
        ['class' => 'fa-brands fa-whatsapp', 'label' => 'whatsapp', 'style' => 'Brands'],
    ];

    public static function options(): array
    {
        $path = resource_path('data/font-awesome-icons.json');
        $icons = is_file($path)
            ? json_decode((string) file_get_contents($path), true)
            : null;

        if (! is_array($icons)) {
            return self::FALLBACK;
        }

        return collect($icons)
            ->filter(fn ($icon) => is_array($icon) && filled($icon['class'] ?? null) && filled($icon['label'] ?? null))
            ->values()
            ->all() ?: self::FALLBACK;
    }
}
