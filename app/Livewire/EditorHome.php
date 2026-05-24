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

        $this->selectedCarousel = $this->homeConfigQuery(HomeConfig::SECTION_CAROUSEL)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->selectedBonuses = $this->homeConfigQuery(HomeConfig::SECTION_BONUSES)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->selectedBlogs = $this->homeConfigQuery(HomeConfig::SECTION_BLOG)
            ->orderBy('order')
            ->pluck('item_id')
            ->toArray();

        $this->selectedRafflesUpcoming = $this->homeConfigQuery(HomeConfig::SECTION_RAFFLES_UPCOMING)
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
                $section = HomeSection::create([
                    'vendor_id' => $this->activeVendorId(),
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
                'repeater_data' => is_array($section->repeater_data) ? $section->repeater_data : ($defaults['repeater_data'] ?? []),
                'raffle_type' => $section->raffle_type ?? '',
                'raffle_ids' => is_array($section->raffle_ids) ? implode(',', $section->raffle_ids) : '',
                'post_type' => $section->post_type ?? '',
                'post_ids' => is_array($section->post_ids) ? implode(',', $section->post_ids) : '',
                'bonus_type' => $section->bonus_type ?? '',
                'bonus_ids' => is_array($section->bonus_ids) ? implode(',', $section->bonus_ids) : '',
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

        $parseToArray = function ($str, ?string $model = null) {
            $arr = array_values(array_filter(array_map('trim', explode(',', (string) $str))));
            if ($model && session('active_vendor_id')) {
                $arr = $this->filterScopedIds($model, $arr);
            }

            return $arr; // always return array (empty = explicitly none selected)
        };

        $repeaterData = $data['repeater_data'] ?? null;
        if (! is_array($repeaterData)) {
            $repeaterData = null;
        }

        HomeSection::updateOrCreate(
            ['vendor_id' => $this->activeVendorId(), 'section_key' => $key],
            [
                'kicker' => $data['kicker'] ?? null,
                'title' => $data['title'] ?? null,
                'highlight' => $data['highlight'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'content' => $data['content'] ?? null,
                'repeater_data' => $repeaterData,
                'raffle_type' => $data['raffle_type'] ?? null,
                'raffle_ids' => $parseToArray($raffleIds, Raffle::class),
                'post_type' => $data['post_type'] ?? null,
                'post_ids' => $parseToArray($postIds, Post::class),
                'bonus_type' => $data['bonus_type'] ?? null,
                'bonus_ids' => $parseToArray($bonusIds, Bonus::class),
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
            $section->update(['enabled' => ! $section->enabled]);
            $this->sections[$key]['enabled'] = ! $section->enabled;
        }
    }

    public function loadCarouselItems(): void
    {
        $this->carouselItems = CarouselItem::where('vendor_id', $this->activeVendorId())->orderBy('order')->get()->toArray();
    }

    public function addCarouselItem(): void
    {
        $this->ensureCanEditHome();

        $this->validate([
            'newCarouselImage' => 'required|image|max:5120',
            'newCarouselTitle' => 'nullable|string|max:255',
            'newCarouselLink' => 'nullable|string|max:500',
        ]);

        $maxOrder = CarouselItem::where('vendor_id', $this->activeVendorId())->max('order') ?? 0;

        CarouselItem::create([
            'image' => ImageStorage::store($this->newCarouselImage, 'carousel'),
            'vendor_id' => $this->activeVendorId(),
            'title' => $this->newCarouselTitle,
            'link' => $this->newCarouselLink,
            'order' => $maxOrder + 1,
            'line_id' => $this->requireLineIdForScopedCreate(),
        ]);

        $this->newCarouselTitle = '';
        $this->newCarouselLink = '';
        $this->newCarouselImage = null;

        $this->loadCarouselItems();
    }

    public function removeCarouselItem($itemId): void
    {
        $this->ensureCanEditHome();

        $this->authorizeScopedItem(CarouselItem::class, $itemId);
        $item = CarouselItem::where('vendor_id', $this->activeVendorId())->find($itemId);
        if ($item) {
            ImageStorage::delete($item->image);
            $item->delete();
        }

        $this->loadCarouselItems();
    }

    public function moveCarouselUp($itemId): void
    {
        $this->ensureCanEditHome();

        $this->authorizeScopedItem(CarouselItem::class, $itemId);
        $item = CarouselItem::where('vendor_id', $this->activeVendorId())->find($itemId);
        if (! $item) {
            return;
        }

        $prev = CarouselItem::where('vendor_id', $this->activeVendorId())
            ->where('order', '<', $item->order)
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

        $this->authorizeScopedItem(CarouselItem::class, $itemId);
        $item = CarouselItem::where('vendor_id', $this->activeVendorId())->find($itemId);
        if (! $item) {
            return;
        }

        $next = CarouselItem::where('vendor_id', $this->activeVendorId())
            ->where('order', '>', $item->order)
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
        $this->authorizeScopedItem(CarouselItem::class, $itemId);

        if (in_array($itemId, $this->selectedCarousel)) {
            $this->homeConfigQuery(HomeConfig::SECTION_CAROUSEL)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedCarousel = array_values(array_diff($this->selectedCarousel, [$itemId]));
        } else {
            $order = count($this->selectedCarousel);
            HomeConfig::create([
                'vendor_id' => $this->activeVendorId(),
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
        $this->authorizeScopedItem(Raffle::class, $itemId);

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
        $this->authorizeScopedItem(Bonus::class, $itemId);

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
        $this->authorizeScopedItem(Post::class, $itemId);

        $current = array_filter(array_map('trim', explode(',', $this->sections['blog']['post_ids'] ?? '')));
        $itemId = (string) $itemId;

        if (in_array($itemId, $current)) {
            $current = array_values(array_filter($current, fn ($id) => $id !== $itemId));
        } else {
            $current[] = $itemId;
        }

        $this->sections['blog']['post_ids'] = implode(',', $current);
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

    public function moveRaffleUp($itemId)
    {
        $this->ensureCanEditHome();

        $current = array_filter(array_map('trim', explode(',', $this->sections['sorteo']['raffle_ids'] ?? '')));
        $itemId = (string) $itemId;
        $idx = array_search($itemId, $current);

        if ($idx === false || $idx === 0) {
            return;
        }

        $values = array_values($current);
        $tmp = $values[$idx];
        $values[$idx] = $values[$idx - 1];
        $values[$idx - 1] = $tmp;

        $this->sections['sorteo']['raffle_ids'] = implode(',', $values);
    }

    public function moveRaffleDown($itemId)
    {
        $this->ensureCanEditHome();

        $current = array_filter(array_map('trim', explode(',', $this->sections['sorteo']['raffle_ids'] ?? '')));
        $itemId = (string) $itemId;
        $idx = array_search($itemId, $current);

        if ($idx === false || $idx === count($current) - 1) {
            return;
        }

        $values = array_values($current);
        $tmp = $values[$idx];
        $values[$idx] = $values[$idx + 1];
        $values[$idx + 1] = $tmp;

        $this->sections['sorteo']['raffle_ids'] = implode(',', $values);
    }

    public function moveItemUp($section, $itemId)
    {
        $this->ensureCanEditHome();

        $item = $this->homeConfigQuery($section)->where('item_id', $itemId)->first();
        if (! $item || $item->order === 0) {
            return;
        }

        $prev = $this->homeConfigQuery($section)
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

        $item = $this->homeConfigQuery($section)->where('item_id', $itemId)->first();
        if (! $item) {
            return;
        }

        $maxOrder = $this->homeConfigQuery($section)->max('order');
        if ($item->order >= $maxOrder) {
            return;
        }

        $next = $this->homeConfigQuery($section)
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
        $items = $this->homeConfigQuery($section)->orderBy('order')->get();
        foreach ($items as $index => $item) {
            $item->update(['order' => $index]);
        }
        $this->refreshSelections();
    }

    private function refreshSelections()
    {
        $this->selectedBonuses = $this->homeConfigQuery(HomeConfig::SECTION_BONUSES)->orderBy('order')->pluck('item_id')->toArray();
        $this->selectedBlogs = $this->homeConfigQuery(HomeConfig::SECTION_BLOG)->orderBy('order')->pluck('item_id')->toArray();
        $this->selectedRafflesUpcoming = $this->homeConfigQuery(HomeConfig::SECTION_RAFFLES_UPCOMING)->orderBy('order')->pluck('item_id')->toArray();
    }

    public function saveSectionAjax()
    {
        $this->ensureCanEditHome();

        $key = request()->input('section_key');
        if (! in_array($key, ['sorteo', 'bonos', 'blog'])) {
            return response()->json(['message' => 'Sección inválida'], 400);
        }

        $parseToArray = function ($str, ?string $model = null) {
            if (empty($str)) {
                return null;
            }
            $arr = array_filter(array_map('trim', explode(',', $str)));
            if ($model && session('active_vendor_id')) {
                $arr = $this->filterScopedIds($model, $arr);
            }

            return count($arr) ? $arr : null;
        };

        HomeSection::updateOrCreate(
            ['vendor_id' => $this->activeVendorId(), 'section_key' => $key],
            [
                'raffle_ids' => $key === 'sorteo' ? $parseToArray(request()->input('raffle_ids', ''), Raffle::class) : null,
                'bonus_ids' => $key === 'bonos' ? $parseToArray(request()->input('bonus_ids', ''), Bonus::class) : null,
                'post_ids' => $key === 'blog' ? $parseToArray(request()->input('post_ids', ''), Post::class) : null,
            ]
        );

        return response()->json(['message' => 'Sección guardada correctamente']);
    }

    public function saveSelections()
    {
        $this->ensureCanEditHome();

        $parseToArray = function ($str, ?string $model = null) {
            if (empty($str)) {
                return null;
            }
            $arr = array_filter(array_map('trim', explode(',', $str)));
            if ($model && session('active_vendor_id')) {
                $arr = $this->filterScopedIds($model, $arr);
            }

            return count($arr) ? $arr : null;
        };

        $sorteoIds = $parseToArray(request()->input('sorteo', ''), Raffle::class);
        $bonosIds = $parseToArray(request()->input('bonos', ''), Bonus::class);
        $blogIds = $parseToArray(request()->input('blog', ''), Post::class);

        HomeSection::updateOrCreate(
            ['vendor_id' => $this->activeVendorId(), 'section_key' => 'sorteo'],
            ['raffle_ids' => $sorteoIds]
        );

        HomeSection::updateOrCreate(
            ['vendor_id' => $this->activeVendorId(), 'section_key' => 'bonos'],
            ['bonus_ids' => $bonosIds]
        );

        HomeSection::updateOrCreate(
            ['vendor_id' => $this->activeVendorId(), 'section_key' => 'blog'],
            ['post_ids' => $blogIds]
        );

        session()->flash('message_success', 'Selecciones guardadas correctamente.');

        return redirect()->route('admin.editor.inicio');
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

        $this->activeVendorId();
    }

    private function authorizeScopedItem(string $model, $id): void
    {
        if (! session('active_vendor_id')) {
            return;
        }

        abort_unless($model::whereKey((int) $id)->where('vendor_id', (int) session('active_vendor_id'))->exists(), 403, 'No podes seleccionar contenido fuera de tu vendor.');
    }

    private function filterScopedIds(string $model, array $ids): array
    {
        $ids = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return $model::whereIn('id', $ids)
            ->where('vendor_id', (int) session('active_vendor_id'))
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    private function activeVendorId(): int
    {
        abort_unless(session('active_vendor_id'), 403, 'Selecciona un vendor antes de editar la home.');

        return (int) session('active_vendor_id');
    }

    private function homeConfigQuery(?string $section = null)
    {
        return HomeConfig::query()
            ->where('vendor_id', $this->activeVendorId())
            ->when($section, fn ($query) => $query->where('section', $section));
    }

    private function homeSectionQuery(string $key)
    {
        return HomeSection::query()
            ->where('vendor_id', $this->activeVendorId())
            ->where('section_key', $key);
    }
}
