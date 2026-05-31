<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\Line;
use App\Models\LineRating;
use App\Models\Raffle;
use App\Models\Vendor;
use Livewire\Component;

class LineShow extends Component
{
    public Line $line;

    public ?int $selectedRating = null;

    public string $ratingMessage = '';

    public function mount(Vendor $vendor, Line $line): void
    {
        abort_unless($line->status === 'active', 404);
        abort_unless((int) $line->vendor_id === (int) $vendor->id, 404);

        $this->line = $line->load(['activePlatforms', 'lineAgents.agent']);

        if (auth()->check()) {
            $rating = $this->line->ratings()->withoutGlobalScopes()->where('user_id', auth()->id())->first();
            $this->selectedRating = $rating?->rating;
            $this->ratingMessage = $rating?->message ?? '';
        }
    }

    public function setRating(int $rating): void
    {
        abort_unless($rating >= 1 && $rating <= 5, 422);

        $this->selectedRating = $rating;
    }

    public function saveRating(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $validated = $this->validate([
            'selectedRating' => ['required', 'integer', 'min:1', 'max:5'],
            'ratingMessage' => ['nullable', 'string', 'max:500'],
        ], [
            'selectedRating.required' => 'Elegí una valoración.',
            'ratingMessage.max' => 'El mensaje no puede superar 500 caracteres.',
        ]);

        LineRating::withoutGlobalScopes()->updateOrCreate(
            ['line_id' => $this->line->id, 'user_id' => auth()->id()],
            [
                'vendor_id' => $this->line->vendor_id,
                'rating' => $validated['selectedRating'],
                'message' => trim($validated['ratingMessage'] ?? '') ?: null,
            ]
        );
    }

    public function render()
    {
        $ratingAverage = (float) $this->line->ratings()->withoutGlobalScopes()->avg('rating');
        $ratingCount = $this->line->ratings()->withoutGlobalScopes()->count();

        $activeBonuses = Bonus::withoutGlobalScopes()
            ->where('line_id', $this->line->id)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $activeRaffles = $this->line->belongsToMany(Raffle::class, 'line_raffle')
            ->withoutGlobalScopes()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('frontend.pages.line-show', [
            'line' => $this->line,
            'ratingAverage' => $ratingAverage,
            'ratingCount' => $ratingCount,
            'ratings' => $this->line->ratings()->withoutGlobalScopes()->with('user')->latest()->take(8)->get(),
            'activeBonuses' => $activeBonuses,
            'activeRaffles' => $activeRaffles,
        ])->layout('frontend.layouts.app');
    }
}
