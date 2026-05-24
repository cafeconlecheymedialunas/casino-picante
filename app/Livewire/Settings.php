<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public string $activeTab = 'notifications';

    public string $dashboardTheme = 'dark';

    public string $siteTitle = '';

    public string $siteLogo = '';

    public $siteLogoUpload = null;

    public string $siteFavicon = '';

    public $siteFaviconUpload = null;

    public ?int $vendorId = null;

    public string $name = '';

    public string $slug = '';

    public ?string $logo = null;

    public $logoUpload = null;

    public ?string $heroImage = null;

    public $heroImageUpload = null;

    public ?string $portraitImage = null;

    public $portraitImageUpload = null;

    public string $description = '';

    public array $contacts = [];

    public string $brandingJson = '{}';

    public ?int $cajeroUserId = null;

    public string $cajeroUsername = '';

    public string $cajeroName = '';

    public string $cajeroApellido = '';

    public string $cajeroEmail = '';

    public string $cajeroPhone = '';

    public string $cajeroContact = '';

    public string $cajeroPassword = '';

    public function mount(): void
    {
        $this->ensureAdmin();
        $this->loadVendorForm();
        $this->loadCajeroUserForm();
        $this->loadDashboardTheme();
        $this->loadBrandingAssets();

        if ($this->currentVendor()) {
            $this->activeTab = 'vendor';
        }
    }

    public function updatedName(string $value): void
    {
        if ($this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function setTab(string $tab): void
    {
        $this->ensureAdmin();

        if ($tab === 'vendor' && ! $this->currentVendor()) {
            abort(403, 'No hay cajero activo para editar.');
        }

        $this->activeTab = $tab;
    }

    public function saveDashboardTheme(): void
    {
        $this->ensureAdmin();

        $this->validate([
            'dashboardTheme' => ['required', Rule::in(['dark', 'light'])],
        ]);

        Setting::updateOrCreate(
            ['key' => 'dashboard_theme'],
            ['value' => $this->dashboardTheme],
        );

        session()->flash('theme_message', 'Apariencia actualizada correctamente.');
        $this->dispatch('dashboard-theme-updated', theme: $this->dashboardTheme);
    }

    public function saveBrandingAssets(): void
    {
        $this->ensureAdmin();

        $this->validate([
            'siteTitle' => ['required', 'string', 'min:2', 'max:80'],
            'siteLogoUpload' => ['nullable', 'image', 'max:5120'],
            'siteFaviconUpload' => ['nullable', 'image', 'max:1024'],
        ]);

        $logo = $this->siteLogo;
        $favicon = $this->siteFavicon ?: Setting::DEFAULT_FAVICON;

        if ($this->siteLogoUpload) {
            $this->deletePublicAsset($logo);
            $logo = $this->siteLogoUpload->store('branding', 'public');
        }

        if ($this->siteFaviconUpload) {
            $this->deletePublicAsset($favicon);
            $favicon = $this->siteFaviconUpload->store('branding', 'public');
        }

        Setting::updateOrCreate(['key' => 'site_title'], ['value' => trim($this->siteTitle)]);
        Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $logo ?: Setting::DEFAULT_LOGO]);
        Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => $favicon ?: Setting::DEFAULT_FAVICON]);

        $this->siteTitle = trim($this->siteTitle);
        $this->siteLogo = $logo ?: Setting::DEFAULT_LOGO;
        $this->siteFavicon = $favicon ?: Setting::DEFAULT_FAVICON;
        $this->siteLogoUpload = null;
        $this->siteFaviconUpload = null;

        session()->flash('branding_assets_message', 'Logo y favicon actualizados correctamente.');
    }

    public function saveVendor(): void
    {
        $this->ensureAdmin();
        $vendor = $this->currentVendor();
        abort_unless($vendor, 403, 'No hay cajero activo para editar.');

        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:160'],
            'slug' => ['required', 'string', 'max:180', Rule::unique('vendors', 'slug')->ignore($vendor->id)],
            'logoUpload' => ['nullable', 'image', 'max:5120'],
            'heroImageUpload' => ['nullable', 'image', 'max:5120'],
            'portraitImageUpload' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string', 'max:1200'],
            'contacts' => ['array'],
            'contacts.*.type' => ['nullable', 'string', 'max:40'],
            'contacts.*.value' => ['nullable', 'string', 'max:255'],
            'contacts.*.name' => ['nullable', 'string', 'max:80'],
            'brandingJson' => ['nullable', 'string'],
        ]);

        $branding = $this->decodeBranding();
        $logo = $this->logo;
        $heroImage = $this->heroImage;
        $portraitImage = $this->portraitImage;

        if ($this->logoUpload) {
            $logo = $this->logoUpload->store("vendors/{$vendor->id}/logos", 'public');
        }

        if ($this->heroImageUpload) {
            $heroImage = $this->heroImageUpload->store("vendors/{$vendor->id}/public", 'public');
        }

        if ($this->portraitImageUpload) {
            $portraitImage = $this->portraitImageUpload->store("vendors/{$vendor->id}/public", 'public');
        }

        $vendor->update([
            'name' => trim($this->name),
            'slug' => Str::slug($this->slug),
            'logo' => $logo,
            'hero_image' => $heroImage,
            'portrait_image' => $portraitImage,
            'description' => trim($this->description) ?: null,
            'contacts' => $this->normalizedContacts(),
            'branding' => $branding,
        ]);

        $this->logo = $logo;
        $this->logoUpload = null;
        $this->heroImage = $heroImage;
        $this->heroImageUpload = null;
        $this->portraitImage = $portraitImage;
        $this->portraitImageUpload = null;
        session()->flash('vendor_message', 'Cajero actualizado correctamente.');
    }

    public function saveCajeroUser(): void
    {
        $this->ensureAdmin();
        $user = $this->currentCajeroUser();
        abort_unless($user, 403, 'No hay usuario cajero para editar.');

        $this->validate([
            'cajeroUsername' => ['required', 'string', 'min:3', 'max:60', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'cajeroName' => ['required', 'string', 'min:2', 'max:100'],
            'cajeroApellido' => ['nullable', 'string', 'max:100'],
            'cajeroEmail' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'cajeroPhone' => ['nullable', 'string', 'max:50'],
            'cajeroContact' => ['nullable', 'string', 'max:100'],
            'cajeroPassword' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'username' => trim($this->cajeroUsername),
            'name' => trim($this->cajeroName),
            'apellido' => trim($this->cajeroApellido) ?: null,
            'email' => trim($this->cajeroEmail),
            'phone' => trim($this->cajeroPhone) ?: null,
            'contact' => trim($this->cajeroContact) ?: null,
        ];

        if ($this->cajeroPassword !== '') {
            $data['password'] = $this->cajeroPassword;
        }

        $user->update($data);
        $this->cajeroPassword = '';
        session()->flash('cajero_user_message', 'Usuario cajero actualizado correctamente.');
    }

    public function removeLogo(): void
    {
        $this->ensureAdmin();
        $vendor = $this->currentVendor();
        abort_unless($vendor, 403, 'No hay cajero activo para editar.');

        $vendor->update(['logo' => null]);
        $this->logo = null;
        $this->logoUpload = null;
    }

    public function removeHeroImage(): void
    {
        $this->ensureAdmin();
        $vendor = $this->currentVendor();
        abort_unless($vendor, 403, 'No hay cajero activo para editar.');

        $vendor->update(['hero_image' => null]);
        $this->heroImage = null;
        $this->heroImageUpload = null;
    }

    public function removePortraitImage(): void
    {
        $this->ensureAdmin();
        $vendor = $this->currentVendor();
        abort_unless($vendor, 403, 'No hay cajero activo para editar.');

        $vendor->update(['portrait_image' => null]);
        $this->portraitImage = null;
        $this->portraitImageUpload = null;
    }

    public function removeSiteLogo(): void
    {
        $this->ensureAdmin();
        $this->deletePublicAsset($this->siteLogo);

        Setting::updateOrCreate(['key' => 'site_logo'], ['value' => Setting::DEFAULT_LOGO]);
        $this->siteLogo = Setting::DEFAULT_LOGO;
        $this->siteLogoUpload = null;
    }

    public function removeSiteFavicon(): void
    {
        $this->ensureAdmin();
        $this->deletePublicAsset($this->siteFavicon);

        Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => Setting::DEFAULT_FAVICON]);
        $this->siteFavicon = Setting::DEFAULT_FAVICON;
        $this->siteFaviconUpload = null;
    }

    public function render()
    {
        return view('livewire.settings', [
            'canEditVendor' => (bool) $this->currentVendor(),
        ])->layout('layouts.dashboard');
    }

    private function ensureAdmin(): void
    {
        $user = auth()->user();

        if (! $user?->hasRole(Roles::ADMIN) && ! $user?->hasRole(Roles::CAJERO)) {
            abort(403, 'Solo administradores y cajeros pueden acceder a configuracion.');
        }
    }

    private function currentVendor(): ?Vendor
    {
        $user = auth()->user();

        if ($user?->hasRole(Roles::CAJERO)) {
            return $user->vendor_id ? Vendor::query()->whereKey($user->vendor_id)->first() : null;
        }

        $vendorId = session('active_vendor_id');

        return $vendorId ? Vendor::query()->whereKey((int) $vendorId)->first() : null;
    }

    private function currentCajeroUser(): ?User
    {
        $vendor = $this->currentVendor();
        if (! $vendor?->user_id) {
            return null;
        }

        $user = User::withoutGlobalScopes()->with('role')->find($vendor->user_id);

        return $user?->hasRole(Roles::CAJERO) ? $user : null;
    }

    private function loadVendorForm(): void
    {
        $vendor = $this->currentVendor();
        if (! $vendor) {
            return;
        }

        $this->vendorId = $vendor->id;
        $this->name = $vendor->name;
        $this->slug = $vendor->slug;
        $this->logo = $vendor->logo;
        $this->heroImage = $vendor->hero_image;
        $this->portraitImage = $vendor->portrait_image;
        $this->description = $vendor->description ?? '';
        $this->contacts = $vendor->contacts ?? [];
        $this->brandingJson = json_encode($vendor->branding ?: new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function loadCajeroUserForm(): void
    {
        $user = $this->currentCajeroUser();
        if (! $user) {
            return;
        }

        $this->cajeroUserId = $user->id;
        $this->cajeroUsername = $user->username ?? '';
        $this->cajeroName = $user->name ?? '';
        $this->cajeroApellido = $user->apellido ?? '';
        $this->cajeroEmail = $user->email ?? '';
        $this->cajeroPhone = $user->phone ?? '';
        $this->cajeroContact = $user->contact ?? '';
        $this->cajeroPassword = '';
    }

    private function loadDashboardTheme(): void
    {
        $theme = Setting::query()->where('key', 'dashboard_theme')->value('value');

        $this->dashboardTheme = in_array($theme, ['dark', 'light'], true) ? $theme : 'dark';
    }

    private function loadBrandingAssets(): void
    {
        $this->siteTitle = Setting::siteTitle();
        $this->siteLogo = Setting::siteLogoPath();
        $this->siteFavicon = Setting::siteFaviconPath();
    }

    private function decodeBranding(): array
    {
        if (blank($this->brandingJson)) {
            return [];
        }

        $decoded = json_decode($this->brandingJson, true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'brandingJson' => 'El branding debe ser un JSON valido.',
            ]);
        }

        return $decoded;
    }

    private function normalizedContacts(): array
    {
        return collect($this->contacts)
            ->filter(fn ($contact) => filled($contact['value'] ?? null))
            ->map(fn ($contact) => [
                'type' => $contact['type'] ?? 'other',
                'value' => trim((string) ($contact['value'] ?? '')),
                'name' => trim((string) ($contact['name'] ?? '')),
            ])
            ->values()
            ->all();
    }

    private function deletePublicAsset(?string $path): void
    {
        if (! $path || $path === Setting::DEFAULT_FAVICON || str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
