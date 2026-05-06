<?php

namespace App\Livewire;

use Livewire\Component;

class Settings extends Component
{
    public string $activeTab = 'notifications';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings')->layout('layouts.dashboard');
    }
}
