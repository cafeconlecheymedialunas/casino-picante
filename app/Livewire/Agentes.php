<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Role;
use App\Models\User;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
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

    public ?int $permEditAgentId = null;

    public ?int $permEditLineId = null;

    public array $permEditSelected = [];

    public array $permEditAvailable = [];

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
        $this->checkLinePermission(Permissions::AGENT_CREATE);
        $this->resetForm();
        $this->lineIds = array_filter([(int) (session('active_line_id') ?: Line::orderBy('name')->value('id'))]);
        $this->showModal = true;
    }

    public function openEditModal(int $agentId): void
    {
        $this->checkLinePermission(Permissions::AGENT_UPDATE);

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
            ? $this->checkLinePermission(Permissions::AGENT_UPDATE)
            : $this->checkLinePermission(Permissions::AGENT_CREATE);

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
            $this->syncAgentUser($agent);
            session()->flash('message', 'Agente actualizado correctamente.');

            $editorName = $this->currentAgentDisplayName();
            $this->notify('Agente actualizado', "{$editorName} actualizó los datos del agente {$agent->name}.", 'agents', '/agentes', 'info');
            $this->notifyAffectedAgent($agent, 'Tu perfil fue actualizado', "{$editorName} modificó tus datos de agente.", 'info');
        } else {
            $data['password'] = Hash::make($this->password);
            $agent = Agent::create($data);
            $this->syncAgentUser($agent);
            session()->flash('message', 'Agente creado correctamente.');

            $creatorName = $this->currentAgentDisplayName();
            $this->notify('Nuevo agente creado', "{$creatorName} creó el agente {$agent->name} exitosamente.", 'agents', '/agentes', 'success');
            $this->notifyAffectedAgent($agent, 'Bienvenido al panel', "Tu cuenta de agente fue creada por {$creatorName}.", 'success');
        }

        $this->syncLineAssignment($agent);
        $this->closeModal();
    }

    public function toggleStatus(int $agentId): void
    {
        $this->checkLinePermission(Permissions::AGENT_UPDATE);

        $agent = Agent::findOrFail($agentId);
        $this->authorizeAgentScope($agent);

        $newStatus = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->update(['status' => $newStatus]);
        $agent->user?->update(['status' => $newStatus]);

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
        $this->checkLinePermission(Permissions::AGENT_UPDATE);

        $agent = Agent::findOrFail($agentId);
        $this->authorizeAgentScope($agent);
        $agentName = $agent->name;
        $agent->user?->update(['status' => 'inactive']);
        $agent->delete();

        session()->flash('message', 'Agente eliminado correctamente.');

        $this->notify('Agente eliminado', "El agente {$agentName} fue eliminado del sistema.", 'agents', '/agentes', 'danger');
    }

    public function openPermissions(int $agentId, int $lineId): void
    {
        $this->checkLinePermission(Permissions::AGENT_PERMISSIONS);

        $line = Line::findOrFail($lineId);
        $this->authorizeLineScope($lineId);
        $linePermissions = $line->permissions ?? [];

        if ($this->isAdminMode()) {
            $available = $linePermissions;
        } else {
            $currentAgentId = (int) session('active_agent_id');
            $myPerms = LineAgentPermission::where('line_id', $lineId)
                ->where('agent_id', $currentAgentId)
                ->pluck('permission')
                ->toArray();
            $available = array_values(array_intersect($linePermissions, $myPerms));
        }

        $this->permEditAgentId = $agentId;
        $this->permEditLineId  = $lineId;
        $this->permEditAvailable = $available;

        // Load current permissions; default to all available if none stored yet
        $current = LineAgentPermission::where('line_id', $lineId)
            ->where('agent_id', $agentId)
            ->pluck('permission')
            ->toArray();

        $this->permEditSelected = empty($current) ? $available : $current;
    }

    public function savePermissions(): void
    {
        $this->checkLinePermission(Permissions::AGENT_PERMISSIONS);

        if (! $this->permEditAgentId || ! $this->permEditLineId) {
            return;
        }

        $this->authorizeLineScope($this->permEditLineId);

        $toSave = array_values(array_intersect($this->permEditSelected, $this->permEditAvailable));

        $lineAgent = LineAgent::where('line_id', $this->permEditLineId)
            ->where('agent_id', $this->permEditAgentId)
            ->firstOrFail();

        $lineAgent->syncPermissions($toSave);

        session()->flash('message', 'Permisos guardados.');
        $this->closePermissions();
    }

    public function closePermissions(): void
    {
        $this->permEditAgentId   = null;
        $this->permEditLineId    = null;
        $this->permEditSelected  = [];
        $this->permEditAvailable = [];
    }

    public function getCanCreateAgentsProperty(): bool
    {
        return $this->hasLinePermission(Permissions::AGENT_CREATE);
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
            'agents'           => $agents,
            'metrics'          => $metrics,
            'lines'            => $this->availableLines,
            'canCreateAgents'  => $this->canCreateAgents,
            'detailAgent'      => $detailAgent,
            'permissionCatalog' => Permissions::catalog(),
        ])->layout('layouts.dashboard');
    }

    private function rules(): array
    {
        $id = $this->editingAgentId ?: 'NULL';
        $userId = $this->editingAgentId
            ? (Agent::find($this->editingAgentId)?->user_id ?: 'NULL')
            : 'NULL';

        return [
            'username' => "nullable|min:3|max:60|alpha_dash|unique:agents,username,{$id}|unique:users,username,{$userId}",
            'name' => 'required|min:2|max:100',
            'apellido' => 'nullable|max:100',
            'email' => "required|email|unique:agents,email,{$id}|unique:users,email,{$userId}",
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
                    'role' => $this->cargo === 'super_agente' ? LineRoles::ENCARGADO : LineRoles::MIEMBRO,
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

    private function syncAgentUser(Agent $agent): void
    {
        $roleId = Role::where('name', Roles::AGENTE)->value('id');
        $userData = [
            'role_id' => $roleId,
            'username' => $agent->username,
            'name' => $agent->name,
            'apellido' => $agent->apellido,
            'email' => $agent->email,
            'phone' => $agent->phone,
            'status' => $agent->status,
            'avatar' => $agent->avatar,
        ];

        if ($this->password !== '') {
            $userData['password'] = Hash::make($this->password);
        }

        $user = $agent->user ?: User::where('email', $agent->email)->first();
        if ($user) {
            $user->update($userData);
        } else {
            $userData['password'] = $userData['password'] ?? $agent->password;
            $user = User::create($userData);
        }

        if (! $agent->user_id) {
            $agent->update(['user_id' => $user->id]);
        }
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

    private function authorizeLineScope(int $lineId): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        if (! in_array((int) $lineId, $this->availableLineIds(), true)) {
            abort(403, 'No podes gestionar permisos fuera de tus lineas.');
        }
    }

    private function currentAgentDisplayName(): string
    {
        $agentId = session('active_agent_id');
        if ($agentId) {
            $agent = Agent::find($agentId);
            return $agent ? trim($agent->name.' '.($agent->apellido ?? '')) : 'Un encargado';
        }

        $user = auth()->user();
        return $user ? trim($user->name.' '.($user->apellido ?? '')) : 'El administrador';
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
