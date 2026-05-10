<?php

namespace App\Livewire\Agents;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Role;
use App\Support\AvatarLibrary;
use App\Support\LineRoles;
use App\Support\Roles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class AgentRegister extends Component
{
    public string $name = '';

    public string $apellido = '';

    public string $email = '';

    public string $username = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $phone = '';

    public string $avatar = '';

    public array $selectedLines = [];

    public bool $registered = false;

    public string $error = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:100',
            'apellido' => 'nullable|max:100',
            'email' => 'required|email',
            'username' => 'nullable|min:3|max:60|alpha_dash',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|max:30',
            'selectedLines' => 'array',
            'selectedLines.*' => 'integer|exists:lines,id',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 2 caracteres.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Ingresá un email válido.',
        'email.unique' => 'Este email ya está registrado.',
        'username.alpha_dash' => 'Solo letras, números, guiones y guion bajo.',
        'username.unique' => 'Este username ya está en uso.',
        'username.min' => 'El username debe tener al menos 3 caracteres.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function mount()
    {
        $this->avatar = AvatarLibrary::default();
    }

    public function register(): void
    {
        $this->error = '';

        if (Agent::where('email', trim(strtolower($this->email)))->exists()) {
            $this->addError('email', 'Este email ya está registrado.');

            return;
        }

        if (User::where('email', trim(strtolower($this->email)))->exists()) {
            $this->addError('email', 'Este email ya está registrado en una cuenta.');

            return;
        }

        $this->validate();

        $username = trim($this->username);
        if (empty($username)) {
            $username = $this->makeUsername($this->name, $this->email);
        } else {
            if (Agent::where('username', $username)->exists()) {
                $this->addError('username', 'Este username ya está en uso.');

                return;
            }
            if (\App\Models\User::where('username', $username)->exists()) {
                $this->addError('username', 'Este username ya está en uso.');

                return;
            }
        }

        $roleId = Role::where('name', Roles::AGENTE)->value('id');
        $name = trim($this->name);
        $apellido = trim($this->apellido) ?: null;

        $agent = Agent::create([
            'name' => $name,
            'apellido' => $apellido,
            'email' => trim(strtolower($this->email)),
            'username' => $username,
            'password' => Hash::make($this->password),
            'phone' => trim($this->phone) ?: null,
            'avatar' => $this->avatar,
            'status' => 'inactive',
            'cargo' => 'agente',
        ]);

        $user = \App\Models\User::create([
            'role_id' => $roleId,
            'name' => $name,
            'apellido' => $apellido,
            'email' => trim(strtolower($this->email)),
            'username' => $username,
            'password' => Hash::make($this->password),
            'avatar' => $this->avatar,
            'status' => 'inactive',
        ]);

        $agent->update(['user_id' => $user->id]);

        if (! empty($this->selectedLines)) {
            $lineIds = collect($this->selectedLines)->map(fn ($id) => (int) $id)->unique()->values();
            foreach ($lineIds as $lineId) {
                LineAgent::create([
                    'line_id' => $lineId,
                    'agent_id' => $agent->id,
                    'role' => LineRoles::MIEMBRO,
                    'is_active' => false,
                ]);
            }
        }

        $this->registered = true;
    }

    private function makeUsername(string $name, string $email): string
    {
        $base = Str::slug($name, '_') ?: Str::before($email, '@') ?: 'agente';
        $base = Str::limit($base, 50, '');
        $username = $base;
        $suffix = 1;

        while (Agent::where('username', $username)->exists()
            || \App\Models\User::where('username', $username)->exists()) {
            $username = Str::limit($base, 46, '').'_'.$suffix++;
        }

        return $username;
    }

    public function render()
    {
        $lines = Line::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.agents.agent-register', [
            'lines' => $lines,
        ])->layout('layouts.dashboard');
    }
}
