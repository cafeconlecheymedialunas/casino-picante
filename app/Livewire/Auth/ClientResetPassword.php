<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ClientResetPassword extends Component
{
    public string $email = '';

    public string $selectedVendorId = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Locked]
    public string $token = '';

    protected $rules = [
        'selectedVendorId' => 'required|integer|exists:vendors,id',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
        'password_confirmation' => 'required',
    ];

    protected $messages = [
        'selectedVendorId.required' => 'Elegi el cajero de tu cuenta.',
        'email.required' => 'Ingresa tu email.',
        'email.email' => 'Ingresa un email valido.',
        'password.required' => 'Ingresa tu nueva contrasena.',
        'password.confirmed' => 'Las contrasenas no coinciden.',
        'password_confirmation.required' => 'Confirma tu nueva contrasena.',
    ];

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->selectedVendorId = (string) $this->defaultVendorId();

        if (request()->filled('vendor')) {
            $vendor = Vendor::where('slug', request('vendor'))
                ->where('is_active', true)
                ->first();

            if ($vendor) {
                $this->selectedVendorId = (string) $vendor->id;
            }
        }
    }

    public function resetPassword(): void
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

        $record = DB::table('password_reset_tokens')
            ->where('email', $this->resetKey($vendorId, $email))
            ->first();

        if (! $record || ! Hash::check($this->token, $record->token)) {
            $this->addError('email', 'El enlace de recuperacion no es valido.');

            return;
        }

        $expiresAt = now()->subMinutes((int) config('auth.passwords.users.expire', 60));
        if ($record->created_at && Carbon::parse($record->created_at)->lt($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $this->resetKey($vendorId, $email))->delete();
            $this->addError('email', 'El enlace de recuperacion expiro.');

            return;
        }

        $client->forceFill([
            'password' => $this->password,
        ])->save();

        DB::table('password_reset_tokens')->where('email', $this->resetKey($vendorId, $email))->delete();

        session()->flash('success', 'Tu contrasena fue restablecida correctamente. Ya podes iniciar sesion.');
        $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.auth.client-reset-password', [
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
