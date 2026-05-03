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
        'whatsapp',
        'whatsapp_message',
        'telegram',
        'telegram_message',
        'whatsapp_channel',
        'facebook',
        'instagram',
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
}
