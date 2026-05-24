<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use Livewire\Component;

class BonusShow extends Component
{
    public string $bonusSlug;

    public function mount(string $bonusSlug): void
    {
        $this->bonusSlug = $bonusSlug;
    }

    public function render()
    {
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId = $routeVendor?->id;

        $bonus = Bonus::withoutGlobalScopes()
            ->with(['line.activePlatforms', 'platform'])
            ->withCount(['assignments as active_assignments_count' => fn ($query) => $query->whereIn('status', Bonus::CONSUMED_STATUSES)])
            ->when($vendorId, fn ($query) => $query->where('vendor_id', $vendorId))
            ->where('slug', $this->bonusSlug)
            ->firstOrFail();

        $assignment = auth()->id()
            ? BonusAssignment::where('user_id', auth()->id())->where('bonus_id', $bonus->id)->first()
            : null;

        return view('frontend.pages.bonus-show', [
            'bonus' => $bonus,
            'assignment' => $assignment,
        ])->layout('frontend.layouts.app');
    }
}
