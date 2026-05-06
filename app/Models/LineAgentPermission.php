<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineAgentPermission extends Model
{
    public $timestamps = false;

    protected $fillable = ['line_id', 'agent_id', 'permission'];

    // All permissions available in the system grouped by resource
    public static array $catalog = [
        'promo' => ['read', 'create', 'update', 'delete'],
        'ticket' => ['read', 'update', 'close'],
        'line' => ['read', 'view', 'create', 'edit.basic', 'edit.contacts', 'edit.branding'],
        'agent' => ['create', 'assign', 'update', 'permissions'],
        'bono' => ['read', 'create', 'update', 'delete'],
        'sorteo' => ['read', 'create', 'update', 'delete'],
        'news' => ['read', 'create', 'update', 'delete'],
        'user' => ['read', 'update', 'block'],
        'platform' => ['read', 'create', 'update', 'delete'],
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
