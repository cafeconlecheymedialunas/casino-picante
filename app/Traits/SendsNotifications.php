<?php

namespace App\Traits;

use App\Services\NotificationService;

trait SendsNotifications
{
    protected function notify(string $title, string $message, string $module, ?string $link = null, string $type = 'success'): void
    {
        $this->notifyAgent($this->currentNotificationAgentId(), $title, $message, $module, $link, $type);
    }

    protected function notifyAgent(?int $agentId, string $title, string $message, string $module, ?string $link = null, string $type = 'success'): void
    {
        NotificationService::send($title, $message, $agentId, $type, $link, $module);
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('notification-created');
        }
    }

    private function currentNotificationAgentId(): ?int
    {
        $agentId = session('active_agent_id');

        return $agentId ? (int) $agentId : null;
    }
}
