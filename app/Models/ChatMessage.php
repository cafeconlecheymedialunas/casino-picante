<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
        'chat_id',
        'user_id',
        'agent_id',
        'message',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
