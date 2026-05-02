<?php

namespace App\Livewire;

use Livewire\Component;

abstract class DashboardComponent extends Component
{
    public function render()
    {
        return view('layouts.dashboard', [
            'content' => $this->getContentView(),
        ])->extends('layouts.dashboard');
    }

    abstract protected function getContentView();
}
