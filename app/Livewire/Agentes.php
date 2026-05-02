<?php

namespace App\Livewire;

use App\Models\Agent;
use Livewire\Component;

class Agentes extends Component
{
    public $search = '';

    public $selectedAgent = null;

    public $showModal = false;

    public $editingAgent = null;

    public function selectAgent($agentId)
    {
        $this->selectedAgent = Agent::with('permissions')->find($agentId);
    }

    public function openCreateModal()
    {
        $this->editingAgent = null;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingAgent = null;
    }

    public function getAgents()
    {
        $query = Agent::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('name')->get();
    }

    public function render()
    {
        $agents = $this->getAgents();

        return view('livewire.agentes', compact('agents'))->extends('layouts.dashboard');
    }
}
