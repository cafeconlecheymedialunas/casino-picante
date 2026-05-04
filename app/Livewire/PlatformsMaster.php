<?php

namespace App\Livewire;

use App\Models\Platform;
use Livewire\Component;

class PlatformsMaster extends Component
{
    public $showModal = false;

    public $editingPlatform = null;

    public $name = '';

    public $slug = '';

    public $logo_url = '';

    public $description = '';

    public $website_url = '';

    public $is_active = true;

    public array $contacts = [];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($platformId)
    {
        $platform = Platform::find($platformId);
        $this->editingPlatform = $platform;
        $this->name = $platform->name;
        $this->slug = $platform->slug;
        $this->logo_url = $platform->logo_url ?? '';
        $this->description = $platform->description ?? '';
        $this->website_url = $platform->website_url ?? '';
        $this->is_active = $platform->is_active;
        $this->contacts = $platform->contacts ?? [];
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
        $this->description = '';
        $this->website_url = '';
        $this->is_active = true;
        $this->contacts = [];
    }

    public function addContact()
    {
        $this->contacts[] = ['type' => 'whatsapp', 'value' => '', 'message' => ''];
    }

    public function removeContact(int $index)
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts);
    }

    public function savePlatform()
    {
        $rules = [
            'name' => 'required|min:2',
            'slug' => 'required|min:2|unique:platforms,slug'.($this->editingPlatform ? ','.$this->editingPlatform->id : ''),
            'logo_url' => 'nullable|url',
            'website_url' => 'nullable|url',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $this->logo_url ?: null,
            'description' => $this->description ?: null,
            'website_url' => $this->website_url ?: null,
            'is_active' => $this->is_active,
            'contacts' => array_values(array_filter($this->contacts, fn($c) => !empty($c['value']))),
        ];

        if ($this->editingPlatform) {
            $this->editingPlatform->update($data);
            session()->flash('message', 'Plataforma actualizada.');
        } else {
            Platform::create($data);
            session()->flash('message', 'Plataforma creada.');
        }

        $this->closeModal();
    }

    public function toggleActive($platformId)
    {
        $platform = Platform::find($platformId);
        $platform->update(['is_active' => ! $platform->is_active]);
    }

    public function deletePlatform($platformId)
    {
        $platform = Platform::find($platformId);
        $platform->delete();
        if ($this->editingPlatform && $this->editingPlatform->id == $platformId) {
            $this->closeModal();
        }
        session()->flash('message', 'Plataforma eliminada.');
    }

    public function render()
    {
        $platforms = Platform::orderBy('name')->get();

        return view('livewire.platforms-master', compact('platforms'))->layout('layouts.dashboard');
    }
}
