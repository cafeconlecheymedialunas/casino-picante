<?php

namespace App\Livewire;

use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use Livewire\Component;

class Chats extends Component
{
    use HasLinePermissions;

    public function render()
    {
        if (! $this->hasLinePermission(Permissions::TICKET_READ) && ! $this->hasLinePermission(Permissions::USER_READ)) {
            abort(403, 'Sin permiso para ver chats.');
        }

        return view('livewire.chats')->layout('layouts.dashboard');
    }
}
