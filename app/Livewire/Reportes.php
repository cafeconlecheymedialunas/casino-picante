<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\BonusAssignment;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Reportes extends Component
{
    public $dateRange = 'month';

    public $reportType = 'general';

    public function getUserStats()
    {
        $now = Carbon::now();
        $startOfDay = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $today = User::where('created_at', '>=', $startOfDay)->count();
        $thisWeek = User::where('created_at', '>=', $startOfWeek)->count();
        $thisMonth = User::where('created_at', '>=', $startOfMonth)->count();
        $lastMonth = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $total = User::count();
        $active = User::where('status', 'active')->count();
        $blocked = User::where('status', 'blocked')->count();

        $growth = $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100) : 0;

        return [
            'today' => $today,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'lastMonth' => $lastMonth,
            'growth' => $growth,
            'total' => $total,
            'active' => $active,
            'blocked' => $blocked,
        ];
    }

    public function getTicketStats()
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();

        $total = Ticket::count();
        $open = Ticket::where('status', 'open')->count();
        $pending = Ticket::where('status', 'pending')->count();
        $closed = Ticket::where('status', 'closed')->count();

        $thisWeek = Ticket::where('created_at', '>=', $startOfWeek)->count();
        $thisMonth = Ticket::where('created_at', '>=', $startOfMonth)->count();

        $resolvedThisMonth = Ticket::where('status', 'closed')
            ->where('updated_at', '>=', $startOfMonth)->count();

        $avgResponseTime = '15 min';

        return [
            'total' => $total,
            'open' => $open,
            'pending' => $pending,
            'closed' => $closed,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'resolved' => $resolvedThisMonth,
            'avgResponse' => $avgResponseTime,
        ];
    }

    public function getPromotionStats()
    {
        $now = Carbon::now();

        $total = Promotion::count();
        $active = Promotion::where('status', 'published')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        $upcoming = Promotion::where('status', 'published')
            ->where('start_date', '>', $now)
            ->count();
        $ended = Promotion::where('end_date', '<', $now)->count();
        $draft = Promotion::where('status', 'draft')->count();

        return [
            'total' => $total,
            'active' => $active,
            'upcoming' => $upcoming,
            'ended' => $ended,
            'draft' => $draft,
        ];
    }

    public function getAgentStats()
    {
        $total = Agent::count();
        // Count agents who are encargados in at least one line
        $encargados = Agent::whereHas('activeLines', function ($query) {
            $query->where('role', 'encargado');
        })->count();
        // Count agents who are only miembros (no encargado roles)
        $miembros = Agent::whereHas('activeLines', function ($query) {
            $query->where('role', 'miembro');
        })->whereDoesntHave('activeLines', function ($query) {
            $query->where('role', 'encargado');
        })->count();
        $active = Agent::where('status', 'active')->count();

        return [
            'total' => $total,
            'encargados' => $encargados,
            'miembros' => $miembros,
            'active' => $active,
        ];
    }

    public function getBonusStats()
    {
        $total = BonusAssignment::count();
        $available = BonusAssignment::where('status', 'available')->count();
        $used = BonusAssignment::where('status', 'used')->count();
        $expired = BonusAssignment::where('status', 'expired')->count();

        $now = Carbon::now();
        $thisMonth = BonusAssignment::where('created_at', '>=', $now->startOfMonth())->count();
        $usedThisMonth = BonusAssignment::where('status', 'used')
            ->where('used_at', '>=', $now->startOfMonth())->count();

        return [
            'total' => $total,
            'available' => $available,
            'used' => $used,
            'expired' => $expired,
            'thisMonth' => $thisMonth,
            'usedThisMonth' => $usedThisMonth,
        ];
    }

    public function getRaffleStats()
    {
        $total = Raffle::count();
        $active = Raffle::where('status', 'active')->count();
        $upcoming = Raffle::where('status', 'upcoming')->count();
        $ended = Raffle::where('status', 'ended')->count();

        $totalNumbers = RaffleNumber::count();
        $assignedNumbers = RaffleNumber::whereNotNull('user_id')->count();

        return [
            'total' => $total,
            'active' => $active,
            'upcoming' => $upcoming,
            'ended' => $ended,
            'totalNumbers' => $totalNumbers,
            'assignedNumbers' => $assignedNumbers,
        ];
    }

    public function getTopUsers()
    {
        return User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $user->tickets_count = Ticket::where('user_id', $user->id)->count();
                $user->bonuses_count = BonusAssignment::where('user_id', $user->id)->count();

                return $user;
            });
    }

    public function getContentStats()
    {
        $total = Post::count();
        $published = Post::where('status', 'published')->count();
        $draft = Post::where('status', 'draft')->count();

        $novedades = Post::where('type', 'novedad')->count();
        $blog = Post::where('type', 'blog')->count();
        $carrusel = Post::where('type', 'carrusel')->count();

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
            'novedades' => $novedades,
            'blog' => $blog,
            'carrusel' => $carrusel,
        ];
    }

    public function mount()
    {
        return redirect('/dashboard');
    }

    public function render()
    {
        return view('livewire.overview')->layout('layouts.dashboard');
    }
}
