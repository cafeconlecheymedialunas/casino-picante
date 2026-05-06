<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Agentes extends Component
{
    use HasLinePermissions, SendsNotifications, WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $cargoFilter = 'all';

    public bool $showModal = false;

    public bool $showDetailModal = false;

    public ?int $editingAgentId = null;

    public ?int $detailAgentId = null;

    public string $username = '';

    public string $name = '';

    public string $apellido = '';

    public string $email = '';

    public string $password = '';

    public string $phone = '';

    public string $status = 'active';

    public string $cargo = 'agente';

    public array $lineIds = [];

    public function mount(): void
    {
        $this->lineIds = array_filter([(int) session('active_line_id')]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCargoFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->checkLinePermission('agent.create');
        $this->resetForm();
        $this->lineIds = array_filter([(int) (session('active_line_id') ?: Line::orderBy('name')->value('id'))]);
        $this->showModal = true;
    }

    public function openEditModal(int $agentId): void
    {
        $this->checkLinePermission('agent.update');

        $agent = Agent::with('lineAgents')->findOrFail($agentId);
        $this->authorizeAgentScope($agent);

        $this->editingAgentId = $agent->id;
        $this->username = $agent->username ?? '';
        $this->name = $agent->name;
        $this->apellido = $agent->apellido ?? '';
        $this->email = $agent->email;
        $this->phone = $agent->phone ?? '';
        $this->status = $agent->status === 'inactive' ? 'inactive' : 'active';
        $this->cargo = $agent->cargo ?: 'agente';
        $this->password = '';
        $assignedLineIds = $agent->lineAgents()
            ->pluck('line_id')
            ->map(fn ($lineId) => (int) $lineId)
            ->toArray();

        $this->lineIds = ! empty($assignedLineIds)
            ? $assignedLineIds
            : array_filter([(int) (session('active_line_id') ?: Line::orderBy('name')->value('id'))]);

        $this->showModal = true;
        $this->showDetailModal = false;
    }

    public function openDetailModal(int $agentId): void
    {
        $agent = Agent::findOrFail($agentId);
        $this->authorizeAgentScope($agent);

        $this->detailAgentId = $agent->id;
        $this->showDetailModal = true;
        $this->showModal = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showDetailModal = false;
        $this->resetForm();
    }

    public function saveAgent(): void
    {
        $this->editingAgentId
            ? $this->checkLinePermission('agent.update')
            : $this->checkLinePermission('agent.create');

        $this->validate($this->rules());
        $this->authorizeSelectedLines();

        $username = trim($this->username);
        $data = [
            'username' => $username !== '' ? $username : $this->makeUsername($this->name, $this->email),
            'name' => trim($this->name),
            'apellido' => trim($this->apellido) ?: null,
            'email' => trim($this->email),
            'phone' => trim($this->phone) ?: null,
            'status' => $this->status,
            'cargo' => $this->cargo,
        ];

        if ($this->password !== '') {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingAgentId) {
            $agent = Agent::findOrFail($this->editingAgentId);
            $this->authorizeAgentScope($agent);
            $agent->update($data);
            session()->flash('message', 'Agente actualizado correctamente.');

            $this->notify('Agente actualizado', "El agente {$agent->name} fue actualizado.", 'agents', '/agentes', 'info');
        } else {
            $data['password'] = Hash::make($this->password);
            $agent = Agent::create($data);
            session()->flash('message', 'Agente creado correctamente.');

            $this->notify('Nuevo agente creado', "El agente {$agent->name} fue creado exitosamente.", 'agents', '/agentes', 'success');
        }

        $this->syncLineAssignment($agent);
        $this->notifyAffectedAgent($agent);
        $this->closeModal();
    }

    public function toggleStatus(int $agentId): void
    {
        $this->checkLinePermission('agent.update');

        $agent = Agent::findOrFail($agentId);
        $this->authorizeAgentScope($agent);

        $newStatus = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->update(['status' => $newStatus]);

        LineAgent::where('agent_id', $agentId)->update(['is_active' => $newStatus === 'active']);
        session()->flash('message', $newStatus === 'active' ? 'Agente activado.' : 'Agente pausado.');

        $this->notify('Estado de agente cambiado', "El agente {$agent->name} fue ".($newStatus === 'active' ? 'activado' : 'pausado').'.', 'agents', '/agentes', 'warning');

        $this->notifyAffectedAgent(
            $agent,
            'Tu acceso fue '.($newStatus === 'active' ? 'activado' : 'pausado'),
            'Tu usuario de agente fue '.($newStatus === 'active' ? 'activado' : 'pausado').'.',
            'warning'
        );

        if ($this->detailAgentId === $agentId) {
            $this->detailAgentId = $agentId;
        }
    }

    public function deleteAgent(int $agentId): void
    {
        $this->checkLinePermission('agent.update');

        $agent = Agent::findOrFail($agentId);
        $this->authorizeAgentScope($agent);
        $agentName = $agent->name;
        $agent->delete();

        session()->flash('message', 'Agente eliminado correctamente.');

        $this->notify('Agente eliminado', "El agente {$agentName} fue eliminado del sistema.", 'agents', '/agentes', 'danger');
    }

    public function getCanCreateAgentsProperty(): bool
    {
        return $this->hasLinePermission('agent.create');
    }

    public function getAvailableLinesProperty()
    {
        if ($this->isAdminMode()) {
            return Line::orderBy('name')->get();
        }

        $agentId = session('active_agent_id');

        return Line::whereHas('lineAgents', fn ($query) => $query
            ->where('agent_id', $agentId)
            ->where('is_active', true)
        )->orderBy('name')->get();
    }

    public function render()
    {
        $query = Agent::query()
            ->with(['assignedLines'])
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', $this->search)
                        ->orWhere('username', 'like', $search)
                        ->orWhere('name', 'like', $search)
                        ->orWhere('apellido', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            })
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->cargoFilter !== 'all', fn ($query) => $query->where('cargo', $this->cargoFilter))
            ->orderBy('created_at', 'desc');

        $this->scopeAgentsToAvailableLines($query);
        $agents = $query->paginate(10);
        $metricsQuery = Agent::query();
        $this->scopeAgentsToAvailableLines($metricsQuery);
        $detailAgent = $this->detailAgentId
            ? Agent::with(['assignedLines'])->find($this->detailAgentId)
            : null;

        $metrics = [
            'total' => (clone $metricsQuery)->count(),
            'active' => (clone $metricsQuery)->where('status', 'active')->count(),
            'inactive' => (clone $metricsQuery)->where('status', 'inactive')->count(),
            'with_lines' => (clone $metricsQuery)->whereHas('assignedLines')->count(),
        ];

        return view('livewire.agentes', [
            'agents' => $agents,
            'metrics' => $metrics,
            'lines' => $this->availableLines,
            'canCreateAgents' => $this->canCreateAgents,
            'detailAgent' => $detailAgent,
        ])->layout('layouts.dashboard');
    }

    private function rules(): array
    {
        $id = $this->editingAgentId ?: 'NULL';

        return [
            'username' => "nullable|min:3|max:60|alpha_dash|unique:agents,username,{$id}",
            'name' => 'required|min:2|max:100',
            'apellido' => 'nullable|max:100',
            'email' => "required|email|unique:agents,email,{$id}",
            'password' => $this->editingAgentId ? 'nullable|min:6' : 'required|min:6',
            'phone' => 'nullable|max:30',
            'status' => 'required|in:active,inactive',
            'cargo' => 'required|in:super_agente,agente',
            'lineIds' => 'required|array|min:1',
            'lineIds.*' => 'integer|exists:lines,id',
        ];
    }

    private function resetForm(): void
    {
        $this->editingAgentId = null;
        $this->username = '';
        $this->name = '';
        $this->apellido = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->status = 'active';
        $this->cargo = 'agente';
        $this->lineIds = array_filter([(int) session('active_line_id')]);
        $this->resetValidation();
    }

    private function syncLineAssignment(Agent $agent): void
    {
        $lineIds = collect($this->lineIds)
            ->filter()
            ->map(fn ($lineId) => (int) $lineId)
            ->unique()
            ->values();

        if ($lineIds->isEmpty()) {
            return;
        }

        LineAgent::where('agent_id', $agent->id)
            ->whereNotIn('line_id', $lineIds)
            ->delete();

        foreach ($lineIds as $lineId) {
            LineAgent::updateOrCreate(
                ['line_id' => $lineId, 'agent_id' => $agent->id],
                [
                    'role' => $this->cargo === 'super_agente' ? 'encargado' : 'miembro',
                    'is_active' => $this->status === 'active',
                ]
            );
        }
    }

    private function notifyAffectedAgent(Agent $agent, ?string $title = null, ?string $message = null, string $type = 'info'): void
    {
        $currentAgentId = session('active_agent_id') ? (int) session('active_agent_id') : null;

        if ((int) $agent->id === $currentAgentId) {
            return;
        }

        $this->notifyAgent(
            (int) $agent->id,
            $title ?: 'Tu usuario fue actualizado',
            $message ?: 'Se actualizaron datos o lineas asignadas de tu usuario.',
            'agents',
            '/perfil',
            $type
        );
    }

    private function availableLineIds(): array
    {
        if ($this->isAdminMode()) {
            return Line::pluck('id')
                ->map(fn ($lineId) => (int) $lineId)
                ->toArray();
        }

        return LineAgent::where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
            ->pluck('line_id')
            ->map(fn ($lineId) => (int) $lineId)
            ->toArray();
    }

    private function scopeAgentsToAvailableLines($query): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $lineIds = $this->availableLineIds();

        if (empty($lineIds)) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereHas('lineAgents', fn ($inner) => $inner->whereIn('line_id', $lineIds));
    }

    private function authorizeAgentScope(Agent $agent): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $allowed = LineAgent::where('agent_id', $agent->id)
            ->whereIn('line_id', $this->availableLineIds())
            ->exists();

        if (! $allowed) {
            abort(403, 'No podes gestionar agentes fuera de tus lineas.');
        }
    }

    private function authorizeSelectedLines(): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $allowedLineIds = $this->availableLineIds();
        $selectedLineIds = collect($this->lineIds)
            ->map(fn ($lineId) => (int) $lineId)
            ->unique();

        if ($selectedLineIds->diff($allowedLineIds)->isNotEmpty()) {
            abort(403, 'No podes asignar agentes a lineas fuera de tu alcance.');
        }
    }

    private function makeUsername(string $name, string $email): string
    {
        $base = Str::slug($name, '_') ?: Str::before($email, '@') ?: 'agente';
        $base = Str::limit($base, 50, '');
        $username = $base;
        $suffix = 1;

        while (Agent::where('username', $username)
            ->when($this->editingAgentId, fn ($query) => $query->where('id', '!=', $this->editingAgentId))
            ->exists()) {
            $username = Str::limit($base, 46, '').'_'.$suffix++;
        }

        return $username;
    }
}
