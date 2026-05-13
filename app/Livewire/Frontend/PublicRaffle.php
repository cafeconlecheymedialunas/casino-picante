<?php

namespace App\Livewire\Frontend;

use App\Models\Raffle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicRaffle extends Component
{
    public function getActiveRaffle()
    {
        return Raffle::with(['lines', 'platform'])
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    public function getEndedRaffle()
    {
        return Raffle::with(['winner', 'lines', 'platform'])
            ->where('status', 'finished')
            ->latest('end_date')
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
        $endedRaffle = $this->getEndedRaffle();
        $myNumbers = $activeRaffle ? $this->getMyNumbers($activeRaffle) : collect();
        $isLogged = Auth::check();
        $user = Auth::user();

        return view('livewire.frontend.public-raffle', compact(
            'activeRaffle', 'endedRaffle', 'myNumbers', 'isLogged', 'user'
        ))->layout('layouts.frontend');
    }
}
