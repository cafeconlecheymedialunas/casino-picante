<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ClientRegister extends Component
{
    public string $name = '';

    public string $apellido = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $recibir_bonos = true;

    public string $selectedVendorId = '';

    public bool $showForm = false;

    protected $messages = [
        'name.required' => 'Ingresa tu nombre.',
        'selectedVendorId.required' => 'Elegi un cajero para crear tu cuenta.',
        'username.required' => 'Elegí un nombre de cliente.',
        'username.alpha_dash' => 'El nombre de cliente solo puede usar letras, números, guiones y guion bajo.',
        'username.unique' => 'Ese nombre de cliente ya está registrado.',
        'email.required' => 'Ingresa tu email.',
        'email.unique' => 'Ese email ya está registrado.',
        'password.required' => 'Ingresa una contraseña.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function mount(): void
    {
        $this->selectedVendorId = (string) $this->defaultVendorId();

        if (request()->filled('vendor')) {
            $vendor = Vendor::where('slug', request('vendor'))
                ->where('is_active', true)
                ->first();

            if ($vendor) {
                $this->selectedVendorId = (string) $vendor->id;
                $this->showForm = true;
            }
        }
    }

    public function selectVendor(int $vendorId): void
    {
        abort_unless(Vendor::whereKey($vendorId)->where('is_active', true)->exists(), 404);

        $this->selectedVendorId = (string) $vendorId;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function backToVendors(): void
    {
        $this->showForm = false;
    }

    public function register(): void
    {
        $validated = $this->validate();
        $vendorId = (int) $validated['selectedVendorId'];

        if (! $vendorId) {
            throw ValidationException::withMessages([
                'username' => 'No hay cajeros activos disponibles en este momento.',
            ]);
        }

        $role = Role::firstOrCreate(
            ['name' => Roles::CLIENTE],
            ['label' => 'Cliente']
        );

        $user = User::create([
            'role_id' => $role->id,
            'vendor_id' => (int) $vendorId,
            'name' => $validated['name'],
            'apellido' => $validated['apellido'] ?? null,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'wants_bonus_emails' => (bool) ($validated['recibir_bonos'] ?? false),
            'password' => $validated['password'],
            'status' => 'active',
        ]);

        event(new Registered($user));

        Auth::login($user);
        session()->forget(['active_agent_id', 'active_line_id']);
        session()->regenerate();
        session(['active_vendor_id' => $vendorId]);

        $this->redirect(route('client.account'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.client-register', [
            'vendors' => $this->vendors(),
            'selectedVendor' => $this->selectedVendor(),
        ])->layout('layouts.auth');
    }

    protected function rules(): array
    {
        $vendorId = (int) $this->selectedVendorId;

        return [
            'selectedVendorId' => ['required', 'integer', Rule::exists('vendors', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:40',
                'alpha_dash',
                Rule::unique('users', 'username')->where(fn ($query) => $query->where('vendor_id', $vendorId)),
                $this->panelCredentialRule('username'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(fn ($query) => $query->where('vendor_id', $vendorId)),
                $this->panelCredentialRule('email'),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers(),
            ],
            'recibir_bonos' => ['boolean'],
        ];
    }

    private function vendors()
    {
        return Vendor::query()
            ->where('is_active', true)
            ->orderByDesc('is_direct')
            ->orderBy('name')
            ->get();
    }

    private function selectedVendor(): ?Vendor
    {
        return $this->selectedVendorId
            ? Vendor::whereKey((int) $this->selectedVendorId)->first()
            : null;
    }

    private function defaultVendorId(): ?int
    {
        $vendorId = session('active_vendor_id');

        if ($vendorId && Vendor::query()->whereKey($vendorId)->where('is_active', true)->exists()) {
            return (int) $vendorId;
        }

        return Vendor::where('is_direct', true)->where('is_active', true)->value('id')
            ?? Vendor::where('is_active', true)->value('id');
    }

    private function panelCredentialRule(string $column): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($column): void {
            $exists = User::withoutGlobalScopes()
                ->where($column, $value)
                ->whereHas('role', fn ($query) => $query->where('name', '!=', Roles::CLIENTE))
                ->exists();

            if ($exists) {
                $fail($attribute === 'email'
                    ? 'Ese email ya esta reservado para una cuenta del panel.'
                    : 'Ese nombre de cliente ya esta reservado para una cuenta del panel.');
            }
        };
    }
}
