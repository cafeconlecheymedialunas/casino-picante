<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPermission extends Model
{
    protected $table = 'agent_permissions';

    protected $fillable = [
        'agent_id',
        'section',
        'level',
    ];

    public $timestamps = false;
}
