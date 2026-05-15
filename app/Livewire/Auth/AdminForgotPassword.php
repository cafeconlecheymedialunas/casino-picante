<?php

namespace App\Livewire\Auth;

use App\Models\Agent;
use App\Models\User;
use App\Notifications\AdminPasswordReset;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminForgotPassword extends Component
{
    public string $email = '';

    public bool $sent = false;

    public string $error = '';

    protected $rules = ['email' => 'required|email'];

    protected $messages = [
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Ingresá un email válido.',
    ];

    public function mount()
    {
        if (auth()->check()) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function sendResetLink(): void
    {
        $this->error = '';
        $this->validate();
        $email = trim(strtolower($this->email));

        $agent = Agent::where('email', $email)->first();
        $user = User::where('email', $email)
            ->whereHas('role', fn ($role) => $role->whereIn('name', [Roles::ADMIN, Roles::AGENTE]))
            ->first();

        if (! $agent && ! $user) {
            $this->addError('email', 'No existe una cuenta con este email.');

            return;
        }

        $token = Str::random(64);
        DB::delete('DELETE FROM password_reset_tokens WHERE email = ?', [$email]);
        DB::insert('INSERT INTO password_reset_tokens (email, token, created_at) VALUES (?, ?, ?)', [$email, $token, now()]);

        try {
            ($agent ?? $user)->notify(new AdminPasswordReset(url('/admin/reset-password/'.$token)));
            $this->sent = true;
        } catch (\Exception) {
            $this->error = 'No se pudo enviar el enlace. Intentá nuevamente.';
        }
    }

    public function render()
    {
        return view('livewire.auth.admin-forgot-password')->layout('layouts.auth');
    }
}
