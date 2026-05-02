<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RafflePosition extends Model
{
    protected $fillable = [
        'raffle_id', 'position', 'prize_description', 'prize_amount',
        'winner_user_id', 'winner_number',
    ];

    protected $casts = [
        'prize_amount' => 'decimal:2',
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }
}
