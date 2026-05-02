<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Role;
use Livewire\Component;

class Roles extends Component
{
    public $search = '';

    public $showModal = false;

    public $editingRole = null;

    public $name = '';

    public $description = '';

    public $is_active = true;

    public $permissions = [];

    public $availableSections = [
        'dashboard' => 'Dashboard',
        'usuarios' => 'Usuarios',
        'agentes' => 'Agentes',
        'promociones' => 'Promociones',
        'novedades' => 'Novedades',
        'lineas' => 'Líneas',
        'tickets' => 'Tickets',
        'caja' => 'Caja/Pagos',
        'bonos' => 'Bonos',
        'juegos' => 'Juegos',
        'banners' => 'Banners',
        'reportes' => 'Reportes',
        'logs' => 'Logs',
        'ajustes' => 'Ajustes',
    ];

    public $permissionLevels = [
        'none' => 'Sin acceso',
        'read' => 'Solo lectura',
        'create' => 'Crear',
        'edit' => 'Editar',
        'delete' => 'Eliminar',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($roleId)
    {
        $role = Role::find($roleId);
        $this->editingRole = $role;
        $this->name = $role->name;
        $this->description = $role->description ?? '';
        $this->is_active = $role->is_active;
        $this->permissions = $role->permissions ?? [];
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingRole = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
        $this->permissions = [];
    }

    public function togglePermission($section, $level)
    {
        $this->permissions[$section] = $level;
    }

    public function saveRole()
    {
        $this->validate([
            'name' => 'required|min:2|unique:roles,name'.($this->editingRole ? ','.$this->editingRole->id : ''),
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'permissions' => $this->permissions,
        ];

        if ($this->editingRole) {
            $this->editingRole->update($data);
            session()->flash('message', 'Rol actualizado correctamente');
        } else {
            Role::create($data);
            session()->flash('message', 'Rol creado correctamente');
        }

        $this->closeModal();
    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        if ($role->agents()->count() > 0) {
            session()->flash('error', 'No se puede eliminar un rol que tiene agentes asignados');

            return;
        }
        $role->delete();
        session()->flash('message', 'Rol eliminado correctamente');
    }

    public function toggleStatus($roleId)
    {
        $role = Role::find($roleId);
        $role->update(['is_active' => ! $role->is_active]);
    }

    public function getRoles()
    {
        $query = Role::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('name')->get();
    }

    public function getMetrics()
    {
        return [
            'total' => Role::count(),
            'active' => Role::where('is_active', true)->count(),
            'agents' => Agent::whereNotNull('role_id')->count(),
        ];
    }

    public function render()
    {
        $roles = $this->getRoles();
        $metrics = $this->getMetrics();

        return view('livewire.roles', compact('roles', 'metrics'))->layout('layouts.dashboard');
    }
}
