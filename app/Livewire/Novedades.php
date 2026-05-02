<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class Novedades extends Component
{
    public $tab = 'novedad';

    public $selectedPost = null;

    public $showModal = false;

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->selectedPost = null;
    }

    public function selectPost($id)
    {
        $this->selectedPost = Post::find($id);
    }

    public function getPosts()
    {
        return Post::where('type', $this->tab)->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $posts = $this->getPosts();

        return view('livewire.novedades', compact('posts'))->extends('layouts.dashboard');
    }
}
