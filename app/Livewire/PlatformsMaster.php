<?php

namespace App\Livewire;

use App\Models\Platform;
use App\Support\ImageStorage;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlatformsMaster extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

    protected array $dispatchesEvents = [
        'page-header-action' => 'handlePageHeaderAction',
    ];

    public $showModal = false;

    public $editingPlatform = null;

    public $name = '';

    public $slug = '';

    public $logo_url = '';

    public $logoUpload = null;

    public $description = '';

    public $website_url = '';

    public $is_active = true;

    public function openCreateModal()
    {
        $this->ensurePlatformAdmin();
        $this->resetForm();
        $this->showModal = true;
    }

    public function handlePageHeaderAction(string $action): void
    {
        if ($action === 'openCreateModal') {
            $this->openCreateModal();
        }
    }

    public function openEditModal($platformId)
    {
        $this->ensurePlatformAdmin();

        $platform = Platform::withoutGlobalScopes()->find($platformId);
        $this->authorizeVendorRecord($platform);
        $this->editingPlatform = $platform;
        $this->name = $platform->name;
        $this->slug = $platform->slug;
        $this->logo_url = $platform->logo_url ?? '';
        $this->logoUpload = null;
        $this->description = $platform->description ?? '';
        $this->website_url = $platform->website_url ?? '';
        $this->is_active = $platform->is_active;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPlatform = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->logo_url = '';
        $this->logoUpload = null;
        $this->description = '';
        $this->website_url = '';
        $this->is_active = true;
    }

    public function savePlatform()
    {
        $this->ensurePlatformAdmin();

        $rules = [
            'name' => 'required|min:2',
            'slug' => [
                'required',
                'min:2',
                Rule::unique('platforms', 'slug')
                    ->ignore($this->editingPlatform?->id),
            ],
            'logoUpload' => 'nullable|image|max:4096',
            'website_url' => 'nullable|url',
        ];

        $this->validate($rules);

        $logoPath = $this->logo_url;

        if ($this->logoUpload) {
            $logoPath = ImageStorage::store($this->logoUpload, 'plataformas/logos', $this->logo_url ?: null);
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $logoPath ?: null,
            'description' => $this->description ?: null,
            'website_url' => $this->website_url ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingPlatform) {
            $this->authorizeVendorRecord($this->editingPlatform);
            $this->editingPlatform->update($data);
            session()->flash('message', 'Plataforma actualizada.');
            $this->notifyPlatform('Plataforma actualizada', "La plataforma {$this->name} fue actualizada.", 'info');
        } else {
            $data['vendor_id'] = null;
            Platform::create($data);
            session()->flash('message', 'Plataforma creada.');
            $this->notifyPlatform('Plataforma creada', "La plataforma {$this->name} fue creada exitosamente.", 'success');
        }

        $this->closeModal();
    }

    public function toggleActive($platformId)
    {
        $this->ensurePlatformAdmin();

        $platform = Platform::withoutGlobalScopes()->find($platformId);
        $this->authorizeVendorRecord($platform);
        $platform->update(['is_active' => ! $platform->is_active]);

        $this->notifyPlatform(
            'Estado de plataforma cambiado',
            "La plataforma {$platform->name} fue ".($platform->is_active ? 'activada' : 'pausada').'.',
            'warning'
        );
    }

    public function deletePlatform($platformId)
    {
        $this->ensurePlatformAdmin();

        $platform = Platform::withoutGlobalScopes()->find($platformId);
        $this->authorizeVendorRecord($platform);
        $platformName = $platform->name;
        ImageStorage::delete($platform?->logo_url);
        $platform->delete();
        if ($this->editingPlatform && $this->editingPlatform->id == $platformId) {
            $this->closeModal();
        }
        session()->flash('message', 'Plataforma eliminada.');
        $this->notifyPlatform('Plataforma eliminada', "La plataforma {$platformName} fue eliminada del sistema.", 'danger');
    }

    public function removeLogo(): void
    {
        $this->ensurePlatformAdmin();

        if ($this->editingPlatform && $this->logo_url) {
            ImageStorage::delete($this->logo_url);
            $this->editingPlatform->update(['logo_url' => null]);
        }

        $this->logoUpload = null;
        $this->logo_url = '';
    }

    public function render()
    {
        $this->ensureAdmin();
        $vendorId = session('active_vendor_id');
        $platforms = Platform::withoutGlobalScopes()
            ->where(function ($query) use ($vendorId) {
                $query->whereNull('vendor_id')
                    ->when($vendorId, fn ($platforms) => $platforms->orWhere('vendor_id', (int) $vendorId));
            })
            ->orderBy('name')
            ->get();
        $canManagePlatforms = auth()->user()?->hasRole(\App\Support\Roles::ADMIN) ?? false;

        return view('livewire.platforms-master', compact('platforms', 'canManagePlatforms'))->layout('layouts.dashboard');
    }

    private function ensureAdmin(): void
    {
        if (! $this->isAdminMode()) {
            abort(403, 'Solo administradores y cajeros pueden gestionar plataformas.');
        }
    }

    private function ensurePlatformAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole(\App\Support\Roles::ADMIN), 403, 'Solo el administrador puede gestionar el catalogo universal de plataformas.');
    }

    private function notifyPlatform(string $title, string $message, string $type): void
    {
        if (! session('active_vendor_id')) {
            return;
        }

        $this->notify($title, $message, 'platforms', '/plataformas', $type);
    }

    private function authorizeVendorRecord(?Platform $platform): void
    {
        if (! $platform) {
            return;
        }

        abort_unless($platform->vendor_id === null, 403, 'Solo se puede editar el catalogo universal de plataformas.');
    }
}
