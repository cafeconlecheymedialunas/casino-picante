<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Raffle extends Model
{
    use HasVendorScope;

    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope);

        static::saving(function (Raffle $raffle) {
            if (blank($raffle->slug)) {
                $raffle->slug = static::uniqueSlug($raffle->title ?: 'sorteo', $raffle->id);
            }
        });
    }

    protected $fillable = [
        'vendor_id',
        'slug',
        'title', 'description', 'status', 'start_date', 'end_date',
        'end_number', 'line_id', 'platform_id', 'start_number',
        'winner_user_id', 'winner_number', 'prizes', 'numbers_limit',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'sorteo';
        $slug = $base;
        $suffix = 2;

        while (static::withoutGlobalScopes()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prizes' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_raffle')
            ->withoutGlobalScopes()
            ->withPivot('vendor_id')
            ->withTimestamps();
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class)->withoutGlobalScopes();
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function numbers()
    {
        return $this->hasMany(RaffleNumber::class)->withoutGlobalScopes();
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
                    'vendor_id' => $this->vendor_id ?: session('active_vendor_id'),
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
