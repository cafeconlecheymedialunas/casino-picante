<?php

namespace App\Services;

use App\Models\DashboardNotification;
use App\Models\NotificationPreference;

class NotificationService
{
    public static function send(
        string $title,
        string $message,
        ?int $agentId = null,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general'
    ): ?DashboardNotification {
        if (! NotificationPreference::isEnabled($module, $agentId)) {
            return null;
        }

        return DashboardNotification::create([
            'agent_id' => $agentId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'module' => $module,
        ]);
    }

    public static function info(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, 'info', $link, $module);
    }

    public static function success(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, 'success', $link, $module);
    }

    public static function warning(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, 'warning', $link, $module);
    }

    public static function danger(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, 'danger', $link, $module);
    }
}
