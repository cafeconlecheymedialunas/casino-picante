<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    public const CLAIMED_STATUSES = ['used', 'claimed'];

    public const CONSUMED_STATUSES = ['active', 'used', 'claimed', 'available'];

    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope);
    }

    protected $fillable = [
        'code', 'title', 'description', 'start_date', 'end_date',
        'type', 'user_id', 'status', 'created_by',
        'bonus_percent', 'bonus_amount', 'min_deposit', 'max_bonus',
        'total_quantity', 'per_user_limit',
        'line_id', 'platform_id',
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

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
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

    /**
     * Update status automatically based on current date/time
     */
    public function updateStatus(): void
    {
        $this->update(['status' => self::statusForPeriod($this->start_date, $this->end_date)]);
    }

    /**
     * Check if bonus is upcoming (not started yet)
     */
    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Get claimed count for this bonus
     */
    public function getClaimedCountAttribute(): int
    {
        return $this->assignments()->whereIn('status', self::CLAIMED_STATUSES)->count();
    }

    public function getAssignedCountAttribute(): int
    {
        return $this->assignments()->whereIn('status', self::CONSUMED_STATUSES)->count();
    }

    /**
     * Get remaining quantity available
     */
    public function getRemainingQuantityAttribute(): ?int
    {
        if (is_null($this->total_quantity)) {
            return null; // Unlimited
        }

        return max(0, $this->total_quantity - $this->assigned_count);
    }

    /**
     * Check if a user can claim this bonus
     */
    public function canUserClaim(int $userId): bool
    {
        // Check if user already reached per-user limit
        if (! is_null($this->per_user_limit)) {
            $userAssigned = $this->assignments()
                ->where('user_id', $userId)
                ->whereIn('status', self::CONSUMED_STATUSES)
                ->count();

            if ($userAssigned >= $this->per_user_limit) {
                return false;
            }
        }

        // Check total remaining quantity
        if (! is_null($this->remaining_quantity) && $this->remaining_quantity <= 0) {
            return false;
        }

        return true;
    }

    public static function statusForPeriod($startDate, $endDate): string
    {
        $now = now();
        $start = $startDate instanceof \Carbon\CarbonInterface ? $startDate : \Carbon\Carbon::parse($startDate);
        $end = $endDate instanceof \Carbon\CarbonInterface ? $endDate : \Carbon\Carbon::parse($endDate);

        if ($now->lt($start)) {
            return 'upcoming';
        }

        if ($now->gt($end)) {
            return 'expired';
        }

        return 'active';
    }

    /**
     * Generate a unique bonus code
     */
    public static function generateCode(): string
    {
        do {
            $code = 'BONO-'.strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
