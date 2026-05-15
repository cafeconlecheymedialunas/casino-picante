<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ClientRegister extends Component
{
    public string $name = '';

    public string $apellido = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $recibir_bonos = true;

    protected $messages = [
        'name.required' => 'Ingresa tu nombre.',
        'username.required' => 'Elegí un nombre de cliente.',
        'username.alpha_dash' => 'El nombre de cliente solo puede usar letras, números, guiones y guion bajo.',
        'username.unique' => 'Ese nombre de cliente ya está registrado.',
        'email.required' => 'Ingresa tu email.',
        'email.unique' => 'Ese email ya está registrado.',
        'password.required' => 'Ingresa una contraseña.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function register(): void
    {
        $validated = $this->validate();

        $role = Role::firstOrCreate(
            ['name' => Roles::CLIENTE],
            ['label' => 'Cliente']
        );

        $user = User::create([
            'role_id' => $role->id,
            'name' => $validated['name'],
            'apellido' => $validated['apellido'] ?? null,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'wants_bonus_emails' => (bool) ($validated['recibir_bonos'] ?? false),
            'password' => $validated['password'],
            'status' => 'active',
        ]);

        Auth::login($user);
        session()->forget(['active_agent_id', 'active_line_id']);
        session()->regenerate();

        $this->redirect(route('client.account'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.client-register')->layout('layouts.auth');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'recibir_bonos' => ['boolean'],
        ];
    }
}
