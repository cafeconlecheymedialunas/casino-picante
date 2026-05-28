<?php

namespace App\Services;

use App\Jobs\SendNotificationJob;
use App\Models\Agent;
use App\Models\DashboardNotification;
use App\Models\NotificationPreference;
use App\Models\User;

class NotificationService
{
    public static function send(
        string $title,
        string $message,
        ?int $agentId = null,
        int|string|null $userId = null,
        ?string $type = 'info',
        ?string $link = null,
        string $module = 'general',
        bool $sync = false,
        ?int $vendorId = null
    ): ?DashboardNotification {
        if (is_string($userId)) {
            $module = is_string($link) ? $link : 'general';
            $link = is_string($type) ? $type : null;
            $type = $userId;
            $userId = null;
        }

        $type ??= 'info';
        $vendorId ??= self::resolveVendorId($agentId, $userId);

        if ($sync) {
            return self::sendSync($title, $message, $agentId, $userId, $type, $link, $module, $vendorId);
        }

        SendNotificationJob::dispatch(
            $agentId,
            $userId,
            $title,
            $message,
            $type,
            $link,
            $module,
            $vendorId
        );

        return null;
    }

    private static function sendSync(
        string $title,
        string $message,
        ?int $agentId = null,
        ?int $userId = null,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general',
        ?int $vendorId = null
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
            'vendor_id' => $vendorId,
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
        string $module = 'general',
        bool $sync = false
    ): ?DashboardNotification {
        return static::send($title, $message, null, $userId, $type, $link, $module, $sync);
    }

    public static function sendToAgent(
        string $title,
        string $message,
        ?int $agentId,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general',
        bool $sync = false
    ): ?DashboardNotification {
        return static::send($title, $message, $agentId, null, $type, $link, $module, $sync);
    }

    public static function sendToAll(
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null,
        string $module = 'general',
        bool $sync = false
    ): void {
        static::send($title, $message, null, null, $type, $link, $module, $sync);
    }

    private static function resolveVendorId(?int $agentId, ?int $userId): ?int
    {
        if ($vendorId = session('active_vendor_id')) {
            return (int) $vendorId;
        }

        if ($agentId) {
            return Agent::withoutGlobalScopes()->whereKey($agentId)->value('vendor_id');
        }

        if ($userId) {
            return User::withoutGlobalScopes()->whereKey($userId)->value('vendor_id');
        }

        return null;
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
