<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class LineRating extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
        'line_id',
        'user_id',
        'rating',
        'message',
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
