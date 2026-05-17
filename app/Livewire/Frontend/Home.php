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
        $ids = HomeConfig::where('section', HomeConfig::SECTION_CAROUSEL)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        if (! empty($ids)) {
            return CarouselItem::whereIn('id', $ids)->get()->sortBy(fn ($c) => array_search($c->id, $ids))->values();
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
        $section = HomeSection::where('section_key', 'sorteo')->first();
        $ids = $this->ensureArray($section?->raffle_ids);
        $raffleType = $section?->raffle_type ?? '';

        $query = Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        if ($raffleType === 'active') {
            $query->where('status', 'active');
        }

        if (! empty($ids)) {
            return $query->whereIn('id', $ids)->get()->sortBy(fn ($r) => array_search($r->id, $ids))->values();
        }

        return $query->orderBy('start_date', 'asc')->take(3)->get();
    }

    private function bonusItems(): EloquentCollection
    {
        $section = HomeSection::where('section_key', 'bonos')->first();
        $ids = $this->ensureArray($section?->bonus_ids);
        $bonusType = $section?->bonus_type ?? '';

        $baseQuery = Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->where('status', 'active')
            ->where('end_date', '>=', now());

        if ($bonusType === 'active') {
            $baseQuery->where('status', 'active');
        }

        if (! empty($ids)) {
            return $baseQuery->whereIn('id', $ids)->get()->sortBy(fn ($b) => array_search($b->id, $ids))->values();
        }

        return $baseQuery->latest('start_date')->take(5)->get();
    }

    private function blogPosts(): EloquentCollection
    {
        $section = HomeSection::where('section_key', 'blog')->first();
        $ids = $this->ensureArray($section?->post_ids);
        $postType = $section?->post_type ?? '';

        $baseQuery = Post::withoutGlobalScopes()
            ->with(['category', 'authorAgent'])
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at');

        if ($postType && is_numeric($postType)) {
            $baseQuery->where('category_id', $postType);
        }

        if (! empty($ids)) {
            return $baseQuery->whereIn('id', $ids)->get()->sortBy(fn ($p) => array_search($p->id, $ids))->values();
        }

        return $baseQuery->latest('published_at')->take(3)->get();
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
