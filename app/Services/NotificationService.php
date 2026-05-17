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
        ?int $userId = null,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general'
    ): ?DashboardNotification {
        if ($userId) {
            $isEnabled = true;
        } elseif ($agentId) {
            $isEnabled = NotificationPreference::isEnabled($module, $agentId);
        } else {
            $isEnabled = NotificationPreference::isEnabled($module, null);
        }

        if (! $isEnabled) {
            return null;
        }

        return DashboardNotification::create([
            'agent_id' => $agentId,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'module' => $module,
        ]);
    }

    public static function sendToClient(
        string $title,
        string $message,
        int $userId,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general'
    ): ?DashboardNotification {
        return static::send($title, $message, null, $userId, $type, $link, $module);
    }

    public static function sendToAgent(
        string $title,
        string $message,
        ?int $agentId,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general'
    ): ?DashboardNotification {
        return static::send($title, $message, $agentId, null, $type, $link, $module);
    }

    public static function sendToAll(
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general'
    ): void {
        static::send($title, $message, null, null, $type, $link, $module);
    }

    public static function info(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, null, 'info', $link, $module);
    }

    public static function success(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, null, 'success', $link, $module);
    }

    public static function warning(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, null, 'warning', $link, $module);
    }

    public static function danger(string $title, string $message, ?int $agentId = null, ?string $link = null, string $module = 'general'): ?DashboardNotification
    {
        return static::send($title, $message, $agentId, null, 'danger', $link, $module);
    }
}
