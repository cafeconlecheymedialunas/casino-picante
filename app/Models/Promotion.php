<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope());
    }

    protected $fillable = [
        'title',
        'description',
        'code',
        'icon',
        'type',
        'bonus_percent',
        'bonus_amount',
        'min_deposit',
        'max_bonus',
        'is_recurring',
        'recurring_days',
        'start_date',
        'end_date',
        'status',
        'line_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'recurring_days' => 'array',
        'is_recurring' => 'boolean',
        'bonus_percent' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'min_deposit' => 'decimal:2',
        'max_bonus' => 'decimal:2',
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
