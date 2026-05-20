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
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('frontend.pages.vendors-index', [
            'vendors' => $vendors,
        ])->layout('frontend.layouts.app', [
            'title' => 'Cajeros - RED PICANTES',
        ]);
    }
}
