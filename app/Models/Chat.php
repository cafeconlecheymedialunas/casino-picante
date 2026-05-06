<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'agent_id',
        'subject',
        'status',
        'context_type',
        'context_name',
        'context_email',
        'context_phone',
        'context_label',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
