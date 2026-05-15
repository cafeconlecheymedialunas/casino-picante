<?php

namespace App\Traits;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Support\Roles;

trait HasLinePermissions
{
    // Validates that the session active_agent_id belongs to the authenticated user
    private function validateSessionAgent(): ?int
    {
        $agentId = session('active_agent_id');
        if (! $agentId) {
            return null;
        }

        $user = auth()->user();
        if (! $user) {
            session()->forget(['active_agent_id', 'active_line_id']);

            return null;
        }

        $valid = Agent::where('id', $agentId)
            ->where('user_id', $user->id)
            ->exists();

        if (! $valid) {
            session()->forget(['active_agent_id', 'active_line_id']);

            return null;
        }

        return $agentId;
    }

    // Returns the currently active Line from session, or null (admin mode)
    public function getActiveLine(): ?Line
    {
        $lineId = session('active_line_id');
        if (! $lineId) {
            return null;
        }

        $line = Line::find($lineId);

        // For agents, verify they have access to this line
        if (! $this->isAdminMode()) {
            $agentId = $this->validateSessionAgent();
            if ($agentId && $line) {
                $hasAccess = LineAgent::where('line_id', $lineId)
                    ->where('agent_id', $agentId)
                    ->where('is_active', true)
                    ->exists();
                if (! $hasAccess) {
                    return null;
                }
            }
        }

        return $line;
    }

    // Returns the LineAgent pivot for the current agent+line, or null (admin mode)
    public function getCurrentLineAgent(): ?LineAgent
    {
        $agentId = $this->validateSessionAgent();
        $lineId = session('active_line_id');

        if (! $agentId || ! $lineId) {
            return null;
        }

        return LineAgent::where('line_id', $lineId)
            ->where('agent_id', $agentId)
            ->where('is_active', true)
            ->first();
    }

    // Returns the current authenticated Agent model, or null
    public function getCurrentAgent(): ?Agent
    {
        $agentId = $this->validateSessionAgent();

        if ($agentId) {
            return Agent::find($agentId);
        }

        return auth()->user()?->agent;
    }

    // Returns true when the authenticated user has the global admin role.
    public function isAdminMode(): bool
    {
        return auth()->user()?->hasRole(Roles::ADMIN) ?? false;
    }

    // True if current agent has the given permission on the active line.
    // Always true in admin mode.
    public function hasLinePermission(string $permission): bool
    {
        if ($this->isAdminMode()) {
            return true;
        }

        $lineAgent = $this->getCurrentLineAgent();
        if (! $lineAgent) {
            return false;
        }

        return $lineAgent->hasPermission($permission);
    }

    // Abort with 403 if the current agent lacks the permission
    public function checkLinePermission(string $permission): void
    {
        if (! $this->hasLinePermission($permission)) {
            abort(403, "Sin permiso: {$permission}");
        }
    }

    // Returns the list of permissions the current agent has on the active line.
    // Returns all permissions in admin mode.
    public function currentLinePermissions(): array
    {
        if ($this->isAdminMode()) {
            return LineAgentPermission::allPermissions();
        }

        $lineAgent = $this->getCurrentLineAgent();

        return $lineAgent ? $lineAgent->getPermissionsListAttribute() : [];
    }

    // True if the current agent can delegate a given permission to another agent.
    // Delegation rule: you can only grant what you yourself have.
    public function canDelegate(string $permission): bool
    {
        return $this->hasLinePermission($permission);
    }

    // Returns the list of line IDs the current user/agent can see.
    // Admin with active_line_id set sees only that line; without it sees all (null).
    // Agents see only their assigned active lines.
    public function visibleLineIds(): ?array
    {
        $activeLineId = session('active_line_id');

        if ($this->isAdminMode()) {
            return $activeLineId ? [(int) $activeLineId] : null;
        }

        if ($activeLineId) {
            return [(int) $activeLineId];
        }

        $agentId = session('active_agent_id');
        if (! $agentId) {
            return [];
        }

        return LineAgent::where('agent_id', $agentId)
            ->where('is_active', true)
            ->pluck('line_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    // Returns line IDs where the current agent has any of the given permissions.
    // Admin keeps the same active-line/all-lines behavior as visibleLineIds().
    public function visibleLineIdsWithPermission(array|string $permissions, bool $respectActiveLine = true): ?array
    {
        if ($this->isAdminMode()) {
            return $this->visibleLineIds();
        }

        $agentId = $this->validateSessionAgent() ?: auth()->user()?->agent?->id;
        if (! $agentId) {
            return [];
        }

        $permissions = array_filter(array_map('trim', (array) $permissions));

        $lineIds = LineAgent::where('agent_id', $agentId)
            ->where('is_active', true)
            ->when($permissions !== [], fn ($query) => $query->whereExists(function ($permissionQuery) use ($permissions) {
                $permissionQuery->selectRaw('1')
                    ->from('line_agent_permissions')
                    ->whereColumn('line_agent_permissions.line_id', 'line_agents.line_id')
                    ->whereColumn('line_agent_permissions.agent_id', 'line_agents.agent_id')
                    ->whereIn('line_agent_permissions.permission', $permissions);
            }))
            ->pluck('line_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $activeLineId = session('active_line_id');
        if ($respectActiveLine && $activeLineId && in_array((int) $activeLineId, $lineIds, true)) {
            return [(int) $activeLineId];
        }

        return $lineIds;
    }
}
