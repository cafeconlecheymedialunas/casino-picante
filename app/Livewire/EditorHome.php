<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\CarouselItem;
use App\Models\Category;
use App\Models\HomeConfig;
use App\Models\HomeSection;
use App\Models\Line;
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
    public $lineItems = [];
    public $categories = [];
    public $selectedCarousel = [];
    public $newCarouselTitle = '';
    public $newCarouselLink = '';
    public $newCarouselImage = null;
    public $sections = [];
    public $editingSection = null;
    public $pendingSave = [];

    public function mount()
    {
        $this->ensureCanEditHome();

        $this->loadCarouselItems();

        $this->bonusItems = Bonus::withoutGlobalScopes()
            ->where('status', 'active')
            ->with('vendor:id,name,is_direct')
            ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = bonuses.vendor_id AND vendors.is_direct = 1) DESC')
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();

        $this->blogPosts = Post::withoutGlobalScopes()
            ->where('status', Post::STATUS_PUBLISHED)
            ->with('vendor:id,name,is_direct')
            ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = posts.vendor_id AND vendors.is_direct = 1) DESC')
            ->orderBy('published_at', 'desc')
            ->get()
            ->toArray();

        $this->raffleItems = Raffle::withoutGlobalScopes()
            ->whereIn('status', ['active', 'upcoming'])
            ->with('vendor:id,name,is_direct')
            ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = raffles.vendor_id AND vendors.is_direct = 1) DESC')
            ->orderBy('start_date', 'asc')
            ->get()
            ->toArray();

        $this->lineItems = Line::withoutGlobalScopes()
            ->where('status', 'active')
            ->with('vendor:id,name,is_direct')
            ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = lines.vendor_id AND vendors.is_direct = 1) DESC')
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->categories = Category::withoutGlobalScopes()->get()->toArray();

        $this->selectedCarousel = $this->homeConfigQuery(HomeConfig::SECTION_CAROUSEL)
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
                    ['title' => 'Cargá saldo', 'subtitle' => 'Consultá medios de carga y bonos disponibles para tu cuenta.'],
                    ['title' => 'Entrá a jugar', 'subtitle' => 'Disfrutá tus juegos favoritos, participá en sorteos y pedí asistencia cuando quieras.'],
                ],
            ],
            'lineas' => ['kicker' => 'Empeza a jugar', 'title' => 'Lineas de', 'highlight' => 'atencion', 'subtitle' => 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.'],
            'sorteo' => ['kicker' => 'Muy pronto', 'title' => 'PRÓXIMOS', 'highlight' => 'SORTEOS', 'subtitle' => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.'],
            'nosotros' => [
                'kicker' => 'Sobre RED PICANTES',
                'title' => 'Casino online con atencion',
                'highlight' => 'real',
                'subtitle' => 'Una experiencia pensada para jugar facil: acceso rapido, bonos claros, sorteos activos y soporte humano para acompaniarte.',
                'repeater_data' => [
                    ['title' => 'Alta rapida', 'subtitle' => 'Contactás una línea y pedís tu usuario sin formularios eternos.'],
                    ['title' => 'Bonos vigentes', 'subtitle' => 'Bonos para recargar, arrancar con ventaja y jugar más.'],
                    ['title' => 'Sorteos activos', 'subtitle' => 'Premios y chances extra para usuarios que participan.'],
                    ['title' => 'Soporte humano', 'subtitle' => 'Atención directa para cargas, retiros, dudas y novedades.'],
                ],
            ],
            'bonos' => ['kicker' => 'Bonos para jugar mas', 'title' => 'Bonos', 'highlight' => 'activos', 'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.'],
            'blog' => ['kicker' => 'Noticias y jugadas', 'title' => 'Noticias y', 'highlight' => 'jugadas', 'subtitle' => 'Enterate de novedades, sorteos, recomendaciones y bonos nuevos antes de que pasen.'],
        ];

        foreach ($defaultSections as $key => $defaults) {
            $section = $this->homeSectionQuery($key)->first();
            if (! $section) {
                $section = HomeSection::withoutGlobalScopes()->create([
                    'vendor_id' => null,
                    'section_key' => $key,
                    'enabled' => true,
                    'order' => array_search($key, array_keys($defaultSections)),
                    'kicker' => $defaults['kicker'] ?? null,
                    'title' => $defaults['title'] ?? null,
                    'highlight' => $defaults['highlight'] ?? null,
                    'subtitle' => $defaults['subtitle'] ?? null,
                    'repeater_data' => isset($defaults['repeater_data']) ? json_encode($defaults['repeater_data']) : null,
                ]);
            }

            $this->sections[$key] = [
                'id' => $section->id,
                'kicker' => $section->kicker ?? $defaults['kicker'] ?? '',
                'title' => $section->title ?? $defaults['title'] ?? '',
                'highlight' => $section->highlight ?? $defaults['highlight'] ?? '',
                'subtitle' => $section->subtitle ?? $defaults['subtitle'] ?? '',
                'content' => $section->content ?? $defaults['content'] ?? '',
                'action_text' => $section->action_text ?? '',
                'action_url' => $section->action_url ?? '',
                'repeater_data' => is_array($section->repeater_data) ? $section->repeater_data : ($defaults['repeater_data'] ?? []),
                'raffle_type' => $section->raffle_type ?? '',
                'raffle_ids' => is_array($section->raffle_ids) ? implode(',', $section->raffle_ids) : '',
                'post_type' => $section->post_type ?? '',
                'post_ids' => is_array($section->post_ids) ? implode(',', $section->post_ids) : '',
                'bonus_type' => $section->bonus_type ?? '',
                'bonus_ids' => is_array($section->bonus_ids) ? implode(',', $section->bonus_ids) : '',
                'line_ids' => is_array($section->line_ids) ? implode(',', $section->line_ids) : '',
                'enabled' => $section->enabled,
            ];
        }
    }

    public function saveSection(string $key): void
    {
        $this->ensureCanEditHome();

        $data = $this->sections[$key] ?? [];

        $parseToArray = function ($str) {
            return array_values(array_filter(array_map('trim', explode(',', (string) $str))));
        };

        $repeaterData = $data['repeater_data'] ?? null;
        if (! is_array($repeaterData)) {
            $repeaterData = null;
        }

        HomeSection::withoutGlobalScopes()->updateOrCreate(
            ['vendor_id' => null, 'section_key' => $key],
            [
                'kicker' => $data['kicker'] ?? null,
                'title' => $data['title'] ?? null,
                'highlight' => $data['highlight'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'content' => $data['content'] ?? null,
                'action_text' => $data['action_text'] ?? null,
                'action_url' => $data['action_url'] ?? null,
                'repeater_data' => $repeaterData,
                'raffle_type' => $data['raffle_type'] ?? null,
                'raffle_ids' => $parseToArray($data['raffle_ids'] ?? ''),
                'post_type' => $data['post_type'] ?? null,
                'post_ids' => $parseToArray($data['post_ids'] ?? ''),
                'bonus_type' => $data['bonus_type'] ?? null,
                'bonus_ids' => $parseToArray($data['bonus_ids'] ?? ''),
                'line_ids' => $parseToArray($data['line_ids'] ?? ''),
                'enabled' => $data['enabled'] ?? true,
            ]
        );

        $this->loadSections();
        session()->flash('message_success', 'Sección guardada correctamente.');
    }

    public function addRepeaterItem(string $key): void
    {
        if (! isset($this->sections[$key]['repeater_data']) || ! is_array($this->sections[$key]['repeater_data'])) {
            $this->sections[$key]['repeater_data'] = [];
        }
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

        $section = $this->homeSectionQuery($key)->first();
        if ($section) {
            $newEnabled = ! $section->enabled;
            $section->update(['enabled' => $newEnabled]);
            $this->sections[$key]['enabled'] = $newEnabled;
        }
    }

    public function loadCarouselItems(): void
    {
        $this->carouselItems = CarouselItem::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function addCarouselItem(): void
    {
        $this->ensureCanEditHome();

        $this->validate([
            'newCarouselImage' => 'required|image|max:5120',
            'newCarouselTitle' => 'nullable|string|max:255',
            'newCarouselLink' => 'nullable|string|max:500',
        ]);

        $maxOrder = CarouselItem::withoutGlobalScopes()->whereNull('vendor_id')->max('order') ?? 0;

        CarouselItem::withoutGlobalScopes()->create([
            'image' => ImageStorage::store($this->newCarouselImage, 'carousel'),
            'vendor_id' => null,
            'line_id' => null,
            'title' => $this->newCarouselTitle,
            'link' => $this->newCarouselLink,
            'order' => $maxOrder + 1,
        ]);

        $this->newCarouselTitle = '';
        $this->newCarouselLink = '';
        $this->newCarouselImage = null;

        $this->loadCarouselItems();
    }

    public function removeCarouselItem($itemId): void
    {
        $this->ensureCanEditHome();

        $item = CarouselItem::withoutGlobalScopes()->whereNull('vendor_id')->find($itemId);
        if ($item) {
            ImageStorage::delete($item->image);
            $item->delete();
        }

        $this->loadCarouselItems();
    }

    public function moveCarouselUp($itemId): void
    {
        $this->ensureCanEditHome();

        $item = CarouselItem::withoutGlobalScopes()->whereNull('vendor_id')->find($itemId);
        if (! $item) {
            return;
        }

        $prev = CarouselItem::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->where('order', '<', $item->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($prev) {
            [$item->order, $prev->order] = [$prev->order, $item->order];
            $item->save();
            $prev->save();
        }

        $this->loadCarouselItems();
    }

    public function moveCarouselDown($itemId): void
    {
        $this->ensureCanEditHome();

        $item = CarouselItem::withoutGlobalScopes()->whereNull('vendor_id')->find($itemId);
        if (! $item) {
            return;
        }

        $next = CarouselItem::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->where('order', '>', $item->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($next) {
            [$item->order, $next->order] = [$next->order, $item->order];
            $item->save();
            $next->save();
        }

        $this->loadCarouselItems();
    }

    public function toggleCarousel($itemId)
    {
        $this->ensureCanEditHome();

        if (in_array($itemId, $this->selectedCarousel)) {
            $this->homeConfigQuery(HomeConfig::SECTION_CAROUSEL)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedCarousel = array_values(array_diff($this->selectedCarousel, [$itemId]));
        } else {
            $order = count($this->selectedCarousel);
            HomeConfig::withoutGlobalScopes()->create([
                'vendor_id' => null,
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

        $current = array_filter(array_map('trim', explode(',', $this->sections['sorteo']['raffle_ids'] ?? '')));
        $itemId = (string) $itemId;

        if (in_array($itemId, $current)) {
            $current = array_values(array_filter($current, fn ($id) => $id !== $itemId));
        } else {
            $current[] = $itemId;
        }

        $this->sections['sorteo']['raffle_ids'] = implode(',', $current);
    }

    public function toggleBonus($itemId)
    {
        $this->ensureCanEditHome();

        $current = array_filter(array_map('trim', explode(',', $this->sections['bonos']['bonus_ids'] ?? '')));
        $itemId = (string) $itemId;

        if (in_array($itemId, $current)) {
            $current = array_values(array_filter($current, fn ($id) => $id !== $itemId));
        } else {
            $current[] = $itemId;
        }

        $this->sections['bonos']['bonus_ids'] = implode(',', $current);
    }

    public function togglePost($itemId)
    {
        $this->ensureCanEditHome();

        $current = array_filter(array_map('trim', explode(',', $this->sections['blog']['post_ids'] ?? '')));
        $itemId = (string) $itemId;

        if (in_array($itemId, $current)) {
            $current = array_values(array_filter($current, fn ($id) => $id !== $itemId));
        } else {
            $current[] = $itemId;
        }

        $this->sections['blog']['post_ids'] = implode(',', $current);
    }

    public function toggleLine($itemId)
    {
        $this->ensureCanEditHome();

        $current = array_filter(array_map('trim', explode(',', $this->sections['lineas']['line_ids'] ?? '')));
        $itemId = (string) $itemId;

        if (in_array($itemId, $current)) {
            $current = array_values(array_filter($current, fn ($id) => $id !== $itemId));
        } else {
            $current[] = $itemId;
        }

        $this->sections['lineas']['line_ids'] = implode(',', $current);
    }

    public function saveAllSelections(): void
    {
        $this->ensureCanEditHome();
        $this->saveSection('sorteo');
        $this->saveSection('bonos');
        $this->saveSection('blog');
    }

    public function saveSingleSection(string $key): void
    {
        $this->ensureCanEditHome();
        $this->saveSection($key);
    }

    public function moveItemUp($section, $itemId)
    {
        $this->ensureCanEditHome();

        $item = $this->homeConfigQuery($section)->where('item_id', $itemId)->first();
        if (! $item || $item->order === 0) {
            return;
        }

        $prev = $this->homeConfigQuery($section)->where('order', $item->order - 1)->first();
        if ($prev) {
            $prev->update(['order' => $item->order]);
            $item->update(['order' => $item->order - 1]);
        }
    }

    public function moveItemDown($section, $itemId)
    {
        $this->ensureCanEditHome();

        $item = $this->homeConfigQuery($section)->where('item_id', $itemId)->first();
        if (! $item) {
            return;
        }

        $maxOrder = $this->homeConfigQuery($section)->max('order');
        if ($item->order >= $maxOrder) {
            return;
        }

        $next = $this->homeConfigQuery($section)->where('order', $item->order + 1)->first();
        if ($next) {
            $next->update(['order' => $item->order]);
            $item->update(['order' => $item->order + 1]);
        }
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

    private function homeConfigQuery(?string $section = null)
    {
        return HomeConfig::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->when($section, fn ($query) => $query->where('section', $section));
    }

    private function homeSectionQuery(string $key)
    {
        return HomeSection::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->where('section_key', $key);
    }
}
