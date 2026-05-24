<?php

namespace App\Livewire\Frontend;

use App\Models\Line;
use App\Models\Vendor;
use Livewire\Component;

class LinePlatforms extends Component
{
    public Line $line;

    public ?Vendor $vendor = null;

    public function mount(Line $line, ?Vendor $vendor = null): void
    {
        abort_unless($line->status === 'active', 404);

        if ($vendor) {
            abort_unless($vendor->is_active && (int) $line->vendor_id === (int) $vendor->id, 404);
            $this->vendor = $vendor;
        } else {
            $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
            $vendorId = $routeVendor?->id;
            abort_unless(! $vendorId || (int) $line->vendor_id === (int) $vendorId, 404);
        }

        $this->line = $line->load('vendor');
    }

    public function render()
    {
        $platforms = $this->line->activePlatforms()
            ->where('platforms.is_active', true)
            ->orderBy('platforms.name')
            ->get();

        return view('frontend.pages.line-platforms', [
            'line' => $this->line,
            'vendor' => $this->vendor ?: $this->line->vendor,
            'platforms' => $platforms,
        ])->layout('frontend.layouts.app', [
            'title' => 'Plataformas '.$this->line->name.' - RED PICANTES',
        ]);
    }
}
