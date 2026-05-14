<?php

namespace App\Livewire\Frontend;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Component;

class PostShow extends Component
{
    public Post $post;

    public string $commentContent = '';

    public ?int $replyTo = null;

    public string $replyContent = '';

    public ?string $commentNotice = null;

    public function mount(Post $post): void
    {
        abort_unless($post->status === Post::STATUS_PUBLISHED, 404);

        $this->post = $post->load('category');
    }

    public function addComment(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $validated = $this->validate([
            'commentContent' => ['required', 'string', 'min:3', 'max:800'],
        ], [
            'commentContent.required' => 'Escribi un comentario.',
            'commentContent.min' => 'El comentario es demasiado corto.',
            'commentContent.max' => 'El comentario no puede superar 800 caracteres.',
        ]);

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => trim($validated['commentContent']),
            'is_approved' => false,
        ]);

        $this->commentContent = '';
        $this->commentNotice = 'Tu comentario fue enviado y queda pendiente de aprobacion.';
    }

    public function startReply(int $commentId): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->replyTo = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply(): void
    {
        $this->replyTo = null;
        $this->replyContent = '';
    }

    public function addReply(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        if (! $this->replyTo) {
            return;
        }

        $parent = Comment::where('post_id', $this->post->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->findOrFail($this->replyTo);

        $validated = $this->validate([
            'replyContent' => ['required', 'string', 'min:3', 'max:800'],
        ], [
            'replyContent.required' => 'Escribi una respuesta.',
            'replyContent.min' => 'La respuesta es demasiado corta.',
            'replyContent.max' => 'La respuesta no puede superar 800 caracteres.',
        ]);

        Comment::create([
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'user_id' => auth()->id(),
            'content' => trim($validated['replyContent']),
            'is_approved' => false,
        ]);

        $this->replyTo = null;
        $this->replyContent = '';
        $this->commentNotice = 'Tu respuesta fue enviada y queda pendiente de aprobacion.';
    }

    public function render()
    {
        $userId = auth()->id();

        $comments = Comment::where('post_id', $this->post->id)
            ->whereNull('parent_id')
            ->where(function ($query) use ($userId) {
                $query->where('is_approved', true);

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->with([
                'user',
                'replies' => function ($query) use ($userId) {
                    $query->where(function ($replyQuery) use ($userId) {
                        $replyQuery->where('is_approved', true);

                        if ($userId) {
                            $replyQuery->orWhere('user_id', $userId);
                        }
                    })->with('user')->orderBy('created_at');
                },
            ])
            ->latest()
            ->take(20)
            ->get();

        return view('frontend.pages.post-show', [
            'post' => $this->post,
            'comments' => $comments,
        ])->layout('frontend.layouts.app');
    }
}
