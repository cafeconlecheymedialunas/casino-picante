<?php

namespace App\Livewire;

use App\Models\NotificationPreference;
use Livewire\Component;

class NotificationSettings extends Component
{
    public array $preferences = [];

    public const MODULES = [
        'agents' => ['label' => 'Agentes', 'icon' => 'AG'],
        'users' => ['label' => 'Clientes', 'icon' => 'CL'],
        'bonuses' => ['label' => 'Bonos', 'icon' => 'BO'],
        'raffles' => ['label' => 'Sorteos', 'icon' => 'SO'],
        'tickets' => ['label' => 'Tickets', 'icon' => 'TI'],
        'lines' => ['label' => 'Lineas', 'icon' => 'LI'],
        'sales' => ['label' => 'Ventas', 'icon' => 'VE'],
        'posts' => ['label' => 'Novedades', 'icon' => 'NO'],
        'promotions' => ['label' => 'Promociones', 'icon' => 'PR'],
    ];

    public function mount(): void
    {
        $agentId = $this->agentId();

        foreach (self::MODULES as $module => $data) {
            $pref = NotificationPreference::where('module', $module)
                ->where('agent_id', $agentId)
                ->first();

            $this->preferences[$module] = $pref
                ? (bool) $pref->is_enabled
                : NotificationPreference::isEnabled($module, $agentId);
        }
    }

    public function toggle(string $module): void
    {
        if (! array_key_exists($module, self::MODULES)) {
            return;
        }

        $this->preferences[$module] = ! $this->preferences[$module];

        NotificationPreference::updateOrCreate(
            ['module' => $module, 'agent_id' => $this->agentId()],
            ['is_enabled' => $this->preferences[$module]]
        );

        $this->dispatch('toast', message: 'Preferencia actualizada', type: 'success');
    }

    public function render()
    {
        return view('livewire.notification-settings', [
            'modules' => self::MODULES,
        ])->layout('layouts.dashboard');
    }

    private function agentId(): ?int
    {
        return session('active_agent_id') ? (int) session('active_agent_id') : null;
    }
}
