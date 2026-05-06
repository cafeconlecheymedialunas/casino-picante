<?php

namespace App\Support;

use App\Models\DashboardNotification;

class DashboardNotifier
{
    public static function important(string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        DashboardNotification::create([
            'agent_id' => session('active_agent_id'),
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }
}
