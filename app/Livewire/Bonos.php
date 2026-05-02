<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Bonos extends Component
{
    public $search = '';

    public $filter = 'all';

    public $showModal = false;

    public $editingBonus = null;

    public $showAssignModal = false;

    public $selectedBonus = null;

    public $selectedUser = null;

    public $title = '';

    public $description = '';

    public $type = 'general';

    public $bonus_percent = 0;

    public $bonus_amount = 0;

    public $min_deposit = 0;

    public $max_bonus = 0;

    public $start_date = '';

    public $end_date = '';

    public $status = 'active';

    protected $rules = [
        'title' => 'required|min:3',
        'type' => 'required|in:general,specific',
        'start_date' => 'required',
        'end_date' => 'required',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->addWeek()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal($bonusId)
    {
        $bonus = Bonus::find($bonusId);
        $this->editingBonus = $bonus;
        $this->title = $bonus->title;
        $this->description = $bonus->description ?? '';
        $this->type = $bonus->type;
        $this->bonus_percent = $bonus->bonus_percent ?? 0;
        $this->bonus_amount = $bonus->bonus_amount ?? 0;
        $this->min_deposit = $bonus->min_deposit ?? 0;
        $this->max_bonus = $bonus->max_bonus ?? 0;
        $this->start_date = $bonus->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $bonus->end_date?->format('Y-m-d') ?? '';
        $this->status = $bonus->status;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingBonus = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->type = 'general';
        $this->bonus_percent = 0;
        $this->bonus_amount = 0;
        $this->min_deposit = 0;
        $this->max_bonus = 0;
        $this->start_date = '';
        $this->end_date = '';
        $this->status = 'active';
    }

    public function saveBonus()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'bonus_percent' => $this->bonus_percent,
            'bonus_amount' => $this->bonus_amount,
            'min_deposit' => $this->min_deposit,
            'max_bonus' => $this->max_bonus,
            'start_date' => Carbon::parse($this->start_date),
            'end_date' => Carbon::parse($this->end_date),
            'status' => $this->status,
        ];

        if ($this->editingBonus) {
            $this->editingBonus->update($data);
            session()->flash('message', 'Bono actualizado correctamente');
        } else {
            Bonus::create($data);
            session()->flash('message', 'Bono creado correctamente');
        }

        $this->closeModal();
    }

    public function deleteBonus($bonusId)
    {
        $bonus = Bonus::find($bonusId);
        $bonus->assignments()->delete();
        $bonus->delete();
        session()->flash('message', 'Bono eliminado correctamente');
    }

    public function toggleStatus($bonusId)
    {
        $bonus = Bonus::find($bonusId);
        $bonus->update(['status' => $bonus->status === 'active' ? 'inactive' : 'active']);
    }

    public function openAssignModal($bonusId)
    {
        $this->selectedBonus = Bonus::find($bonusId);
        $this->selectedUser = '';
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->selectedBonus = null;
        $this->selectedUser = '';
    }

    public function assignToUser()
    {
        if (! $this->selectedUser || ! $this->selectedBonus) {
            return;
        }

        $user = User::find($this->selectedUser);

        BonusAssignment::create([
            'bonus_id' => $this->selectedBonus->id,
            'user_id' => $user->id,
            'status' => 'available',
            'assigned_at' => Carbon::now(),
        ]);

        session()->flash('message', 'Bono asignado a '.$user->name);
        $this->closeAssignModal();
    }

    public function markAsUsed($assignmentId)
    {
        $assignment = BonusAssignment::find($assignmentId);
        $assignment->update(['status' => 'used', 'used_at' => Carbon::now()]);
        session()->flash('message', 'Bono marcado como usado');
    }

    public function markAsExpired($assignmentId)
    {
        $assignment = BonusAssignment::find($assignmentId);
        $assignment->update(['status' => 'expired', 'expired_at' => Carbon::now()]);
        session()->flash('message', 'Bono marcado como expirado');
    }

    public function removeAssignment($assignmentId)
    {
        BonusAssignment::find($assignmentId)->delete();
        session()->flash('message', 'Bono eliminado del usuario');
    }

    public function getBonuses()
    {
        $query = Bonus::query();

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getUsers()
    {
        return User::orderBy('name')->get();
    }

    public function getMetrics()
    {
        $now = Carbon::now();

        return [
            'total' => Bonus::count(),
            'active' => Bonus::where('status', 'active')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->count(),
            'assigned' => BonusAssignment::count(),
            'used' => BonusAssignment::where('status', 'used')->count(),
        ];
    }

    public function getAssignments($bonusId)
    {
        return BonusAssignment::where('bonus_id', $bonusId)
            ->with('user')
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    public function render()
    {
        $bonuses = $this->getBonuses();
        $users = $this->getUsers();
        $metrics = $this->getMetrics();

        return view('livewire.bonos', compact('bonuses', 'users', 'metrics'))->layout('layouts.dashboard');
    }
}
