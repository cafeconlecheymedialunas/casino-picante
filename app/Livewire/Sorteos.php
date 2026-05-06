<?php

namespace App\Livewire;

use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\User;
use App\Models\Line;
use App\Traits\HasLinePermissions;
use Carbon\Carbon;
use Livewire\Component;

class Sorteos extends Component
{
    use HasLinePermissions;

    public $search = '';
    public $filterStatus = 'all';
    public $viewMode = 'board'; // Default to board as requested

    // Selected raffle for management
    public $selectedRaffleId = null;

    // Create/edit raffle modal
    public $showModal = false;
    public $editingRaffle = null;
    public $title = '';
    public $description = '';
    public $status = 'inactive';
    public $start_date = '';
    public $end_date = '';
    public $start_number = 1;
    public $end_number = 1000;
    public $platform_id = '';

    // Number assignment
    public $assignUserId = '';
    public $assignCount = 1;
    public $manualNumbers = '';

    // Winner registration
    public $showWinnerModal = false;
    public $winner_user_id = '';
    public $winner_number = '';

    // Numbers search
    public $numbersSearch = '';

    protected $rules = [
        'title' => 'required|min:2',
        'description' => 'nullable',
        'status' => 'required|in:active,inactive',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'start_number' => 'required|integer|min:0',
        'end_number' => 'required|integer|gt:start_number',
        'platform_id' => 'nullable|exists:platforms,id',
    ];

    public function openCreate()
    {
        $this->checkLinePermission('sorteo.create');
        $this->resetForm();
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $this->checkLinePermission('sorteo.update');
        $raffle = Raffle::findOrFail($id);
        $this->editingRaffle = $raffle;
        $this->title = $raffle->title;
        $this->description = $raffle->description ?? '';
        $this->status = $raffle->status;
        $this->start_date = $raffle->start_date->format('Y-m-d');
        $this->end_date = $raffle->end_date->format('Y-m-d');
        $this->start_number = $raffle->start_number;
        $this->end_number = $raffle->end_number;
        $this->platform_id = $raffle->platform_id ?? '';
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
        $this->status = 'inactive';
        $this->start_date = '';
        $this->end_date = '';
        $this->start_number = 1;
        $this->end_number = 1000;
        $this->platform_id = '';
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
            'start_number' => $this->start_number,
            'end_number' => $this->end_number,
            'platform_id' => $this->platform_id ?: null,
        ];

        if ($this->editingRaffle) {
            $this->checkLinePermission('sorteo.update');
            $this->editingRaffle->update($data);
            session()->flash('message', 'Sorteo actualizado');
        } else {
            $this->checkLinePermission('sorteo.create');
            $data['line_id'] = session('active_line_id');
            Raffle::create($data);
            session()->flash('message', 'Sorteo creado');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $this->checkLinePermission('sorteo.delete');
        $raffle = Raffle::findOrFail($id);
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
        $this->checkLinePermission('sorteo.update');
        $this->validate([
            'assignUserId' => 'required|exists:users,id',
            'assignCount' => 'required|integer|min:1|max:100',
        ]);

        $raffle = Raffle::findOrFail($this->selectedRaffleId);
        
        if (!$raffle->isAvailable()) {
            session()->flash('error', 'El sorteo no está activo o vigente');
            return;
        }

        $assigned = $raffle->assignNumbers((int) $this->assignUserId, (int) $this->assignCount);

        if (empty($assigned)) {
            session()->flash('error', 'No hay números disponibles en el rango del tablero');
        } else {
            session()->flash('message', count($assigned).' número(s) asignado(s): '.implode(', ', $assigned));
        }

        $this->assignUserId = '';
        $this->assignCount = 1;
    }

