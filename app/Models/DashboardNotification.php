<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class DashboardNotification extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
        'agent_id',
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'module',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function markRead(): void
    {
        if (! $this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}
