<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use App\Support\ImageStorage;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Livewire\Component;
use Livewire\WithFileUploads;

class Novedades extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

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

    public $imageUpload = null;

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'nullable',
        'excerpt' => 'nullable',
        'status' => 'required|in:draft,published,hidden',
        'type' => 'required|in:novedad,blog,carrusel',
    ];

    public function canCreate(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_CREATE);
    }

    public function canUpdate(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_UPDATE);
    }

    public function canDelete(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_DELETE);
    }

    public function canRead(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_READ);
    }

    public function canModerateComments(): bool
    {
        return $this->hasLinePermission(Permissions::NEWS_UPDATE);
    }

    public function selectPost($id)
    {
        $this->checkLinePermission(Permissions::NEWS_READ);
        $this->selectedPost = Post::findOrFail($id);
    }

    public function openCreateModal()
    {
        $this->checkLinePermission(Permissions::NEWS_CREATE);
        $this->resetForm();
        $this->type = $this->tab;
        $this->showModal = true;
    }

    public function openEditModal($postId)
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);
        $post = Post::find($postId);
        $this->editingPost = $post;
        $this->title = $post->title;
        $this->content = $post->content ?? '';
        $this->excerpt = $post->excerpt ?? '';
        $this->status = $post->status;
        $this->type = $post->type;
        $this->image = $post->image ?? '';
        $this->imageUpload = null;
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
        $this->imageUpload = null;
    }

    public function savePost()
    {
        $this->editingPost
            ? $this->checkLinePermission(Permissions::NEWS_UPDATE)
            : $this->checkLinePermission(Permissions::NEWS_CREATE);

        $this->validate([
            ...$this->rules,
            'imageUpload' => 'nullable|image|max:4096',
        ]);

        $imagePath = $this->image;

        if ($this->imageUpload) {
            $imagePath = ImageStorage::store($this->imageUpload, 'contenidos', $this->image ?: null);
        }

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'type' => $this->type,
            'image' => $imagePath ?: null,
        ];

        if ($this->editingPost) {
            $this->editingPost->update($data);
            session()->flash('message', 'Contenido actualizado correctamente');

            $this->notify('Contenido actualizado', "El contenido {$this->editingPost->title} fue actualizado.", 'posts', '/novedades', 'info');
        } else {
            $data['line_id'] = session('active_line_id');
            $post = Post::create($data);
            session()->flash('message', 'Contenido creado correctamente');

            $this->notify('Nuevo contenido creado', "El contenido {$post->title} fue creado exitosamente.", 'posts', '/novedades', 'success');
        }

        $this->closeModal();
    }

    public function removeImage(): void
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);

        if ($this->editingPost && $this->image) {
            ImageStorage::delete($this->image);
            $this->editingPost->update(['image' => null]);
        }

        $this->imageUpload = null;
        $this->image = '';
    }

    public function deletePost($postId)
    {
        $this->checkLinePermission(Permissions::NEWS_DELETE);
        $post = Post::find($postId);
        ImageStorage::delete($post?->image);
        $postTitle = $post?->title;
        $post?->delete();

        if ($this->selectedPost && $this->selectedPost->id === $postId) {
            $this->selectedPost = null;
        }

        session()->flash('message', 'Contenido eliminado correctamente');

        $this->notify('Contenido eliminado', "El contenido {$postTitle} fue eliminado del sistema.", 'posts', '/novedades', 'danger');
    }

    public function toggleStatus($postId)
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);
        $post = Post::find($postId);
        $newStatus = $post->status === 'published' ? 'draft' : 'published';
        $post->update(['status' => $newStatus]);

        $this->notify('Estado de contenido cambiado', "El contenido {$post->title} fue ".($newStatus === 'published' ? 'publicado' : 'puesto en borrador').'.', 'posts', '/novedades', 'warning');
    }

    public function addComment()
    {
        if (! $this->selectedPost) {
            return;
        }

        $this->authorizePostLineAccess($this->selectedPost->id);

        $this->validate([
            'newComment' => 'required|string|min:3|max:1000',
        ]);

        Comment::create([
            'post_id' => $this->selectedPost->id,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
            'is_approved' => $this->canModerateComments(),
        ]);

        $this->newComment = '';
        session()->flash('message', $this->canModerateComments() ? 'Comentario agregado' : 'Comentario enviado para aprobación');
    }

    public function approveComment($commentId)
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);
        $comment = Comment::with('post')->findOrFail($commentId);
        $this->authorizePostLineAccess($comment->post_id);
        $comment->update(['is_approved' => true]);
        session()->flash('message', 'Comentario aprobado');
    }

    public function deleteComment($commentId)
    {
        $this->checkLinePermission(Permissions::NEWS_DELETE);
        $comment = Comment::with('post')->findOrFail($commentId);
        $this->authorizePostLineAccess($comment->post_id);
        $comment->delete();
        session()->flash('message', 'Comentario eliminado');
    }

    private function authorizePostLineAccess(int $postId): void
    {
        $lineIds = $this->visibleLineIds();
        if ($lineIds === null) {
            return;
        }
        $post = Post::findOrFail($postId);
        if (! in_array($post->line_id, $lineIds)) {
            abort(403, 'Sin acceso a este contenido.');
        }
    }

    public function getPosts()
    {
        $this->checkLinePermission(Permissions::NEWS_READ);

        $lineIds = $this->visibleLineIds();

        $query = Post::query();

        if ($lineIds !== null) {
            $query->whereIn('line_id', $lineIds);
        }

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
