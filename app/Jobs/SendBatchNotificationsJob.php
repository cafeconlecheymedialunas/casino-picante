<?php

namespace App\Jobs;

use App\Models\LineAgent;
use App\Support\LineRoles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBatchNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public ?int $actingAgentId,
        public int $lineId,
        public string $title,
        public string $message,
        public string $type,
        public ?string $link,
        public string $module
    ) {}

    public function handle(): void
    {
        $isEncargado = LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $this->actingAgentId)
            ->where('role', LineRoles::ENCARGADO)
            ->where('is_active', true)
            ->exists();

        if ($isEncargado) {
            SendNotificationJob::dispatch(null, null, $this->title, $this->message, $this->type, $this->link, $this->module);

            return;
        }

        $encargadoIds = LineAgent::where('line_id', $this->lineId)
            ->where('role', LineRoles::ENCARGADO)
            ->where('is_active', true)
            ->whereHas('agent', fn ($q) => $q->where('status', 'active'))
            ->pluck('agent_id');

        foreach ($encargadoIds as $encargadoId) {
            SendNotificationJob::dispatch($encargadoId, null, $this->title, $this->message, $this->type, $this->link, $this->module);
        }

        SendNotificationJob::dispatch(null, null, $this->title, $this->message, $this->type, $this->link, $this->module);
    }
}
