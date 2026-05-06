<?php

namespace App\Livewire;

use App\Models\NotificationPreference;
use Livewire\Component;

class NotificationSettings extends Component
{
    public array $preferences = [];

    public const MODULES = [
        'agents' => ['label' => 'Agentes', 'icon' => '👤'],
        'users' => ['label' => 'Clientes', 'icon' => '👥'],
        'bonuses' => ['label' => 'Bonos', 'icon' => '🎁'],
        'raffles' => ['label' => 'Sorteos', 'icon' => '🎲'],
        'tickets' => ['label' => 'Tickets', 'icon' => '🎫'],
        'lines' => ['label' => 'Líneas', 'icon' => '📶'],
        'promotions' => ['label' => 'Promociones', 'icon' => '📢'],
        'posts' => ['label' => 'Novedades', 'icon' => '📰'],
    ];

    public function mount(): void
    {
        foreach (self::MODULES as $module => $data) {
            $pref = NotificationPreference::where('module', $module)
                ->whereNull('agent_id')
                ->first();
            $this->preferences[$module] = $pref ? $pref->is_enabled : true;
        }
    }

    public function toggle(string $module): void
    {
        $this->preferences[$module] = ! $this->preferences[$module];

        NotificationPreference::updateOrCreate(
            ['module' => $module, 'agent_id' => null],
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
}
