<?php

namespace App\Livewire\Users;

use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Role;
use App\Models\User;
use App\Support\AvatarLibrary;
use App\Support\Permissions;
use App\Support\Roles;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class UsersIndex extends Component
{
    use HasLinePermissions, WithPagination, SendsNotifications;

    public string $search = '';

    public string $filterStatus = '';

    public bool $showModal = false;

    public bool $showDetailModal = false;

    public ?int $editingUserId = null;

    public ?int $detailUserId = null;

    public string $username = '';

    public string $name = '';

    public string $apellido = '';

    public string $email = '';

    public string $password = '';

    public string $phone = '';

    public string $contact = '';

    public string $avatar = '';

    public string $userStatus = 'active';

    public ?int $preferredLineId = null;

    public array $selectedLines = [];

    protected $messages = [
        'username.alpha_dash' => 'El username solo puede usar letras, numeros, guiones y guion bajo.',
        'username.unique' => 'Este username ya esta registrado.',
        'name.required' => 'El nombre es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 2 caracteres.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Ingresa un email valido.',
        'email.unique' => 'Este email ya esta registrado.',
        'password.required' => 'La contrasena es obligatoria.',
        'password.min' => 'La contrasena debe tener al menos 6 caracteres.',
        'userStatus.in' => 'Estado invalido.',
        'preferredLineId.exists' => 'La linea preferida seleccionada no existe.',
    ];

    protected function rules(): array
    {
        $uniqueEmail = $this->editingUserId
            ? 'unique:users,email,'.$this->editingUserId
            : 'unique:users,email';

        $uniqueUsername = $this->editingUserId
            ? 'unique:users,username,'.$this->editingUserId
            : 'unique:users,username';

        return [
            'username' => ['nullable', 'min:3', 'max:60', 'alpha_dash', $uniqueUsername],
            'name' => 'required|min:2|max:100',
            'apellido' => 'nullable|max:100',
            'email' => "required|email|{$uniqueEmail}",
            'password' => $this->editingUserId ? 'nullable|min:6' : 'required|min:6',
            'phone' => 'nullable|max:30',
            'contact' => 'nullable|max:100',
            'avatar' => ['required', 'string', 'regex:/^avatar_[A-Za-z0-9_-]{1,80}$/'],
            'userStatus' => 'required|in:active,inactive',
            'preferredLineId' => 'nullable|exists:lines,id',
            'selectedLines' => 'array',
            'selectedLines.*' => 'integer|exists:lines,id',
        ];
    }

    public function getAllLinesProperty()
    {
        if (! $this->isAdminMode()) {
            return Line::whereIn('id', $this->availableLineIds())->orderBy('name')->get();
        }

        return Line::orderBy('name')->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->checkLinePermission(Permissions::USER_UPDATE);
        $this->resetForm();
        $this->editingUserId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->checkLinePermission(Permissions::USER_UPDATE);
        $this->authorizeClientScope($user);

        $this->editingUserId = $user->id;
        $this->username = $user->username ?? '';
        $this->name = $user->name;
        $this->apellido = $user->apellido ?? '';
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->contact = $user->contact ?? '';
        $this->avatar = AvatarLibrary::isValid($user->avatar) ? $user->avatar : AvatarLibrary::default();
        $this->userStatus = $user->status === 'blocked' ? 'inactive' : $user->status;
        $this->preferredLineId = $user->line_id ? (int) $user->line_id : null;
        $this->password = '';
        $this->selectedLines = DB::table('line_clients')
            ->where('user_id', $user->id)
            ->pluck('line_id')
            ->map(fn ($lineId) => (int) $lineId)
            ->toArray();

        $this->showDetailModal = false;
        $this->showModal = true;
    }

    public function openDetailModal(int $userId): void
    {
        $this->checkLinePermission(Permissions::USER_READ);
        $this->authorizeClientScope(User::findOrFail($userId));
        $this->detailUserId = $userId;
        $this->showDetailModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showDetailModal = false;
        $this->resetForm();
    }

    public function saveUser(): void
    {
        $this->checkLinePermission(Permissions::USER_UPDATE);
        $this->validate();
        $this->authorizeSelectedLines();

        $username = trim($this->username);
        $status = $this->userStatus === 'inactive' ? 'inactive' : 'active';

        $data = [
            'role_id' => Role::where('name', Roles::CLIENTE)->value('id'),
            'username' => $username !== '' ? $username : $this->makeUsername(trim($this->name), trim($this->email)),
            'name' => trim($this->name),
            'apellido' => trim($this->apellido) ?: null,
            'line_id' => $this->preferredLineId,
            'email' => trim($this->email),
            'phone' => $this->phone ?: null,
            'contact' => $this->contact ?: null,
            'avatar' => $this->avatar,
            'status' => $status,
        ];

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $this->authorizeClientScope($user);

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
            $this->dispatch('toast', message: 'Cliente actualizado correctamente.', type: 'success');

            $this->notify('Cliente actualizado', "El cliente {$user->name} fue actualizado.", 'users', route('clientes', [], false), 'info');
            $this->dispatch('notification-created');
        } else {
            $data['password'] = Hash::make($this->password);
            $user = User::create($data);
            $this->dispatch('toast', message: 'Cliente creado correctamente.', type: 'success');

            $this->notify('Nuevo cliente registrado', "El cliente {$user->name} fue creado exitosamente.", 'users', route('clientes', [], false), 'success');
            $this->dispatch('notification-created');
        }

        $this->syncClientLines($user->id, $status);
        $this->closeModal();
    }

    public function deleteUser(int $userId): void
    {
        $this->checkLinePermission(Permissions::USER_BLOCK);
        $this->authorizeClientScope(User::findOrFail($userId));
        DB::table('line_clients')->where('user_id', $userId)->delete();
        $user = User::findOrFail($userId);
        $userName = $user->name;
        $user->delete();
        $this->dispatch('toast', message: 'Cliente eliminado.', type: 'danger');

        $this->notify('Cliente eliminado', "El cliente {$userName} fue eliminado del sistema.", 'users', route('clientes', [], false), 'danger');
        $this->dispatch('notification-created');
    }

    public function setStatus(int $userId, string $status): void
    {
        $this->checkLinePermission(Permissions::USER_BLOCK);
        $this->authorizeClientScope(User::findOrFail($userId));

        if (! in_array($status, ['active', 'inactive'], true)) {
            return;
        }

        User::findOrFail($userId)->update(['status' => $status]);
        DB::table('line_clients')->where('user_id', $userId)->update(['is_active' => $status === 'active']);

        $label = $status === 'active' ? 'activado' : 'pausado';
        $this->dispatch('toast', message: "Cliente {$label}.", type: $status === 'active' ? 'success' : 'danger');

        $user = User::findOrFail($userId);
        $this->notify('Estado de cliente cambiado', "El cliente {$user->name} fue " . ($status === 'active' ? 'activado' : 'pausado') . ".", 'users', route('clientes', [], false), 'warning');
        $this->dispatch('notification-created');

        if ($this->detailUserId === $userId) {
            // Re-assign triggers Livewire to re-render detail panel with fresh data
            $this->detailUserId = null;
            $this->detailUserId = $userId;
        }
    }

    public function render()
    {
        $this->checkLinePermission(Permissions::USER_READ);

        $query = User::query()
            ->with(['preferredLine', 'role'])
            ->whereHas('role', fn ($role) => $role->where('name', Roles::CLIENTE))
            ->when($this->search, function ($q) {
                $search = '%'.$this->search.'%';

                $q->where(function ($query) use ($search) {
                    $query->where('id', $this->search)
                        ->orWhere('username', 'like', $search)
                        ->orWhere('name', 'like', $search)
                        ->orWhere('apellido', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                });
            })
            ->when($this->filterStatus, function ($q) {
                $status = $this->filterStatus === 'inactive' ? ['inactive', 'blocked'] : [$this->filterStatus];

                $q->whereIn('status', $status);
            });

        $this->scopeClientsToAvailableLines($query);

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $detailUser = $this->detailUserId ? User::with('preferredLine')->find($this->detailUserId) : null;
        if ($detailUser) {
            $this->authorizeClientScope($detailUser);
        }
        $lines = $this->allLines;

        return view('livewire.users.users-index', [
            'users' => $users,
            'metrics' => $this->getMetrics(),
            'detailUser' => $detailUser,
            'lines' => $lines,
        ]);
    }

    private function resetForm(): void
    {
        $this->username = '';
        $this->name = '';
        $this->apellido = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->contact = '';
        $this->avatar = AvatarLibrary::default();
        $this->userStatus = 'active';
        $this->preferredLineId = null;
        $this->editingUserId = null;
        $this->selectedLines = [];
        $this->resetValidation();
    }

    private function syncClientLines(int $userId, string $status): void
    {
        $lineIds = collect($this->selectedLines)
            ->push($this->preferredLineId)
            ->filter()
            ->map(fn ($lineId) => (int) $lineId)
            ->unique()
            ->values();

        DB::table('line_clients')->where('user_id', $userId)->delete();

        foreach ($lineIds as $lineId) {
            DB::table('line_clients')->insert([
                'line_id' => $lineId,
                'user_id' => $userId,
                'is_active' => $status === 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function makeUsername(string $name, string $email): string
    {
        $base = Str::slug($name, '_') ?: Str::before($email, '@');
        $base = Str::limit($base, 50, '');
        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)
            ->when($this->editingUserId, fn ($query) => $query->where('id', '!=', $this->editingUserId))
            ->exists()) {
            $username = Str::limit($base, 46, '').'_'.$suffix++;
        }

        return $username;
    }

    private function getMetrics(): array
    {
        $now = Carbon::now();
        $base = User::query()->whereHas('role', fn ($role) => $role->where('name', Roles::CLIENTE));
        $this->scopeClientsToAvailableLines($base);

        $total = (clone $base)->count();
        $active = (clone $base)->where('status', 'active')->count();
        $inactive = (clone $base)->whereIn('status', ['inactive', 'blocked', 'pending'])->count();
        $todayNew = (clone $base)->whereDate('created_at', Carbon::today())->count();
        $weekNew = (clone $base)->where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $monthNew = (clone $base)->where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $lastMonth = (clone $base)->whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth(),
        ])->count();
        $growth = $lastMonth > 0
            ? round(($monthNew - $lastMonth) / $lastMonth * 100)
            : ($monthNew > 0 ? 100 : 0);

        return compact('total', 'active', 'inactive', 'todayNew', 'weekNew', 'monthNew', 'growth');
    }

    private function availableLineIds(): array
    {
        if ($this->isAdminMode()) {
            return Line::pluck('id')->map(fn ($lineId) => (int) $lineId)->toArray();
        }

        return LineAgent::where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
            ->pluck('line_id')
            ->map(fn ($lineId) => (int) $lineId)
            ->toArray();
    }

    private function scopeClientsToAvailableLines($query): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $lineIds = $this->availableLineIds();
        if (empty($lineIds)) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where(function ($inner) use ($lineIds) {
            $inner->whereIn('line_id', $lineIds)
                ->orWhereHas('lines', fn ($line) => $line->whereIn('lines.id', $lineIds));
        });
    }

    private function authorizeClientScope(User $user): void
    {
        if (! $user->hasRole(Roles::CLIENTE)) {
            abort(403, 'Solo podes gestionar clientes desde esta pantalla.');
        }

        if ($this->isAdminMode()) {
            return;
        }

        $lineIds = $this->availableLineIds();
        $allowed = ($user->line_id && in_array((int) $user->line_id, $lineIds, true))
            || $user->lines()->whereIn('lines.id', $lineIds)->exists();

        if (! $allowed) {
            abort(403, 'No podes gestionar clientes fuera de tus lineas.');
        }
    }

    private function authorizeSelectedLines(): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $selected = collect($this->selectedLines)
            ->push($this->preferredLineId)
            ->filter()
            ->map(fn ($lineId) => (int) $lineId)
            ->unique();

        if ($selected->diff($this->availableLineIds())->isNotEmpty()) {
            abort(403, 'No podes asignar clientes a lineas fuera de tu alcance.');
        }
    }
}
