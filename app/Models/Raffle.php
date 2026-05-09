<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope);
    }

    protected $fillable = [
        'title', 'description', 'status', 'start_date', 'end_date',
        'end_number', 'line_id', 'platform_id', 'start_number',
        'winner_user_id', 'winner_number', 'prizes', 'numbers_limit',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prizes' => 'array',
    ];

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_raffle')->withTimestamps();
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function numbers()
    {
        return $this->hasMany(RaffleNumber::class);
    }

    /**
     * Finds the first available number in the range [start, end]
     */
    public function getFirstAvailableNumber(): ?int
    {
        $occupied = $this->numbers()->pluck('number')->toArray();
        $end = $this->numbers_limit
            ? $this->start_number + $this->numbers_limit - 1
            : ($this->end_number ?? ($this->start_number + 1000));

        for ($i = $this->start_number; $i <= $end; $i++) {
            if (! in_array($i, $occupied)) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Assigns X sequential numbers skipping occupied ones
     */
    public function assignNumbers(int $userId, int $count, ?int $lineId = null): array
    {
        $assigned = [];
        $occupied = $this->numbers()->pluck('number')->toArray();
        $current = $this->start_number;
        $end = $this->numbers_limit
            ? $this->start_number + $this->numbers_limit - 1
            : max($this->end_number ?? $this->start_number, $this->numbers()->max('number') + $count + 100);

        while (count($assigned) < $count && $current <= $end) {
            if (! in_array($current, $occupied)) {
                RaffleNumber::create([
                    'raffle_id' => $this->id,
                    'user_id' => $userId,
                    'line_id' => $lineId,
                    'number' => $current,
                ]);
                $assigned[] = $current;
                $occupied[] = $current;
            }
            $current++;
        }

        return $assigned;
    }

    public function hasWinner(): bool
    {
        return ! empty($this->winner_user_id);
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    public function isExpired(): bool
    {
        return now()->gt($this->end_date);
    }

    public function isUpcoming(): bool
    {
        return now()->lt($this->start_date);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'active' && now()->between($this->start_date, $this->end_date);
    }
}
