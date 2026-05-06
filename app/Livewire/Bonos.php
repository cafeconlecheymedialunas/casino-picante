<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\User;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class Bonos extends Component
{
    use HasLinePermissions, SendsNotifications;

    public string $search = '';

    public string $filter = 'all';

    public bool $showModal = false;

    public bool $showAssignModal = false;

    public ?int $editingBonusId = null;

    public ?int $selectedBonusId = null;

    public string $title = '';

    public string $code = '';

    public string $description = '';

    public string $startDate = '';

    public string $startTime = '00:00';

    public string $endDate = '';

    public string $endTime = '23:59';

    public string $status = 'active';

    public string $lineId = '';

    public bool $unlimitedQuantity = true;

    public string $totalQuantity = '';

    public bool $unlimitedPerUser = false;

    public string $perUserLimit = '1';

    public string $assignUsername = '';

    public string $assignLineId = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:160',
            'code' => 'nullable|min:3|max:80',
            'description' => 'nullable|max:2000',
            'startDate' => 'required|date',
            'startTime' => 'required',
            'endDate' => 'required|date',
            'endTime' => 'required',
            'status' => 'required|in:active,upcoming,expired,inactive',
            'lineId' => 'required|integer|exists:lines,id',
            'totalQuantity' => 'nullable|integer|min:0',
            'perUserLimit' => 'nullable|integer|min:1',
        ];
    }

    public function openCreateModal(): void
    {
        $this->checkLinePermission('bono.create');
        $this->resetForm();
        $this->code = Bonus::generateCode();
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addWeek()->format('Y-m-d');
        $this->lineId = (string) (session('active_line_id') ?: $this->availableLines()->first()?->id);
        $this->showModal = true;
    }

    public function openEditModal(int $bonusId): void
    {
        $this->checkLinePermission('bono.update');
        $bonus = Bonus::withoutGlobalScopes()->findOrFail($bonusId);
        $this->editingBonusId = $bonus->id;
        $this->title = $bonus->title;
        $this->code = $bonus->code ?? '';
        $this->description = $bonus->description ?? '';
        $this->startDate = $bonus->start_date?->format('Y-m-d') ?? '';
        $this->startTime = $bonus->start_date?->format('H:i') ?? '00:00';
        $this->endDate = $bonus->end_date?->format('Y-m-d') ?? '';
        $this->endTime = $bonus->end_date?->format('H:i') ?? '23:59';
        $this->status = $bonus->status;
        $this->lineId = (string) $bonus->line_id;
        $this->unlimitedQuantity = $bonus->total_quantity === null;
        $this->totalQuantity = $bonus->total_quantity === null ? '' : (string) $bonus->total_quantity;
        $this->unlimitedPerUser = $bonus->per_user_limit === null;
        $this->perUserLimit = $bonus->per_user_limit === null ? '' : (string) $bonus->per_user_limit;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingBonusId = null;
        $this->resetForm();
    }

    public function saveBonus(): void
    {
        $this->editingBonusId
            ? $this->checkLinePermission('bono.update')
            : $this->checkLinePermission('bono.create');

        $this->validate();
        $this->authorizeLineChoice((int) $this->lineId);

        $start = Carbon::parse($this->startDate.' '.$this->startTime);
        $end = Carbon::parse($this->endDate.' '.$this->endTime);

        if ($end->lt($start)) {
            $this->addError('endDate', 'La fecha de fin debe ser posterior al inicio.');

            return;
        }

        $data = [
            'title' => trim($this->title),
            'code' => trim($this->code) ?: Bonus::generateCode(),
            'description' => trim($this->description) ?: null,
            'type' => 'general',
            'start_date' => $start,
            'end_date' => $end,
            'status' => $this->status,
            'line_id' => (int) $this->lineId,
            'total_quantity' => $this->unlimitedQuantity ? null : (int) $this->totalQuantity,
            'per_user_limit' => $this->unlimitedPerUser ? null : (int) ($this->perUserLimit ?: 1),
        ];

        if ($this->editingBonusId) {
            $bonus = Bonus::withoutGlobalScopes()->findOrFail($this->editingBonusId);
            $bonus->update($data);
            session()->flash('message', 'Bono actualizado correctamente.');

            $this->notify('Bono actualizado', "El bono {$bonus->title} fue actualizado.", 'bonuses', '/bonos', 'info');
        } else {
            $data['created_by'] = session('active_agent_id');
            $bonus = Bonus::create($data);
            session()->flash('message', 'Bono creado correctamente.');

$this->notify('Bono creado', "El bono {$bonus->title} fue creado exitosamente.", 'bonuses', '/bonos', 'success');
            $this->dispatch('notification-created');
        }

        $this->closeModal();
    }

    public function openAssignModal(int $bonusId): void
    {
        $this->checkLinePermission('bono.read');
        $bonus = Bonus::withoutGlobalScopes()->findOrFail($bonusId);
        $this->authorizeLineChoice((int) $bonus->line_id);
        $this->selectedBonusId = $bonus->id;
        $this->assignLineId = (string) $bonus->line_id;
        $this->assignUsername = '';
        $this->showAssignModal = true;
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal = false;
        $this->selectedBonusId = null;
        $this->assignUsername = '';
        $this->assignLineId = '';
    }

    public function assignToUser(): void
    {
        $this->checkLinePermission('bono.read');
        $this->validate([
            'assignUsername' => 'required|string|min:2',
            'assignLineId' => 'required|integer|exists:lines,id',
        ]);
        $this->authorizeLineChoice((int) $this->assignLineId);

        $bonus = Bonus::withoutGlobalScopes()->findOrFail($this->selectedBonusId);
        $user = User::where('username', $this->assignUsername)
            ->orWhere('email', $this->assignUsername)
            ->first();

        if (! $user) {
            $this->addError('assignUsername', 'No existe un usuario con ese username o email.');

            return;
        }

        if ((int) $bonus->line_id !== (int) $this->assignLineId) {
            $this->addError('assignLineId', 'Ese bono no pertenece a la linea elegida.');

            return;
        }

        if (! $bonus->canUserClaim($user->id)) {
            $this->addError('assignUsername', 'El usuario ya alcanzo el limite de uso para este bono.');

            return;
        }

        BonusAssignment::create([
            'bonus_id' => $bonus->id,
            'user_id' => $user->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        session()->flash('message', 'Bono otorgado a '.$user->username.'.');

        $this->notify('Bono asignado', "El bono {$bonus->title} fue asignado a {$user->username}.", 'bonuses', '/bonos', 'success');

        $this->closeAssignModal();
    }

    public function markClaimed(int $assignmentId): void
    {
        $this->checkLinePermission('bono.read');
        $assignment = BonusAssignment::with('bonus')->findOrFail($assignmentId);
        $this->authorizeLineChoice((int) $assignment->bonus->line_id);
        $assignment->update(['status' => 'used', 'used_at' => now()]);
        session()->flash('message', 'Bono marcado como reclamado.');

        $this->notify('Bono reclamado', "El bono {$assignment->bonus->title} fue marcado como reclamado.", 'bonuses', '/bonos', 'info');
    }

    public function deleteBonus(int $bonusId): void
    {
        $this->checkLinePermission('bono.delete');
        $bonus = Bonus::withoutGlobalScopes()->findOrFail($bonusId);
        $this->authorizeLineChoice((int) $bonus->line_id);
        $bonusTitle = $bonus->title;
        $bonus->assignments()->delete();
        $bonus->delete();
        session()->flash('message', 'Bono eliminado correctamente.');

        $this->notify('Bono eliminado', "El bono {$bonusTitle} fue eliminado del sistema.", 'bonuses', '/bonos', 'danger');
    }

    public function render()
    {
        return view('livewire.bonos', [
            'bonuses' => $this->bonuses(),
            'metrics' => $this->metrics(),
            'lines' => $this->availableLines(),
            'selectedBonus' => $this->selectedBonusId ? Bonus::withoutGlobalScopes()->find($this->selectedBonusId) : null,
            'canCreateBonus' => $this->hasLinePermission('bono.create'),
        ])->layout('layouts.dashboard');
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->code = '';
        $this->description = '';
        $this->startDate = '';
        $this->startTime = '00:00';
        $this->endDate = '';
        $this->endTime = '23:59';
        $this->status = 'active';
        $this->lineId = '';
        $this->unlimitedQuantity = true;
        $this->totalQuantity = '';
        $this->unlimitedPerUser = false;
        $this->perUserLimit = '1';
        $this->resetValidation();
    }

    private function bonuses(): Collection
    {
        $query = Bonus::withoutGlobalScopes()
            ->with(['line', 'assignments.user'])
            ->whereIn('line_id', $this->availableLines()->pluck('id'))
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(fn ($inner) => $inner
                    ->where('title', 'like', $search)
                    ->orWhere('code', 'like', $search)
                    ->orWhereHas('line', fn ($line) => $line->where('name', 'like', $search)));
            })
            ->when($this->filter !== 'all', fn ($query) => $query->where('status', $this->filter));

        return $query->orderByDesc('created_at')->get();
    }

    private function metrics(): array
    {
        $bonuses = $this->bonuses();

        return [
            'total' => $bonuses->count(),
            'active' => $bonuses->where('status', 'active')->count(),
            'upcoming' => $bonuses->where('status', 'upcoming')->count(),
            'expired' => $bonuses->where('status', 'expired')->count(),
            'claimed' => $bonuses->sum(fn (Bonus $bonus) => $bonus->assignments->whereIn('status', ['used', 'claimed'])->count()),
        ];
    }

    private function availableLines(): Collection
    {
        if ($this->isAdminMode()) {
            return Line::orderBy('name')->get();
        }

        return Line::whereHas('lineAgents', fn ($query) => $query
            ->where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
        )->orderBy('name')->get();
    }

    private function authorizeLineChoice(int $lineId): void
    {
        if (! $this->availableLines()->pluck('id')->contains($lineId)) {
            abort(403, 'No podes operar bonos fuera de tus lineas.');
        }
    }
}
