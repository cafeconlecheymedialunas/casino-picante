<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\Platform;
use App\Models\User;
use App\Services\NotificationService;
use App\Models\Role;
use App\Support\Permissions;
use App\Support\Roles;
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

    public string $platformId = '';

    public string $bonusType = 'general';

    public string $specificUsername = '';

    public string $bonusPercent = '';

    public string $minDeposit = '';

    public string $maxBonus = '';

    public string $assignUsername = '';

    public string $assignLineId = '';

    public array $assignUserIds = [];

    public bool $showAssignmentsPanel = false;

    public ?int $bonusForAssignments = null;

    public string $assignmentsSearch = '';

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
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addWeek()->format('Y-m-d');
        $this->lineId = (string) ($this->lineIdForScopedCreate() ?: '');
        $this->showModal = true;
    }

    public function openEditModal(int $bonusId): void
    {
        $this->checkLinePermission(Permissions::BONO_UPDATE);
        $bonus = Bonus::withoutGlobalScopes()->findOrFail($bonusId);
        $this->authorizeLineChoice((int) $bonus->line_id);
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
        $line = Line::withoutGlobalScopes()->findOrFail((int) $this->lineId);
        $this->authorizeLineChoice($line->id);
        $this->authorizePlatformChoice($this->platformId, $line);

        $start = Carbon::parse($this->startDate.' '.$this->startTime);
        $end = Carbon::parse($this->endDate.' '.$this->endTime);

        if ($end->lt($start)) {
            $this->addError('endDate', 'La fecha de fin debe ser posterior al inicio.');

            return;
        }

        $specificUser = null;
        if ($this->bonusType === 'specific') {
            $specificUser = User::where('email', $this->specificUsername)
                ->orWhere('username', $this->specificUsername)
                ->first();

            if (! $specificUser || $specificUser->status !== 'active' || ! $this->clientBelongsToLine($specificUser, (int) $this->lineId)) {
                $this->addError('specificUsername', 'Selecciona un cliente activo de la linea del bono.');

                return;
            }
        }

        $data = [
            'title' => trim($this->title),
            'code' => trim($this->code) ?: Bonus::generateCode(),
            'description' => trim($this->description) ?: null,
            'type' => $this->bonusType ?: 'general',
            'start_date' => $start,
            'end_date' => $end,
            'status' => Bonus::statusForPeriod($start, $end),
            'line_id' => (int) $this->lineId,
            'platform_id' => $this->platformId ? (int) $this->platformId : null,
            'user_id' => $specificUser?->id,
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
            $data['vendor_id'] = $line->vendor_id ?: session('active_vendor_id');
            $bonus = Bonus::create($data);
            session()->flash('message', 'Bono creado correctamente.');

            $this->notify('Bono creado', "El bono {$bonus->title} fue creado exitosamente.", 'bonuses', '/bonos', 'success');
        }

        $this->closeModal();
    }

    public function openAssignModal(int $bonusId): void
    {
        $this->checkLinePermission(Permissions::BONO_READ);
        $this->requireVendorContextForWrite();
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
        $this->assignUserIds = [];
    }

    public function assignToUser(): void
    {
        $this->checkLinePermission(Permissions::BONO_READ);
        $this->requireVendorContextForWrite();

        if (empty($this->assignUserIds)) {
            $this->addError('assignUserIds', 'Seleccioná al menos un usuario.');

            return;
        }

        $this->authorizeLineChoice((int) $this->assignLineId);

        $bonus = Bonus::withoutGlobalScopes()->findOrFail($this->selectedBonusId);
        $this->authorizeLineChoice((int) $bonus->line_id);

        if ((int) $this->assignLineId !== (int) $bonus->line_id) {
            $this->addError('assignUserIds', 'La linea seleccionada no coincide con el bono.');

            return;
        }

        $bonus->updateStatus();

        if ($bonus->status !== 'active') {
            $this->addError('assignUserIds', 'Solo se pueden otorgar bonos activos.');

            return;
        }

        $assigned = [];
        $skipped = [];

        foreach ($this->assignUserIds as $userId) {
            $user = User::find($userId);
            if (! $user) {
                continue;
            }

            $lineCheck = ! $bonus->line_id || $this->clientBelongsToLine($user, (int) $bonus->line_id);
            if ($user->status !== 'active' || ! $lineCheck) {
                $skipped[] = $user->username ?? $user->email;

                continue;
            }

            if (! $bonus->canUserClaim($user->id)) {
                $skipped[] = $user->username ?? $user->email;

                continue;
            }

            BonusAssignment::create([
                'vendor_id' => $bonus->vendor_id ?: session('active_vendor_id'),
                'bonus_id' => $bonus->id,
                'user_id' => $user->id,
                'status' => 'active',
                'assigned_at' => now(),
            ]);

            NotificationService::sendToClient(
                '¡Nuevo bono recibido!',
                "Recibiste el bono {$bonus->title}. Canjealo ahora.",
                $user->id,
                'success',
                route('frontend.bonos', [], false),
                'bonuses'
            );

            $assigned[] = $user->username ?? $user->email;
        }

        $msg = 'Bono otorgado a '.implode(', ', $assigned).'.';
        if ($skipped) {
            $msg .= ' Omitidos (límite alcanzado): '.implode(', ', $skipped).'.';
        }

        session()->flash('message', $msg);
        $this->notify('Bono asignado', "El bono {$bonus->title} fue asignado a ".count($assigned).' usuario(s).', 'bonuses', '/bonos', 'success');

        $this->closeAssignModal();
    }

    public function markClaimed(int $assignmentId): void
    {
        $this->checkLinePermission(Permissions::BONO_READ);
        $this->requireVendorContextForWrite();
        $assignment = BonusAssignment::withoutGlobalScopes()->with('bonus')->findOrFail($assignmentId);
        $this->authorizeLineChoice((int) $assignment->bonus->line_id);
        $assignment->update(['status' => 'used', 'used_at' => now()]);

        NotificationService::sendToClient(
            'Bono activado',
            "Tu bono {$assignment->bonus->title} ha sido activado",
            $assignment->user_id,
            'success',
            route('frontend.bonos', [], false),
            'bonuses'
        );

        session()->flash('message', 'Bono marcado como reclamado.');

        $this->notify('Bono activado', "El bono {$assignment->bonus->title} fue marcado como activado.", 'bonuses', '/bonos', 'info');
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
        $panelBonus = $this->bonusForAssignments
            ? $this->accessibleBonusesQuery()->with('line')->find($this->bonusForAssignments)
            : null;

        $panelAssignments = $this->getAssignmentsForPanel();

        return view('livewire.bonos', [
            'bonuses' => $this->bonuses(),
            'metrics' => $this->metrics(),
            'platforms' => $this->availablePlatforms(),
            'selectedBonus' => $this->selectedBonusId ? $this->accessibleBonusesQuery()->with('line')->find($this->selectedBonusId) : null,
            'canCreateBonus' => $this->hasLinePermission(Permissions::BONO_CREATE),
            'panelBonus' => $panelBonus,
            'panelAssignments' => $panelAssignments,
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
        $vendorId = session('active_vendor_id');

        if ($this->isAdminMode()) {
            return Line::when($vendorId, fn ($q) => $q->where('vendor_id', (int) $vendorId))
                ->orderBy('name')->get();
        }

        return Line::whereHas('lineAgents', fn ($query) => $query
            ->where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
        )
        ->when($vendorId, fn ($q) => $q->where('vendor_id', (int) $vendorId))
        ->orderBy('name')->get();
    }

    private function authorizeLineChoice(int $lineId): void
    {
        $this->ensureLineMatchesActiveVendor($lineId, 'No podes operar bonos en lineas fuera del vendor activo.');

        if (! $this->availableLines()->pluck('id')->contains($lineId)) {
            abort(403, 'No podes operar bonos fuera de tus lineas.');
        }
    }

    private function authorizePlatformChoice(string $platformId, ?Line $line = null): void
    {
        if ($platformId === '') {
            return;
        }

        $platform = Platform::withoutGlobalScopes()->find((int) $platformId);
        abort_unless($platform, 403, 'La plataforma seleccionada no existe.');
        abort_unless(! $platform->vendor_id || (int) $platform->vendor_id === (int) $line?->vendor_id, 403, 'No podes usar plataformas fuera del vendor activo.');
    }

    private function availablePlatforms(): Collection
    {
        $vendorId = session('active_vendor_id');

        return Platform::withoutGlobalScopes()
            ->where('is_active', true)
            ->when($vendorId, fn ($query) => $query->where('vendor_id', (int) $vendorId))
            ->orderBy('name')
            ->get();
    }

    public function openAssignmentsPanel(int $bonusId): void
    {
        $this->checkLinePermission(Permissions::BONO_READ);
        $bonus = Bonus::withoutGlobalScopes()->findOrFail($bonusId);
        $this->authorizeLineChoice((int) $bonus->line_id);

        $this->bonusForAssignments = $bonusId;
        $this->assignmentsSearch = '';
        $this->showAssignmentsPanel = true;
    }

    public function closeAssignmentsPanel(): void
    {
        $this->showAssignmentsPanel = false;
        $this->bonusForAssignments = null;
        $this->assignmentsSearch = '';
    }

    public function getAssignmentsForPanel()
    {
        if (! $this->bonusForAssignments) {
            return collect();
        }

        return BonusAssignment::withoutGlobalScopes()->with('user')
            ->where('bonus_id', $this->bonusForAssignments)
            ->whereHas('bonus', fn ($bonus) => $bonus->whereIn('line_id', $this->availableLines()->pluck('id')))
            ->when($this->assignmentsSearch, function ($q) {
                $s = '%'.$this->assignmentsSearch.'%';
                $q->whereHas('user', fn ($u) => $u->where('username', 'like', $s)->orWhere('email', 'like', $s));
            })
            ->orderByDesc('assigned_at')
            ->get();
    }

    public function getUsersForAssign(): array
    {
        $clientRoleId = Role::where('name', Roles::CLIENTE)->value('id');
        $lineId = $this->selectedBonusId
            ? (int) $this->accessibleBonusesQuery()->find($this->selectedBonusId)?->line_id
            : (int) session('active_line_id');
        $vendorId = session('active_vendor_id');

        $query = User::withoutGlobalScopes()
            ->when($clientRoleId, fn ($q) => $q->where('role_id', $clientRoleId))
            ->where('status', 'active')
            ->orderBy('username');

        if ($lineId) {
            $query->where(function ($q) use ($lineId, $vendorId) {
                $q->where('line_id', $lineId)
                    ->orWhereHas('lines', fn ($lq) => $lq->where('lines.id', $lineId)->wherePivot('is_active', true))
                    ->when($vendorId, fn ($inner) => $inner->orWhere('vendor_id', $vendorId));
            });
        } elseif ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        return $query
            ->get(['id', 'username', 'name', 'email'])
            ->map(fn ($u) => ['id' => $u->id, 'label' => $u->username ?? $u->name ?? $u->email])
            ->toArray();
    }

    private function clientBelongsToLine(User $user, int $lineId): bool
    {
        return (int) $user->line_id === $lineId
            || $user->lines()->where('lines.id', $lineId)->wherePivot('is_active', true)->exists();
    }

    private function accessibleBonusesQuery()
    {
        return Bonus::withoutGlobalScopes()
            ->whereIn('line_id', $this->availableLines()->pluck('id'));
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
