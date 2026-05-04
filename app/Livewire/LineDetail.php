<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Traits\HasLinePermissions;
use Illuminate\Database\Eloquent\Collection;
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

    public string $editDescription = '';

    public string $editIcon = '';

    public string $editStatus = 'active';

    // Contact links repeater
    public array $editContactLinks = []; // [{type: 'whatsapp', label: '', value: ''}, ...]

    // Platforms from catalog (line_platform pivot)
    public array $editPlatforms = []; // [['platform_id' => 1, 'custom_message' => ''], ...]

    // Available platforms from master catalog
    public function getAvailablePlatformsProperty()
    {
        return Platform::where('is_active', true)->orderBy('name')->get();
    }

    // Assign agent modal
    public bool $showAssignModal = false;

    public string $assignAgentSearch = '';

    public ?int $assignAgentId = null;

    public string $assignRole = 'miembro';

    // Encargado selection
    public $selectedEncargadoId = null;

    // Permissions panel
    public ?int $editingPermAgentId = null;

    public array $editingPerms = []; // ['promo.create' => true/false, ...]

    // Line permissions
    public array $linePermissionsList = [];

    public function mount(int $id): void
    {
        $this->lineId = $id;
        $this->line = Line::findOrFail($id);
        $this->linePermissionsList = $this->line->permissions ?? [];
    }

    // ── Edit Line ──────────────────────────────────────────────────────────

    public function openEditModal(): void
    {
        $this->checkLinePermission('line.edit.basic');
        $this->editName = $this->line->name;
        $this->editType = $this->line->type ?? 'whatsapp';
        $this->editPhone = $this->line->phone ?? '';
        $this->editDescription = $this->line->description ?? '';
        $this->editIcon = $this->line->icon ?? '';
        $this->editStatus = $this->line->status;

        // Load contact links (normalize to include 'message' and 'has_message' keys)
        $rawLinks = $this->line->contact_links ?? [
            ['type' => 'whatsapp', 'label' => 'WhatsApp', 'value' => $this->line->whatsapp ?? '', 'message' => $this->line->whatsapp_message ?? ''],
            ['type' => 'telegram', 'label' => 'Telegram', 'value' => $this->line->telegram ?? '', 'message' => $this->line->telegram_message ?? ''],
        ];
        $this->editContactLinks = array_map(function ($link) {
            return array_merge([
                'message' => '',
                'has_message' => false,
            ], $link);
        }, $rawLinks);

        // Load platforms from pivot table
        $this->editPlatforms = $this->line->platforms()->get()->map(function ($platform) {
            return [
                'platform_id' => $platform->id,
                'name' => $platform->name,
                'slug' => $platform->slug,
                'logo_url' => $platform->logo_url,
                'custom_message' => $platform->pivot->custom_message ?? '',
                'is_active' => $platform->pivot->is_active,
            ];
        })->toArray();

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function saveLineEdit(): void
    {
        $this->checkLinePermission('line.edit.basic');

        $this->line->update([
            'name' => $this->editName,
            'type' => $this->editType,
            'phone' => $this->editPhone,
            'description' => $this->editDescription,
            'icon' => $this->editIcon,
            'contact_links' => $this->editContactLinks,
            'status' => $this->editStatus,
        ]);

        // Update platforms in pivot table
        $this->line->platforms()->detach();
        foreach ($this->editPlatforms as $p) {
            if (! empty($p['platform_id'])) {
                $this->line->platforms()->attach($p['platform_id'], [
                    'custom_message' => $p['custom_message'] ?? '',
                    'is_active' => $p['is_active'] ?? true,
                ]);
            }
        }

        $this->line->refresh();
        session()->flash('message', 'Línea actualizada.');
    }

    // ── Contact Links Repeater ────────────────────────────────

    public function addContactLink(): void
    {
        $this->editContactLinks[] = ['type' => 'whatsapp', 'label' => '', 'value' => '', 'message' => ''];
    }

    public function removeContactLink(int $index): void
    {
        unset($this->editContactLinks[$index]);
        $this->editContactLinks = array_values($this->editContactLinks);
    }

    // ── Platforms ────────────────────────────────────────

    public function togglePlatform(int $platformId): void
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
                    'slug' => $platform->slug,
                    'logo_url' => $platform->logo_url,
                    'custom_message' => '',
                    'is_active' => true,
                ];
            }
        }
    }

    public function updatePlatformMessage(int $platformId, string $message): void
    {
        foreach ($this->editPlatforms as &$p) {
            if ($p['platform_id'] == $platformId) {
                $p['custom_message'] = $message;
            }
        }
    }

    public function togglePlatformActivation($platformId): void
    {
        $this->checkLinePermission('line.edit.basic');

        $platform = Platform::find($platformId);
        if (! $platform) {
            return;
        }

        $alreadyAttached = $this->line->platforms()->wherePivot('platform_id', $platformId)->exists();

        if ($alreadyAttached) {
            // toggle is_active
            $pivot = $this->line->platforms()->wherePivot('platform_id', $platformId)->first();
            $newStatus = ! $pivot->pivot->is_active;
            $this->line->platforms()->updateExistingPivot($platformId, ['is_active' => $newStatus]);
        } else {
            // attach as active
            $this->line->platforms()->attach($platformId, [
                'is_active' => true,
                'custom_message' => '',
            ]);
        }

        $this->line->refresh();
    }

    // ── Assign Agent ───────────────────────────────────────────────────────

    public function openAssignModal(): void
    {
        $this->checkLinePermission('agent.assign');
        $this->assignAgentSearch = '';
        $this->assignAgentId = null;
        $this->assignRole = 'miembro';
        $this->showAssignModal = true;
    }

    public function getSearchAgentsProperty(): Collection
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
        $this->assignAgentId = $agentId;
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

        // Validate role is either 'encargado' or 'miembro'
        if (! in_array($role, ['encargado', 'miembro'])) {
            session()->flash('error', 'Rol inválido. Debe ser encargado o miembro.');

            return;
        }

        LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->update(['role' => $role]);
    }

    // ── Permissions panel ──────────────────────────────────────────────────

    public function openPermissions(int $agentId): void
    {
        $this->checkLinePermission('agent.permissions');

        $this->editingPermAgentId = $agentId;

        // Get line permissions (max allowed)
        $linePerms = $this->line->permissions ?? [];

        // Get encargado permissions (if exists)
        $encargadoPerms = [];
        if ($this->line->encargado_id) {
            $encargadoPerms = LineAgentPermission::where('line_id', $this->lineId)
                ->where('agent_id', $this->line->encargado_id)
                ->pluck('permission')
                ->toArray();
        }

        // Get current agent permissions
        $granted = LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->pluck('permission')
            ->flip()
            ->toArray();

        // Build checkbox map - only show permissions allowed by line AND (if there's encargado) by encargado
        $this->editingPerms = [];
        foreach (LineAgentPermission::allPermissions() as $perm) {
            // Only show permissions the delegator actually has (delegation rule)
            if ($this->canDelegate($perm)) {
                // Only show permissions that are allowed by line
                $allowedByLine = in_array($perm, $linePerms);

                // If there's an encargado, also check their permissions
                $allowedByEncargado = empty($encargadoPerms) || in_array($perm, $encargadoPerms);

                if ($allowedByLine && $allowedByEncargado) {
                    $this->editingPerms[$perm] = isset($granted[$perm]);
                }
            }
        }
    }

    public function savePermissions(): void
    {
        $this->checkLinePermission('agent.permissions');

        if (! $this->editingPermAgentId) {
            return;
        }

        // Get line permissions (max allowed)
        $linePerms = $this->line->permissions ?? [];

        // Get encargado permissions (if exists)
        $encargadoPerms = [];
        if ($this->line->encargado_id) {
            $encargadoPerms = LineAgentPermission::where('line_id', $this->lineId)
                ->where('agent_id', $this->line->encargado_id)
                ->pluck('permission')
                ->toArray();
        }

        // Collect only the checked ones that are allowed (subset of line AND encargado permissions AND delegation rule)
        $toGrant = [];
        $invalidPerms = [];
        foreach ($this->editingPerms as $perm => $checked) {
            if ($checked) {
                // Check if permission is allowed by line AND (if there's an encargado) by encargado
                $allowedByLine = in_array($perm, $linePerms);
                $allowedByEncargado = empty($encargadoPerms) || in_array($perm, $encargadoPerms);
                // Delegation rule: you can only grant what you yourself have
                $allowedByDelegation = $this->canDelegate($perm);

                if ($allowedByLine && $allowedByEncargado && $allowedByDelegation) {
                    $toGrant[] = $perm;
                } else {
                    $invalidPerms[] = $perm;
                }
            }
        }

        if (! empty($invalidPerms)) {
            session()->flash('error', 'Algunos permisos exceden los límites permitidos. Los permisos del agente no pueden ser mayores a los de la línea o del encargado.');

            return;
        }

        // Remove old permissions for this agent/line (only those the delegator can touch)
        $delegatable = array_filter(
            LineAgentPermission::allPermissions(),
            fn ($p) => $this->canDelegate($p)
        );

        LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $this->editingPermAgentId)
            ->whereIn('permission', $delegatable)
            ->delete();

        foreach ($toGrant as $perm) {
            LineAgentPermission::firstOrCreate([
                'line_id' => $this->lineId,
                'agent_id' => $this->editingPermAgentId,
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

    // ── Encargado selection ────────────────────────────────────────

    public function getAvailableAgentsProperty(): Collection
    {
        return Agent::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function getCurrentEncargadoProperty()
    {
        return $this->line->encargado;
    }

    public function assignEncargado($agentId): void
    {
        $this->checkLinePermission('line.edit.basic');

        $this->line->update(['encargado_id' => $agentId ?: null]);
        $this->line->refresh();
        $this->selectedEncargadoId = $agentId;
        session()->flash('message', $agentId ? 'Encargado asignado.' : 'Encargado removido.');
    }

    // ── Line Permissions ──────────────────────────────────────────────────

    public function toggleLinePermission(string $permission): void
    {
        if (! in_array('line.edit.basic', $this->linePermissionsList) && ! $this->hasLinePermission('line.edit.basic')) {
            return;
        }

        if (in_array($permission, $this->linePermissionsList)) {
            $this->linePermissionsList = array_filter($this->linePermissionsList, fn ($p) => $p !== $permission);
        } else {
            $this->linePermissionsList[] = $permission;
        }

        $this->line->update(['permissions' => array_values($this->linePermissionsList)]);
        $this->line->refresh();
        session()->flash('message', 'Permisos de línea actualizados.');
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $availableAgents = $this->availableAgents;
        $currentEncargado = $this->currentEncargado;
        $availablePlatforms = $this->availablePlatforms;

        return view('livewire.line-detail', compact('availableAgents', 'currentEncargado', 'availablePlatforms'))
            ->layout('layouts.dashboard');
    }
}
