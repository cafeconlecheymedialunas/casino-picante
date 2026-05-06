<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Ticket;
use App\Models\User;
use App\Services\SalesStats;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Overview extends Component
{
    public $editingStats = false;

    public $editBestSales = [];

    public $editLineId = null;

    public function getAlerts(): array
    {
        $alerts = [];

        $stale = Ticket::where('status', 'open')
            ->where('created_at', '<=', Carbon::now()->subHours(2))
            ->count();
        if ($stale > 0) {
            $alerts[] = ['type' => 'danger', 'icon' => '⚠️',
                'msg' => "$stale ticket".($stale > 1 ? 's' : '').' sin respuesta hace más de 2 horas',
                'route' => 'tickets', 'link' => 'Ver tickets →'];
        }

        $expiring = Promotion::where('status', 'published')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addHours(24)])
            ->count();
        if ($expiring > 0) {
            $alerts[] = ['type' => 'info', 'icon' => '📢',
                'msg' => "$expiring promoción".($expiring > 1 ? 'es' : '').' vence'.($expiring > 1 ? 'n' : '').' en las próximas 24h',
                'route' => 'promociones', 'link' => 'Ver promociones →'];
        }

        return $alerts;
    }

    public function getUserStats(): array
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $total = User::count();
        $active = User::where('status', 'active')->count();
        $blocked = User::where('status', 'blocked')->count();

        $todayNew = User::whereDate('created_at', $today)->count();
        $yesterdayNew = User::whereDate('created_at', $yesterday)->count();
        $weekNew = User::where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $monthNew = User::where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $lastMonthNew = User::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth(),
        ])->count();

        $vsYesterday = $yesterdayNew > 0
            ? round(($todayNew - $yesterdayNew) / $yesterdayNew * 100)
            : ($todayNew > 0 ? 100 : 0);
        $vsLastMonth = $lastMonthNew > 0
            ? round(($monthNew - $lastMonthNew) / $lastMonthNew * 100)
            : ($monthNew > 0 ? 100 : 0);

        return compact('total', 'active', 'blocked',
            'todayNew', 'yesterdayNew', 'weekNew', 'monthNew', 'lastMonthNew',
            'vsYesterday', 'vsLastMonth');
    }

    public function getTicketStats(): array
    {
        $now = Carbon::now();

        $open = Ticket::where('status', 'open')->count();
        $progress = Ticket::where('status', 'progress')->count();
        $closed = Ticket::where('status', 'closed')->count();
        $total = $open + $progress + $closed;

        $stale = Ticket::where('status', 'open')
            ->where('created_at', '<=', $now->copy()->subHours(2))->count();
        $closedToday = Ticket::where('status', 'closed')
            ->whereDate('updated_at', Carbon::today())->count();
        $openedToday = Ticket::whereDate('created_at', Carbon::today())->count();
        $weekTotal = Ticket::where('created_at', '>=', $now->copy()->startOfWeek())->count();

        $resolutionRate = $total > 0 ? round($closed / $total * 100) : 0;

        return compact('open', 'progress', 'closed', 'total',
            'stale', 'closedToday', 'openedToday', 'weekTotal', 'resolutionRate');
    }

    public function getBonusStats(): array
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        $activeBonuses = Bonus::where('status', 'active')
            ->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();
        $pausedBonuses = Bonus::where('status', 'paused')->count();
        $expiredBonuses = Bonus::where('end_date', '<', $now)->count();
        $totalBonuses = Bonus::count();

        $activeAssign = BonusAssignment::where('status', 'active')->count();
        $usedAssign = BonusAssignment::where('status', 'used')->count();
        $expiredAssign = BonusAssignment::where('status', 'expired')->count();
        $totalAssign = BonusAssignment::count();

        $usedMonth = BonusAssignment::where('status', 'used')
            ->where('used_at', '>=', $monthStart)->count();
        $expiredMonth = BonusAssignment::where('status', 'expired')
            ->where('updated_at', '>=', $monthStart)->count();
        $denominator = $usedMonth + $expiredMonth;
        $conversionRate = $denominator > 0 ? round($usedMonth / $denominator * 100) : 0;

        return compact('activeBonuses', 'pausedBonuses', 'expiredBonuses', 'totalBonuses',
            'activeAssign', 'usedAssign', 'expiredAssign', 'totalAssign',
            'usedMonth', 'conversionRate');
    }

    public function getRaffleStats(): array
    {
        $active = Raffle::where('status', 'active')->count();
        $upcoming = Raffle::where('status', 'upcoming')->count();
        $ended = Raffle::where('status', 'ended')->count();
        $total = $active + $upcoming + $ended;

        $totalNumbers = RaffleNumber::count();
        $uniqueParticip = RaffleNumber::distinct('user_id')->count('user_id');

        $activeRaffle = Raffle::where('status', 'active')->orderBy('end_date')->first();
        $numbersActive = $activeRaffle
            ? RaffleNumber::where('raffle_id', $activeRaffle->id)->count()
            : 0;

        return compact('active', 'upcoming', 'ended', 'total',
            'totalNumbers', 'uniqueParticip', 'activeRaffle', 'numbersActive');
    }

    public function getPromoStats(): array
    {
        $now = Carbon::now();

        $active = Promotion::where('status', 'published')
            ->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();
        $upcoming = Promotion::where('status', 'published')->where('start_date', '>', $now)->count();
        $ended = Promotion::where('end_date', '<', $now)->count();
        $draft = Promotion::where('status', 'draft')->count();
        $expiring = Promotion::where('status', 'published')
            ->whereBetween('end_date', [$now, $now->copy()->addHours(24)])->count();

        return compact('active', 'upcoming', 'ended', 'draft', 'expiring');
    }

    public function getAgentStats(): array
    {
        $total = Agent::count();
        $active = Agent::where('status', 'active')->count();
        $inactive = Agent::where('status', '!=', 'active')->count();
        $parents = Agent::whereNull('parent_id')->count();
        $children = Agent::whereNotNull('parent_id')->count();

        return compact('total', 'active', 'inactive', 'parents', 'children');
    }

    public function getContentStats(): array
    {
        $published = Post::where('status', 'published')->count();
        $draft = Post::where('status', 'draft')->count();
        $novedades = Post::where('type', 'novedad')->where('status', 'published')->count();
        $carrusel = Post::where('type', 'carrusel')->where('status', 'published')->count();
        $blog = Post::where('type', 'blog')->where('status', 'published')->count();
        $total = Post::count();

        return compact('published', 'draft', 'novedades', 'carrusel', 'blog', 'total');
    }

    public function getRegisteredUsersCount(): int
    {
        return User::count();
    }

    public function getAgentsCount(): int
    {
        return Agent::count();
    }

    public function getAgentsCountByLine($lineId = null, $role = null): int
    {
        $query = LineAgent::query();

        if ($lineId) {
            $query->where('line_id', $lineId);
        }

        if ($role) {
            $query->where('role', $role);
        }

        return $query->count();
    }

    public function getActiveBonosCount(): int
    {
        return Bonus::where('status', 'active')
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->count();
    }

    public function getRafflesByLineCount($lineId = null): int
    {
        $query = Raffle::query();
        if ($lineId) {
            $query->where('line_id', $lineId);
        }

        return $query->count();
    }

    public function getBestSellingLineOfMonth(): ?array
    {
        return SalesStats::bestSellingLineOfMonth();
    }

    public function getLast10RegisteredUsers()
    {
        return User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getRecentUsers()
    {
        return User::orderBy('created_at', 'desc')->limit(6)->get();
    }

    public function getUrgentTickets()
    {
        return Ticket::with('user')
            ->where('status', 'open')
            ->orderBy('created_at', 'asc')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.overview', [
            'alerts' => $this->getAlerts(),
            'users' => $this->getUserStats(),
            'tickets' => $this->getTicketStats(),
            'bonuses' => $this->getBonusStats(),
            'raffles' => $this->getRaffleStats(),
            'promos' => $this->getPromoStats(),
            'agents' => $this->getAgentStats(),
            'content' => $this->getContentStats(),
            'recentUsers' => $this->getRecentUsers(),
            'urgentTickets' => $this->getUrgentTickets(),
            'registeredUsersCount' => $this->getRegisteredUsersCount(),
            'agentsCount' => $this->getAgentsCount(),
            'agentsCountByLine' => LineAgent::count(),
            'agentsCountEncargado' => LineAgent::where('role', 'encargado')->count(),
            'activeBonosCount' => $this->getActiveBonosCount(),
            'rafflesByLineCount' => $this->getRafflesByLineCount(),
            'bestSellingLine' => $this->getBestSellingLineOfMonth(),
            'last10Users' => $this->getLast10RegisteredUsers(),
        ]);
    }
}
