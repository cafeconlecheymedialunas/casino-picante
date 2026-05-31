<?php

namespace App\Livewire\Frontend;

use App\Models\Post;
use App\Support\PublicPageContent;
use Livewire\Component;

class Blog extends Component
{
    public string $search = '';

    public function render()
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId    = $routeVendor?->id;

        // Cuando no es un cajero específico, respetar la selección del editor
        $selectedIds = [];
        $pageSection = null;
        if (! $vendorId) {
            $pageSection = PublicPageContent::page('novedades');
            $tabs        = PublicPageContent::tabs($pageSection);
            $selectedIds = collect($tabs)->flatMap(fn ($t) => $t['item_ids'] ?? [])->unique()->filter()->map(fn ($id) => (int) $id)->values()->all();
        }

        $query = Post::withoutGlobalScopes()
            ->with(['category', 'authorAgent'])
            ->when($vendorId, fn ($q) => $q->where('vendor_id', $vendorId))
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->when($selectedIds, fn ($q) => $q->whereIn('id', $selectedIds))
            ->when(trim($this->search) !== '', function ($q) {
                $s = trim($this->search);
                $q->where(fn ($q2) => $q2
                    ->where('title', 'like', "%$s%")
                    ->orWhere('excerpt', 'like', "%$s%")
                    ->orWhere('content', 'like', "%$s%")
                );
            })
            ->orderBy('published_at', 'desc');

        $posts = $query->get();

        // Mantener orden manual si hay selección y no hay búsqueda activa
        if ($selectedIds && trim($this->search) === '') {
            $posts = $posts->sortBy(fn ($p) => array_search($p->id, $selectedIds))->values();
        }

        $featuredPost   = $posts->first();
        $asidePosts     = $posts->slice(1, 3);
        $remainingPosts = $posts->slice(4);

        return view('frontend.pages.blog', [
            'posts'         => $posts,
            'featuredPost'  => $featuredPost,
            'asidePosts'    => $asidePosts,
            'remainingPosts'=> $remainingPosts,
            'pageSection'   => $pageSection,
        ])->layout('frontend.layouts.app');
    }
}
