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
            'raffles' => $this->raffles(),
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

    private function raffles(): EloquentCollection
    {
        $selected = $this->configuredIds(HomeConfig::SECTION_RAFFLES_UPCOMING);

        $query = Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where(function($q) {
                $q->where('status', 'active')->where('start_date', '>', now())
                  ->orWhere('status', 'upcoming');
            });

        if ($selected->isNotEmpty()) {
            return $this->orderedByConfig(
                $query->whereIn('id', $selected)->get(),
                $selected
            );
        }

        return $query->orderBy('start_date')->take(3)->get();
    }

    private function bonusItems(): EloquentCollection
    {
        $selected = $this->configuredIds(HomeConfig::SECTION_BONUSES);

        $baseQuery = Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->whereHas('line', fn ($line) => $line->where('status', 'active'));

        if ($selected->isNotEmpty()) {
            return $this->orderedByConfig(
                $baseQuery->whereIn('id', $selected)->get(),
                $selected
            );
        }

        return $baseQuery->latest('start_date')->take(5)->get();
    }

    private function blogPosts(): EloquentCollection
    {
        $selected = $this->configuredIds(HomeConfig::SECTION_BLOG);

        $baseQuery = Post::withoutGlobalScopes()
            ->with(['category', 'authorAgent'])
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at');

        if ($selected->isNotEmpty()) {
            return $this->orderedByConfig(
                $baseQuery->whereIn('id', $selected)->get(),
                $selected
            );
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
                'kicker' => 'Muy pronto',
                'title' => 'PRÓXIMOS',
                'highlight' => 'SORTEOS',
                'subtitle' => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.',
            ],
            'nosotros' => [
                'kicker' => 'Sobre RED PICANTES',
                'title' => 'Casino online con atencion',
                'highlight' => 'real',
                'subtitle' => 'Una experiencia pensada para jugar facil: acceso rapido, promos claras, sorteos activos y soporte humano para acompaniarte.',
            ],
            'bonos' => [
                'kicker' => 'Promos para jugar mas',
                'title' => 'Bonos',
                'highlight' => 'activos',
                'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
            ],
            'blog' => [
                'kicker' => 'Noticias y jugadas',
                'title' => 'Noticias y',
                'highlight' => 'jugadas',
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