    public function assignManual()
    {
        $this->checkLinePermission('sorteo.update');
        
        $this->validate([
            'assignUserId' => 'required|exists:users,id',
            'manualNumbers' => 'required|string',
        ]);

        $raffle = Raffle::findOrFail($this->selectedRaffleId);
        
        if (!$raffle->isAvailable()) {
            session()->flash('error', 'El sorteo no está activo o vigente');
            return;
        }

        $numbers = preg_split('/[,\s-]+/', $this->manualNumbers);
        $numbers = array_filter(array_map('intval', $numbers));

        $assignedCount = 0;
        $errors = [];

        foreach ($numbers as $n) {
            if ($n < $raffle->start_number || $n > $raffle->end_number) {
                $errors[] = "Número {$n} fuera de rango del tablero ({$raffle->start_number}-{$raffle->end_number})";
                continue;
            }

            $exists = RaffleNumber::where('raffle_id', $this->selectedRaffleId)
                ->where('number', $n)
                ->exists();
            
            if ($exists) {
                $errors[] = "Número {$n} ya está ocupado";
                continue;
            }

            RaffleNumber::create([
                'raffle_id' => $this->selectedRaffleId,
                'user_id' => $this->assignUserId,
                'number' => $n
            ]);
            $assignedCount++;
        }

        if ($assignedCount > 0) {
            session()->flash('message', "{$assignedCount} números asignados correctamente");
            $this->manualNumbers = '';
        }

        if (!empty($errors)) {
            session()->flash('error', implode('. ', $errors));
        }
    }

    public function toggleNumber($number)
    {
        $this->checkLinePermission('sorteo.update');
        
        if (!$this->selectedRaffleId) return;
        $raffle = Raffle::findOrFail($this->selectedRaffleId);

        $existing = RaffleNumber::where('raffle_id', $this->selectedRaffleId)
            ->where('number', $number)
            ->first();

        if ($existing) {
            session()->flash('info', "El número {$number} ya está asignado a " . ($existing->user->name ?? 'alguien'));
            return;
        }

        if (!$this->assignUserId) {
            session()->flash('error', 'Selecciona un cliente primero para asignar el número ' . $number);
            return;
        }

        if (!$raffle->isAvailable()) {
            session()->flash('error', 'El sorteo no está activo o vigente');
            return;
        }

        RaffleNumber::create([
            'raffle_id' => $this->selectedRaffleId,
            'user_id' => $this->assignUserId,
            'number' => $number
        ]);

        session()->flash('message', "Número {$number} asignado correctamente");
    }

    public function removeNumber($numberId)
    {
        $this->checkLinePermission('sorteo.update');
        RaffleNumber::findOrFail($numberId)->delete();
        session()->flash('message', 'Número eliminado');
    }

    public function openWinnerModal($raffleId)
    {
        $this->checkLinePermission('sorteo.update');
        $raffle = Raffle::findOrFail($raffleId);
        $this->selectedRaffleId = $raffleId;
        $this->winner_user_id = $raffle->winner_user_id ?? '';
        $this->winner_number = $raffle->winner_number ?? '';
        $this->showWinnerModal = true;
    }

    public function saveWinner()
    {
        $this->checkLinePermission('sorteo.update');
        $this->validate([
            'winner_user_id' => 'nullable|exists:users,id',
            'winner_number' => 'nullable|integer',
        ]);

        Raffle::where('id', $this->selectedRaffleId)->update([
            'winner_user_id' => $this->winner_user_id ?: null,
            'winner_number' => $this->winner_number ?: null,
        ]);

        $this->showWinnerModal = false;
        session()->flash('message', 'Ganador registrado');
    }

    public function toggleStatus($id)
    {
        $this->checkLinePermission('sorteo.update');
        $raffle = Raffle::findOrFail($id);
        $raffle->update([
            'status' => $raffle->status === 'active' ? 'inactive' : 'active'
        ]);
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

        return Raffle::with(['winner', 'numbers' => function ($q) {
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
        $this->checkLinePermission('sorteo.read');
        $raffles = $this->getRaffles();
        $selectedRaffle = $this->getSelectedRaffle();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        
        $line = Line::find(session('active_line_id'));
        $platforms = $line ? $line->platforms : collect();

        return view('livewire.sorteos', compact('raffles', 'selectedRaffle', 'users', 'platforms'))
            ->layout('layouts.dashboard');
    }
}
