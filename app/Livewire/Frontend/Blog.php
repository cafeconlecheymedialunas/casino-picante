<?php

namespace App\Livewire\Frontend;

use App\Models\Post;
use Livewire\Component;

class Blog extends Component
{
    public string $search = '';

    public function render()
    {
        $posts = Post::withoutGlobalScopes()
            ->with('category')
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', '%'.$search.'%')
                        ->orWhere('excerpt', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('published_at', 'desc')
            ->get();

        $featuredPost = $posts->first();
        $asidePosts = $posts->slice(1, 3);
        $remainingPosts = $posts->slice(4);

        return view('frontend.pages.blog', [
            'posts' => $posts,
            'featuredPost' => $featuredPost,
            'asidePosts' => $asidePosts,
            'remainingPosts' => $remainingPosts,
        ])->layout('frontend.layouts.app');
    }
}
