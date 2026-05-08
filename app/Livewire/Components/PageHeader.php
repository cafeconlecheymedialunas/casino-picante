<?php

namespace App\Livewire\Components;

use App\Models\Agent;
use App\Models\DashboardNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PageHeader extends Component
{
    protected $listeners = ['notification-created' => '$refresh'];

    public string $title = '';

    public string $subtitle = '';

    public string $buttonText = '';

    public string $buttonAction = '';

    public function markRead(int $notificationId): void
    {
        $this->notificationsQuery()->whereKey($notificationId)->first()?->markRead();
    }

    public function markAllRead(): void
    {
        $this->notificationsQuery()->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function render()
    {
        $currentAgent = $this->currentAgent();
        $currentUser = Auth::user();
        $displayName = $currentAgent
            ? trim($currentAgent->name.' '.($currentAgent->apellido ?? ''))
            : ($currentUser ? trim($currentUser->name.' '.($currentUser->apellido ?? '')) : 'Administrador');
        $role = $currentAgent
            ? ($currentAgent->cargo === 'super_agente' ? 'Encargado' : 'Agente')
            : 'Admin general';

        $avatarSeed = $currentAgent?->avatar ?: ($currentUser?->avatar ?: $displayName);
        $seed = str_replace('avatar_', '', $avatarSeed);
        $avatarUrl = $this->avatarUrl($seed ?: 'Admin');

        $notifications = $this->notificationsQuery()->latest()->take(8)->get();
        $unreadCount = $this->notificationsQuery()->whereNull('read_at')->count();

        return view('livewire.components.page-header', compact(
            'displayName',
            'role',
            'avatarUrl',
            'notifications',
            'unreadCount'
        ));
    }

    private function currentAgent(): ?Agent
    {
        return session('active_agent_id') ? Agent::find(session('active_agent_id')) : null;
    }

    private function notificationsQuery()
    {
        $agentId = session('active_agent_id');

        return DashboardNotification::query()
            ->when($agentId, fn ($query) => $query->where('agent_id', $agentId))
            ->when(! $agentId, fn ($query) => $query->whereNull('agent_id'));
    }

    private function avatarUrl(string $name): string
    {
        return 'https://api.dicebear.com/9.x/adventurer/svg?seed='.urlencode($name ?: 'Admin').'&backgroundColor=ffdfbf,ffd5dc,d1d4f9,b6e3f4';
    }
}
