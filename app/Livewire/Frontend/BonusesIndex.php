<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use Livewire\Component;

class BonusesIndex extends Component
{
    public function render()
    {
        $userId = auth()->id();
        $bonuses = Bonus::withoutGlobalScopes()
            ->with(['line', 'platform'])
            ->withCount(['assignments as active_assignments_count' => fn ($query) => $query->whereIn('status', Bonus::CONSUMED_STATUSES)])
            ->whereNotNull('line_id')
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('line', fn ($line) => $line->where('status', 'active'))
            ->latest('start_date')
            ->get();

        $assignments = $userId
            ? BonusAssignment::where('user_id', $userId)
                ->whereIn('bonus_id', $bonuses->pluck('id'))
                ->get()
                ->keyBy('bonus_id')
            : collect();

        return view('frontend.pages.bonuses-index', [
            'bonuses' => $bonuses,
            'assignments' => $assignments,
        ])->layout('frontend.layouts.app');
    }
}
