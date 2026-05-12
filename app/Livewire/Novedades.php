<?php

namespace App\Livewire;

use App\Models\Post;
use App\Support\ImageStorage;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Novedades extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

    public $statusFilter = 'all';

    public $showPanel = false;

    public $search = '';

    public $title = '';

    public $content = '';

    public $excerpt = '';

    public $status = 'published';

    public $image = '';

    public $imageUpload = null;

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'nullable',
        'excerpt' => 'nullable',
        'status' => 'required|in:draft,published,hidden',
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

    public function resetForm(): void
    {
        $this->title = '';
        $this->content = '';
        $this->excerpt = '';
        $this->status = 'published';
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
            'image' => $imagePath,
            'line_id' => session('active_line_id'),
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
        $query = Post::query();

        if ($lineIds !== null) {
            $query->whereIn('line_id', $lineIds);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
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
            'canDelete' => $this->canDelete(),
        ])->layout('layouts.dashboard');
    }
}
