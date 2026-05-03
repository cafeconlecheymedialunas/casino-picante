<?php

namespace App\Livewire;

use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\RafflePosition;
use App\Models\User;
use App\Traits\HasLinePermissions;
use Carbon\Carbon;
use Livewire\Component;

class Sorteos extends Component
{
    use HasLinePermissions;

    public $search = '';
    public $filterStatus = 'all';

    // Selected raffle for management
    public $selectedRaffleId = null;

    // Create/edit raffle modal
    public $showModal = false;
    public $editingRaffle = null;
    public $title = '';
    public $description = '';
    public $status = 'upcoming';
    public $start_date = '';
    public $end_date = '';
    public $number_type = 'infinite';
    public $max_numbers = '';

    // Positions modal
    public $showPositionsModal = false;
    public $positions = []; // [{position, prize_description, prize_amount}]

    // Number assignment
    public $assignUserId = '';
    public $assignCount = 1;

    // Winners modal
    public $showWinnersModal = false;
    public $winners = []; // position_id => {user_id, number}

    // Numbers search
    public $numbersSearch = '';

    protected $rules = [
        'title' => 'required|min:2',
        'description' => 'nullable',
        'status' => 'required|in:upcoming,active,ended',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'number_type' => 'required|in:4digits,infinite',
        'max_numbers' => 'nullable|integer|min:1',
    ];

    public function openCreate()
    {
        $this->resetForm();
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->positions = [
            ['position' => 1, 'prize_description' => '1er Premio', 'prize_amount' => ''],
            ['position' => 2, 'prize_description' => '2do Premio', 'prize_amount' => ''],
            ['position' => 3, 'prize_description' => '3er Premio', 'prize_amount' => ''],
        ];
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $raffle = Raffle::with('positions')->findOrFail($id);
        $this->editingRaffle = $raffle;
        $this->title = $raffle->title;
        $this->description = $raffle->description ?? '';
        $this->status = $raffle->status;
        $this->start_date = $raffle->start_date->format('Y-m-d');
        $this->end_date = $raffle->end_date->format('Y-m-d');
        $this->number_type = $raffle->number_type;
        $this->max_numbers = $raffle->max_numbers ?? '';
        $this->positions = $raffle->positions->map(fn ($p) => [
            'id' => $p->id,
            'position' => $p->position,
            'prize_description' => $p->prize_description,
            'prize_amount' => $p->prize_amount ?? '',
        ])->toArray();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingRaffle = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->status = 'upcoming';
        $this->start_date = '';
        $this->end_date = '';
        $this->number_type = 'infinite';
        $this->max_numbers = '';
        $this->positions = [];
    }

    public function addPosition()
    {
        $nextPos = count($this->positions) + 1;
        $this->positions[] = [
            'position' => $nextPos,
            'prize_description' => $nextPos.'º Premio',
            'prize_amount' => '',
        ];
    }

    public function removePosition($index)
    {
        array_splice($this->positions, $index, 1);
        // Reindex
        foreach ($this->positions as $i => &$pos) {
            $pos['position'] = $i + 1;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => Carbon::parse($this->start_date),
            'end_date' => Carbon::parse($this->end_date),
            'number_type' => $this->number_type,
            'max_numbers' => $this->number_type === '4digits' ? 9999 : ($this->max_numbers ?: null),
        ];

        if ($this->editingRaffle) {
            $this->editingRaffle->update($data);
            $raffle = $this->editingRaffle;
            session()->flash('message', 'Sorteo actualizado');
        } else {
            $data['line_id'] = session('active_line_id');
            $raffle = Raffle::create($data);
            session()->flash('message', 'Sorteo creado');
        }

        // Sync positions
        $existingIds = [];
        foreach ($this->positions as $pos) {
            if (! empty($pos['prize_description'])) {
                $p = RafflePosition::updateOrCreate(
                    ['raffle_id' => $raffle->id, 'position' => $pos['position']],
                    [
                        'prize_description' => $pos['prize_description'],
                        'prize_amount' => $pos['prize_amount'] ?: null,
                    ]
                );
                $existingIds[] = $p->id;
            }
        }
        // Remove deleted positions
        $raffle->positions()->whereNotIn('id', $existingIds)->delete();

        $this->closeModal();
    }

    public function delete($id)
    {
        $raffle = Raffle::findOrFail($id);
        // Deleting raffle resets all numbers (cascade) and positions
        $raffle->delete();

        if ($this->selectedRaffleId == $id) {
            $this->selectedRaffleId = null;
        }

        session()->flash('message', 'Sorteo eliminado');
    }

    public function selectRaffle($id)
    {
        $this->selectedRaffleId = $id;
        $this->assignUserId = '';
        $this->assignCount = 1;
        $this->numbersSearch = '';
    }

    public function assignNumbers()
    {
        $this->validate([
            'assignUserId' => 'required|exists:users,id',
            'assignCount' => 'required|integer|min:1|max:100',
        ]);

        $raffle = Raffle::findOrFail($this->selectedRaffleId);
        $assigned = $raffle->assignNumbers((int) $this->assignUserId, (int) $this->assignCount);

        if (empty($assigned)) {
            session()->flash('error', 'No hay números disponibles');
        } else {
            session()->flash('message', count($assigned).' número(s) asignado(s): '.implode(', ', $assigned));
        }

        $this->assignUserId = '';
        $this->assignCount = 1;
    }

    public function removeNumber($numberId)
    {
        RaffleNumber::findOrFail($numberId)->delete();
        session()->flash('message', 'Número eliminado');
    }

    public function openWinners($raffleId)
    {
        $raffle = Raffle::with(['positions.winner', 'numbers.user'])->findOrFail($raffleId);
        $this->selectedRaffleId = $raffleId;

        $this->winners = [];
        foreach ($raffle->positions as $pos) {
            $this->winners[$pos->id] = [
                'user_id' => $pos->winner_user_id ?? '',
                'number' => $pos->winner_number ?? '',
            ];
        }
        $this->showWinnersModal = true;
    }

    public function saveWinners()
    {
        foreach ($this->winners as $posId => $winner) {
            RafflePosition::where('id', $posId)->update([
                'winner_user_id' => $winner['user_id'] ?: null,
                'winner_number' => $winner['number'] ?: null,
            ]);
        }
        $this->showWinnersModal = false;
        session()->flash('message', 'Ganadores registrados');
    }

    public function closeWinners()
    {
        $this->showWinnersModal = false;
        $this->winners = [];
    }

    public function updateStatus($id, $status)
    {
        Raffle::findOrFail($id)->update(['status' => $status]);
        session()->flash('message', 'Estado actualizado');
    }

    public function getRaffles()
    {
        return Raffle::withCount('numbers')
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->latest()
            ->get();
    }

    public function getSelectedRaffle()
    {
        if (! $this->selectedRaffleId) {
            return null;
        }

        return Raffle::with(['positions.winner', 'numbers' => function ($q) {
            $q->with('user')
                ->when($this->numbersSearch, function ($nq) {
                    $nq->whereHas('user', fn ($uq) => $uq->where('name', 'like', '%'.$this->numbersSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->numbersSearch.'%'));
                })
                ->orderBy('number');
        }])->find($this->selectedRaffleId);
    }

    public function render()
    {
        $raffles = $this->getRaffles();
        $selectedRaffle = $this->getSelectedRaffle();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('livewire.sorteos', compact('raffles', 'selectedRaffle', 'users'))
            ->layout('layouts.dashboard');
    }
}
