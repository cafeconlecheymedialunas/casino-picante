<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'parent_id',
        'role',
        'status',
        'lines',
    ];

    protected $casts = [
        'lines' => 'array',
    ];

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
}
