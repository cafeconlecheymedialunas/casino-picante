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
        $this->ensureAdmin();
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
        $this->ensureAdmin();

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
        $this->ensureAdmin();

        $rules = [
            'name' => 'required|min:2',
            'slug' => [
                'required',
                'min:2',
                Rule::unique('platforms', 'slug')
                    ->where(fn ($query) => $query->where('vendor_id', session('active_vendor_id')))
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
            $this->notify('Plataforma actualizada', "La plataforma {$this->name} fue actualizada.", 'platforms', '/plataformas', 'info');
        } else {
            $data['vendor_id'] = session('active_vendor_id');
            Platform::create($data);
            session()->flash('message', 'Plataforma creada.');
            $this->notify('Plataforma creada', "La plataforma {$this->name} fue creada exitosamente.", 'platforms', '/plataformas', 'success');
        }

        $this->closeModal();
    }

    public function toggleActive($platformId)
    {
        $this->ensureAdmin();

        $platform = Platform::withoutGlobalScopes()->find($platformId);
        $this->authorizeVendorRecord($platform);
        $platform->update(['is_active' => ! $platform->is_active]);

        $this->notify(
            'Estado de plataforma cambiado',
            "La plataforma {$platform->name} fue ".($platform->is_active ? 'activada' : 'pausada').'.',
            'platforms',
            '/plataformas',
            'warning'
        );
    }

    public function deletePlatform($platformId)
    {
        $this->ensureAdmin();

        $platform = Platform::withoutGlobalScopes()->find($platformId);
        $this->authorizeVendorRecord($platform);
        $platformName = $platform->name;
        ImageStorage::delete($platform?->logo_url);
        $platform->delete();
        if ($this->editingPlatform && $this->editingPlatform->id == $platformId) {
            $this->closeModal();
        }
        session()->flash('message', 'Plataforma eliminada.');
        $this->notify('Plataforma eliminada', "La plataforma {$platformName} fue eliminada del sistema.", 'platforms', '/plataformas', 'danger');
    }

    public function removeLogo(): void
    {
        $this->ensureAdmin();

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
        $platforms = Platform::withoutGlobalScopes()->orderBy('name')->get();
        $canManagePlatforms = $this->isAdminMode();

        return view('livewire.platforms-master', compact('platforms', 'canManagePlatforms'))->layout('layouts.dashboard');
    }

    private function ensureAdmin(): void
    {
        if (! $this->isAdminMode()) {
            abort(403, 'Solo administradores y cajeros pueden gestionar plataformas.');
        }
    }

    private function authorizeVendorRecord(?Platform $platform): void
    {
        if (! $platform || ! session('active_vendor_id')) {
            return;
        }

        abort_unless((int) $platform->vendor_id === (int) session('active_vendor_id'), 403, 'No podes operar plataformas fuera del vendor activo.');
    }
}
