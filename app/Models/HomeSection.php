<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $table = 'home_sections';

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'kicker',
        'highlight',
        'content',
        'action_text',
        'action_url',
        'raffle_type',
        'raffle_ids',
        'post_type',
        'post_ids',
        'bonus_type',
        'bonus_ids',
        'repeater_data',
        'enabled',
        'order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'raffle_ids' => 'array',
        'post_ids' => 'array',
        'bonus_ids' => 'array',
        'repeater_data' => 'array',
    ];

    public static function getSection(string $key): ?self
    {
        return self::where('section_key', $key)->first();
    }

    public static function getSectionData(string $key, array $defaults = []): array
    {
        $section = self::getSection($key);

        if (! $section) {
            return $defaults;
        }

        return [
            'kicker' => $section->kicker ?? $defaults['kicker'] ?? null,
            'title' => $section->title ?? $defaults['title'] ?? null,
            'highlight' => $section->highlight ?? $defaults['highlight'] ?? null,
            'subtitle' => $section->subtitle ?? $defaults['subtitle'] ?? null,
            'content' => $section->content ?? $defaults['content'] ?? null,
            'action' => $section->action_text && $section->action_url
                ? '<a class="fe-btn ghost" href="'.$section->action_url.'" wire:navigate>'.$section->action_text.'</a>'
                : ($defaults['action'] ?? null),
            'enabled' => $section->enabled,
            'raffle_type' => $section->raffle_type,
            'raffle_ids' => self::ensureArray($section->raffle_ids),
            'post_type' => $section->post_type,
            'post_ids' => self::ensureArray($section->post_ids),
            'bonus_type' => $section->bonus_type,
            'bonus_ids' => self::ensureArray($section->bonus_ids),
            'repeater_data' => self::ensureArray($section->repeater_data, $defaults['repeater_data'] ?? []),
        ];
    }

    private static function ensureArray($value, array $default = []): array
    {
        if ($value === null || $value === '') {
            return $default;
        }
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $default;
    }
}
