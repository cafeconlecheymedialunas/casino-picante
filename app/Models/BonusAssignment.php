<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class BonusAssignment extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id', 'bonus_id', 'user_id', 'status', 'assigned_at', 'used_at', 'expired_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
