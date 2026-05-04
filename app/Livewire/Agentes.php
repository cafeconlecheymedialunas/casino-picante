<?php

namespace App\Livewire;

use App\Models\Agent;
use Livewire\Component;

class Agentes extends Component
{
    public $search = '';

    public $statusFilter = 'all';

    public $showModal = false;

    public $editingAgent = null;

    public $name = '';

    public $email = '';

    public $password = '';

    public $phone = '';

    public $status = 'active';

    protected $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($agentId)
    {
        $agent = Agent::find($agentId);
        $this->editingAgent = $agent;
        $this->name = $agent->name;
        $this->email = $agent->email;
        $this->phone = $agent->phone ?? '';
        $this->status = $agent->status;
        $this->password = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingAgent = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->status = 'active';
    }

    public function saveAgent()
    {
        $rules = [
            'name' => 'required|min:2',
            'email' => 'required|email',
        ];

        if ($this->editingAgent) {
            $rules['email'] = 'required|email|unique:agents,email,'.$this->editingAgent->id;
            if ($this->password) {
                $rules['password'] = 'min:6';
            }
        } else {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->editingAgent) {
            $this->editingAgent->update($data);
            session()->flash('message', 'Agente actualizado correctamente');
        } else {
            Agent::create($data);
            session()->flash('message', 'Agente creado correctamente');
        }

        $this->closeModal();
    }

    public function toggleStatus($agentId)
    {
        $agent = Agent::find($agentId);
        $newStatus = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->update(['status' => $newStatus]);
        session()->flash('message', 'Estado actualizado');
    }

    public function deleteAgent($agentId)
    {
        $agent = Agent::find($agentId);
        $agent->delete();
        session()->flash('message', 'Agente eliminado correctamente');
    }

    public function getAgents()
    {
        $query = Agent::query()->with('activeLines');

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy('name')->get();
    }

    public function getMetrics()
    {
        return [
            'total' => Agent::count(),
            'active' => Agent::where('status', 'active')->count(),
            'inactive' => Agent::where('status', 'inactive')->count(),
            'with_lines' => Agent::whereHas('activeLines')->count(),
        ];
    }

    public function render()
    {
        $agents = $this->getAgents();
        $metrics = $this->getMetrics();

        return view('livewire.agentes', compact('agents', 'metrics'))->layout('layouts.dashboard');
    }
}
