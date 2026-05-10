<?php

namespace App\Livewire\Auth;

use App\Models\Agent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AdminResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $reset = false;

    public string $error = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ];

    protected $messages = [
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Ingresá un email válido.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function mount(string $token = '')
    {
        $this->token = $token;

        if (auth()->check()) {
            $this->redirect('/dashboard', navigate: true);
        }

        if (empty($token)) {
            $this->error = 'Token inválido.';
        }
    }

    public function resetPassword(): void
    {
        $this->error = '';
        $this->validate();

        $record = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (! $record) {
            $this->addError('email', 'El enlace de restablecimiento es inválido o expiró.');

            return;
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $this->email)->delete();
            $this->addError('email', 'El enlace de restablecimiento expiró. Solicitá uno nuevo.');

            return;
        }

        $agent = Agent::where('email', $this->email)->first();
        $user = User::where('email', $this->email)->first();

        if ($agent) {
            $agent->update(['password' => Hash::make($this->password)]);
        }
        if ($user) {
            $user->update(['password' => Hash::make($this->password)]);
        }

        DB::table('password_reset_tokens')->where('email', $this->email)->delete();
        $this->reset = true;
    }

    public function render()
    {
        return view('livewire.auth.admin-reset-password')
            ->layout('layouts.auth');
    }
}
