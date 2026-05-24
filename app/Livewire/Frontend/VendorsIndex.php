<?php

namespace App\Livewire\Frontend;

use App\Models\Vendor;
use Livewire\Component;

class VendorsIndex extends Component
{
    public function render()
    {
        $vendors = Vendor::query()
            ->with(['user'])
            ->withCount([
                'lines as active_lines_count' => fn ($query) => $query->where('status', 'active'),
            ])
            ->leftJoin('lines', 'vendors.id', '=', 'lines.vendor_id')
            ->leftJoin('line_ratings', 'lines.id', '=', 'line_ratings.line_id')
            ->leftJoin('sales', 'lines.id', '=', 'sales.line_id')
            ->select('vendors.*')
            ->selectRaw('COALESCE(AVG(line_ratings.rating), 0) as avg_rating')
            ->selectRaw('COUNT(DISTINCT sales.client_id) as total_clients')
            ->groupBy('vendors.id')
            ->where('vendors.is_active', true)
            ->orderByDesc('avg_rating')
            ->orderByDesc('total_clients')
            ->orderBy('vendors.name')
            ->get();

        return view('frontend.pages.vendors-index', [
            'vendors' => $vendors,
        ])->layout('frontend.layouts.app', [
            'title' => 'Cajeros - RED PICANTES',
        ]);
    }
}
