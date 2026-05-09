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
use App\Support\LineRoles;
use App\Support\Roles;
use App\Traits\HasLinePermissions;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Overview extends Component
{
    use HasLinePermissions;

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

        return $alerts;
    }

    public function getUserStats(): array
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $total = $this->clientUsersQuery()->count();
        $active = $this->clientUsersQuery()->where('status', 'active')->count();
        $blocked = $this->clientUsersQuery()->where('status', 'blocked')->count();

        $todayNew = $this->clientUsersQuery()->whereDate('created_at', $today)->count();
        $yesterdayNew = $this->clientUsersQuery()->whereDate('created_at', $yesterday)->count();
        $weekNew = $this->clientUsersQuery()->where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $monthNew = $this->clientUsersQuery()->where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $lastMonthNew = $this->clientUsersQuery()->whereBetween('created_at', [
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

        $activeAssign = $this->bonusAssignmentsQuery()->where('status', 'active')->count();
        $usedAssign = $this->bonusAssignmentsQuery()->where('status', 'used')->count();
        $expiredAssign = $this->bonusAssignmentsQuery()->where('status', 'expired')->count();
        $totalAssign = $this->bonusAssignmentsQuery()->count();

        $usedMonth = $this->bonusAssignmentsQuery()->where('status', 'used')
            ->where('used_at', '>=', $monthStart)->count();
        $expiredMonth = $this->bonusAssignmentsQuery()->where('status', 'expired')
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

        $totalNumbers = $this->raffleNumbersQuery()->count();
        $uniqueParticip = $this->raffleNumbersQuery()->select('user_id')->distinct()->count();

        $activeRaffle = Raffle::where('status', 'active')->orderBy('end_date')->first();
        $numbersActive = $activeRaffle
            ? $this->raffleNumbersQuery()->where('raffle_id', $activeRaffle->id)->count()
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
        $total = $this->agentsQuery()->count();
        $active = $this->agentsQuery()->where('status', 'active')->count();
        $inactive = $this->agentsQuery()->where('status', '!=', 'active')->count();
        $parents = $this->agentsQuery()->whereNull('parent_id')->count();
        $children = $this->agentsQuery()->whereNotNull('parent_id')->count();

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
        return $this->clientUsersQuery()->count();
    }

    public function getAgentsCount(): int
    {
        return $this->agentsQuery()->count();
    }

    public function getAgentsCountByLine($lineId = null, $role = null): int
    {
        $query = $this->lineAgentsQuery();

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
        return $this->clientUsersQuery()->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getRecentUsers()
    {
        return $this->clientUsersQuery()->orderBy('created_at', 'desc')->limit(6)->get();
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
        $this->ensureAdmin();

        $lines = Line::where('status', 'active')->get();

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
            'agentsCountByLine' => $this->lineAgentsQuery()->count(),
            'agentsCountEncargado' => $this->lineAgentsQuery()->where('role', LineRoles::ENCARGADO)->count(),
            'activeBonosCount' => $this->getActiveBonosCount(),
            'rafflesByLineCount' => $this->getRafflesByLineCount(),
            'bestSellingLine' => $this->getBestSellingLineOfMonth(),
            'last10Users' => $this->getLast10RegisteredUsers(),
            'totalSales' => SalesStats::globalTotalSales($lines),
            'monthlyGrowth' => SalesStats::globalMonthlyGrowth($lines),
            'topPlatform' => SalesStats::globalTopPlatform($lines),
            'topBuyer' => SalesStats::globalTopBuyer($lines),
            'topAgent' => SalesStats::globalTopAgent($lines),
            'salesSummary' => SalesStats::globalSalesSummary($lines),
            'dailySales' => SalesStats::globalDailySales(30, $lines),
            'platformComparison' => SalesStats::globalPlatformComparison($lines),
            'lineComparison' => SalesStats::globalLineComparison($lines),
            'dailyRegistrations' => $this->getDailyRegistrations(),
        ]);
    }

    public function getDailyRegistrations(): array
    {
        $days = 15;
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $data[] = $this->clientUsersQuery()->whereDate('created_at', $date)->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function ensureAdmin(): void
    {
        if (! $this->isAdminMode()) {
            abort(403, 'Solo el administrador general puede acceder al dashboard.');
        }
    }

    // Usa el método del trait HasLinePermissions::visibleLineIds()

    private function clientUsersQuery()
    {
        $query = User::query()
            ->whereHas('role', fn ($role) => $role->where('name', Roles::CLIENTE));

        $lineIds = $this->visibleLineIds();
        if ($lineIds !== null) {
            $query->where(function ($inner) use ($lineIds) {
                if ($lineIds === []) {
                    $inner->whereRaw('1 = 0');

                    return;
                }

                $inner->whereIn('line_id', $lineIds)
                    ->orWhereHas('lines', fn ($line) => $line
                        ->whereIn('lines.id', $lineIds)
                        ->where('line_clients.is_active', true));
            });
        }

        return $query;
    }

    private function agentsQuery()
    {
        $query = Agent::query();
        $lineIds = $this->visibleLineIds();

        if ($lineIds !== null) {
            $query->whereHas('lineAgents', fn ($lineAgent) => $lineAgent
                ->whereIn('line_id', $lineIds)
                ->where('is_active', true));
        }

        return $query;
    }

    private function lineAgentsQuery()
    {
        $query = LineAgent::query();
        $lineIds = $this->visibleLineIds();

        if ($lineIds !== null) {
            $query->whereIn('line_id', $lineIds);
        }

        return $query;
    }

    private function bonusAssignmentsQuery()
    {
        $query = BonusAssignment::query();
        $lineIds = $this->visibleLineIds();

        if ($lineIds !== null) {
            $query->whereHas('bonus', fn ($bonus) => $bonus->whereIn('line_id', $lineIds));
        }

        return $query;
    }

    private function raffleNumbersQuery()
    {
        $query = RaffleNumber::query();
        $lineIds = $this->visibleLineIds();

        if ($lineIds !== null) {
            $query->whereIn('line_id', $lineIds);
        }

        return $query;
    }
}
