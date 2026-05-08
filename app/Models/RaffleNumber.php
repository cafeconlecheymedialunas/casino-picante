<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleNumber extends Model
{
    protected $fillable = ['raffle_id', 'user_id', 'line_id', 'number'];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function getFormattedNumberAttribute(): string
    {
        return (string) $this->number;
    }
}
