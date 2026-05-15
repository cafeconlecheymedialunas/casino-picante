<?php

namespace App\Livewire\Frontend;

use App\Models\Raffle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicRaffle extends Component
{
    public function getActiveRaffle(): ?Raffle
    {
        return Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('end_date')
            ->first();
    }

    public function getEndedRaffle(): ?Raffle
    {
        return Raffle::withoutGlobalScopes()
            ->with(['winner', 'lines', 'platform'])
            ->where('status', 'finished')
            ->latest('end_date')
            ->first();
    }

    public function getUpcomingRaffle(): ?Raffle
    {
        return Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('status', 'inactive')
            ->where('start_date', '>', now())
            ->oldest('start_date')
            ->first();
    }

    public function getMyNumbers(Raffle $raffle)
    {
        if (! Auth::check()) {
            return collect();
        }

        return $raffle->numbers()
            ->where('user_id', Auth::id())
            ->with('raffle')
            ->orderBy('number')
            ->get();
    }

    public function render()
    {
        $activeRaffle = $this->getActiveRaffle();
        $upcomingRaffle = $this->getUpcomingRaffle();
        $endedRaffle = $this->getEndedRaffle();
        $myNumbers = $activeRaffle ? $this->getMyNumbers($activeRaffle) : collect();
        $isLogged = Auth::check();
        $user = Auth::user();
        $raffles = Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->withCount('numbers')
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'inactive' THEN 1 ELSE 2 END")
            ->latest('end_date')
            ->get();

        return view('livewire.frontend.public-raffle', compact(
            'activeRaffle', 'upcomingRaffle', 'endedRaffle', 'myNumbers', 'isLogged', 'user', 'raffles'
        ))->layout('frontend.layouts.app');
    }
}
