<?php

namespace App\Livewire\Frontend;

use App\Models\Raffle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RaffleShow extends Component
{
    public Raffle $raffle;

    public function mount(int|string $raffleId): void
    {
        $this->raffle = Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform', 'winner'])
            ->findOrFail($raffleId);
    }

    public function getMyNumbersProperty(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return $this->raffle->numbers()
            ->where('user_id', Auth::id())
            ->orderBy('number')
            ->get();
    }

    public function getLeadersProperty(): Collection
    {
        return $this->raffle->numbers()
            ->with(['user', 'line'])
            ->get()
            ->groupBy('user_id')
            ->map(function ($numbers) {
                $first = $numbers->first();

                return [
                    'user' => $first?->user,
                    'line' => $first?->line,
                    'numbers' => $numbers->pluck('number')->sort()->values(),
                    'participations' => $numbers->count(),
                    'score' => $numbers->count() * 125.10,
                ];
            })
            ->sortByDesc('participations')
            ->values()
            ->take(10);
    }

    public function getPrizeWinnersProperty(): Collection
    {
        if (! $this->raffle->isFinished()) {
            return collect();
        }

        return collect($this->raffle->prizes ?? [])
            ->filter(fn ($prize) => ! empty($prize['winner_number']) || ! empty($prize['winner_name']) || ! empty($prize['winner_username']))
            ->sortBy(fn ($prize) => (int) ($prize['position'] ?? 999))
            ->values();
    }

    public function render()
    {
        $numbersCount = $this->raffle->numbers()->count();
        $participantsCount = $this->raffle->numbers()->distinct('user_id')->count('user_id');

        return view('frontend.pages.raffle-show', [
            'raffle' => $this->raffle,
            'myNumbers' => $this->myNumbers,
            'leaders' => $this->leaders,
            'prizeWinners' => $this->prizeWinners,
            'numbersCount' => $numbersCount,
            'participantsCount' => $participantsCount,
        ])->layout('frontend.layouts.app');
    }
}
