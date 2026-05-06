<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope());
    }

    protected $fillable = [
        'user_id',
        'line_id',
        'subject',
        'status',
        'priority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
