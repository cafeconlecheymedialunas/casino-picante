<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Vendor;
use App\Notifications\ClientPasswordReset;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class ClientForgotPassword extends Component
{
    public string $email = '';

    public string $selectedVendorId = '';

    protected $rules = [
        'selectedVendorId' => 'required|integer|exists:vendors,id',
        'email' => 'required|email',
    ];

    protected $messages = [
        'selectedVendorId.required' => 'Elegi el cajero de tu cuenta.',
        'email.required' => 'Ingresa tu email.',
        'email.email' => 'Ingresa un email valido.',
    ];

    public function mount(): void
    {
        $this->selectedVendorId = (string) $this->defaultVendorId();
    }

    public function sendResetLink(): void
    {
        $this->validate();
        $email = trim(strtolower($this->email));
        $vendorId = (int) $this->selectedVendorId;

        $client = User::withoutGlobalScopes()
            ->where('vendor_id', $vendorId)
            ->where('email', $email)
            ->whereHas('role', fn ($role) => $role->where('name', Roles::CLIENTE))
            ->first();

        if (! $client) {
            $this->addError('email', 'No existe una cuenta de cliente con este email para ese cajero.');

            return;
        }

        $token = Str::random(64);
        $resetKey = $this->resetKey($vendorId, $email);

        DB::table('password_reset_tokens')->where('email', $resetKey)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $resetKey,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $client->notify(new ClientPasswordReset(route('client.password.reset', [
            'token' => $token,
            'vendor' => $client->vendor?->slug,
        ])));

        session()->flash('success', 'Te enviamos un enlace para restablecer tu contrasena. Revisa tu email.');
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.auth.client-forgot-password', [
            'vendors' => Vendor::where('is_active', true)
                ->orderByDesc('is_direct')
                ->orderBy('name')
                ->get(),
        ])->layout('layouts.auth');
    }

    private function defaultVendorId(): ?int
    {
        $vendorId = session('active_vendor_id');

        if ($vendorId && Vendor::whereKey($vendorId)->where('is_active', true)->exists()) {
            return (int) $vendorId;
        }

        return Vendor::where('is_direct', true)->where('is_active', true)->value('id')
            ?? Vendor::where('is_active', true)->value('id');
    }

    private function resetKey(int $vendorId, string $email): string
    {
        return $vendorId.'|'.$email;
    }
}
