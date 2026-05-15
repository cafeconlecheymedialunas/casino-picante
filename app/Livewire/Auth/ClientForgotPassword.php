<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ClientForgotPassword extends Component
{
    public string $email = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'Ingresa tu email.',
        'email.email' => 'Ingresa un email válido.',
    ];

    public function sendResetLink(): void
    {
        $this->validate();
        $email = trim(strtolower($this->email));

        $client = User::where('email', $email)
            ->whereHas('role', fn ($role) => $role->where('name', Roles::CLIENTE))
            ->first();

        if (! $client) {
            $this->addError('email', 'No existe una cuenta de cliente con este email.');

            return;
        }

        $status = Password::sendResetLink(
            ['email' => $email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Te enviamos un enlace para restablecer tu contraseña. Revisá tu email.');
            $this->reset('email');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.client-forgot-password')->layout('layouts.auth');
    }
}
