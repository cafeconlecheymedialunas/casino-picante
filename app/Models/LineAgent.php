<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LineAgent extends Model
{
    use HasVendorScope;

    protected $fillable = ['vendor_id', 'line_id', 'agent_id', 'role', 'is_active', 'parent_id', 'porcentaje_ganancia'];

    protected $casts = ['is_active' => 'boolean'];

    protected $permissionsCache = [];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Agent::class, 'parent_id');
    }

    public function linePermissions()
    {
        return LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id);
    }

    public function getPermissionsListAttribute(): array
    {
        $cacheKey = "line_agent_permissions:{$this->line_id}:{$this->agent_id}";

        return Cache::remember($cacheKey, 300, function () {
            return LineAgentPermission::where('line_id', $this->line_id)
                ->where('agent_id', $this->agent_id)
                ->pluck('permission')
                ->toArray();
        });
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! isset($this->permissionsCache[$this->line_id])) {
            $this->permissionsCache[$this->line_id] = $this->getPermissionsListAttribute();
        }

        return in_array($permission, $this->permissionsCache[$this->line_id], true);
    }

    public function grantPermission(string $permission): void
    {
        LineAgentPermission::firstOrCreate(
            [
                'line_id' => $this->line_id,
                'agent_id' => $this->agent_id,
                'permission' => $permission,
            ],
            ['vendor_id' => $this->vendor_id]
        );

        $this->clearPermissionsCache();
    }

    public function revokePermission(string $permission): void
    {
        LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id)
            ->where('permission', $permission)
            ->delete();

        $this->clearPermissionsCache();
    }

    public function syncPermissions(array $permissions): void
    {
        LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id)
            ->delete();

        foreach ($permissions as $perm) {
            LineAgentPermission::create([
                'vendor_id' => $this->vendor_id,
                'line_id' => $this->line_id,
                'agent_id' => $this->agent_id,
                'permission' => $perm,
            ]);
        }

        $this->clearPermissionsCache();
    }

    public function syncRegularAgentPermissions(): void
    {
        $allPermissions = LineAgentPermission::allPermissions();
        $excludedPermissions = [
            Permissions::LINE_EDIT,
            Permissions::AGENT_CREATE,
            Permissions::AGENT_ASSIGN,
            Permissions::AGENT_UPDATE,
            Permissions::AGENT_PERMISSIONS,
        ];
        $permissionsToAssign = array_diff($allPermissions, $excludedPermissions);

        $this->syncPermissions($permissionsToAssign);
    }

    public function syncEncargadoPermissions(): void
    {
        $this->syncPermissions(LineAgentPermission::allPermissions());
    }

    protected function clearPermissionsCache(): void
    {
        $cacheKey = "line_agent_permissions:{$this->line_id}:{$this->agent_id}";
        Cache::forget($cacheKey);
        unset($this->permissionsCache[$this->line_id]);
    }
}
