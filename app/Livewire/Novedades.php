<?php

namespace App\Livewire;

use App\Models\Post;
use App\Traits\HasLinePermissions;
use Livewire\Component;

class Novedades extends Component
{
    use HasLinePermissions;

    public $tab = 'novedad';

    public $statusFilter = 'all';

    public $selectedPost = null;

    public $showModal = false;

    public $editingPost = null;

    public $search = '';

    public $title = '';

    public $content = '';

    public $excerpt = '';

    public $status = 'draft';

    public $type = 'novedad';

    public $image = '';

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'nullable',
        'excerpt' => 'nullable',
        'status' => 'required|in:draft,published,hidden',
        'type' => 'required|in:novedad,blog,carrusel',
    ];

    public function canCreate(): bool
    {
        return $this->hasLinePermission('news.create');
    }

    public function canUpdate(): bool
    {
        return $this->hasLinePermission('news.update');
    }

    public function canDelete(): bool
    {
        return $this->hasLinePermission('news.delete');
    }

    public function canRead(): bool
    {
        return $this->hasLinePermission('news.read');
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->type = $tab;
        $this->selectedPost = null;
    }

    public function selectPost($id)
    {
        $this->selectedPost = Post::find($id);
    }

    public function openCreateModal()
    {
        $this->checkLinePermission('news.create');
        $this->resetForm();
        $this->type = $this->tab;
        $this->showModal = true;
    }

    public function openEditModal($postId)
    {
        $this->checkLinePermission('news.update');
        $post = Post::find($postId);
        $this->editingPost = $post;
        $this->title = $post->title;
        $this->content = $post->content ?? '';
        $this->excerpt = $post->excerpt ?? '';
        $this->status = $post->status;
        $this->type = $post->type;
        $this->image = $post->image ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPost = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->excerpt = '';
        $this->status = 'draft';
        $this->image = '';
    }

    public function savePost()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'type' => $this->type,
            'image' => $this->image,
        ];

        if ($this->editingPost) {
            $this->editingPost->update($data);
            session()->flash('message', 'Contenido actualizado correctamente');
        } else {
            $data['line_id'] = session('active_line_id');
            Post::create($data);
            session()->flash('message', 'Contenido creado correctamente');
        }

        $this->closeModal();
    }

    public function deletePost($postId)
    {
        $this->checkLinePermission('news.delete');
        Post::find($postId)->delete();

        if ($this->selectedPost && $this->selectedPost->id === $postId) {
            $this->selectedPost = null;
        }

        session()->flash('message', 'Contenido eliminado correctamente');
    }

    public function toggleStatus($postId)
    {
        $this->checkLinePermission('news.update');
        $post = Post::find($postId);
        $post->update(['status' => $post->status === 'published' ? 'draft' : 'published']);
    }

    public function getPosts()
    {
        $this->checkLinePermission('news.read');

        $query = Post::query();

        if ($this->tab !== 'all') {
            $query->where('type', $this->tab);
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
        $posts = $this->getPosts();

        return view('livewire.novedades', [
            'posts' => $posts,
            'canCreate' => $this->canCreate(),
            'canUpdate' => $this->canUpdate(),
            'canDelete' => $this->canDelete(),
            'canRead' => $this->canRead(),
        ])->layout('layouts.dashboard');
    }
}
