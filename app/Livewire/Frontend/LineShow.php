<?php

namespace App\Livewire\Frontend;

use App\Models\Line;
use App\Models\LineRating;
use Livewire\Component;

class LineShow extends Component
{
    public Line $line;

    public ?int $selectedRating = null;

    public string $ratingMessage = '';

    public function mount(Line $line): void
    {
        abort_unless($line->status === 'active', 404);

        $this->line = $line->load(['activePlatforms', 'lineAgents.agent']);

        if (auth()->check()) {
            $rating = $this->line->ratings()->where('user_id', auth()->id())->first();
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

        LineRating::updateOrCreate(
            ['line_id' => $this->line->id, 'user_id' => auth()->id()],
            [
                'rating' => $validated['selectedRating'],
                'message' => trim($validated['ratingMessage'] ?? '') ?: null,
            ]
        );
    }

    public function render()
    {
        $ratingAverage = (float) $this->line->ratings()->avg('rating');
        $ratingCount = $this->line->ratings()->count();

        return view('frontend.pages.line-show', [
            'line' => $this->line,
            'ratingAverage' => $ratingAverage ?: 5,
            'ratingCount' => $ratingCount,
            'ratings' => $this->line->ratings()->with('user')->latest()->take(8)->get(),
        ])->layout('frontend.layouts.app');
    }
}
