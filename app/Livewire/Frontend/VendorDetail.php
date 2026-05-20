<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\Line;
use App\Models\Raffle;
use App\Models\Vendor;
use Livewire\Component;

class VendorDetail extends Component
{
    public Vendor $vendor;

    public function mount(Vendor $vendor): void
    {
        $this->vendor = $vendor->load('user');
    }

    public function render()
    {
        $vendorId = $this->vendor->id;

        $lines = Line::withoutGlobalScopes()
            ->with(['activePlatforms', 'lineAgents.agent', 'ratings'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $bonuses = Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest('start_date')
            ->take(3)
            ->get();

        $raffles = Raffle::withoutGlobalScopes()
            ->with(['lines', 'platform'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('end_date')
            ->take(2)
            ->get();

        return view('frontend.pages.vendor-detail', [
            'vendor' => $this->vendor,
            'cajero' => $this->vendor->user,
            'contacts' => collect($this->vendor->contacts ?? [])
                ->filter(fn ($contact) => filled($contact['value'] ?? null))
                ->values(),
            'lines' => $lines,
            'bonuses' => $bonuses,
            'raffles' => $raffles,
            'totalPlatforms' => $lines->flatMap(fn ($line) => $line->activePlatforms)->unique('id')->count(),
            'averageRating' => round((float) $lines->avg(fn ($line) => $line->average_rating), 1),
            'ratingsCount' => (int) $lines->sum(fn ($line) => $line->ratings_count),
        ])->layout('frontend.layouts.app', [
            'title' => $this->vendor->name.' - RED PICANTES',
        ]);
    }
}
