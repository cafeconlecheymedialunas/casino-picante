<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = ['module', 'is_enabled', 'agent_id'];

    public $timestamps = true;

    public static function isEnabled(string $module, ?int $agentId = null): bool
    {
        if ($agentId) {
            $agentPref = static::where('module', $module)
                ->where('agent_id', $agentId)
                ->first();

            return $agentPref ? (bool) $agentPref->is_enabled : true;
        }

        $pref = static::where('module', $module)
            ->whereNull('agent_id')
            ->first();

        return $pref ? (bool) $pref->is_enabled : true;
    }
}
