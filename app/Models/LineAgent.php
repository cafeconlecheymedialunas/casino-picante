<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineAgent extends Model
{
    protected $fillable = ['line_id', 'agent_id', 'role', 'is_active', 'parent_id', 'porcentaje_ganancia'];

    protected $casts = ['is_active' => 'boolean'];

    public function line()
    {
        return $this->belongsTo(Line::class);
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
        return LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id)
            ->pluck('permission')
            ->toArray();
    }

    public function hasPermission(string $permission): bool
    {
        return LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id)
            ->where('permission', $permission)
            ->exists();
    }

    public function grantPermission(string $permission): void
    {
        LineAgentPermission::firstOrCreate([
            'line_id' => $this->line_id,
            'agent_id' => $this->agent_id,
            'permission' => $permission,
        ]);
    }

    public function revokePermission(string $permission): void
    {
        LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id)
            ->where('permission', $permission)
            ->delete();
    }

    public function syncPermissions(array $permissions): void
    {
        LineAgentPermission::where('line_id', $this->line_id)
            ->where('agent_id', $this->agent_id)
            ->delete();

        foreach ($permissions as $perm) {
            LineAgentPermission::create([
                'line_id' => $this->line_id,
                'agent_id' => $this->agent_id,
                'permission' => $perm,
            ]);
        }
    }
}
