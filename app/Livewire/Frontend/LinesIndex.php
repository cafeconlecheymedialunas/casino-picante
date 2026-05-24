<?php

namespace App\Livewire\Frontend;

use App\Models\Line;
use Livewire\Component;

class LinesIndex extends Component
{
    public function render()
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId = $routeVendor?->id;

        $lines = Line::withoutGlobalScopes()
            ->with(['activePlatforms', 'lineAgents.agent', 'ratings', 'vendor'])
            ->when($vendorId, fn ($query) => $query->where('vendor_id', $vendorId))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('frontend.pages.lines-index', [
            'lines' => $lines,
        ])->layout('frontend.layouts.app');
    }
}
