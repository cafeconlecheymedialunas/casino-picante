<?php

namespace App\Livewire\Frontend;

use App\Models\Platform;
use App\Models\Vendor;
use Livewire\Component;

class VendorPlatforms extends Component
{
    public Vendor $vendor;

    public function mount(Vendor $vendor): void
    {
        abort_unless($vendor->is_active, 404);
        $this->vendor = $vendor->load('user');
    }

    public function render()
    {
        $platforms = Platform::withoutGlobalScopes()
            ->where('platforms.is_active', true)
            ->whereHas('lines', function ($q) {
                $q->withoutGlobalScopes()
                    ->where('lines.vendor_id', $this->vendor->id)
                    ->where('lines.status', 'active')
                    ->wherePivot('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('frontend.pages.vendor-platforms', [
            'vendor' => $this->vendor,
            'platforms' => $platforms,
        ])->layout('frontend.layouts.app', [
            'title' => 'Plataformas '.$this->vendor->name.' - RED PICANTES',
        ]);
    }
}
