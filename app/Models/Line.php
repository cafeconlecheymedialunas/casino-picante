<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'name',
        'type',
        'phone',
        'icon',
        'description',
        'status',
        'encargado_id',
        'contact_links',
        'permissions',
    ];

    protected $casts = [
        'contact_links' => 'array',
        'permissions' => 'array',
    ];

    public function lineAgents()
    {
        return $this->hasMany(LineAgent::class);
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'line_agents')
            ->withPivot(['role', 'is_active', 'parent_id'])
            ->withTimestamps();
    }

    public function activeAgents()
    {
        return $this->agents()->wherePivot('is_active', true);
    }

    public function managers()
    {
        return $this->agents()->wherePivot('role', 'manager')->wherePivot('is_active', true);
    }

    public function encargado()
    {
        return $this->belongsTo(Agent::class, 'encargado_id');
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'line_platform')
            ->withPivot('custom_message', 'is_active')
            ->withTimestamps();
    }

    public function activePlatforms()
    {
        return $this->platforms()->wherePivot('is_active', true);
    }
}
