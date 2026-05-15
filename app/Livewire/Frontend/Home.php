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
            'sections' => $this->sections(),
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
        return Line::with(['activePlatforms', 'lineAgents.agent', 'ratings'])
            ->where('status', 'active')
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    private function activeRaffle(): ?Raffle
    {
        $sectionData = HomeSection::getSection('sorteo');

        $query = Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        if ($sectionData && $sectionData->raffle_ids) {
            return $query->whereIn('id', $sectionData->raffle_ids)->first();
        }

        return $query->orderBy('end_date')->first();
    }

    private function bonusItems(): EloquentCollection
    {
        $sectionData = HomeSection::getSection('bonos');

        $baseQuery = Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('line', fn ($line) => $line->where('status', 'active'));

        if ($sectionData && $sectionData->bonus_ids) {
            $configured = (clone $baseQuery)->whereIn('id', $sectionData->bonus_ids)->get();
            if ($configured->isNotEmpty()) {
                return $configured->take(5);
            }
        }

        return $baseQuery->latest('start_date')->take(5)->get();
    }

    private function blogPosts(): EloquentCollection
    {
        $sectionData = HomeSection::getSection('blog');

        $baseQuery = Post::withoutGlobalScopes()
            ->with(['category', 'authorAgent'])
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at');

        if ($sectionData && $sectionData->post_ids) {
            $configured = (clone $baseQuery)->whereIn('id', $sectionData->post_ids)->get();
            if ($configured->isNotEmpty()) {
                return $configured->take(3);
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

    private function sections(): array
    {
        $defaultSections = [
            'como-empezar' => [
                'kicker' => 'Como funciona',
                'title' => 'Empeza en',
                'highlight' => '3 pasos',
                'subtitle' => 'Sin vueltas: contacto, carga y juego. Si necesitás ayuda, una persona te responde.',
            ],
            'lineas' => [
                'kicker' => 'Empeza a jugar',
                'title' => 'Lineas de',
                'highlight' => 'atencion',
                'subtitle' => 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.',
            ],
            'sorteo' => [
                'kicker' => 'Mas chances para ganar',
                'title' => 'Sorteos de',
                'highlight' => 'esta semana',
                'subtitle' => 'Jugá, participá y seguí los premios disponibles en cada sorteo activo.',
            ],
            'nosotros' => [
                'title' => 'Casino online con atencion',
                'highlight' => 'real',
                'content' => 'Una experiencia pensada para jugar facil: acceso rapido, promos claras, sorteos activos y soporte humano para acompanarte.',
            ],
            'bonos' => [
                'kicker' => 'Promos para jugar mas',
                'title' => 'Bonos',
                'highlight' => 'activos',
                'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
            ],
            'blog' => [
                'kicker' => 'Noticias y jugadas',
                'highlight' => 'Novedades',
                'subtitle' => 'Enterate de novedades, sorteos, recomendaciones y promos nuevas antes de que pasen.',
            ],
        ];

        $sections = [];

        foreach ($defaultSections as $key => $defaults) {
            $sections[$key] = HomeSection::getSectionData($key, $defaults);
        }

        return $sections;
    }
}
