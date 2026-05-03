<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope());
    }

    protected $fillable = [
        'title', 'description', 'status', 'start_date', 'end_date',
        'number_type', 'max_numbers', 'next_number', 'line_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function positions()
    {
        return $this->hasMany(RafflePosition::class)->orderBy('position');
    }

    public function numbers()
    {
        return $this->hasMany(RaffleNumber::class);
    }

    public function assignNumbers(int $userId, int $count): array
    {
        $assigned = [];

        for ($i = 0; $i < $count; $i++) {
            $number = $this->next_number;

            if ($this->number_type === '4digits' && $number > 9999) {
                break;
            }

            if ($this->max_numbers && $number > $this->max_numbers) {
                break;
            }

            RaffleNumber::create([
                'raffle_id' => $this->id,
                'user_id' => $userId,
                'number' => $number,
            ]);

            $assigned[] = $number;
            $this->increment('next_number');
        }

        return $assigned;
    }

    public function hasWinners(): bool
    {
        return $this->positions()->whereNotNull('winner_user_id')->exists();
    }
}
