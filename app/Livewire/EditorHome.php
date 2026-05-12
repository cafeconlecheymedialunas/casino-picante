<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\CarouselItem;
use App\Models\HomeConfig;
use App\Models\Post;
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

    public $selectedCarousel = [];

    public $selectedBonuses = [];

    public $selectedBlogs = [];

    public $newCarouselTitle = '';

    public $newCarouselLink = '';

    public $newCarouselImage = null;

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

    public function toggleBonus($itemId)
    {
        $this->ensureCanEditHome();

        if (in_array($itemId, $this->selectedBonuses)) {
            HomeConfig::where('section', HomeConfig::SECTION_BONUSES)
                ->where('item_id', $itemId)
                ->delete();
            $this->selectedBonuses = array_values(array_diff($this->selectedBonuses, [$itemId]));
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
