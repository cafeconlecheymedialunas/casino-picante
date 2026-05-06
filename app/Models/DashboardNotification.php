<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardNotification extends Model
{
    protected $fillable = [
        'agent_id',
        'title',
        'message',
        'type',
        'link',
        'module',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function markRead(): void
    {
        if (! $this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}
