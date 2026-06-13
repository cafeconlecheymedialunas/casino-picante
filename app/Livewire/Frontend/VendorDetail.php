<?php

namespace App\Livewire\Frontend;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\Line;
use App\Models\Platform;
use App\Models\Raffle;
use App\Models\Sale;
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

        $lineLimit = 4;
        $bonusLimit = 3;
        $raffleLimit = 2;

        $linesQuery = Line::withoutGlobalScopes()
            ->where('vendor_id', $vendorId)
            ->where('status', 'active');

        $totalLines = $linesQuery->count();

        $allLineIds = $linesQuery->clone()->pluck('id');

        $lines = $linesQuery->clone()
            ->with(['activePlatforms', 'lineAgents.agent', 'ratings'])
            ->orderBy('name')
            ->take($lineLimit)
            ->get();

        $bonusesQuery = Bonus::withoutGlobalScopes()
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->where('end_date', '>=', now());

        $totalBonuses = $bonusesQuery->count();

        $bonuses = $bonusesQuery->clone()
            ->with(['line', 'platform'])
            ->latest('start_date')
            ->take($bonusLimit)
            ->get();

        $rafflesQuery = Raffle::withoutGlobalScopes()
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        $totalRaffles = $rafflesQuery->count();

        $raffles = $rafflesQuery->clone()
            ->with(['lines', 'platform'])
            ->orderBy('end_date')
            ->take($raffleLimit)
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
            'totalLines' => $totalLines,
            'totalBonuses' => $totalBonuses,
            'totalRaffles' => $totalRaffles,
            'lineLimit' => $lineLimit,
            'bonusLimit' => $bonusLimit,
            'raffleLimit' => $raffleLimit,
            'totalPlatforms' => Platform::whereHas('lines', fn ($q) => $q->withoutGlobalScopes()->whereIn('lines.id', $allLineIds)->where('line_platform.is_active', true)
            )->where('is_active', true)->count(),
            'averageRating' => round((float) \App\Models\LineRating::withoutGlobalScopes()
                ->whereIn('line_id', $allLineIds)->avg('rating') ?? 0, 1),
            'ratingsCount' => (int) \App\Models\LineRating::withoutGlobalScopes()
                ->whereIn('line_id', $allLineIds)->count(),
            'agents' => Agent::whereHas('lines', fn ($q) => $q->where('lines.vendor_id', $vendorId))->get(),
            'totalClients' => Sale::withoutGlobalScopes()
                ->whereIn('line_id', $allLineIds)
                ->distinct('client_id')->count('client_id'),
            'totalOperations' => Sale::withoutGlobalScopes()
                ->whereIn('line_id', $allLineIds)
                ->count(),
            'memberSince' => $this->vendor->created_at,
        ])->layout('frontend.layouts.app', [
            'title' => $this->vendor->name.' - RED PICANTES',
        ]);
    }
}
