<?php

namespace App\Livewire\Frontend;

use App\Models\Raffle;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicRaffle extends Component
{
    public string $tab = 'general';

    public function getActiveRaffle(): ?Raffle
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId = $routeVendor?->id;

        return Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->when($vendorId, fn ($q) => $q->where('vendor_id', $vendorId))
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('end_date')
            ->first();
    }

    public function getEndedRaffle(): ?Raffle
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId = $routeVendor?->id;

        return Raffle::withoutGlobalScopes()
            ->with(['winner', 'lines', 'platform'])
            ->when($vendorId, fn ($q) => $q->where('vendor_id', $vendorId))
            ->where('status', 'finished')
            ->latest('end_date')
            ->first();
    }

    public function getUpcomingRaffle(): ?Raffle
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId = $routeVendor?->id;

        return Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->when($vendorId, fn ($q) => $q->where('vendor_id', $vendorId))
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
        $activeRaffle   = $this->getActiveRaffle();
        $upcomingRaffle = $this->getUpcomingRaffle();
        $endedRaffle    = $this->getEndedRaffle();
        $myNumbers      = $activeRaffle ? $this->getMyNumbers($activeRaffle) : collect();
        $isLogged       = Auth::check();
        $user           = Auth::user();

        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId    = $routeVendor?->id;

        $baseQuery = fn () => Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->withCount('numbers')
            ->whereIn('status', ['active', 'inactive'])
            ->orderByRaw("CASE status WHEN 'active' THEN 0 ELSE 1 END")
            ->latest('end_date');

        if ($vendorId) {
            $raffles = $baseQuery()->where('vendor_id', $vendorId)->get();

            return view('livewire.frontend.public-raffle', compact(
                'activeRaffle', 'upcomingRaffle', 'endedRaffle', 'myNumbers',
                'isLogged', 'user', 'raffles'
            ) + ['showTabs' => false, 'generalRaffles' => collect(), 'cajeroRaffles' => collect()])
                ->layout('frontend.layouts.app');
        }

        $directIds     = Vendor::where('is_direct', true)->pluck('id');
        $generalRaffles = $baseQuery()->whereIn('vendor_id', $directIds)->get();
        $cajeroRaffles  = $baseQuery()->whereNotIn('vendor_id', $directIds)->get();
        $raffles        = $this->tab === 'general' ? $generalRaffles : $cajeroRaffles;

        return view('livewire.frontend.public-raffle', compact(
            'activeRaffle', 'upcomingRaffle', 'endedRaffle', 'myNumbers',
            'isLogged', 'user', 'raffles'
        ) + ['showTabs' => true, 'generalRaffles' => $generalRaffles, 'cajeroRaffles' => $cajeroRaffles])
            ->layout('frontend.layouts.app');
    }
}
