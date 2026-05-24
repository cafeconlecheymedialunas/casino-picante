<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Vendor;
use Livewire\Component;

class BonusesIndex extends Component
{
    public string $tab = 'general';

    public function render()
    {
        $userId = auth()->id();
        $routeVendor = request()->routeIs('frontend.cajero.*') ? request()->route('vendor') : null;
        $vendorId = $routeVendor?->id;

        $baseQuery = fn () => Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->withCount(['assignments as active_assignments_count' => fn ($q) => $q->whereIn('status', Bonus::CONSUMED_STATUSES)])
            ->whereNotNull('line_id')
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('line', fn ($l) => $l->where('status', 'active'))
            ->latest('start_date');

        // Dentro de un cajero específico: sin tabs
        if ($vendorId) {
            $bonuses = $baseQuery()->where('vendor_id', $vendorId)->get();

            return view('frontend.pages.bonuses-index', [
                'bonuses'        => $bonuses,
                'assignments'    => $this->assignments($userId, $bonuses),
                'showTabs'       => false,
                'generalBonuses' => collect(),
                'cajeroBonuses'  => collect(),
            ])->layout('frontend.layouts.app');
        }

        $directIds = Vendor::where('is_direct', true)->pluck('id');

        $generalBonuses = $baseQuery()->whereIn('vendor_id', $directIds)->get();
        $cajeroBonuses  = $baseQuery()->whereNotIn('vendor_id', $directIds)->get();
        $bonuses        = $this->tab === 'general' ? $generalBonuses : $cajeroBonuses;

        return view('frontend.pages.bonuses-index', [
            'bonuses'        => $bonuses,
            'assignments'    => $this->assignments($userId, $bonuses),
            'showTabs'       => true,
            'generalBonuses' => $generalBonuses,
            'cajeroBonuses'  => $cajeroBonuses,
        ])->layout('frontend.layouts.app');
    }

    private function assignments($userId, $bonuses)
    {
        return $userId
            ? BonusAssignment::where('user_id', $userId)
                ->whereIn('bonus_id', $bonuses->pluck('id'))
                ->get()
                ->keyBy('bonus_id')
            : collect();
    }
}
