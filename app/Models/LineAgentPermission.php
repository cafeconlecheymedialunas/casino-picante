<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineAgentPermission extends Model
{
    public $timestamps = false;

    protected $fillable = ['line_id', 'agent_id', 'permission'];

    // All permissions available in the system grouped by resource
    public static array $catalog = [
        'promo'   => ['read', 'create', 'update', 'delete'],
        'ticket'  => ['read', 'update', 'close'],
        'line'    => ['view', 'edit.basic', 'edit.contacts', 'edit.branding'],
        'agent'   => ['create', 'assign', 'update', 'permissions'],
        'bonus'   => ['read', 'create', 'update'],
        'sorteo'  => ['read', 'create', 'update', 'delete'],
        'novedad' => ['read', 'create', 'update', 'delete'],
        'user'    => ['read', 'update', 'block'],
    ];

    public static function allPermissions(): array
    {
        $all = [];
        foreach (self::$catalog as $resource => $actions) {
            foreach ($actions as $action) {
                $all[] = "{$resource}.{$action}";
            }
        }
        return $all;
    }
}
