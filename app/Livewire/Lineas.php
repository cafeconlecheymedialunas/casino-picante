<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Platform;
use Livewire\Component;

class Lineas extends Component
{
    public $showModal = false;

    public $editingLine = null;

    public $search = '';

    // Basic fields
    public $name = '';

    public $phone = '';

    public $whatsapp = '';

    public $telegram = '';

    public $status = 'active';

    public $type = 'whatsapp';

    // Extended fields for edit modal
    public $description = '';

    public $icon = '';

    public $encargado_id = null;

    public array $editContactLinks = [];

    public array $editPlatforms = [];

    protected $rules = [
        'name' => 'required|min:2',
        'phone' => 'nullable',
        'whatsapp' => 'nullable',
        'telegram' => 'nullable',
        'status' => 'required|in:active,inactive',
        'type' => 'required|in:whatsapp,telegram,phone',
    ];

    public function toggleLine($id)
    {
        $line = Line::find($id);
        $line->update(['status' => $line->status === 'active' ? 'inactive' : 'active']);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $line = Line::find($id);
        $this->editingLine = $line;
        $this->name = $line->name;
        $this->phone = $line->phone ?? '';
        $this->whatsapp = $line->whatsapp ?? '';
        $this->telegram = $line->telegram ?? '';
        $this->status = $line->status;
        $this->type = $line->type ?? 'whatsapp';
        $this->description = $line->description ?? '';
        $this->icon = $line->icon ?? '';
        $this->encargado_id = $line->encargado_id;

        // Load contact links
        $rawLinks = $line->contact_links ?? [];
        $this->editContactLinks = array_map(function ($link) {
            return array_merge([
                'type' => 'whatsapp',
                'value' => '',
                'has_message' => false,
                'message' => '',
            ], $link);
        }, $rawLinks);

        // Load platforms from pivot
        $this->editPlatforms = $line->platforms()->get()->map(function ($platform) {
            return [
                'platform_id' => $platform->id,
                'name' => $platform->name,
                'is_active' => $platform->pivot->is_active ?? true,
                'custom_message' => $platform->pivot->custom_message ?? '',
            ];
        })->toArray();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingLine = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->phone = '';
        $this->whatsapp = '';
        $this->telegram = '';
        $this->status = 'active';
        $this->type = 'whatsapp';
        $this->description = '';
        $this->icon = '';
        $this->encargado_id = null;
        $this->editContactLinks = [];
        $this->editPlatforms = [];
    }

    public function addContactLink()
    {
        $this->editContactLinks[] = [
            'type' => 'whatsapp',
            'value' => '',
            'has_message' => false,
            'message' => '',
        ];
    }

    public function removeContactLink($index)
    {
        unset($this->editContactLinks[$index]);
        $this->editContactLinks = array_values($this->editContactLinks);
    }

    public function togglePlatform($platformId)
    {
        $found = false;
        foreach ($this->editPlatforms as &$p) {
            if ($p['platform_id'] == $platformId) {
                $p['is_active'] = ! ($p['is_active'] ?? true);
                $found = true;
            }
        }

        if (! $found) {
            $platform = Platform::find($platformId);
            if ($platform) {
                $this->editPlatforms[] = [
                    'platform_id' => $platformId,
                    'name' => $platform->name,
                    'is_active' => true,
                    'custom_message' => '',
                ];
            }
        }
    }

    public function saveLine()
    {
        $this->validate();

        $contactLinks = array_filter($this->editContactLinks, function ($link) {
            return ! empty($link['value']);
        });
        $contactLinks = array_values($contactLinks);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'telegram' => $this->telegram,
            'status' => $this->status,
            'type' => $this->type,
            'description' => $this->description,
            'icon' => $this->icon,
            'encargado_id' => $this->encargado_id ?: null,
            'contact_links' => $contactLinks,
        ];

        if ($this->editingLine) {
            $this->editingLine->update($data);

            // Update platforms
            $this->editingLine->platforms()->detach();
            foreach ($this->editPlatforms as $p) {
                if (! empty($p['platform_id'])) {
                    $this->editingLine->platforms()->attach($p['platform_id'], [
                        'custom_message' => $p['custom_message'] ?? '',
                        'is_active' => $p['is_active'] ?? true,
                    ]);
                }
            }

            session()->flash('message', 'Línea actualizada correctamente');
        } else {
            Line::create($data);
            session()->flash('message', 'Línea creada correctamente');
        }

        $this->closeModal();
    }

    public function deleteLine($id)
    {
        Line::find($id)->delete();
        session()->flash('message', 'Línea eliminada correctamente');
    }

    public function getLines()
    {
        $query = Line::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('phone', 'like', '%'.$this->search.'%')
                ->orWhere('whatsapp', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('id')->get();
    }

    public function getAvailablePlatforms()
    {
        return Platform::where('is_active', true)->orderBy('name')->get();
    }

    public function getAvailableAgents()
    {
        return Agent::where('status', 'active')->orderBy('name')->get();
    }

    public function render()
    {
        $lines = $this->getLines();

        // Eager-load agent count per line to avoid N+1
        $agentCounts = LineAgent::whereIn('line_id', $lines->pluck('id'))
            ->where('is_active', true)
            ->selectRaw('line_id, count(*) as total')
            ->groupBy('line_id')
            ->pluck('total', 'line_id');

        $availablePlatforms = $this->getAvailablePlatforms();
        $availableAgents = $this->getAvailableAgents();

        return view('livewire.lineas', compact('lines', 'agentCounts', 'availablePlatforms', 'availableAgents'))->layout('layouts.dashboard');
    }
}
