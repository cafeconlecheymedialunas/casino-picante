<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
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

    private const LOCKOUT_SECONDS = 60;

    public function login(): void
    {
        $this->validate();

        if ($this->isLockedOut()) {
            $seconds = $this->secondsUntilAvailable();
            $this->addError('username', "Demasiados intentos. Intenta nuevamente en {$seconds} segundos.");

            return;
        }

        $user = $this->findUserForLogin();

        if ($user && $user->status !== 'active') {
            RateLimiter::hit($this->throttleKey(), self::LOCKOUT_SECONDS);
            $this->addError('username', 'Usuario o contrasena incorrectos.');
            $this->reset('password');

            return;
        }

        if ($this->attemptClientLogin('username') || $this->attemptClientLogin('email')) {
            RateLimiter::clear($this->throttleKey());

            return;
        }

        RateLimiter::hit($this->throttleKey(), self::LOCKOUT_SECONDS);
        $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($this->throttleKey());
        $this->addError('username', "Usuario o contrasena incorrectos. ({$remaining} intentos restantes)");
        $this->reset('password');
    }

    private function findUserForLogin(): ?User
    {
        return User::withoutGlobalScopes()
            ->where('username', $this->username)
            ->orWhere('email', $this->username)
            ->first();
    }

    private function isLockedOut(): bool
    {
        return RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS);
    }

    private function secondsUntilAvailable(): int
    {
        return RateLimiter::availableIn($this->throttleKey());
    }

    private function throttleKey(): string
    {
        $ip = request()->ip();

        return "client-login:{$ip}:{$this->username}";
    }

    public function render()
    {
        return view('livewire.auth.client-login')->layout('layouts.auth');
    }

    private function attemptClientLogin(string $field): bool
    {
        $user = $this->findUserForLogin();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            return false;
        }

        Auth::login($user, false);

        if ($user?->hasRole(Roles::CLIENTE)) {
            if (! $user->vendor_id || ! $user->vendor?->is_active) {
                Auth::logout();
                $this->addGenericError();

                return true;
            }

            session()->forget(['active_agent_id', 'active_line_id']);
            session()->regenerate();

            session(['active_vendor_id' => $user->vendor_id]);

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
