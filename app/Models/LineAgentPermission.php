<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LineAgentPermission extends Model
{
    use HasVendorScope;

    public $timestamps = false;

    protected $fillable = ['vendor_id', 'line_id', 'agent_id', 'permission'];

    protected static function booted(): void
    {
        $clearCache = function (self $permission): void {
            Cache::forget("line_agent_permissions:{$permission->line_id}:{$permission->agent_id}");
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    public static function allPermissions(): array
    {
        return Permissions::all();
    }
}
