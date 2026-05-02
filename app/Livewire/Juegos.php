<?php

namespace App\Livewire;

use Livewire\Component;

class Juegos extends Component
{
    public function render()
    {
        return view('livewire.juegos')->extends('layouts.dashboard');
    }
}

class Banners extends Component
{
    public function render()
    {
        return view('livewire.banners')->extends('layouts.dashboard');
    }
}

class Reportes extends Component
{
    public function render()
    {
        return view('livewire.reportes')->extends('layouts.dashboard');
    }
}

class Logs extends Component
{
    public function render()
    {
        return view('livewire.logs')->extends('layouts.dashboard');
    }
}
