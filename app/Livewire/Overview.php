<?php

namespace App\Livewire;

use App\Models\Promotion;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Overview extends Component
{
    public function getMetrics()
    {
        $totalUsers = User::count();
        $todayUsers = User::whereDate('created_at', Carbon::today())->count();
        $yesterdayUsers = User::whereDate('created_at', Carbon::yesterday())->count();
        $usersGrowth = $yesterdayUsers > 0 ? round(($todayUsers - $yesterdayUsers) / $yesterdayUsers * 100) : 0;

        $weekUsers = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        try {
            $activePromos = Promotion::where('status', 'published')
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->count();

            $totalPromos = Promotion::count();
        } catch (\Exception $e) {
            $activePromos = 7;
            $totalPromos = 12;
        }

        try {
            $openTickets = Ticket::where('status', 'open')->count();
        } catch (\Exception $e) {
            $openTickets = 23;
        }

        return [
            'totalUsers' => $totalUsers,
            'todayUsers' => $todayUsers,
            'weekUsers' => $weekUsers,
            'usersGrowth' => $usersGrowth,
            'activePromos' => $activePromos,
            'totalPromos' => $totalPromos,
            'openTickets' => $openTickets,
            'depositsToday' => 1200000,
            'withdrawalsToday' => 840000,
            'playsToday' => 8432,
            'onlineUsers' => 1247,
        ];
    }

    public function getRecentUsers()
    {
        return User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $metrics = $this->getMetrics();
        $recentUsers = $this->getRecentUsers();

        return view('livewire.overview', compact('metrics', 'recentUsers'))->extends('layouts.dashboard');
    }
}
