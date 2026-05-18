<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Model;

class LineAgentPermission extends Model
{
    use HasVendorScope;

    public $timestamps = false;

    protected $fillable = ['vendor_id', 'line_id', 'agent_id', 'permission'];

    public static function allPermissions(): array
    {
        return Permissions::all();
    }
}
