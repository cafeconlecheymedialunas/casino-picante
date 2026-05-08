<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Login extends Component
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

    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_MINUTES = 1;

    public function login(): void
    {
        if ($this->isLockedOut()) {
            $this->addError('username', 'Demasiados intentos. Intenta nuevamente en un minuto.');

            return;
        }

        $this->validate();

        if ($this->attemptPanelLogin('username') || $this->attemptPanelLogin('email')) {
            $this->clearAttempts();

            return;
        }

        $this->incrementAttempts();
        $remaining = self::MAX_ATTEMPTS - $this->getAttempts();
        $this->addError('username', 'Usuario o contrasena incorrectos. ('.$remaining.' intentos restantes)');
        $this->reset('password');
    }

    private function isLockedOut(): bool
    {
        $lockedUntil = Session::get('login_locked_until');

        return $lockedUntil && now()->timestamp < $lockedUntil;
    }

    private function getAttempts(): int
    {
        return Session::get('login_attempts', 0);
    }

    private function incrementAttempts(): void
    {
        $attempts = $this->getAttempts() + 1;
        Session::put('login_attempts', $attempts);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Session::put('login_locked_until', now()->addMinutes(self::LOCKOUT_MINUTES)->timestamp);
        }
    }

    private function clearAttempts(): void
    {
        Session::forget(['login_attempts', 'login_locked_until']);
    }

    public function render()
    {
        return view('livewire.auth.login', [
            'heading' => 'Panel de administracion',
            'submitLabel' => 'INGRESAR',
        ])->layout('layouts.auth');
    }

    private function attemptPanelLogin(string $field): bool
    {
        if (! Auth::attempt([$field => $this->username, 'password' => $this->password], false)) {
            return false;
        }

        $user = User::with(['role', 'agent'])->find(Auth::id());

        if ($user?->status !== 'active') {
            Auth::logout();
            $this->addError('username', 'Esta cuenta esta inactiva.');
            $this->reset('password');

            return true;
        }

        if ($user?->hasRole(Roles::ADMIN)) {
            session()->forget(['active_agent_id', 'active_line_id']);
            session()->regenerate();
            $this->redirect('/dashboard', navigate: true);

            return true;
        }

        if ($user?->hasRole(Roles::AGENTE) && $user->agent?->status === 'active') {
            session()->regenerate();
            session(['active_agent_id' => $user->agent->id]);
            $this->redirect('/dashboard', navigate: true);

            return true;
        }

        Auth::logout();
        $this->addError('username', 'Esta cuenta no tiene acceso al panel.');
        $this->reset('password');

        return true;
    }
}
