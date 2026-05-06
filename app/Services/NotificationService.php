<?php

namespace App\Services;

use App\Models\DashboardNotification;

class NotificationService
{
    public static function send(
        string $title,
        string $message,
        ?int $agentId = null,
        string $type = 'info',
        ?string $link = null
    ): DashboardNotification {
        return DashboardNotification::create([
            'agent_id' => $agentId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }

    public static function info(string $title, string $message, ?int $agentId = null, ?string $link = null): DashboardNotification
    {
        return static::send($title, $message, $agentId, 'info', $link);
    }

    public static function success(string $title, string $message, ?int $agentId = null, ?string $link = null): DashboardNotification
    {
        return static::send($title, $message, $agentId, 'success', $link);
    }

    public static function warning(string $title, string $message, ?int $agentId = null, ?string $link = null): DashboardNotification
    {
        return static::send($title, $message, $agentId, 'warning', $link);
    }

    public static function danger(string $title, string $message, ?int $agentId = null, ?string $link = null): DashboardNotification
    {
        return static::send($title, $message, $agentId, 'danger', $link);
    }
}
