<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineRating extends Model
{
    protected $fillable = [
        'line_id',
        'user_id',
        'rating',
        'message',
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
