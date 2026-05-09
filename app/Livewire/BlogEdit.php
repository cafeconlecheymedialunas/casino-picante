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

class BlogEdit extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

    public Post $post;

    public $title = '';
    public $content = '';
    public $excerpt = '';
    public $status = 'published';
    public $image = '';
    public $imageUpload = null;

    public $replyTo = null;
    public $replyContent = '';

    protected $rules = [
        'title'   => 'required|min:3',
        'content' => 'nullable',
        'excerpt' => 'nullable',
        'status'  => 'required|in:draft,published,hidden',
    ];

    public function mount(int $id): void
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);
        $this->post = Post::with([
            'comments' => fn($q) => $q->whereNull('parent_id')
                ->with(['user', 'replies.user'])
                ->orderBy('created_at'),
        ])->findOrFail($id);

        $this->title   = $this->post->title;
        $this->content = $this->post->content ?? '';
        $this->excerpt = $this->post->excerpt ?? '';
        $this->status  = $this->post->status;
        $this->image   = $this->post->image ?? '';
    }

    public function savePost(): void
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);

        $this->validate([
            ...$this->rules,
            'imageUpload' => 'nullable|image|max:4096',
        ]);

        $imagePath = $this->image;

        if ($this->imageUpload) {
            $imagePath = ImageStorage::store($this->imageUpload, 'contenidos', $this->image ?: null);
        }

        $this->post->update([
            'title'   => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status'  => $this->status,
            'image'   => $imagePath ?: null,
        ]);

        $this->image       = $imagePath ?: '';
        $this->imageUpload = null;

        session()->flash('message', 'Post actualizado correctamente');
        $this->notify('Post actualizado', "El post {$this->post->title} fue actualizado.", 'posts', '/novedades', 'info');
    }

    public function removeImage(): void
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);
        if ($this->image) {
            ImageStorage::delete($this->image);
            $this->post->update(['image' => null]);
        }
        $this->imageUpload = null;
        $this->image = '';
    }

    public function startReply(int $commentId): void
    {
        $this->replyTo      = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply(): void
    {
        $this->replyTo      = null;
        $this->replyContent = '';
    }

    public function submitReply(): void
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);

        $this->validate(['replyContent' => 'required|string|min:1|max:2000']);

        $parent = Comment::findOrFail($this->replyTo);

        Comment::create([
            'post_id'   => $parent->post_id,
            'parent_id' => $parent->id,
            'user_id'   => auth()->id(),
            'content'   => $this->replyContent,
            'is_approved' => true,
        ]);

        $this->replyTo      = null;
        $this->replyContent = '';
        $this->refreshComments();
    }

    public function approveComment(int $commentId): void
    {
        $this->checkLinePermission(Permissions::NEWS_UPDATE);
        Comment::findOrFail($commentId)->update(['is_approved' => true]);
        $this->refreshComments();
    }

    public function deleteComment(int $commentId): void
    {
        $this->checkLinePermission(Permissions::NEWS_DELETE);
        Comment::findOrFail($commentId)->delete();
        $this->refreshComments();
    }

    private function refreshComments(): void
    {
        $this->post = $this->post->fresh([
            'comments' => fn($q) => $q->whereNull('parent_id')
                ->with(['user', 'replies.user'])
                ->orderBy('created_at'),
        ]);
    }

    public function render()
    {
        return view('livewire.blog-edit')->layout('layouts.dashboard');
    }
}
