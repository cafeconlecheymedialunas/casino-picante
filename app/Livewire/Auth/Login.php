<?php

namespace App\Livewire\Auth;

use App\Models\Agent;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
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

        if ($this->attemptPanelLogin('username') || $this->attemptPanelLogin('email')) {
            RateLimiter::clear($this->throttleKey());

            return;
        }

        RateLimiter::hit($this->throttleKey(), self::LOCKOUT_SECONDS);
        $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($this->throttleKey());
        $this->addError('username', 'Usuario o contrasena incorrectos.');
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

        return "panel-login:{$ip}:{$this->username}";
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
        $user = $this->findUserForLogin();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            return false;
        }

        Auth::login($user, false);
        session()->regenerate();

        if ($user?->hasRole(Roles::ADMIN)) {
            session()->forget(['active_agent_id', 'active_line_id', 'active_vendor_id']);

            $this->redirect(route('admin.dashboard'), navigate: false);

            return true;
        }

        $agent = Agent::withoutGlobalScopes()->where('user_id', $user->id)->first();
        if ($user?->hasRole(Roles::AGENTE) && $agent?->status === 'active') {
            session()->forget(['active_vendor_id']);
            session(['active_agent_id' => $agent->id]);

            $this->redirect(route('admin.dashboard'), navigate: false);

            return true;
        }

        if ($user?->hasRole(Roles::CAJERO) && $user->vendor_id && $user->vendor?->is_active) {
            session()->forget(['active_agent_id', 'active_line_id']);
            session(['active_vendor_id' => $user->vendor_id]);

            $this->redirect(route('admin.dashboard'), navigate: false);

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
