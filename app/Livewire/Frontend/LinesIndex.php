<?php

namespace App\Livewire\Frontend;

use App\Models\Line;
use Livewire\Component;

class LinesIndex extends Component
{
    public function render()
    {
        $lines = Line::with(['activePlatforms', 'lineAgents.agent'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('frontend.pages.lines-index', [
            'lines' => $lines,
        ])->layout('frontend.layouts.app');
    }
}
