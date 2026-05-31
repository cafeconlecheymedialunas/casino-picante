<?php

namespace App\Livewire\Frontend;

use App\Models\Vendor;
use App\Support\PublicPageContent;
use Livewire\Component;

class VendorsIndex extends Component
{
    public function render()
    {
        $pageSection = PublicPageContent::page('cajeros');
        $tabs        = PublicPageContent::tabs($pageSection);
        $selectedIds = collect($tabs)->flatMap(fn ($t) => $t['item_ids'] ?? [])->unique()->filter()->values()->all();

        $baseQuery = Vendor::query()
            ->with(['user'])
            ->withCount(['lines as active_lines_count' => fn ($q) => $q->where('status', 'active')])
            ->leftJoin('lines', 'vendors.id', '=', 'lines.vendor_id')
            ->leftJoin('line_ratings', 'lines.id', '=', 'line_ratings.line_id')
            ->leftJoin('sales', 'lines.id', '=', 'sales.line_id')
            ->select('vendors.*')
            ->selectRaw('COALESCE(AVG(line_ratings.rating), 0) as avg_rating')
            ->selectRaw('COUNT(DISTINCT sales.client_id) as total_clients')
            ->groupBy('vendors.id')
            ->where('vendors.is_active', true);

        if ($selectedIds) {
            $vendors = $baseQuery->whereIn('vendors.id', $selectedIds)->get()
                ->sortBy(fn ($v) => array_search((int) $v->id, array_map('intval', $selectedIds)))
                ->values();
        } else {
            $vendors = $baseQuery->orderByDesc('avg_rating')->orderByDesc('total_clients')->orderBy('vendors.name')->get();
        }

        return view('frontend.pages.vendors-index', [
            'vendors'     => $vendors,
            'pageSection' => $pageSection,
        ])->layout('frontend.layouts.app', [
            'title' => ($pageSection?->title ?: 'Cajeros').' - RED PICANTES',
        ]);
    }
}
