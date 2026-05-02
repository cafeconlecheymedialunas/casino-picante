<?php

namespace App\Livewire;

use Livewire\Component;

class Banners extends Component
{
    public function render()
    {
        return view('livewire.banners')->layout('layouts.dashboard');
    }
}
