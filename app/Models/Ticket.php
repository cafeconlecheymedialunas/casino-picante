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
        'tracking_code',
        'subject',
        'category',
        'status',
        'priority',
    ];

    protected static function booting(): void
    {
        static::creating(function (self $ticket) {
            if (empty($ticket->tracking_code)) {
                $ticket->tracking_code = static::generateTrackingCode();
            }
        });
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (static::withoutGlobalScopes()->where('tracking_code', $code)->exists());

        return $code;
    }

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
