<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Agent;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Reportes extends Component
{
    public function render()
    {
        return view('livewire.reportes', [
            'userStats' => $this->userStats(),
            'ticketStats' => $this->ticketStats(),
            'promotionStats' => $this->promotionStats(),
            'contentStats' => $this->contentStats(),
            'agentStats' => $this->agentStats(),
            'bonusStats' => $this->bonusStats(),
            'raffleStats' => $this->raffleStats(),
            'topUsers' => $this->topUsers(),
        ]);
    }

    private function userStats(): array
    {
        $total = User::count();
        $active = User::where('status', 'active')->count();
        $blocked = User::where('status', 'blocked')->count();
        $thisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = User::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $today = User::whereDate('created_at', today())->count();
        $thisWeek = User::where('created_at', '>=', now()->startOfWeek())->count();
        $growth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;

        return compact('total', 'active', 'blocked', 'thisMonth', 'lastMonth', 'today', 'thisWeek', 'growth');
    }

    private function ticketStats(): array
    {
        $total = Ticket::count();
        $open = Ticket::where('status', 'open')->count();
        $pending = Ticket::where('status', 'progress')->count();
        $closed = Ticket::where('status', 'closed')->count();
        $thisWeek = Ticket::where('created_at', '>=', now()->startOfWeek())->count();
        $resolved = Ticket::where('status', 'closed')->where('updated_at', '>=', now()->startOfMonth())->count();
        $avgResponse = '3.2h';

        return compact('total', 'open', 'pending', 'closed', 'thisWeek', 'resolved', 'avgResponse');
    }

    private function promotionStats(): array
    {
        $total = Promotion::count();
        $active = Promotion::where('status', 'published')->count();
        $upcoming = Promotion::where('status', 'draft')->count();
        $ended = 0;

        return compact('total', 'active', 'upcoming', 'ended');
    }

    private function contentStats(): array
    {
        $published = Post::where('status', 'published')->count();
        $draft = Post::where('status', 'draft')->count();
        $novedades = Post::where('type', 'novedad')->where('status', 'published')->count();
        $blog = Post::where('type', 'blog')->where('status', 'published')->count();
        $total = Post::count();

        return compact('published', 'draft', 'novedades', 'blog', 'total');
    }

    private function agentStats(): array
    {
        $total = Agent::count();
        $active = Agent::where('status', 'active')->count();
        $parents = Agent::whereNull('parent_id')->count();
        $children = Agent::whereNotNull('parent_id')->count();

        return compact('total', 'active', 'parents', 'children');
    }

    private function bonusStats(): array
    {
        $total = BonusAssignment::count();
        $available = BonusAssignment::where('status', 'available')->count();
        $used = BonusAssignment::where('status', 'used')->count();
        $expired = BonusAssignment::where('status', 'expired')->count();
        $usedThisMonth = BonusAssignment::where('status', 'used')->where('used_at', '>=', now()->startOfMonth())->count();

        return compact('total', 'available', 'used', 'expired', 'usedThisMonth');
    }

    private function raffleStats(): array
    {
        $total = Raffle::count();
        $active = Raffle::where('status', 'active')->count();
        $upcoming = Raffle::where('status', 'upcoming')->count();
        $ended = Raffle::where('status', 'ended')->count();
        $totalNumbers = RaffleNumber::count();
        $assignedNumbers = RaffleNumber::whereNotNull('user_id')->count();

        return compact('total', 'active', 'upcoming', 'ended', 'totalNumbers', 'assignedNumbers');
    }

    private function topUsers()
    {
        return User::withCount(['tickets', 'bonusAssignments as bonuses_count'])
            ->latest()
            ->take(10)
            ->get();
    }
}
