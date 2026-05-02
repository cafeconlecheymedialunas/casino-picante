<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\AgentPermission;
use App\Models\Role;
use Livewire\Component;

class Agentes extends Component
{
    public $search = '';

    public $selectedAgent = null;

    public $showModal = false;

    public $editingAgent = null;

    public $showPermModal = false;

    public $name = '';

    public $email = '';

    public $password = '';

    public $phone = '';

    public $role = 'child';

    public $parent_id = null;

    public $role_id = null;

    public $lines = [];

    public $selectedRoleId = '';

    public $permSections = [];

    public $permLevels = ['none', 'read', 'create', 'edit', 'delete'];

    public $currentAgentId = null;

    public $currentAgentRole = 'admin';

    public $availableSections = [
        'blog' => 'Blog',
        'novedades' => 'Novedades',
        'promociones' => 'Promociones',
        'carrusel' => 'Carrusel',
        'tickets' => 'Tickets',
        'usuarios' => 'Usuarios',
        'metricas' => 'Métricas',
        'contactos' => 'Enlaces de contacto',
    ];

    protected $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email',
        'password' => 'required|min:6',
        'role' => 'required|in:parent,child',
    ];

    public function selectAgent($agentId)
    {
        $this->selectedAgent = Agent::with(['permissions', 'roleModel'])->find($agentId);

        $this->permSections = [];
        $sections = ['blog', 'novedades', 'promociones', 'carrusel', 'tickets', 'usuarios', 'agentes', 'lineas', 'reportes'];
        foreach ($sections as $section) {
            $perm = $this->selectedAgent->permissions->firstWhere('section', $section);
            $this->permSections[$section] = $perm ? $perm->level : 'none';
        }
    }

    public function openCreateModal($parentId = null)
    {
        $this->resetForm();
        $this->parent_id = $parentId;
        if ($parentId) {
            $this->role = 'child';
        }
        $this->showModal = true;
    }

    public function openEditModal($agentId)
    {
        $agent = Agent::find($agentId);
        $this->editingAgent = $agent;
        $this->name = $agent->name;
        $this->email = $agent->email;
        $this->phone = $agent->phone ?? '';
        $this->role = $agent->role;
        $this->parent_id = $agent->parent_id;
        $this->role_id = $agent->role_id;
        $this->lines = $agent->lines ?? [];
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
        $this->role = 'child';
        $this->parent_id = null;
        $this->lines = [];
    }

    public function saveAgent()
    {
        $rules = [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'role' => 'required|in:parent,child',
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
            'role' => $this->role,
            'parent_id' => $this->role === 'child' ? $this->parent_id : null,
            'role_id' => $this->role_id,
            'lines' => $this->lines,
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

    public function togglePerm($section, $level)
    {
        $this->permSections[$section] = $level;
    }

    public function savePermissions()
    {
        if (! $this->selectedAgent) {
            return;
        }

        foreach ($this->permSections as $section => $level) {
            AgentPermission::updateOrCreate(
                ['agent_id' => $this->selectedAgent->id, 'section' => $section],
                ['level' => $level]
            );
        }

        $this->selectedAgent = $this->selectedAgent->fresh(['permissions']);
        session()->flash('message', 'Permisos guardados correctamente');
    }

    public function deleteAgent($agentId)
    {
        $agent = Agent::find($agentId);
        $agent->permissions()->delete();
        $agent->children()->update(['parent_id' => null]);
        $agent->delete();

        if ($this->selectedAgent && $this->selectedAgent->id === $agentId) {
            $this->selectedAgent = null;
        }

        session()->flash('message', 'Agente eliminado correctamente');
    }

    public function closeSelectedAgent()
    {
        $this->selectedAgent = null;
    }

    public function toggleLine($line)
    {
        if (in_array($line, $this->lines)) {
            $this->lines = array_filter($this->lines, fn ($l) => $l !== $line);
        } else {
            $this->lines = array_merge($this->lines, [$line]);
        }
    }

    public function assignRole($agentId)
    {
        if (! $this->selectedRoleId) {
            return;
        }

        $agent = Agent::find($agentId);
        $agent->update(['role_id' => $this->selectedRoleId]);
        $this->selectedAgent = $agent->fresh(['role', 'permissions']);
        $this->selectedRoleId = '';
        session()->flash('message', 'Rol asignado correctamente');
    }

    public function removeRole($agentId)
    {
        $agent = Agent::find($agentId);
        $agent->update(['role_id' => null]);
        $this->selectedAgent = $agent->fresh(['role', 'permissions']);
        session()->flash('message', 'Rol eliminado correctamente');
    }

    public function getParents()
    {
        if ($this->currentAgentRole === 'parent') {
            return collect([]);
        }

        return Agent::where('role', 'parent')->orderBy('name')->get();
    }

    public function getRoles()
    {
        return Role::where('is_active', true)->orderBy('name')->get();
    }

    public function getAgents()
    {
        if ($this->currentAgentRole === 'parent') {
            $query = Agent::where('parent_id', $this->currentAgentId)
                ->with(['children', 'permissions']);
        } else {
            $query = Agent::query()->with(['children', 'permissions']);
        }

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('name')->get();
    }

    public function getMyChildren()
    {
        return Agent::where('parent_id', $this->currentAgentId)
            ->with('permissions')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        if ($this->currentAgentRole === 'parent') {
            $agents = $this->getMyChildren();
        } else {
            $agents = $this->getAgents();
        }
        $parents = $this->getParents();
        $roles = $this->getRoles();

        return view('livewire.agentes', compact('agents', 'parents', 'roles'))->layout('layouts.dashboard');
    }
}
