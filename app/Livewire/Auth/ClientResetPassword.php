<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ClientResetPassword extends Component
{
    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Locked]
    public string $token = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
        'password_confirmation' => 'required',
    ];

    protected $messages = [
        'email.required' => 'Ingresa tu email.',
        'email.email' => 'Ingresa un email válido.',
        'password.required' => 'Ingresa tu nueva contraseña.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'password_confirmation.required' => 'Confirma tu nueva contraseña.',
    ];

    public function mount(string $token): void
    {
        $this->token = $token;
    }

    public function resetPassword(): void
    {
        $this->validate();
        $email = trim(strtolower($this->email));

        $isClient = User::where('email', $email)
            ->whereHas('role', fn ($role) => $role->where('name', Roles::CLIENTE))
            ->exists();

        if (! $isClient) {
            $this->addError('email', 'No existe una cuenta de cliente con este email.');

            return;
        }

        $status = Password::reset(
            [
                'email' => $email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Tu contraseña fue restablecida correctamente. Ya podés iniciar sesión.');
            $this->redirectRoute('login');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.client-reset-password')->layout('layouts.auth');
    }
}
