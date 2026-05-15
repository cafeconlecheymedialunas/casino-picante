<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use Livewire\Component;

class BonusShow extends Component
{
    public int $bonusId;

    public function mount(int $bonusId): void
    {
        $this->bonusId = $bonusId;
    }

    public function render()
    {
        $bonus = Bonus::withoutGlobalScopes()
            ->with(['line.activePlatforms', 'platform'])
            ->withCount(['assignments as active_assignments_count' => fn ($query) => $query->whereIn('status', Bonus::CONSUMED_STATUSES)])
            ->findOrFail($this->bonusId);

        $assignment = auth()->id()
            ? BonusAssignment::where('user_id', auth()->id())->where('bonus_id', $bonus->id)->first()
            : null;

        return view('frontend.pages.bonus-show', [
            'bonus' => $bonus,
            'assignment' => $assignment,
        ])->layout('frontend.layouts.app');
    }
}
