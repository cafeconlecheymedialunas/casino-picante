<?php

namespace App\Livewire;

use App\Support\Roles;
use Livewire\Component;

class Settings extends Component
{
    public string $activeTab = 'notifications';

    public function setTab(string $tab): void
    {
        $this->ensureAdmin();
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings')->layout('layouts.dashboard');
    }

    private function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole(Roles::ADMIN)) {
            abort(403, 'Solo el administrador general puede acceder a configuracion.');
        }
    }
}
