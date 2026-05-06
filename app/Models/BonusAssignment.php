<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusAssignment extends Model
{
    protected $fillable = [
        'bonus_id', 'user_id', 'status', 'assigned_at', 'used_at', 'expired_at',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
