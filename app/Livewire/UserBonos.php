<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class UserBonos extends Component
{
    // List filters
    public $search = '';
    public $filterType = 'all'; // all / general / specific
    public $filterStatus = 'all';

    // Assignments tab
    public $assignSearch = '';
    public $activeTab = 'bonuses'; // bonuses / assignments

    // Create/edit modal
    public $showModal = false;
    public $editingBonus = null;
    public $title = '';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $type = 'general';
    public $user_id = '';
    public $status = 'active';

    protected $rules = [
        'title' => 'required|min:2',
        'description' => 'nullable',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'type' => 'required|in:general,specific',
        'user_id' => 'nullable|exists:users,id',
        'status' => 'required|in:active,paused',
    ];

    public function openCreate()
    {
        $this->resetForm();
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->addWeek()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $bonus = Bonus::findOrFail($id);
        $this->editingBonus = $bonus;
        $this->title = $bonus->title;
        $this->description = $bonus->description ?? '';
        $this->start_date = $bonus->start_date->format('Y-m-d');
        $this->end_date = $bonus->end_date->format('Y-m-d');
        $this->type = $bonus->type;
        $this->user_id = $bonus->user_id ?? '';
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
        $this->start_date = '';
        $this->end_date = '';
        $this->type = 'general';
        $this->user_id = '';
        $this->status = 'active';
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->type === 'specific') {
            $rules['user_id'] = 'required|exists:users,id';
        }
        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => Carbon::parse($this->start_date),
            'end_date' => Carbon::parse($this->end_date),
            'type' => $this->type,
            'user_id' => $this->type === 'specific' ? $this->user_id : null,
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

    public function delete($id)
    {
        Bonus::findOrFail($id)->delete();
        session()->flash('message', 'Bono eliminado correctamente');
    }

    public function setAssignmentStatus($bonusId, $userId, $status)
    {
        BonusAssignment::updateOrCreate(
            ['bonus_id' => $bonusId, 'user_id' => $userId],
            [
                'status' => $status,
                'used_at' => $status === 'used' ? now() : null,
                'expired_at' => $status === 'expired' ? now() : null,
            ]
        );
        session()->flash('message', 'Estado actualizado');
    }

    public function getBonuses()
    {
        $query = Bonus::with('user')->latest();

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        return $query->paginate(20);
    }

    public function getAssignments()
    {
        return BonusAssignment::with(['bonus', 'user'])
            ->when($this->assignSearch, function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('name', 'like', '%'.$this->assignSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->assignSearch.'%');
                });
            })
            ->latest()
            ->paginate(30);
    }

    public function getUsersWithActiveBonuses()
    {
        return User::query()
            ->where(function ($q) {
                // Users with specific bonuses
                $q->whereHas('bonuses', function ($bq) {
                    $bq->where('status', 'active')
                        ->where('end_date', '>=', now()->subHours(48));
                })
                // Users who should see general bonuses (no expired assignment)
                ->orWhereDoesntHave('bonusAssignments');
            })
            ->when($this->assignSearch, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', '%'.$this->assignSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->assignSearch.'%');
                });
            })
            ->withCount(['bonusAssignments as active_bonuses' => function ($q) {
                $q->where('status', 'active');
            }])
            ->paginate(30);
    }

    public function render()
    {
        $bonuses = $this->getBonuses();
        $assignments = $this->getAssignments();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('livewire.user-bonos', compact('bonuses', 'assignments', 'users'))
            ->layout('layouts.dashboard');
    }
}
