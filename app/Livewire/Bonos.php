<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\Platform;
use App\Models\User;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use App\Support\Permissions;
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

    public string $platformId = '';

    public string $bonusType = 'general';

    public string $specificUsername = '';

    public string $bonusPercent = '';

    public string $minDeposit = '';

    public string $maxBonus = '';

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
            'status' => 'required|in:active,upcoming,expired',
            'lineId' => 'required|integer|exists:lines,id',
            'totalQuantity' => 'nullable|integer|min:0',
            'perUserLimit' => 'nullable|integer|min:1',
            'platformId' => 'nullable|integer|exists:platforms,id',
            'bonusType' => 'required|in:general,specific',
            'specificUsername' => 'nullable|required_if:bonusType,specific',
            'bonusPercent' => 'nullable|numeric|min:0|max:100',
            'minDeposit' => 'nullable|numeric|min:0',
            'maxBonus' => 'nullable|numeric|min:0',
        ];
    }

    public function openCreateModal(): void
    {
        $this->checkLinePermission(Permissions::BONO_CREATE);
        $this->resetForm();
        $this->code = Bonus::generateCode();
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addWeek()->format('Y-m-d');
        $this->lineId = (string) (session('active_line_id') ?: $this->availableLines()->first()?->id);
        $this->showModal = true;
    }

    public function openEditModal(int $bonusId): void
    {
        $this->checkLinePermission(Permissions::BONO_UPDATE);
        $bonus = Bonus::withoutGlobalScopes()->findOrFail($bonusId);
        $this->editingBonusId = $bonus->id;
        $this->title = $bonus->title;
        $this->code = $bonus->code ?? '';
        $this->description = $bonus->description ?? '';
        $this->startDate = $bonus->start_date?->format('Y-m-d') ?? '';
        $this->startTime = $bonus->start_date?->format('H:i') ?? '00:00';
        $this->endDate = $bonus->end_date?->format('Y-m-d') ?? '';
        $this->endTime = $bonus->end_date?->format('H:i') ?? '23:59';
        $this->status = Bonus::statusForPeriod($bonus->start_date, $bonus->end_date);
        $this->lineId = (string) $bonus->line_id;
        $this->platformId = (string) ($bonus->platform_id ?? '');
        $this->bonusType = $bonus->type ?? 'general';
        $this->specificUsername = $bonus->user?->username ?? $bonus->user?->email ?? '';
        $this->bonusPercent = $bonus->bonus_percent > 0 ? (string) $bonus->bonus_percent : '';
        $this->minDeposit = $bonus->min_deposit > 0 ? (string) $bonus->min_deposit : '';
        $this->maxBonus = $bonus->max_bonus > 0 ? (string) $bonus->max_bonus : '';
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
            ? $this->checkLinePermission(Permissions::BONO_UPDATE)
            : $this->checkLinePermission(Permissions::BONO_CREATE);

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
            'status' => Bonus::statusForPeriod($start, $end),
            'line_id' => (int) $this->lineId,
            'platform_id' => $this->platformId ? (int) $this->platformId : null,
            'type' => $this->bonusType,
            'user_id' => $this->bonusType === 'specific' ? optional(User::where('email', $this->specificUsername)->orWhere('username', $this->specificUsername)->first())->id : null,
            'bonus_percent' => $this->bonusPercent !== '' ? (float) $this->bonusPercent : 0,
            'min_deposit' => $this->minDeposit !== '' ? (float) $this->minDeposit : 0,
            'max_bonus' => $this->maxBonus !== '' ? (float) $this->maxBonus : 0,
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
        }

        $this->closeModal();
    }

    public function openAssignModal(int $bonusId): void
    {
        $this->checkLinePermission(Permissions::BONO_READ);
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
        $this->checkLinePermission(Permissions::BONO_READ);
        $this->validate([
            'assignUsername' => 'required|string|min:2',
            'assignLineId' => 'required|integer|exists:lines,id',
        ]);
        $this->authorizeLineChoice((int) $this->assignLineId);

        $bonus = Bonus::withoutGlobalScopes()->findOrFail($this->selectedBonusId);
        $bonus->updateStatus();

        if ($bonus->status !== 'active') {
            $this->addError('assignUsername', 'Solo se pueden otorgar bonos activos.');

            return;
        }

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

        if (! $this->clientBelongsToLine($user, (int) $this->assignLineId)) {
            $this->addError('assignUsername', 'El usuario no pertenece a la linea elegida.');

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
        $this->checkLinePermission(Permissions::BONO_READ);
        $assignment = BonusAssignment::with('bonus')->findOrFail($assignmentId);
        $this->authorizeLineChoice((int) $assignment->bonus->line_id);
        $assignment->update(['status' => 'used', 'used_at' => now()]);
        session()->flash('message', 'Bono marcado como reclamado.');

        $this->notify('Bono reclamado', "El bono {$assignment->bonus->title} fue marcado como reclamado.", 'bonuses', '/bonos', 'info');
    }

    public function deleteBonus(int $bonusId): void
    {
        $this->checkLinePermission(Permissions::BONO_DELETE);
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
            'platforms' => Platform::orderBy('name')->get(),
            'selectedBonus' => $this->selectedBonusId ? Bonus::withoutGlobalScopes()->with('line')->find($this->selectedBonusId) : null,
            'canCreateBonus' => $this->hasLinePermission(Permissions::BONO_CREATE),
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
        $this->platformId = '';
        $this->bonusType = 'general';
        $this->specificUsername = '';
        $this->bonusPercent = '';
        $this->minDeposit = '';
        $this->maxBonus = '';
        $this->unlimitedQuantity = true;
        $this->totalQuantity = '';
        $this->unlimitedPerUser = false;
        $this->perUserLimit = '1';
        $this->resetValidation();
    }

    private function bonuses(): Collection
    {
        $this->refreshOperationalStatuses();

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
            'claimed' => $bonuses->sum(fn (Bonus $bonus) => $bonus->assignments->whereIn('status', Bonus::CLAIMED_STATUSES)->count()),
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

    private function clientBelongsToLine(User $user, int $lineId): bool
    {
        return (int) $user->line_id === $lineId
            || $user->lines()->where('lines.id', $lineId)->wherePivot('is_active', true)->exists();
    }

    private function refreshOperationalStatuses(): void
    {
        $lineIds = $this->availableLines()->pluck('id');

        if ($lineIds->isEmpty()) {
            return;
        }

        Bonus::withoutGlobalScopes()
            ->whereIn('line_id', $lineIds)
            ->where('start_date', '>', now())
            ->where('status', '!=', 'upcoming')
            ->update(['status' => 'upcoming']);

        Bonus::withoutGlobalScopes()
            ->whereIn('line_id', $lineIds)
            ->where('end_date', '<', now())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        Bonus::withoutGlobalScopes()
            ->whereIn('line_id', $lineIds)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('status', '!=', 'active')
            ->update(['status' => 'active']);
    }
}
