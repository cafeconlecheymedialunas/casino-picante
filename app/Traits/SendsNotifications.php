<?php

namespace App\Traits;

use App\Jobs\SendBatchNotificationsJob;
use App\Jobs\SendNotificationJob;

trait SendsNotifications
{
    protected function notify(string $title, string $message, string $module, ?string $link = null, string $type = 'success'): void
    {
        $agentId = $this->currentNotificationAgentId();

        SendNotificationJob::dispatch($agentId, null, $title, $message, $type, $link, $module);

        $lineId = session('active_line_id');
        if ($lineId && $agentId) {
            SendBatchNotificationsJob::dispatch($agentId, (int) $lineId, $title, $message, $type, $link, $module);
        }

        if (method_exists($this, 'dispatch')) {
            $this->dispatch('notification-created');
        }
    }

    protected function notifyAgent(?int $agentId, string $title, string $message, string $module, ?string $link = null, string $type = 'success'): void
    {
        SendNotificationJob::dispatch($agentId, null, $title, $message, $type, $link, $module);
    }

    private function broadcastToEncargados(?int $actingAgentId, string $title, string $message, string $module, ?string $link, string $type): void {}

    private function currentNotificationAgentId(): ?int
    {
        $agentId = session('active_agent_id');

        return $agentId ? (int) $agentId : null;
    }
}
