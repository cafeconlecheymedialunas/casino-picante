<?php

namespace App\Jobs;

use App\Models\DashboardNotification;
use App\Models\NotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public ?int $agentId,
        public ?int $userId,
        public string $title,
        public string $message,
        public string $type,
        public ?string $link,
        public string $module
    ) {}

    public function handle(): void
    {
        $isEnabled = $this->shouldSend();

        if (! $isEnabled) {
            return;
        }

        DashboardNotification::create([
            'agent_id' => $this->agentId,
            'user_id' => $this->userId,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'link' => $this->link,
            'module' => $this->module,
        ]);
    }

    private function shouldSend(): bool
    {
        if ($this->userId) {
            return true;
        }

        if ($this->agentId) {
            return NotificationPreference::isEnabled($this->module, $this->agentId);
        }

        return NotificationPreference::isEnabled($this->module, null);
    }

    public function tags(): array
    {
        return [
            'notification',
            $this->module,
            $this->agentId ? "agent:{$this->agentId}" : 'admin',
        ];
    }
}
