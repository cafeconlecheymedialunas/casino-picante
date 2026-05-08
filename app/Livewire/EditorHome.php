<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\HomeConfig;
use App\Models\Post;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class EditorHome extends Component
{
    use HasLinePermissions;

    public $carouselPosts = [];
    public $bonusItems = [];
    public $blogPosts = [];

    public $selectedCarousel = [];
    public $selectedBonuses = [];
    public $selectedBlogs = [];

    public function mount()
    {
        $this->ensureCanEditHome();

        $this->carouselPosts = Post::where('type', Post::TYPE_CARRUSEL)
            ->where('status', Post::STATUS_PUBLISHED)
            ->orderBy('published_at', 'desc')
            ->get()
            ->toArray();

        $this->bonusItems = Bonus::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();

        $this->blogPosts = Post::where('type', Post::TYPE_BLOG)
            ->where('status', Post::STATUS_PUBLISHED)
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
