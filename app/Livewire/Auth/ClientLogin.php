<?php

namespace App\Livewire\Auth;

use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        'username.required' => 'Ingresa tu usuario o email.',
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

        if ($this->attemptClientLogin('username') || $this->attemptClientLogin('email')) {
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
        $lockedUntil = Session::get('client_login_locked_until');

        return $lockedUntil && now()->timestamp < $lockedUntil;
    }

    private function getAttempts(): int
    {
        return Session::get('client_login_attempts', 0);
    }

    private function incrementAttempts(): void
    {
        $attempts = $this->getAttempts() + 1;
        Session::put('client_login_attempts', $attempts);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Session::put('client_login_locked_until', now()->addMinutes(self::LOCKOUT_MINUTES)->timestamp);
        }
    }

    private function clearAttempts(): void
    {
        Session::forget(['client_login_attempts', 'client_login_locked_until']);
    }

    public function render()
    {
        return view('livewire.auth.client-login')->layout('layouts.auth');
    }

    private function attemptClientLogin(string $field): bool
    {
        if (! Auth::attempt([$field => $this->username, 'password' => $this->password], false)) {
            return false;
        }

        $user = Auth::user()?->loadMissing('role');

        if ($user?->status !== 'active') {
            Auth::logout();
            $this->addGenericError();

            return true;
        }

        if ($user?->hasRole(Roles::CLIENTE)) {
            session()->forget(['active_agent_id', 'active_line_id']);
            session()->regenerate();
            $this->redirect(route('client.account'), navigate: true);

            return true;
        }

        Auth::logout();
        $this->addGenericError();

        return true;
    }

    private function addGenericError(): void
    {
        $this->addError('username', 'Usuario o contrasena incorrectos.');
        $this->reset('password');
    }
}
