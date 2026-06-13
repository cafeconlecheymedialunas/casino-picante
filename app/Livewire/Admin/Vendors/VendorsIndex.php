<?php

namespace App\Livewire\Admin\Vendors;

use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Support\FontAwesomeIcons;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class VendorsIndex extends Component
{
    use WithFileUploads, WithPagination;

    protected array $dispatchesEvents = [
        'page-header-action' => 'handlePageHeaderAction',
    ];

    public $search = '';
    public $showModal = false;
    public $vendorId = null;

    // Info
    public $name;
    public $slug;
    public $user_id;
    public $is_active = true;
    public $is_direct = false;
    public $description;

    // Imágenes
    public $logo;
    public $heroImage;
    public $portraitImage;
    public $logoUpload;
    public $heroImageUpload;
    public $portraitImageUpload;

    // Contactos por tipo
    public array $contactWhatsapp  = ['value' => '', 'name' => ''];
    public array $contactTelegram  = ['value' => '', 'name' => ''];
    public array $contactInstagram = ['value' => '', 'name' => ''];
    public array $contactFacebook  = ['value' => '', 'name' => ''];
    public array $contactEmail     = ['value' => '', 'name' => ''];
    public array $contactPhone     = ['value' => '', 'name' => ''];
    public array $contactWeb       = ['value' => '', 'name' => ''];

    // Características
    public $features = [];
    private array $existingBranding = [];

    // Usuario
    public $user_mode = 'select';
    public $selected_user_id = null;
    public $username;
    public $email;
    public $password;

    private const CONTACT_TYPES = [
        'Whatsapp', 'Telegram', 'Instagram', 'Facebook', 'Email', 'Phone', 'Web',
    ];

    public function addFeature()
    {
        $this->features[] = ['icon' => 'fa-solid fa-star', 'title' => '', 'description' => ''];
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $cajeroRoleIds = Role::where('name', Roles::CAJERO)->pluck('id');
        $rules = [
            'name'               => 'required|min:3',
            'slug'               => ['required', Rule::unique('vendors', 'slug')->ignore($this->vendorId)],
            'is_active'          => 'boolean',
            'description'        => 'nullable|string',
            'logoUpload'         => 'nullable|image|max:5120',
            'heroImageUpload'    => 'nullable|image|max:5120',
            'portraitImageUpload'=> 'nullable|image|max:5120',
            'features'           => 'array',
            'features.*.icon'    => 'nullable|string|max:120',
            'features.*.title'   => 'nullable|string|max:80',
            'features.*.description' => 'nullable|string|max:255',
        ];

        if ($this->user_mode === 'select') {
            $rules['selected_user_id'] = [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('role_id', $cajeroRoleIds)),
                Rule::unique('vendors', 'user_id')->ignore($this->vendorId),
            ];
        }

        $this->validate($rules);
        $contacts = $this->buildContacts();

        DB::transaction(function () use ($contacts) {
            $previousUserId = $this->vendorId
                ? Vendor::query()->find($this->vendorId)?->user_id
                : null;

            if ($this->user_mode === 'create') {
                $this->validate([
                    'username' => 'required|unique:users,username',
                    'email'    => 'required|email|unique:users,email',
                    'password' => 'required|min:6',
                ]);

                $role = Role::firstOrCreate(
                    ['name' => Roles::CAJERO],
                    ['label' => 'Cajero']
                );

                $user = User::withoutEvents(fn () => User::withoutGlobalScopes()->create([
                    'name'     => $this->username,
                    'username' => $this->username,
                    'email'    => $this->email,
                    'password' => Hash::make($this->password),
                    'role_id'  => $role->id,
                    'status'   => 'active',
                ]));

                $this->user_id = $user->id;
            } else {
                $this->user_id = $this->selected_user_id;
            }

            $vendor = Vendor::updateOrCreate(['id' => $this->vendorId], [
                'name'           => $this->name,
                'slug'           => $this->slug,
                'user_id'        => $this->user_id,
                'logo'           => $this->logo,
                'hero_image'     => $this->heroImage,
                'portrait_image' => $this->portraitImage,
                'description'    => $this->description,
                'contacts'       => $contacts,
                'features'       => $this->normalizedFeatures(),
                'branding'       => $this->existingBranding,
                'is_active'      => $this->is_active,
                'is_direct'      => $this->is_direct,
            ]);

            if ($this->logoUpload) {
                $this->logo = $this->logoUpload->store("vendors/{$vendor->id}/logos", 'public');
                $vendor->forceFill(['logo' => $this->logo])->save();
            }

            if ($this->heroImageUpload) {
                $this->heroImage = $this->heroImageUpload->store("vendors/{$vendor->id}/public", 'public');
                $vendor->forceFill(['hero_image' => $this->heroImage])->save();
            }

            if ($this->portraitImageUpload) {
                $this->portraitImage = $this->portraitImageUpload->store("vendors/{$vendor->id}/public", 'public');
                $vendor->forceFill(['portrait_image' => $this->portraitImage])->save();
            }

            User::withoutGlobalScopes()
                ->where('id', $this->user_id)
                ->update(['vendor_id' => $vendor->id]);

            if ($previousUserId && (int) $previousUserId !== (int) $this->user_id) {
                User::withoutGlobalScopes()
                    ->where('id', $previousUserId)
                    ->where('vendor_id', $vendor->id)
                    ->update(['vendor_id' => null]);
            }
        });

        $this->closeModal();
        $this->dispatch('swal', ['title' => 'Cajero guardado con éxito', 'icon' => 'success']);
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        $this->vendorId         = $vendor->id;
        $this->name             = $vendor->name;
        $this->slug             = $vendor->slug;
        $this->user_id          = $vendor->user_id;
        $this->selected_user_id = $vendor->user_id;
        $this->is_active        = $vendor->is_active;
        $this->is_direct        = $vendor->is_direct;
        $this->description      = $vendor->description;
        $this->logo             = $vendor->logo;
        $this->heroImage        = $vendor->hero_image;
        $this->portraitImage    = $vendor->portrait_image;
        $this->features         = $vendor->features ?? [];
        $this->existingBranding = $vendor->branding ?? [];
        $this->user_mode        = $vendor->user_id ? 'select' : 'create';
        $this->logoUpload       = null;
        $this->heroImageUpload  = null;
        $this->portraitImageUpload = null;

        $this->resetContactFields();
        foreach ($vendor->contacts ?? [] as $contact) {
            $type = ucfirst(strtolower($contact['type'] ?? ''));
            $prop = 'contact' . $type;
            if (property_exists($this, $prop)) {
                $this->$prop = [
                    'value' => $contact['value'] ?? '',
                    'name'  => $contact['name']  ?? '',
                ];
            }
        }

        $this->showModal = true;
    }

    public function removeLogo()        { $this->logo = null;         $this->logoUpload = null; }
    public function removeHeroImage()   { $this->heroImage = null;    $this->heroImageUpload = null; }
    public function removePortraitImage(){ $this->portraitImage = null; $this->portraitImageUpload = null; }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function handlePageHeaderAction(string $action): void
    {
        if ($action === 'openCreateModal') {
            $this->openCreateModal();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->vendorId         = null;
        $this->name             = '';
        $this->slug             = '';
        $this->user_id          = null;
        $this->selected_user_id = null;
        $this->is_active        = true;
        $this->is_direct        = false;
        $this->description      = '';
        $this->logo             = '';
        $this->heroImage        = '';
        $this->portraitImage    = '';
        $this->logoUpload       = null;
        $this->heroImageUpload  = null;
        $this->portraitImageUpload = null;
        $this->features         = [];
        $this->existingBranding = [];
        $this->user_mode        = 'select';
        $this->username         = '';
        $this->email            = '';
        $this->password         = '';
        $this->resetContactFields();
    }

    private function resetContactFields(): void
    {
        foreach (self::CONTACT_TYPES as $type) {
            $this->{'contact' . $type} = ['value' => '', 'name' => ''];
        }
    }

    private function buildContacts(): array
    {
        $map = [
            'whatsapp'  => $this->contactWhatsapp,
            'telegram'  => $this->contactTelegram,
            'instagram' => $this->contactInstagram,
            'facebook'  => $this->contactFacebook,
            'email'     => $this->contactEmail,
            'phone'     => $this->contactPhone,
            'web'       => $this->contactWeb,
        ];

        $result = [];
        foreach ($map as $type => $data) {
            $value = trim((string) ($data['value'] ?? ''));
            if ($value !== '') {
                $result[] = [
                    'type'  => $type,
                    'value' => $value,
                    'name'  => trim((string) ($data['name'] ?? '')),
                ];
            }
        }

        return $result;
    }

    private function normalizedFeatures(): array
    {
        return collect($this->features)
            ->filter(fn ($f) => filled($f['title'] ?? null))
            ->map(fn ($f) => [
                'icon'        => $this->normalizeFeatureIcon((string) ($f['icon'] ?? 'fa-solid fa-star')),
                'title'       => trim((string) ($f['title'] ?? '')),
                'description' => trim((string) ($f['description'] ?? '')),
            ])
            ->values()
            ->all();
    }

    public function render()
    {
        $vendors = Vendor::where('name', 'like', '%' . $this->search . '%')
            ->with('user')
            ->paginate(10);

        $cajeros = User::withoutGlobalScopes()
            ->whereHas('role', fn ($q) => $q->where('name', Roles::CAJERO))
            ->where(function ($query) {
                $query->whereDoesntHave('assignedVendor');
                if ($this->selected_user_id) {
                    $query->orWhere('id', $this->selected_user_id);
                }
            })
            ->get();

        return view('livewire.admin.vendors-index', [
            'vendors'          => $vendors,
            'cajeros'          => $cajeros,
            'fontAwesomeIcons' => FontAwesomeIcons::options(),
        ]);
    }

    private function normalizeFeatureIcon(string $icon): string
    {
        $icon = trim($icon);
        if ($icon === '') return 'fa-solid fa-star';
        if (! str_contains($icon, 'fa-')) return 'fa-solid fa-' . $icon;
        if (! str_contains($icon, 'fa-solid') && ! str_contains($icon, 'fa-regular') && ! str_contains($icon, 'fa-brands')) {
            return 'fa-solid ' . $icon;
        }
        return $icon;
    }
}
