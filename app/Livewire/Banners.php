<?php

namespace App\Livewire;

use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use Livewire\Component;

class Banners extends Component
{
    use HasLinePermissions;

    public function render()
    {
        if (! $this->hasLinePermission(Permissions::LINE_EDIT)) {
            abort(403, 'Sin permiso para gestionar banners.');
        }

        return view('livewire.banners')->layout('layouts.dashboard');
    }
}
