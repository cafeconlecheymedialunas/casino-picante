<?php

namespace App\Livewire\Auth;

use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientLogin extends Component
{
    public string $username = '';

    public string $password = '';

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    protected $messages = [
        'username.required' => 'Ingresa tu usuario.',
        'password.required' => 'Ingresa tu contrasena.',
    ];

    public function login(): void
    {
        $this->validate();

        if ($this->attemptClientLogin('username') || $this->attemptClientLogin('email')) {
            return;
        }

        $this->addError('username', 'Usuario o contrasena incorrectos.');
        $this->reset('password');
    }

    public function render()
    {
        return view('livewire.auth.login', [
            'heading' => 'Acceso clientes',
            'submitLabel' => 'INGRESAR',
        ])->layout('layouts.auth');
    }

    private function attemptClientLogin(string $field): bool
    {
        if (! Auth::attempt([$field => $this->username, 'password' => $this->password], false)) {
            return false;
        }

        $user = Auth::user()?->loadMissing('role');

        if ($user?->status !== 'active') {
            Auth::logout();
            $this->addError('username', 'Esta cuenta esta inactiva.');
            $this->reset('password');

            return true;
        }

        if ($user?->hasRole(Roles::CLIENTE)) {
            session()->forget(['active_agent_id', 'active_line_id']);
            session()->regenerate();
            $this->redirect('/perfil', navigate: true);
            return true;
        }

        Auth::logout();
        $this->addError('username', 'Usa el acceso del panel para esta cuenta.');
        $this->reset('password');

        return true;
    }
}
