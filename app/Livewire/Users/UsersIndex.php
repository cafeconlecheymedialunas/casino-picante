<?php

namespace App\Livewire\Users;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class UsersIndex extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $filterStatus = '';

    // Modal state
    public bool $showModal       = false;
    public bool $showDetailModal = false;
    public ?int $editingUserId   = null;
    public ?int $detailUserId    = null;

    // Form fields
    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $phone    = '';
    public string $contact  = '';
    public string $userStatus = 'active';

    protected function rules(): array
    {
        $unique = $this->editingUserId
            ? 'unique:users,email,' . $this->editingUserId
            : 'unique:users,email';

        return [
            'name'       => 'required|min:3|max:100',
            'email'      => "required|email|{$unique}",
            'password'   => $this->editingUserId ? 'nullable|min:6' : 'required|min:6',
            'phone'      => 'nullable|max:30',
            'contact'    => 'nullable|max:100',
            'userStatus' => 'required|in:active,blocked',
        ];
    }

    protected $messages = [
        'name.required'    => 'El nombre es obligatorio.',
        'name.min'         => 'El nombre debe tener al menos 3 caracteres.',
        'email.required'   => 'El email es obligatorio.',
        'email.email'      => 'Ingresa un email válido.',
        'email.unique'     => 'Este email ya está registrado.',
        'password.required'=> 'La contraseña es obligatoria.',
        'password.min'     => 'La contraseña debe tener al menos 6 caracteres.',
        'userStatus.in'    => 'Estado inválido.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    // ── Open / Close ──────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingUserId = null;
        $this->showModal     = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->name          = $user->name;
        $this->email         = $user->email;
        $this->phone         = $user->phone    ?? '';
        $this->contact       = $user->contact  ?? '';
        $this->userStatus    = $user->status;
        $this->password      = '';
        $this->showDetailModal = false;
        $this->showModal       = true;
    }

    public function openDetailModal(int $userId): void
    {
        $this->detailUserId  = $userId;
        $this->showDetailModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal       = false;
        $this->showDetailModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name         = '';
        $this->email        = '';
        $this->password     = '';
        $this->phone        = '';
        $this->contact      = '';
        $this->userStatus   = 'active';
        $this->editingUserId = null;
        $this->resetValidation();
    }

    // ── CRUD ──────────────────────────────────────────────────────

    public function saveUser(): void
    {
        $this->validate();

        $data = [
            'name'    => trim($this->name),
            'email'   => trim($this->email),
            'phone'   => $this->phone   ?: null,
            'contact' => $this->contact ?: null,
            'status'  => $this->userStatus,
        ];

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $this->dispatch('toast', message: 'Usuario actualizado correctamente.', type: 'success');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            $this->dispatch('toast', message: 'Usuario creado correctamente.', type: 'success');
        }

        $this->closeModal();
    }

    public function deleteUser(int $userId): void
    {
        User::findOrFail($userId)->delete();
        $this->dispatch('toast', message: 'Usuario eliminado.', type: 'danger');
    }

    public function setStatus(int $userId, string $status): void
    {
        User::findOrFail($userId)->update(['status' => $status]);

        // Refresh detail if open
        if ($this->detailUserId === $userId) {
            $this->detailUserId = $userId; // triggers re-fetch in render
        }
    }

    // ── Metrics ───────────────────────────────────────────────────

    private function getMetrics(): array
    {
        $now       = Carbon::now();
        $total     = User::count();
        $active    = User::where('status', 'active')->count();
        $blocked   = User::where('status', 'blocked')->count();
        $todayNew  = User::whereDate('created_at', Carbon::today())->count();
        $weekNew   = User::where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $monthNew  = User::where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $lastMonth = User::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth(),
        ])->count();
        $growth = $lastMonth > 0
            ? round(($monthNew - $lastMonth) / $lastMonth * 100)
            : ($monthNew > 0 ? 100 : 0);

        return compact('total', 'active', 'blocked',
            'todayNew', 'weekNew', 'monthNew', 'growth');
    }

    // ── Render ────────────────────────────────────────────────────

    public function render()
    {
        $users = User::when($this->search, fn ($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $detailUser = $this->detailUserId ? User::find($this->detailUserId) : null;

        return view('livewire.users.users-index', [
            'users'      => $users,
            'metrics'    => $this->getMetrics(),
            'detailUser' => $detailUser,
        ]);
    }
}
