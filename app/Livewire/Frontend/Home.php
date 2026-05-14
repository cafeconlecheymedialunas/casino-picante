<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\CarouselItem;
use App\Models\HomeConfig;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('frontend.pages.home', [
            'carouselItems' => $this->carouselItems(),
            'lines' => $this->lines(),
            'activeRaffle' => $this->activeRaffle(),
            'bonusItems' => $this->bonusItems(),
            'blogPosts' => $this->blogPosts(),
        ])->layout('frontend.layouts.app');
    }

    private function carouselItems(): EloquentCollection
    {
        $selected = $this->configuredIds(HomeConfig::SECTION_CAROUSEL);

        if ($selected->isNotEmpty()) {
            return $this->orderedByConfig(
                CarouselItem::whereIn('id', $selected)->get(),
                $selected
            );
        }

        return CarouselItem::orderBy('order')->take(5)->get();
    }

    private function lines(): EloquentCollection
    {
        return Line::with('activePlatforms')
            ->where('status', 'active')
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    private function activeRaffle(): ?Raffle
    {
        return Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    private function bonusItems(): EloquentCollection
    {
        $selected = $this->configuredIds(HomeConfig::SECTION_BONUSES);
        $baseQuery = Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        if ($selected->isNotEmpty()) {
            $configured = (clone $baseQuery)->whereIn('id', $selected)->get();

            if ($configured->isNotEmpty()) {
                return $this->orderedByConfig($configured, $selected);
            }
        }

        return $baseQuery->latest('start_date')->take(5)->get();
    }

    private function blogPosts(): EloquentCollection
    {
        $selected = $this->configuredIds(HomeConfig::SECTION_BLOG);
        $baseQuery = Post::withoutGlobalScopes()
            ->with('category')
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at');

        if ($selected->isNotEmpty()) {
            $configured = (clone $baseQuery)->whereIn('id', $selected)->get();

            if ($configured->isNotEmpty()) {
                return $this->orderedByConfig($configured, $selected)->take(3);
            }
        }

        return $baseQuery->latest('published_at')->take(3)->get();
    }

    private function configuredIds(string $section): Collection
    {
        return HomeConfig::where('section', $section)
            ->orderBy('order')
            ->pluck('item_id')
            ->map(fn ($id) => (int) $id)
            ->values();
    }

    private function orderedByConfig(EloquentCollection $items, Collection $ids): EloquentCollection
    {
        $positions = $ids->flip();

        return $items
            ->sortBy(fn ($item) => $positions[$item->id] ?? PHP_INT_MAX)
            ->values();
    }
}
