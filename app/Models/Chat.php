<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

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
