<?php

namespace App\Livewire;

use Livewire\Component;

class Caja extends Component
{
    public function render()
    {
        return view('livewire.caja')->extends('layouts.dashboard');
    }
}
