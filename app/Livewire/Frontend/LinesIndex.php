<?php

namespace App\Livewire\Frontend;

use App\Models\Line;
use App\Models\Vendor;
use Livewire\Component;

class LinesIndex extends Component
{
    public string $tab = 'directas';

    public function render()
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;

        // Cuando se navega desde un cajero específico, no mostrar tabs
        if ($routeVendor) {
            $lines = Line::withoutGlobalScopes()
                ->with(['activePlatforms', 'lineAgents.agent', 'ratings', 'vendor'])
                ->where('vendor_id', $routeVendor->id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            return view('frontend.pages.lines-index', [
                'lines'      => $lines,
                'showTabs'   => false,
                'directLines' => collect(),
                'cajeroLines' => collect(),
            ])->layout('frontend.layouts.app');
        }

        $directVendorIds = Vendor::where('is_direct', true)->pluck('id');

        $directLines = Line::withoutGlobalScopes()
            ->with(['activePlatforms', 'lineAgents.agent', 'ratings', 'vendor'])
            ->whereIn('vendor_id', $directVendorIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $cajeroLines = Line::withoutGlobalScopes()
            ->with(['activePlatforms', 'lineAgents.agent', 'ratings', 'vendor'])
            ->whereNotIn('vendor_id', $directVendorIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('frontend.pages.lines-index', [
            'lines'       => $this->tab === 'directas' ? $directLines : $cajeroLines,
            'showTabs'    => true,
            'directLines' => $directLines,
            'cajeroLines' => $cajeroLines,
        ])->layout('frontend.layouts.app');
    }
}
