<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope());
    }

    protected $fillable = [
        'title', 'description', 'start_date', 'end_date',
        'type', 'user_id', 'status', 'created_by',
        'bonus_percent', 'bonus_amount', 'min_deposit', 'max_bonus',
        'line_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'bonus_percent' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'min_deposit' => 'decimal:2',
        'max_bonus' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignments()
    {
        return $this->hasMany(BonusAssignment::class);
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function isVisible(): bool
    {
        return $this->end_date->diffInHours(now()) < 48 || $this->end_date->isFuture();
    }

    public function scopeVisibleToUser($query, int $userId)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', now()->subHours(48))
            ->where(function ($q) use ($userId) {
                $q->where('type', 'general')
                    ->orWhere(function ($q2) use ($userId) {
                        $q2->where('type', 'specific')->where('user_id', $userId);
                    });
            })
            ->whereDoesntHave('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->whereIn('status', ['used', 'expired']);
            });
    }
}
