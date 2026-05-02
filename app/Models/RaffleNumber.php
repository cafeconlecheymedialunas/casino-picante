<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleNumber extends Model
{
    protected $fillable = ['raffle_id', 'user_id', 'number'];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedNumberAttribute(): string
    {
        if ($this->raffle?->number_type === '4digits') {
            return str_pad($this->number, 4, '0', STR_PAD_LEFT);
        }

        return (string) $this->number;
    }
}
