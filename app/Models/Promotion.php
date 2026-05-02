<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'code',
        'icon',
        'start_date',
        'end_date',
        'status',
        'lines',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'lines' => 'array',
    ];

    public function getStatusAttribute()
    {
        if ($this->attributes['status'] === 'draft') {
            return 'draft';
        }

        $now = now();
        if ($this->start_date > $now) {
            return 'upcoming';
        }
        if ($this->end_date < $now) {
            return 'ended';
        }

        return 'active';
    }
}
