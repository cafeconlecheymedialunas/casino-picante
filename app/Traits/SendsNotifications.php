<?php

namespace App\Traits;

use App\Models\LineAgent;
use App\Services\NotificationService;
use App\Support\LineRoles;

trait SendsNotifications
{
    protected function notify(string $title, string $message, string $module, ?string $link = null, string $type = 'success'): void
    {
        $agentId = $this->currentNotificationAgentId();
        $this->notifyAgent($agentId, $title, $message, $module, $link, $type);
        $this->broadcastToEncargados($agentId, $title, $message, $module, $link, $type);

        // Dispatch único al final, no en cada send individual
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('notification-created');
        }
    }

    protected function notifyAgent(?int $agentId, string $title, string $message, string $module, ?string $link = null, string $type = 'success'): void
    {
        NotificationService::send($title, $message, $agentId, null, $type, $link, $module);
    }

    private function broadcastToEncargados(?int $actingAgentId, string $title, string $message, string $module, ?string $link, string $type): void
    {
        $lineId = session('active_line_id');

        if (! $lineId || ! $actingAgentId) {
            return;
        }

        // Si el agente que actúa es encargado, solo notificar al admin y salir
        $isEncargado = LineAgent::where('line_id', $lineId)
            ->where('agent_id', $actingAgentId)
            ->where('role', LineRoles::ENCARGADO)
            ->where('is_active', true)
            ->exists();

        if ($isEncargado) {
            NotificationService::send($title, $message, null, null, $type, $link, $module);

            return;
        }

        // Notificar a encargados activos con agente activo (excluir el actor)
        $encargadoIds = LineAgent::where('line_id', $lineId)
            ->where('role', LineRoles::ENCARGADO)
            ->where('is_active', true)
            ->whereHas('agent', fn ($q) => $q->where('status', 'active'))
            ->pluck('agent_id');

        foreach ($encargadoIds as $encargadoId) {
            NotificationService::send($title, $message, $encargadoId, null, $type, $link, $module);
        }

        // Admin siempre recibe
        NotificationService::send($title, $message, null, null, $type, $link, $module);
    }

    private function currentNotificationAgentId(): ?int
    {
        $agentId = session('active_agent_id');

        return $agentId ? (int) $agentId : null;
    }
}
