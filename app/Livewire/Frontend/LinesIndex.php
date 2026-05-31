<?php

namespace App\Livewire\Frontend;

use App\Models\Line;
use App\Models\Vendor;
use App\Support\PublicPageContent;
use Livewire\Component;

class LinesIndex extends Component
{
    public string $tab = '';

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
                'pageSection' => null,
                'activeTab' => null,
            ])->layout('frontend.layouts.app');
        }

        $pageSection = PublicPageContent::page('lineas');
        $tabs = PublicPageContent::tabs($pageSection);

        if ($tabs) {
            if (! collect($tabs)->firstWhere('key', $this->tab)) {
                $this->tab = $tabs[0]['key'];
            }

            $activeTab = collect($tabs)->firstWhere('key', $this->tab) ?: $tabs[0];

            return view('frontend.pages.lines-index', [
                'lines' => PublicPageContent::orderedItems('lineas', $activeTab['item_ids']),
                'showTabs' => count($tabs) > 1,
                'tabs' => $tabs,
                'activeTab' => $activeTab,
                'pageSection' => $pageSection,
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
            'pageSection' => null,
            'activeTab' => null,
        ])->layout('frontend.layouts.app');
    }
}
