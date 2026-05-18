<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasVendorScope;

    protected $fillable = ['vendor_id', 'module', 'is_enabled', 'agent_id'];

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
