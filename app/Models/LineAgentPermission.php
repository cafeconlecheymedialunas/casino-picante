<?php

namespace App\Models;

use App\Support\Permissions;
use Illuminate\Database\Eloquent\Model;

class LineAgentPermission extends Model
{
    public $timestamps = false;

    protected $fillable = ['line_id', 'agent_id', 'permission'];

    public static function allPermissions(): array
    {
        return Permissions::all();
    }
}
