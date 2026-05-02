<?php

namespace App\Livewire\Users;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $status = '';

    public $showModal = false;

    public $editingUser = null;

    public $selectedUser = null;

    public $showDetailModal = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ];

    public function openCreateModal()
    {
        $this->editingUser = null;
        $this->showModal = true;
    }

    public function openEditModal($userId)
    {
        $this->editingUser = User::find($userId);
        $this->showModal = true;
    }

    public function openDetailModal($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showDetailModal = false;
        $this->editingUser = null;
        $this->selectedUser = null;
    }

    public function saveUser()
    {
        $this->validate();

        if ($this->editingUser) {
            $this->editingUser->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?? null,
                'contact' => $this->contact ?? null,
            ]);
            if ($this->password) {
                $this->editingUser->update(['password' => bcrypt($this->password)]);
            }
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'phone' => $this->phone ?? null,
                'contact' => $this->contact ?? null,
                'status' => 'active',
            ]);
        }

        $this->closeModal();
        session()->flash('message', 'Usuario guardado correctamente');
    }

    public function toggleStatus($userId)
    {
        $user = User::find($userId);
        $user->update(['status' => $user->status === 'active' ? 'blocked' : 'active']);
    }

    public function getMetrics()
    {
        $total = User::count();
        $active = User::where('status', 'active')->count();
        $newThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();
        $verified = User::whereNotNull('email_verified_at')->count();

        $lastMonth = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $growthPercent = $lastMonth > 0 ? round(($newThisMonth - $lastMonth) / $lastMonth * 100) : 0;

        $today = User::whereDate('created_at', Carbon::today())->count();
        $thisWeek = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $thisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();

        return [
            'total' => $total,
            'active' => $active,
            'newThisMonth' => $newThisMonth,
            'verified' => $verified,
            'growthPercent' => $growthPercent,
            'today' => $today,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
        ];
    }

    public function render()
    {
        $metrics = $this->getMetrics();

        $users = User::when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.users.users-index', compact('users', 'metrics'))->extends('layouts.dashboard');
    }
}
