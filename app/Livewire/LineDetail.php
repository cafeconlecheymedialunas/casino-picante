<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Traits\HasLinePermissions;
use Livewire\Component;

class LineDetail extends Component
{
    use HasLinePermissions;

    public int $lineId;

    public Line $line;

    // Edit line modal
    public bool $showEditModal = false;
    public string $editName = '';
    public string $editType = 'whatsapp';
    public string $editPhone = '';
    public string $editWhatsapp = '';
    public string $editTelegram = '';
    public string $editStatus = 'active';

    // Assign agent modal
    public bool $showAssignModal = false;
    public string $assignAgentSearch = '';
    public ?int $assignAgentId = null;
    public string $assignRole = 'agent';

    // Permissions panel
    public ?int $editingPermAgentId = null;
    public array $editingPerms = []; // ['promo.create' => true/false, ...]

    public function mount(int $id): void
    {
        $this->lineId = $id;
        $this->line = Line::findOrFail($id);
    }

    // ── Edit Line ──────────────────────────────────────────────────────────

    public function openEditModal(): void
    {
        $this->checkLinePermission('line.edit.basic');
        $this->editName     = $this->line->name;
        $this->editType     = $this->line->type ?? 'whatsapp';
        $this->editPhone    = $this->line->phone ?? '';
        $this->editWhatsapp = $this->line->whatsapp ?? '';
        $this->editTelegram = $this->line->telegram ?? '';
        $this->editStatus   = $this->line->status;
        $this->showEditModal = true;
    }

    public function saveLineEdit(): void
    {
        $this->checkLinePermission('line.edit.basic');

        $this->line->update([
            'name'     => $this->editName,
            'type'     => $this->editType,
            'phone'    => $this->editPhone,
            'whatsapp' => $this->editWhatsapp,
            'telegram' => $this->editTelegram,
            'status'   => $this->editStatus,
        ]);

        $this->line->refresh();
        $this->showEditModal = false;
        session()->flash('message', 'Línea actualizada.');
    }

    // ── Assign Agent ───────────────────────────────────────────────────────

    public function openAssignModal(): void
    {
        $this->checkLinePermission('agent.assign');
        $this->assignAgentSearch = '';
        $this->assignAgentId     = null;
        $this->assignRole        = 'agent';
        $this->showAssignModal   = true;
    }

    public function getSearchAgentsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        if (strlen($this->assignAgentSearch) < 2) {
            return collect();
        }

        $alreadyIn = LineAgent::where('line_id', $this->lineId)->pluck('agent_id');

        return Agent::where('status', 'active')
            ->whereNotIn('id', $alreadyIn)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->assignAgentSearch}%")
                  ->orWhere('email', 'like', "%{$this->assignAgentSearch}%");
            })
            ->limit(8)
            ->get();
    }

    public function selectAssignAgent(int $agentId): void
    {
        $this->assignAgentId     = $agentId;
        $this->assignAgentSearch = Agent::find($agentId)?->name ?? '';
    }

    public function confirmAssign(): void
    {
        $this->checkLinePermission('agent.assign');

        if (! $this->assignAgentId) {
            return;
        }

        LineAgent::firstOrCreate(
            ['line_id' => $this->lineId, 'agent_id' => $this->assignAgentId],
            ['role' => $this->assignRole, 'is_active' => true]
        );

        $this->showAssignModal = false;
        session()->flash('message', 'Agente asignado correctamente.');
    }

    public function removeAgent(int $agentId): void
    {
        $this->checkLinePermission('agent.assign');

        LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->delete();

        LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->delete();

        if ($this->editingPermAgentId === $agentId) {
            $this->editingPermAgentId = null;
        }

        session()->flash('message', 'Agente removido de la línea.');
    }

    public function toggleAgentActive(int $agentId): void
    {
        $this->checkLinePermission('agent.update');

        $la = LineAgent::where('line_id', $this->lineId)->where('agent_id', $agentId)->first();
        if ($la) {
            $la->update(['is_active' => ! $la->is_active]);
        }
    }

    public function changeAgentRole(int $agentId, string $role): void
    {
        $this->checkLinePermission('agent.update');

        LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->update(['role' => $role]);
    }

    // ── Permissions panel ──────────────────────────────────────────────────

    public function openPermissions(int $agentId): void
    {
        $this->checkLinePermission('agent.permissions');

        $this->editingPermAgentId = $agentId;

        // Build the checkbox map using all catalog permissions as keys
        $granted = LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->pluck('permission')
            ->flip()
            ->toArray();

        $this->editingPerms = [];
        foreach (LineAgentPermission::allPermissions() as $perm) {
            // Only show permissions the delegator actually has (delegation rule)
            if ($this->canDelegate($perm)) {
                $this->editingPerms[$perm] = isset($granted[$perm]);
            }
        }
    }

    public function savePermissions(): void
    {
        $this->checkLinePermission('agent.permissions');

        if (! $this->editingPermAgentId) {
            return;
        }

        // Collect only the checked ones that the current agent can delegate
        $toGrant = [];
        foreach ($this->editingPerms as $perm => $checked) {
            if ($checked && $this->canDelegate($perm)) {
                $toGrant[] = $perm;
            }
        }

        // Remove old permissions for this agent/line (only those the delegator can touch)
        $delegatable = array_filter(
            LineAgentPermission::allPermissions(),
            fn($p) => $this->canDelegate($p)
        );

        LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $this->editingPermAgentId)
            ->whereIn('permission', $delegatable)
            ->delete();

        foreach ($toGrant as $perm) {
            LineAgentPermission::firstOrCreate([
                'line_id'    => $this->lineId,
                'agent_id'   => $this->editingPermAgentId,
                'permission' => $perm,
            ]);
        }

        $this->editingPermAgentId = null;
        session()->flash('message', 'Permisos guardados.');
    }

    public function closePermissions(): void
    {
        $this->editingPermAgentId = null;
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $lineAgents = LineAgent::with('agent')
            ->where('line_id', $this->lineId)
            ->orderByDesc('role') // managers first
            ->get()
            ->map(function (LineAgent $la) {
                $la->permissionsList = LineAgentPermission::where('line_id', $this->lineId)
                    ->where('agent_id', $la->agent_id)
                    ->pluck('permission')
                    ->toArray();
                return $la;
            });

        $editingAgent = $this->editingPermAgentId
            ? Agent::find($this->editingPermAgentId)
            : null;

        $permCatalog = LineAgentPermission::$catalog;

        return view('livewire.line-detail', compact('lineAgents', 'editingAgent', 'permCatalog'))
            ->layout('layouts.dashboard');
    }
}
