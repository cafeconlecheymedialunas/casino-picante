<?php

namespace App\Livewire;

use Livewire\Component;

class Bonos extends Component
{
    public function render()
    {
        return view('livewire.bonos')->extends('layouts.dashboard');
    }
}
