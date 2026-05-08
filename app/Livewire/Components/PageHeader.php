<?php

namespace App\Livewire\Components;

use App\Models\Agent;
use App\Models\DashboardNotification;
use App\Support\AvatarLibrary;
use App\Support\Roles;
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

    public function deleteNotification(int $notificationId): void
    {
        $this->notificationsQuery()->whereKey($notificationId)->delete();
    }

    public function deleteAllRead(): void
    {
        $this->notificationsQuery()->whereNotNull('read_at')->delete();
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
            : ($currentUser?->hasRole(Roles::ADMIN) ? 'Admin general' : ($currentUser?->role?->label ?? 'Usuario'));

        $avatarValue = $currentAgent?->avatar ?: ($currentUser?->avatar ?: AvatarLibrary::default());
        $avatarUrl = AvatarLibrary::url($avatarValue);

        $notifications = $this->notificationsQuery()->latest()->take(8)->get();
        $unreadCount = $this->notificationsQuery()->whereNull('read_at')->count();
        $canOpenSettings = $currentUser?->hasRole(Roles::ADMIN) ?? false;

        return view('livewire.components.page-header', compact(
            'displayName',
            'role',
            'avatarUrl',
            'notifications',
            'unreadCount',
            'canOpenSettings'
        ));
    }

    private function currentAgent(): ?Agent
    {
        return session('active_agent_id') ? Agent::find(session('active_agent_id')) : null;
    }

    private function notificationsQuery()
    {
        $agentId = session('active_agent_id');

        if ($agentId && ! Agent::whereKey($agentId)->exists()) {
            session()->forget(['active_agent_id', 'active_line_id']);
            $agentId = null;
        }

        return DashboardNotification::query()
            ->when($agentId, fn ($query) => $query->where('agent_id', $agentId))
            ->when(! $agentId, fn ($query) => $query->whereNull('agent_id'));
    }

}
