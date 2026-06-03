<?php

namespace App\Support;

use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\Vendor;
use Illuminate\Support\Collection;

class PublicPageContent
{
    public const PAGES = [
        'lineas' => [
            'label' => 'Lineas',
            'item_type' => 'line',
            'section_key' => 'page.lineas',
        ],
        'bonos' => [
            'label' => 'Bonos',
            'item_type' => 'bonus',
            'section_key' => 'page.bonos',
        ],
        'sorteos' => [
            'label' => 'Sorteos',
            'item_type' => 'raffle',
            'section_key' => 'page.sorteos',
        ],
        'cajeros' => [
            'label' => 'Cajeros',
            'item_type' => 'vendor',
            'section_key' => 'page.cajeros',
        ],
        'novedades' => [
            'label' => 'Novedades',
            'item_type' => 'post',
            'section_key' => 'page.novedades',
        ],
    ];

    public static function page(string $page): ?HomeSection
    {
        $config = self::PAGES[$page] ?? null;

        if (! $config) {
            return null;
        }

        return HomeSection::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->where('section_key', $config['section_key'])
            ->first();
    }

    public static function tabs(?HomeSection $section): array
    {
        if (! $section || ! is_array($section->repeater_data)) {
            return [];
        }

        return collect($section->repeater_data)
            ->filter(fn ($tab) => is_array($tab) && ($tab['enabled'] ?? true))
            ->map(function ($tab) {
                $key = (string) ($tab['key'] ?? str($tab['title'] ?? 'tab')->slug());

                return [
                    'key' => $key,
                    'title' => (string) ($tab['title'] ?? 'Tab'),
                    'subtitle' => (string) ($tab['subtitle'] ?? ''),
                    'icon' => (string) ($tab['icon'] ?? ''),
                    'item_ids' => self::ids($tab['item_ids'] ?? []),
                ];
            })
            ->values()
            ->all();
    }

    public static function ids($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : explode(',', $value);
        }

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public static function orderedItems(string $page, array $ids): Collection
    {
        $ids = self::ids($ids);

        if (! $ids) {
            return collect();
        }

        $query = match ($page) {
            'lineas' => Line::withoutGlobalScopes()
                ->with(['activePlatforms', 'lineAgents.agent', 'ratings', 'vendor'])
                ->where('status', 'active'),
            'bonos' => Bonus::withoutGlobalScopes()
                ->with(['line', 'platform', 'vendor'])
                ->withCount(['assignments as active_assignments_count' => fn ($q) => $q->whereIn('status', Bonus::CONSUMED_STATUSES)])
                ->whereNotNull('line_id')
                ->where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->whereHas('line', fn ($line) => $line->where('status', 'active')),
            'sorteos' => Raffle::withoutGlobalScopes()
                ->with(['lines', 'platform', 'vendor'])
                ->withCount('numbers')
                ->whereIn('status', ['active', 'inactive']),
            'cajeros' => Vendor::withoutGlobalScopes()
                ->where('is_active', true)
                ->withCount(['lines as active_lines_count' => fn ($q) => $q->where('status', 'active')])
                ->orderByDesc('is_direct')
                ->orderBy('name'),
            'novedades' => Post::withoutGlobalScopes()
                ->where('status', Post::STATUS_PUBLISHED)
                ->orderBy('published_at', 'desc'),
            default => null,
        };

        if (! $query) {
            return collect();
        }

        return $query->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($item) => array_search((int) $item->id, $ids, true))
            ->values();
    }
}
