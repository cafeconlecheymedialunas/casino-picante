<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'name',
        'apellido',
        'email',
        'password',
        'phone',
        'avatar',
        'parent_id',
        'cargo',
        'status',
    ];

    protected $casts = [];

    public function parent()
    {
        return $this->belongsTo(Agent::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Agent::class, 'parent_id');
    }

    public function permissions()
    {
        return $this->hasMany(AgentPermission::class, 'agent_id');
    }

    public function lineAgents()
    {
        return $this->hasMany(LineAgent::class);
    }

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_agents')
            ->withPivot(['role', 'is_active', 'parent_id'])
            ->withTimestamps();
    }

    public function assignedLines()
    {
        return $this->belongsToMany(Line::class, 'line_agents')
            ->withPivot(['role', 'is_active', 'parent_id'])
            ->withTimestamps();
    }

    public function activeLines()
    {
        return $this->lines()->wherePivot('is_active', true);
    }

    public function linePermissionsFor(int $lineId): array
    {
        return LineAgentPermission::where('line_id', $lineId)
            ->where('agent_id', $this->id)
            ->pluck('permission')
            ->toArray();
    }

    public function hasLinePermission(int $lineId, string $permission): bool
    {
        return LineAgentPermission::where('line_id', $lineId)
            ->where('agent_id', $this->id)
            ->where('permission', $permission)
            ->exists();
    }
}
