<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class RaffleNumber extends Model
{
    use HasVendorScope;

    protected $fillable = ['vendor_id', 'raffle_id', 'user_id', 'line_id', 'number'];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
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
