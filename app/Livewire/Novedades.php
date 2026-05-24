<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Category;
use App\Models\LineAgent;
use App\Models\Post;
use App\Support\ImageStorage;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Novedades extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

    public $statusFilter = 'all';

    public $categoryFilter = 'all';

    public $showPanel = false;

    public $showCategoryPanel = false;

    public $search = '';

    // Post fields
    public $title = '';

    public $content = '';

    public $excerpt = '';

    public $status = 'published';

    public $category_id = '';

    public $author_agent_id = '';

    public $image = '';

    public $imageUpload = null;

    // Category management
    public $newCategoryName = '';

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'nullable',
        'excerpt' => 'nullable',
        'status' => 'required|in:draft,published,hidden',
        'category_id' => 'nullable|exists:categories,id',
        'author_agent_id' => 'nullable|exists:agents,id',
    ];

    public function canCreate(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_CREATE);
    }

    public function canDelete(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_DELETE);
    }

    public function openCreatePanel(): void
    {
        $this->checkLinePermission(Permissions::NEWS_CREATE);
        $this->resetForm();
        $this->showPanel = true;
    }

    public function closeSidePanel(): void
    {
        $this->showPanel = false;
        $this->resetForm();
    }

    public function openCategoryPanel(): void
    {
        $this->checkLinePermission(Permissions::NEWS_CREATE);
        $this->newCategoryName = '';
        $this->showCategoryPanel = true;
    }

    public function saveCategory(): void
    {
        $this->checkLinePermission(Permissions::NEWS_CREATE);
        $this->validate([
            'newCategoryName' => [
                'required',
                'min:2',
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query->where('vendor_id', session('active_vendor_id'))),
            ],
        ]);

        Category::create([
            'name' => $this->newCategoryName,
            'slug' => Str::slug($this->newCategoryName),
            'vendor_id' => session('active_vendor_id'),
        ]);

        $this->newCategoryName = '';
        session()->flash('category_message', 'Categoría creada');
    }

    public function deleteCategory(int $id): void
    {
        $this->checkLinePermission(Permissions::NEWS_DELETE);
        $category = Category::find($id);
        if ($category) {
            $this->authorizeVendorRecord($category);
            $category->delete();
        }
        session()->flash('category_message', 'Categoría eliminada');
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->content = '';
        $this->excerpt = '';
        $this->status = 'published';
        $this->category_id = '';
        $this->author_agent_id = session('active_agent_id') ?: '';
        $this->image = '';
        $this->imageUpload = null;
    }

    public function savePost(): void
    {
        $this->checkLinePermission(Permissions::NEWS_CREATE);

        $this->validate([
            ...$this->rules,
            'imageUpload' => 'nullable|image|max:4096',
        ]);
        $this->authorizeCategoryChoice($this->category_id);
        $this->authorizeAuthorChoice($this->author_agent_id);

        $imagePath = null;
        if ($this->imageUpload) {
            $imagePath = ImageStorage::store($this->imageUpload, 'contenidos');
        }

        $post = Post::create([
            'title' => $this->title,
            'slug' => Str::slug($this->title).'-'.uniqid(),
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->status === Post::STATUS_PUBLISHED ? now() : null,
            'category_id' => $this->category_id ?: null,
            'author_agent_id' => $this->author_agent_id ?: null,
            'image' => $imagePath,
            'line_id' => $this->requireLineIdForScopedCreate(),
        ]);

        session()->flash('message', 'Post creado correctamente');
        $this->notify('Nuevo post creado', "El post {$post->title} fue creado exitosamente.", 'posts', '/novedades', 'success');

        $this->closeSidePanel();
    }

    public function removeImage(): void
    {
        $this->imageUpload = null;
        $this->image = '';
    }

    public function deletePost(int $postId): void
    {
        $this->checkLinePermission(Permissions::NEWS_DELETE);
        $post = Post::find($postId);
        if ($post) {
            $this->authorizeVendorRecord($post);
        }
        ImageStorage::delete($post?->image);
        $postTitle = $post?->title;
        $post?->delete();

        session()->flash('message', 'Post eliminado correctamente');
        $this->notify('Post eliminado', "El post {$postTitle} fue eliminado.", 'posts', '/novedades', 'danger');
    }

    public function getPosts()
    {
        $this->checkLinePermission(Permissions::NEWS_READ);

        $lineIds = $this->visibleLineIds();
        $query = Post::with(['category', 'authorAgent']);

        if ($lineIds !== null) {
            $query->whereIn('line_id', $lineIds);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.novedades', [
            'posts' => $this->getPosts(),
            'categories' => Category::where(fn ($q) => $q->whereNull('vendor_id')->orWhere('vendor_id', session('active_vendor_id')))->orderBy('name')->get(),
            'authors' => $this->availableAuthors(),
            'canDelete' => $this->canDelete(),
        ])->layout('layouts.dashboard');
    }

    private function availableAuthors()
    {
        $query = Agent::orderBy('name');
        $query->when(session('active_vendor_id'), fn ($q, $vendorId) => $q->where('vendor_id', (int) $vendorId));
        $lineIds = $this->visibleLineIds();

        if ($lineIds !== null) {
            $agentIds = LineAgent::whereIn('line_id', $lineIds)
                ->where('is_active', true)
                ->pluck('agent_id');

            $query->whereIn('id', $agentIds);
        }

        return $query->get();
    }

    private function authorizeAuthorChoice($authorAgentId): void
    {
        if (! $authorAgentId) {
            return;
        }

        $allowed = $this->availableAuthors()
            ->pluck('id')
            ->contains((int) $authorAgentId);

        abort_unless($allowed, 403, 'No podes asignar un autor fuera de tu alcance.');
    }

    private function authorizeCategoryChoice($categoryId): void
    {
        if (! $categoryId || ! session('active_vendor_id')) {
            return;
        }

        abort_unless(Category::whereKey((int) $categoryId)->where('vendor_id', (int) session('active_vendor_id'))->exists(), 403, 'No podes usar categorias fuera de tu vendor.');
    }

    private function authorizeVendorRecord($model): void
    {
        if (! session('active_vendor_id')) {
            return;
        }

        abort_unless((int) $model->vendor_id === (int) session('active_vendor_id'), 403, 'No podes operar contenido fuera del vendor activo.');
    }
}
