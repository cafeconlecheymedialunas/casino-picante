<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\CarouselItem;
use App\Models\Category;
use App\Models\HomeConfig;
use App\Models\HomeSection;
use App\Models\Post;
use App\Models\Raffle;
use App\Support\ImageStorage;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class EditorHome extends Component
{
    use HasLinePermissions, WithFileUploads;

    public $carouselItems = [];

    public $bonusItems = [];

    public $blogPosts = [];

    public $raffleItems = [];

    public $categories = [];

    public $selectedCarousel = [];

    public $selectedBonuses = [];

    public $selectedBlogs = [];

    public $selectedRafflesUpcoming = [];

    public $newCarouselTitle = '';

    public $newCarouselLink = '';

    public $newCarouselImage = null;

    public $sections = [];

    public $editingSection = null;

    public function mount()
    {
        $this->ensureCanEditHome();

        $this->loadCarouselItems();

        $this->bonusItems = Bonus::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();

        $this->blogPosts = Post::where('status', Post::STATUS_PUBLISHED)
            ->orderBy('published_at', 'desc')
            ->get()
            ->toArray();

        $this->raffleItems = Raffle::whereIn('status', ['active', 'upcoming'])
            ->orderBy('start_date', 'asc')
            ->get()
            ->toArray();

        $this->categories = Category::all()->toArray();

        $this->selectedCarousel = HomeConfig::where('section', HomeConfig::SECTION_CAROUSEL)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->selectedBonuses = HomeConfig::where('section', HomeConfig::SECTION_BONUSES)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->selectedBlogs = HomeConfig::where('section', HomeConfig::SECTION_BLOG)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->selectedRafflesUpcoming = HomeConfig::where('section', HomeConfig::SECTION_RAFFLES_UPCOMING)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->loadSections();
    }

    public function loadSections(): void
    {
        $defaultSections = [
            'como-empezar' => [
                'kicker' => 'Como funciona',
                'title' => 'Empeza en',
                'highlight' => '3 pasos',
                'subtitle' => 'Sin vueltas: contacto, carga y juego. Si necesitás ayuda, una persona te responde.',
                'repeater_data' => [
                    ['title' => 'Pedí tu usuario', 'subtitle' => 'Elegí una línea de atención y solicitá el acceso para empezar a jugar.'],
                    ['title' => 'Cargá saldo', 'subtitle' => 'Consultá medios de carga, promociones disponibles y bonos para tu cuenta.'],
                    ['title' => 'Entrá a jugar', 'subtitle' => 'Disfrutá tus juegos favoritos, participá en sorteos y pedí asistencia cuando quieras.'],
                ]
            ],
            'lineas' => ['kicker' => 'Empeza a jugar', 'title' => 'Lineas de', 'highlight' => 'atencion', 'subtitle' => 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.'],
            'sorteo' => ['kicker' => 'Muy pronto', 'title' => 'PRÓXIMOS', 'highlight' => 'SORTEOS', 'subtitle' => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.'],
            'nosotros' => [
                'kicker' => 'Sobre RED PICANTES',
                'title' => 'Casino online con atencion',
                'highlight' => 'real',
                'subtitle' => 'Una experiencia pensada para jugar facil: acceso rapido, promos claras, sorteos activos y soporte humano para acompaniarte.',
                'repeater_data' => [
                    ['title' => 'Alta rapida', 'subtitle' => 'Contactás una línea y pedís tu usuario sin formularios eternos.'],
                    ['title' => 'Bonos vigentes', 'subtitle' => 'Promociones para recargar, arrancar con ventaja y jugar más.'],
                    ['title' => 'Sorteos activos', 'subtitle' => 'Premios y chances extra para usuarios que participan.'],
                    ['title' => 'Soporte humano', 'subtitle' => 'Atención directa para cargas, retiros, dudas y novedades.'],
                ]
            ],
            'bonos' => ['kicker' => 'Promos para jugar mas', 'title' => 'Bonos', 'highlight' => 'activos', 'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.'],
            'blog' => ['kicker' => 'Noticias y jugadas', 'title' => 'Noticias y', 'highlight' => 'jugadas', 'subtitle' => 'Enterate de novedades, sorteos, recomendaciones y promos nuevas antes de que pasen.'],
        ];

        foreach ($defaultSections as $key => $defaults) {
            $section = HomeSection::where('section_key', $key)->first();
            if (! $section) {
                $section = HomeSection::create([
                    'section_key' => $key,
                    'enabled' => true,
                    'order' => array_search($key, array_keys($defaultSections)),
                    'kicker' => $defaults['kicker'] ?? null,
                    'title' => $defaults['title'] ?? null,
                    'highlight' => $defaults['highlight'] ?? null,
                    'subtitle' => $defaults['subtitle'] ?? null,
                    'repeater_data' => $defaults['repeater_data'] ?? null,
                ]);
            }

            $this->sections[$key] = [
                'id' => $section->id,
                'kicker' => $section->kicker ?? $defaults['kicker'] ?? '',
                'title' => $section->title ?? $defaults['title'] ?? '',
                'highlight' => $section->highlight ?? $defaults['highlight'] ?? '',
                'subtitle' => $section->subtitle ?? $defaults['subtitle'] ?? '',
                'content' => $section->content ?? $defaults['content'] ?? '',
                'repeater_data' => $section->repeater_data ?? $defaults['repeater_data'] ?? [],
                'raffle_type' => $section->raffle_type ?? '',
                'raffle_ids' => $section->raffle_ids ? implode(',', $section->raffle_ids) : '',
                'post_type' => $section->post_type ?? '',
                'post_ids' => $section->post_ids ? implode(',', $section->post_ids) : '',
                'bonus_type' => $section->bonus_type ?? '',
                'bonus_ids' => $section->bonus_ids ? implode(',', $section->bonus_ids) : '',
                'enabled' => $section->enabled,
            ];
        }
    }

    public function saveSection(string $key): void
    {
        $this->ensureCanEditHome();

        $data = $this->sections[$key] ?? [];

        $raffleIds = $data['raffle_ids'] ?? '';
        $postIds = $data['post_ids'] ?? '';
        $bonusIds = $data['bonus_ids'] ?? '';

        HomeSection::updateOrCreate(
            ['section_key' => $key],
            [
                'kicker' => $data['kicker'] ?? null,
                'title' => $data['title'] ?? null,
                'highlight' => $data['highlight'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'content' => $data['content'] ?? null,
                'repeater_data' => $data['repeater_data'] ?? null,
                'raffle_type' => $data['raffle_type'] ?? null,
                'raffle_ids' => $raffleIds ? array_map('trim', explode(',', $raffleIds)) : null,
                'post_type' => $data['post_type'] ?? null,
                'post_ids' => $postIds ? array_map('trim', explode(',', $postIds)) : null,
                'bonus_type' => $data['bonus_type'] ?? null,
                'bonus_ids' => $bonusIds ? array_map('trim', explode(',', $bonusIds)) : null,
                'enabled' => $data['enabled'] ?? true,
            ]
        );

        session()->flash('message_success', 'Sección guardada correctamente.');
    }

    public function addRepeaterItem(string $key): void
    {
        $this->sections[$key]['repeater_data'][] = ['title' => '', 'subtitle' => ''];
    }

    public function removeRepeaterItem(string $key, int $index): void
    {
        if (isset($this->sections[$key]['repeater_data'][$index])) {
            unset($this->sections[$key]['repeater_data'][$index]);
            $this->sections[$key]['repeater_data'] = array_values($this->sections[$key]['repeater_data']);
        }
    }

    public function toggleSectionEnabled(string $key): void
    {
        $this->ensureCanEditHome();

        $section = HomeSection::where('section_key', $key)->first();
        if ($section) {
            $section->update(['enabled' => ! $section->enabled]);
            $this->sections[$key]['enabled'] = ! $section->enabled;
        }
    }

    public function loadCarouselItems(): void
    {
        $this->carouselItems = CarouselItem::orderBy('order')->get()->toArray();
    }

    public function addCarouselItem(): void
    {
        $this->ensureCanEditHome();

        $this->validate([
            'newCarouselImage' => 'required|image|max:5120',
            'newCarouselTitle' => 'nullable|string|max:255',
            'newCarouselLink' => 'nullable|string|max:500',
        ]);

        $maxOrder = CarouselItem::max('order') ?? 0;

        CarouselItem::create([
            'image' => ImageStorage::store($this->newCarouselImage, 'carousel'),
            'title' => $this->newCarouselTitle,
            'link' => $this->newCarouselLink,
            'order' => $maxOrder + 1,
            'line_id' => session('active_line_id'),
        ]);

        $this->newCarouselTitle = '';
        $this->newCarouselLink = '';
        $this->newCarouselImage = null;

        $this->loadCarouselItems();
    }

    public function removeCarouselItem($itemId): void
    {
        $this->ensureCanEditHome();

        $item = CarouselItem::find($itemId);
        if ($item) {
            ImageStorage::delete($item->image);
            $item->delete();
        }

        $this->loadCarouselItems();
    }

    public function moveCarouselUp($itemId): void
    {
        $this->ensureCanEditHome();

        $item = CarouselItem::find($itemId);
        if (! $item) {
            return;
        }

        $prev = CarouselItem::where('order', '<', $item->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($prev) {
            $temp = $item->order;
            $item->update(['order' => $prev->order]);
            $prev->update(['order' => $temp]);
        }

        $this->loadCarouselItems();
    }

    public function moveCarouselDown($itemId): void
    {
        $this->ensureCanEditHome();

        $item = CarouselItem::find($itemId);
        if (! $item) {
            return;
        }

        $next = CarouselItem::where('order', '>', $item->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($next) {
            $temp = $item->order;
            $item->update(['order' => $next->order]);
            $next->update(['order' => $temp]);
        }

        $this->loadCarouselItems();
    }

    public function toggleCarousel($itemId)
    {
        $this->ensureCanEditHome();

        if (in_array($itemId, $this->selectedCarousel)) {
            HomeConfig::where('section', HomeConfig::SECTION_CAROUSEL)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedCarousel = array_values(array_diff($this->selectedCarousel, [$itemId]));
        } else {
            if (count($this->selectedCarousel) >= 5) {
                session()->flash('message_error', 'Máximo 5 imágenes en el carrusel.');

                return;
            }
            $order = count($this->selectedCarousel);
            HomeConfig::create([
                'section' => HomeConfig::SECTION_CAROUSEL,
                'item_id' => $itemId,
                'order' => $order,
            ]);
            $this->selectedCarousel[] = (int) $itemId;
        }
    }

    public function toggleRaffle($itemId)
    {
        $this->ensureCanEditHome();

        if (in_array($itemId, $this->selectedRafflesUpcoming)) {
            HomeConfig::where('section', HomeConfig::SECTION_RAFFLES_UPCOMING)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedRafflesUpcoming = array_values(array_diff($this->selectedRafflesUpcoming, [$itemId]));
            $this->reorderSection(HomeConfig::SECTION_RAFFLES_UPCOMING);
        } else {
            if (count($this->selectedRafflesUpcoming) >= 5) {
                session()->flash('message_error', 'Máximo 5 sorteos en esta sección.');
                return;
            }
            $order = count($this->selectedRafflesUpcoming);
            HomeConfig::create([
                'section' => HomeConfig::SECTION_RAFFLES_UPCOMING,
                'item_id' => $itemId,
                'order' => $order,
            ]);
            $this->selectedRafflesUpcoming[] = (int) $itemId;
        }
    }

    public function toggleBonus($itemId)
    {
        $this->ensureCanEditHome();

        if (in_array($itemId, $this->selectedBonuses)) {
            HomeConfig::where('section', HomeConfig::SECTION_BONUSES)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedBonuses = array_values(array_diff($this->selectedBonuses, [$itemId]));
            $this->reorderSection(HomeConfig::SECTION_BONUSES);
        } else {
            if (count($this->selectedBonuses) >= 5) {
                session()->flash('message_error', 'Máximo 5 bonos en la home.');
                return;
            }
            $order = count($this->selectedBonuses);
            HomeConfig::create([
                'section' => HomeConfig::SECTION_BONUSES,
                'item_id' => $itemId,
                'order' => $order,
            ]);
            $this->selectedBonuses[] = (int) $itemId;
        }
    }

    public function toggleBlog($itemId)
    {
        $this->ensureCanEditHome();

        if (in_array($itemId, $this->selectedBlogs)) {
            HomeConfig::where('section', HomeConfig::SECTION_BLOG)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedBlogs = array_values(array_diff($this->selectedBlogs, [$itemId]));
            $this->reorderSection(HomeConfig::SECTION_BLOG);
        } else {
            if (count($this->selectedBlogs) >= 3) {
                session()->flash('message_error', 'Máximo 3 entradas de blog en la home.');
                return;
            }
            $order = count($this->selectedBlogs);
            HomeConfig::create([
                'section' => HomeConfig::SECTION_BLOG,
                'item_id' => $itemId,
                'order' => $order,
            ]);
            $this->selectedBlogs[] = (int) $itemId;
        }
    }

    public function moveItemUp($section, $itemId)
    {
        $this->ensureCanEditHome();
        
        $item = HomeConfig::where('section', $section)->where('item_id', $itemId)->first();
        if (!$item || $item->order === 0) return;

        $prev = HomeConfig::where('section', $section)
            ->where('order', $item->order - 1)
            ->first();

        if ($prev) {
            $prev->update(['order' => $item->order]);
            $item->update(['order' => $item->order - 1]);
        }

        $this->refreshSelections();
    }

    public function moveItemDown($section, $itemId)
    {
        $this->ensureCanEditHome();

        $item = HomeConfig::where('section', $section)->where('item_id', $itemId)->first();
        if (!$item) return;

        $maxOrder = HomeConfig::where('section', $section)->max('order');
        if ($item->order >= $maxOrder) return;

        $next = HomeConfig::where('section', $section)
            ->where('order', $item->order + 1)
            ->first();

        if ($next) {
            $next->update(['order' => $item->order]);
            $item->update(['order' => $item->order + 1]);
        }

        $this->refreshSelections();
    }

    private function reorderSection($section)
    {
        $items = HomeConfig::where('section', $section)->orderBy('order')->get();
        foreach ($items as $index => $item) {
            $item->update(['order' => $index]);
        }
        $this->refreshSelections();
    }

    private function refreshSelections()
    {
        $this->selectedBonuses = HomeConfig::where('section', HomeConfig::SECTION_BONUSES)->orderBy('order')->pluck('item_id')->toArray();
        $this->selectedBlogs = HomeConfig::where('section', HomeConfig::SECTION_BLOG)->orderBy('order')->pluck('item_id')->toArray();
        $this->selectedRafflesUpcoming = HomeConfig::where('section', HomeConfig::SECTION_RAFFLES_UPCOMING)->orderBy('order')->pluck('item_id')->toArray();
    }

    public function render()
    {
        $this->ensureCanEditHome();

        return view('livewire.editor-home');
    }

    private function ensureCanEditHome(): void
    {
        if (! $this->hasLinePermission(Permissions::HOME_EDIT)) {
            abort(403, 'Sin permiso para editar la home.');
        }
    }
}
