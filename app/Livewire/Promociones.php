<?php

namespace App\Livewire;

use App\Models\Promotion;
use Carbon\Carbon;
use Livewire\Component;

class Promociones extends Component
{
    public $filter = 'all';

    public $selectedPromo = null;

    public function selectPromo($id)
    {
        $this->selectedPromo = Promotion::find($id);
    }

    public function getPromotions()
    {
        $query = Promotion::query();

        if ($this->filter !== 'all') {
            $now = Carbon::now();
            switch ($this->filter) {
                case 'active':
                    $query->where('status', 'published')
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                    break;
                case 'upcoming':
                    $query->where('status', 'published')
                        ->where('start_date', '>', $now);
                    break;
                case 'ended':
                    $query->where('end_date', '<', $now);
                    break;
                case 'draft':
                    $query->where('status', 'draft');
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $promotions = $this->getPromotions();

        return view('livewire.promociones', compact('promotions'))->extends('layouts.dashboard');
    }
}
