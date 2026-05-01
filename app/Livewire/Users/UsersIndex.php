<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    public function render()
    {
        $users = User::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function($query) {
                $query->where('status', $this->status);
            })
            ->paginate(10);

        return view('livewire.users.users-index', compact('users'));
    }
}