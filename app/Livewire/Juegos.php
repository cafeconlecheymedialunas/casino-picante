<?php

namespace App\Livewire;

use Livewire\Component;

class Juegos extends Component
{
    public function render()
    {
        return view('livewire.juegos')->layout('layouts.dashboard');
    }
}
