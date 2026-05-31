<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\CarouselItem;
use App\Models\HomeConfig;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('frontend.pages.home', [
            'carouselItems' => $this->carouselItems(),
            'lines' => $this->lines(),
            'raffles' => $this->raffles(),
            'bonusItems' => $this->bonusItems(),
            'blogPosts' => $this->blogPosts(),
            'sections' => $this->sections(),
        ])->layout('frontend.layouts.app');
    }

    private function carouselItems(): EloquentCollection
    {
        $ids = HomeConfig::withoutGlobalScopes()
            ->where('section', HomeConfig::SECTION_CAROUSEL)
            ->whereNull('vendor_id')
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        if (empty($ids)) {
            return new EloquentCollection;
        }

        return CarouselItem::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($c) => array_search($c->id, $ids))
            ->values();
    }

    private function lines(): EloquentCollection
    {
        $section = $this->globalSection('lineas');
        $ids = $this->ensureArray($section?->line_ids);

        if (empty($ids)) {
            return new EloquentCollection;
        }

        return Line::withoutGlobalScopes()
            ->with(['activePlatforms', 'lineAgents.agent', 'ratings', 'vendor'])
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($line) => array_search($line->id, $ids))
            ->values();
    }

    private function raffles(): EloquentCollection
    {
        $section = $this->globalSection('sorteo');
        $ids = $this->ensureArray($section?->raffle_ids);

        if (empty($ids)) {
            return new EloquentCollection;
        }

        return Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($raffle) => array_search($raffle->id, $ids))
            ->values();
    }

    private function bonusItems(): EloquentCollection
    {
        $section = $this->globalSection('bonos');
        $ids = $this->ensureArray($section?->bonus_ids);

        if (empty($ids)) {
            return new EloquentCollection;
        }

        return Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($bonus) => array_search($bonus->id, $ids))
            ->values();
    }

    private function blogPosts(): EloquentCollection
    {
        $section = $this->globalSection('blog');
        $ids = $this->ensureArray($section?->post_ids);

        if (empty($ids)) {
            return new EloquentCollection;
        }

        $query = Post::withoutGlobalScopes()
            ->with(['category', 'authorAgent'])
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->whereIn('id', $ids);

        if ($section?->post_type && is_numeric($section->post_type)) {
            $query->where('category_id', $section->post_type);
        }

        return $query->get()
            ->sortBy(fn ($post) => array_search($post->id, $ids))
            ->values();
    }

    private function sections(): array
    {
        $sections = [];

        HomeSection::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->orderBy('order')
            ->get()
            ->each(function (HomeSection $section) use (&$sections): void {
                $sections[$section->section_key] = [
                    'kicker' => $section->kicker,
                    'title' => $section->title,
                    'highlight' => $section->highlight,
                    'subtitle' => $section->subtitle,
                    'content' => $section->content,
                    'action' => $section->action_text && $section->action_url
                        ? '<a class="fe-btn ghost" href="'.$section->action_url.'" wire:navigate>'.$section->action_text.'</a>'
                        : null,
                    'enabled' => $section->enabled,
                    'raffle_type' => $section->raffle_type,
                    'raffle_ids' => $this->ensureArray($section->raffle_ids),
                    'post_type' => $section->post_type,
                    'post_ids' => $this->ensureArray($section->post_ids),
                    'bonus_type' => $section->bonus_type,
                    'bonus_ids' => $this->ensureArray($section->bonus_ids),
                    'line_ids' => $this->ensureArray($section->line_ids),
                    'repeater_data' => $this->ensureArray($section->repeater_data),
                ];
            });

        return $sections;
    }

    private function globalSection(string $key): ?HomeSection
    {
        return HomeSection::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->where('section_key', $key)
            ->first();
    }

    private function ensureArray($value): array
    {
        if ($value === null || $value === '') {
            return [];
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

        return [];
    }
}
